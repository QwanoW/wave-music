<?php
// TODO: REWRITE WITH TRY CATCH

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id']) && isset($_POST['name'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];

  $db = new \App\Database();
  $trackDB = new \App\Objects\Genre($db->getConnection());

  $track = $trackDB->getById($id);

  $oldPhoto = $track['cover_uri'];

  if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['cover'];
    $photo_uri = App\Services\ImageService::save_image($photo);
  } else {
    $photo_uri = $oldPhoto;
  }

  $id = $trackDB->update(['id' => $id, 'name' => $name, 'cover_uri' => $photo_uri]);

  if (!$id) {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось изменить жанр'
    ]);
  } else {
    http_response_code(200);

    echo json_encode([
      'message' => 'Жанр изменен',
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}
