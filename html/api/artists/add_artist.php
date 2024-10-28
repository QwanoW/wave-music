<?php
// TODO: REWRITE WITH TRY CATCH

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['name']) && $_POST['bio'] && isset($_FILES['photo'])) {
  $name = $_POST['name'];
  $bio = $_POST['bio'];
  $photo = $_FILES['photo'];

  $photo_uri = App\Services\ImageService::save_image($photo);

  if ($photo_uri) {
    $db = new \App\Database();
    $trackDB = new \App\Objects\Artist($db->getConnection());

    $id = $trackDB->insert($name, $bio, $photo_uri);

    if (!$id) {
      http_response_code(500);

      echo json_encode([
        'message' => 'Не удалось добавить исполнителя'
      ]);
    } else {
      http_response_code(200);

      echo json_encode([
        'message' => 'Исполнитель добавлен',
      ]);
    }
  } else {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось загрузить фото исполнителя'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}