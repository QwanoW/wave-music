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
              a.id AS album_id, a.title AS album_title, a.cover_uri AS album_cover, at.order AS album_order,
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

  foreach ($tracks as &$track) {
    $track['genre'] = [
      'id' => $track['genre_id'],
      'name' => $track['genre_name'],
      'cover_uri' => $track['genre_cover']
    ];
    $track['content'] = [
      'id' => $track['content_id'],
      'uri' => $track['content_uri'],
      'duration' => $track['duration'],
      'contentType' => $track['contentType'],
      'created_at' => $track['created_at']
    ];
    $track['language'] = [
      'id' => $track['language_id'],
      'name' => $track['language_name']
    ];
    $track['album'] = [
      'id' => $track['album_id'],
      'title' => $track['album_title'],
      'cover_uri' => $track['album_cover'],
      'order' => $track['album_order']
    ];

    // Fetch artists for the track
    $artistsQuery = "SELECT artist.id, artist.name, artist.bio, artist.photo_uri 
                      FROM artist 
                      INNER JOIN trackArtist ON artist.id = trackArtist.artist_id 
                      WHERE trackArtist.track_id = :track_id";
    $albums = $table->fetchAll($artistsQuery, ['track_id' => $track['id']]);
    $track['artists'] = $albums;

    unset(
      $track['genre_id'],
      $track['genre_name'],
      $track['genre_cover'],
      $track['content_id'],
      $track['content_uri'],
      $track['duration'],
      $track['contentType'],
      $track['created_at'],
      $track['language_id'],
      $track['language_name'],
      $track['album_id'],
      $track['album_title'],
      $track['album_cover'],
      $track['artist_ids'],
      $track['album_order']
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

  // Handle 'id' separately as it can be an array or a single value
  if (isset($queryParams['id'])) {
    if (is_array($queryParams['id'])) {
      $whereConditions[] = 't.id IN (' . implode(',', array_fill(0, count($queryParams['id']), '?')) . ')';
      $params = array_merge($params, $queryParams['id']);
    } else {
      $whereConditions[] = 't.id = :id';
      $params['id'] = $queryParams['id'];
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

  return $whereClause . " GROUP BY t.id, g.id, c.id, l.id, a.id, at.order" . $orderClause . $limitClause;
}
