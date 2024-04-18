<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$queryParams = $_GET;

try {
  $db = new \App\Database();
  $table = new \App\Objects\Table($db->getConnection());

  // Prepare the base query
  $baseQuery = "SELECT t.id, t.title, t.cover_uri, 
              g.id AS genre_id, g.name AS genre_name, g.cover_uri AS genre_cover,
              c.id AS content_id, c.uri AS content_uri, c.duration, c.contentType, c.created_at,
              l.id AS language_id, l.name AS language_name,
              a.id AS album_id, a.title AS album_title, a.cover_uri AS album_cover,
              GROUP_CONCAT(DISTINCT ta.artist_id ORDER BY ta.artist_id SEPARATOR ',') AS artist_ids
              FROM track t
              LEFT JOIN genre g ON t.genre_id = g.id
              LEFT JOIN content c ON t.content_id = c.id
              LEFT JOIN language l ON t.language_id = l.id
              LEFT JOIN albumTrack at ON t.id = at.track_id
              LEFT JOIN album a ON at.album_id = a.id
              LEFT JOIN trackArtist ta ON t.id = ta.track_id";

  $params = [];
  $queryClauses = buildQueryClauses($queryParams, $params);

  $query = $baseQuery . $queryClauses;

  // Fetch all tracks
  $tracks = $table->fetchAll($query, $params);

  foreach ($tracks as &$playlist) {
    $playlist['genre'] = [
      'id' => $playlist['genre_id'],
      'name' => $playlist['genre_name'],
      'cover_uri' => $playlist['genre_cover']
    ];
    $playlist['content'] = [
      'id' => $playlist['content_id'],
      'uri' => $playlist['content_uri'],
      'duration' => $playlist['duration'],
      'contentType' => $playlist['contentType'],
      'created_at' => $playlist['created_at']
    ];
    $playlist['language'] = [
      'id' => $playlist['language_id'],
      'name' => $playlist['language_name']
    ];
    $playlist['album'] = [
      'id' => $playlist['album_id'],
      'title' => $playlist['album_title'],
      'cover_uri' => $playlist['album_cover']
    ];

    // Fetch artists for the track
    $artistsQuery = "SELECT artist.id, artist.name, artist.bio, artist.photo_uri 
                      FROM artist 
                      INNER JOIN trackArtist ON artist.id = trackArtist.artist_id 
                      WHERE trackArtist.track_id = :track_id";
    $artists = $table->fetchAll($artistsQuery, ['track_id' => $playlist['id']]);
    $playlist['artists'] = $artists;

    unset(
      $playlist['genre_id'],
      $playlist['genre_name'],
      $playlist['genre_cover'],
      $playlist['content_id'],
      $playlist['content_uri'],
      $playlist['duration'],
      $playlist['contentType'],
      $playlist['created_at'],
      $playlist['language_id'],
      $playlist['language_name'],
      $playlist['album_id'],
      $playlist['album_title'],
      $playlist['album_cover'],
      $playlist['artist_ids']
    );
  }

  echo json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['message' => $e->getMessage()]);
}

function buildQueryClauses($queryParams, &$params)
{
  $whereConditions = [];
  $allowedParams = [
    'id' => 't.id = :id',
    'album_id' => 'at.album_id = :album_id',
    'genre_id' => 't.genre_id = :genre_id',
    'language_id' => 't.language_id = :language_id',
    'search' => 't.title LIKE :search',
    'artist_id' => 'ta.artist_id = :artist_id'
  ];

  foreach ($allowedParams as $key => $condition) {
    if (isset($queryParams[$key])) {
      $whereConditions[] = $condition;
      $params[$key] = $key === 'search' ? '%' . $queryParams[$key] . '%' : $queryParams[$key];
    }
  }

  $whereClause = !empty($whereConditions) ? ' WHERE ' . implode(' AND ', $whereConditions) : '';

  $limitClause = '';
  if (isset($queryParams['limit']) && ctype_digit($queryParams['limit'])) {
    $limitClause = ' LIMIT :limit';
    $params['limit'] = (int) $queryParams['limit'];
  }

  if (isset($queryParams['offset']) && ctype_digit($queryParams['offset'])) {
    $limitClause .= ' OFFSET :offset';
    $params['offset'] = (int) $queryParams['offset'];
  }

  $orderClause = '';
  $allowedSortFields = ['id', 'title', 'created_at'];
  $allowedSortDirections = ['asc', 'desc'];

  if (isset($queryParams['sort_by']) && in_array($queryParams['sort_by'], $allowedSortFields)) {
    $direction = 'asc';
    if (isset($queryParams['sort_direction']) && in_array(strtolower($queryParams['sort_direction']), $allowedSortDirections)) {
      $direction = strtolower($queryParams['sort_direction']);
    }
    $orderClause = ' ORDER BY ' . $queryParams['sort_by'] . ' ' . $direction;
  }

  return $whereClause . " GROUP BY t.id, g.id, c.id, l.id, a.id" . $orderClause . $limitClause;
}
