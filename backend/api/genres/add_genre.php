<?php
// TODO: REWRITE WITH TRY CATCH

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['name']) && isset($_FILES['cover'])) {
  $name = $_POST['name'];
  $photo = $_FILES['cover'];

  $photo_uri = App\Services\ImageService::save_image($photo);

  if ($photo_uri) {
    $db = new \App\Database();
    $trackDB = new \App\Objects\Genre($db->getConnection());

    $id = $trackDB->insert($name, $photo_uri);

    if (!$id) {
      http_response_code(500);

      echo json_encode([
        'message' => 'Не удалось добавить жанр'
      ]);
    } else {
      http_response_code(200);

      echo json_encode([
        'message' => 'Жанр добавлен',
      ]);
    }
  } else {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось загрузить обложку'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}