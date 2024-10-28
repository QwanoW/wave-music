<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id']) && isset($_POST['title']) && isset($_POST['tracks'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $tracks = $_POST['tracks'];

  $cover = $_FILES['cover'] ?? null;

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $albumDB = new \App\Objects\Album($pdo);
    $albumTrackDB = new \App\Objects\AlbumTrack($pdo);


    if (is_null($cover)) {
      $track = $albumDB->getById($id);

      $cover_uri = $track['cover_uri'];
    } else {
      $cover_uri = App\Services\ImageService::save_image($cover);

      if (is_null($cover_uri)) {
        throw new Exception("Не удалось загрузить обложку");
      }
    }

    $albumDB->update(['id' => $id, 'title' => $title, 'cover_uri' => $cover_uri]);



    $albumTrackDB->deleteAllByAlbumId($id);

    foreach ($tracks as $track) {
      $albumTrackDB->insert(['track_id' => $track['id'], 'album_id' => $id, 'order' => $track['order']]);
    }

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Альбом успешно изменен',
    ]);
  } catch (\Exception $e) {
    var_dump($e->getMessage());
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось обновить альбом'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

