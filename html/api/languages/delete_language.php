<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id'])) {
  $id = $_POST['id'];

  $db = new \App\Database();
  $languageDB = new \App\Objects\Language($db->getConnection());

  $id = $languageDB->delete($id);

  if (!$id) {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось удалить язык'
    ]);
  } else {
    http_response_code(200);

    echo json_encode([
      'message' => 'Язык удален',
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}
