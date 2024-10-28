<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::protect_route();

try {
  $db = new \App\Database();
  $table = new \App\Objects\Table($db->getConnection());

  // Выводим данные для дашборда
  $query = "SELECT COUNT(*) FROM track";
  $countTracks = $table->fetchOne($query)['COUNT(*)'];

  $quert = "SELECT COUNT(*) FROM playlist";
  $countPlaylist = $table->fetchOne($quert)['COUNT(*)'];

  $query = "SELECT COUNT(*) FROM user";
  $countUsers = $table->fetchOne($query)['COUNT(*)'];

  $query = "SELECT COUNT(*) FROM album";
  $countAlbums = $table->fetchOne($query)['COUNT(*)'];

  $query = "SELECT COUNT(*) FROM artist";
  $countArtists = $table->fetchOne($query)['COUNT(*)'];

  $query = "SELECT COUNT(*) FROM genre";
  $countGenres = $table->fetchOne($query)['COUNT(*)'];

  $query = "SELECT COUNT(*) FROM userLikedTracks";
  $countUserLikedTracks = $table->fetchOne($query)['COUNT(*)'];

  echo json_encode([
    'tracks' => $countTracks,
    'playlists' => $countPlaylist,
    'users' => $countUsers,
    'albums' => $countAlbums,
    'artists' => $countArtists,
    'genres' => $countGenres,
    'user_liked_tracks' => $countUserLikedTracks
  ]);
} catch (\Exception $e) {
  http_response_code(500);
  echo $e->getMessage();
}