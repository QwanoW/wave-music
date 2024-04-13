<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['name'])) {
  $name = $_POST['name'];

  $db = new \App\Database();
  $languageDB = new \App\Objects\Language($db->getConnection());

  $id = $languageDB->insert($name);

  if (!$id) {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось добавить язык'
    ]);
  } else {
    http_response_code(200);

    echo json_encode([
      'message' => 'Язык добавлен',
      'id' => $id
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}