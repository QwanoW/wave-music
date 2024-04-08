<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['name']) || isset($_FILES['cover'])) {
  $name = $_POST['name'];
  $cover = $_FILES['cover'];

  $cover_name = App\Services\ImageService::save_image($cover);

  if ($cover_name) {
    $db = new \App\Database();
    $genreDB = new \App\Objects\Genre($db->getConnection());

    $ok = $genreDB->insert($name, $cover_name);

    if (!$ok) {
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