<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['title']) && isset($_POST['tracks']) && isset($_FILES['cover'])) {

  $title = $_POST['title'];
  $tracks = $_POST['tracks'];

  $cover = $_FILES['cover'];

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $playlistDB = new \App\Objects\Album($pdo);
    $albumTrackDB = new \App\Objects\AlbumTrack($pdo);

    $cover_uri = App\Services\ImageService::save_image($cover);

    if (is_null($cover_uri)) {
      throw new Exception("Не удалось загрузить обложку");
    }

    $album_id = $playlistDB->insert(['title' => $title, 'cover_uri' => $cover_uri]);

    foreach ($tracks as $playlist) {
      $albumTrackDB->insert(['track_id' => $playlist['id'], 'album_id' => $album_id, 'order' => $playlist['order']]);
    }

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Альбом добавлен',
    ]);
  } catch (\Exception $e) {
    var_dump($e->getMessage());
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось загрузить альбом'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

