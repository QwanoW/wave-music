<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

try {
  $db = new \App\Database();
  $table = new \App\Objects\Table($db->getConnection());
  $query = "SELECT t.id, t.title, t.cover_uri, g.id AS genre_id, g.name AS genre_name, g.cover_uri AS genre_cover,
    c.id AS content_id, c.uri AS content_uri, c.duration, c.contentType, c.created_at,
    l.id AS language_id, l.name AS language_name
    FROM track t
    LEFT JOIN genre g ON t.genre_id = g.id
    LEFT JOIN content c ON t.content_id = c.id
    LEFT JOIN language l ON t.language_id = l.id";

  $tracks = $table->fetchAll($query);

  foreach ($tracks as &$playlist) {
    $playlist['genre'] = ['id' => $playlist['genre_id'], 'name' => $playlist['genre_name'], 'cover_uri' => $playlist['genre_cover']];
    $playlist['content'] = ['id' => $playlist['content_id'], 'uri' => $playlist['content_uri'], 'duration' => $playlist['duration'], 'contentType' => $playlist['contentType'], 'created_at' => $playlist['created_at']];
    $playlist['language'] = ['id' => $playlist['language_id'], 'name' => $playlist['language_name']];
    unset($playlist['genre_name'], $playlist['genre_cover'], $playlist['content_uri'], $playlist['duration'], $playlist['contentType'], $playlist['created_at'], $playlist['language_name'], $playlist['genre_id'], $playlist['content_id'], $playlist['language_id']);

    // fetch all artists
    $query = "SELECT artist.id, artist.name, artist.bio, artist.photo_uri FROM trackArtist INNER JOIN artist ON trackArtist.artist_id = artist.id WHERE track_id = :track_id";
    $artists = $table->fetchAll($query, ['track_id' => $playlist['id']]);
    $playlist['artists'] = $artists;
  }

  echo json_encode($tracks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => $e->getMessage()]);
}
