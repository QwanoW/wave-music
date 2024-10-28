<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$queryParams = $_GET;

try {
  $db = new \App\Database();
  $table = new \App\Objects\Table($db->getConnection());

  // Prepare the base query
  $baseQuery = "SELECT p.id, p.title, p.description, p.cover_uri, p.created_at, p.user_id, p.is_private, u.id AS user_id,
              u.username AS user_name, u.email AS user_email, u.role AS user_role, u.created_at AS user_created_at,
              GROUP_CONCAT(DISTINCT t.id ORDER BY t.id SEPARATOR ',') AS track_ids
              FROM playlist p
              LEFT JOIN playlistTrack pt ON p.id = pt.playlist_id
              LEFT JOIN user u ON p.user_id = u.id
              LEFT JOIN track t ON pt.track_id = t.id";

  $params = [];
  $queryClauses = buildQueryClauses($queryParams, $params);

  $query = $baseQuery . $queryClauses;

  // Fetch all playlists
  $playlists = $table->fetchAll($query, $params);

  foreach ($playlists as &$track) {
    // Fetch tracks for the playlist
    $trackIds = explode(',', $track['track_ids']);
    $trackIdsPlaceholders = implode(',', array_fill(0, count($trackIds), '?'));
    $tracksQuery = "SELECT t.id, t.title, t.cover_uri, 
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
                      LEFT JOIN trackArtist ta ON t.id = ta.track_id
                      WHERE t.id IN ($trackIdsPlaceholders) GROUP BY t.id, g.id, c.id, l.id, a.id";
    $tracks = $table->fetchAll($tracksQuery, $trackIds);

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
        'cover_uri' => $track['album_cover']
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
        $track['artist_ids']
      );
    }

    $track['owner'] = [
      'id' => $track['user_id'],
      'username' => $track['user_name'],
      'email' => $track['user_email'],
      'role' => $track['user_role'],
      'created_at' => $track['user_created_at']
    ];

    $track['tracks'] = $tracks;

    unset($track['track_ids'], $track['user_id'], $track['user_name'], $track['user_email'], $track['user_role'], $track['user_created_at']);
  }

  echo json_encode($playlists, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['message' => $e->getMessage()]);
}

function buildQueryClauses($queryParams, &$params)
{
  $whereConditions = [];
  $allowedParams = [
    'id' => 'p.id = :id',
    'search' => 'p.title LIKE :search',
    'owner' => 'p.user_id = :owner',
  ];

  foreach ($allowedParams as $key => $condition) {
    if (isset($queryParams[$key])) {
      if ($key === 'owner' && $queryParams[$key] === 'null') {
        $whereConditions[] = 'p.user_id IS NULL';
      } else {
        $whereConditions[] = $condition;
        $params[$key] = $key === 'search' ? '%' . $queryParams[$key] . '%' : $queryParams[$key];
      }
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

  return $whereClause . " GROUP BY p.id, p.title, p.description, p.cover_uri" . $orderClause . $limitClause;
}

