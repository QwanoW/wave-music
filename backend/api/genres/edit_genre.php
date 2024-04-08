<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id']) && isset($_POST['name'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];

  $db = new \App\Database();
  $genreDB = new \App\Objects\Genre($db->getConnection());

  $genre = $genreDB->getById($id);

  $oldCover = $genre['cover_uri'];

  if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
    $cover = $_FILES['cover'];
    $cover_name = App\Services\ImageService::save_image($cover);
  } else {
    $cover_name = $oldCover;
  }

  $ok = $genreDB->update(['id' => $id, 'name' => $name, 'cover_uri' => $cover_name]);

  if (!$ok) {
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
