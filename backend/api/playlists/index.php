<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

try {
  $db = new \App\Database();
  $albumDB = new \App\Objects\Playlist($db->getConnection());
  $playlistTrackDB = new \App\Objects\PlaylistTrack($db->getConnection());
  $playlists = $albumDB->getAll();

  foreach ($playlists as &$playlist) {
    $playlist['tracks'] = $playlistTrackDB->getAllByPlaylistId($playlist['id']);
  }

  http_response_code(200);

  echo json_encode($playlists);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['message' => $e->getMessage()]);
}
