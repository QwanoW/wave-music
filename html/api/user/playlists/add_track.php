<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (
  isset($_POST['track_id']) && isset($_POST['playlist_id'])
) {
  $track_id = $_POST['track_id'];
  $playlist_id = $_POST['playlist_id'];

  $db = new \App\Database();
  $pdo = $db->getConnection();

  try {
    $albumDB = new \App\Objects\Playlist($pdo);
    $playlistTrackDB = new \App\Objects\PlaylistTrack($pdo);
    $userDB = new \App\Objects\User($pdo);

    $existing_playlist = $albumDB->getById($playlist_id);
    
    if (!$existing_playlist || $existing_playlist['user_id'] !== $user->id) {
      throw new \Exception('Плейлист не найден');
    }

    $playlistTrackDB->insert(['playlist_id' => $playlist_id, 'track_id' => $track_id]);

    http_response_code(200);

    echo json_encode([
      'message' => 'Плейлист добавлен',
      'data' => $created_playlist
    ]);
  } catch (\Exception $e) {
    http_response_code(500);

    echo json_encode([
      'message' => $e->getMessage()
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}