<?php
// TODO: REWRITE WITH TRY CATCH

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['bio'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];
  $bio = $_POST['bio'];

  $db = new \App\Database();
  $trackDB = new \App\Objects\Artist($db->getConnection());

  $track = $trackDB->getById($id);

  $oldPhoto = $track['photo_uri'];

  if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo = $_FILES['photo'];
    $photo_uri = App\Services\ImageService::save_image($photo);
  } else {
    $photo_uri = $oldPhoto;
  }

  $id = $trackDB->update(['id' => $id, 'name' => $name, 'bio' => $bio, 'photo_uri' => $photo_uri]);

  if (!$id) {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось изменить исполнителя'
    ]);
  } else {
    http_response_code(200);

    echo json_encode([
      'message' => 'Исполнитель изменен',
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

