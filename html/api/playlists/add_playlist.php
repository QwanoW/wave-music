<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (
  isset($_POST['title']) && isset($_FILES['cover']) && isset($_POST['tracks']) && isset($_POST['is_private'])
) {

  $title = $_POST['title'];
  $is_private = (int) $_POST['is_private'];
  $user_id = $_POST['user_id'] ?? null;
  $description = $_POST['description'] ?? null;

  $cover = $_FILES['cover'];
  $tracks = $_POST['tracks'];

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $albumDB = new \App\Objects\Playlist($pdo);
    $playlistTrackDB = new \App\Objects\PlaylistTrack($pdo);

    $cover_uri = App\Services\ImageService::save_image($cover);

    if (is_null($cover_uri)) {
      throw new Exception("Не удалось загрузить обложку");
    }

    $album_id = $albumDB->insert(['title' => $title, 'description' => $description, 'cover_uri' => $cover_uri, 'user_id' => $user_id, 'is_private' => $is_private]);

    foreach ($tracks as $track_id) {
      $playlistTrackDB->insert(['playlist_id' => $album_id, 'track_id' => $track_id]);
    }

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Плейлист добавлен',
    ]);
  } catch (\Exception $e) {
    $pdo->rollBack();

    var_dump($e->getMessage());

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось загрузить плейлист'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

