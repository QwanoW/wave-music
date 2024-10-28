<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

$db = new \App\Database();
$languageDB = new \App\Objects\Language($db->getConnection());

if (isset($_POST['id'])) {
  $id = $_POST['id'];
  $name = $_POST['name'];

  $id = $languageDB->update(['id' => $id, 'name' => $name]);

  if (!$id) {
    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось изменить язык'
    ]);
  } else {
    http_response_code(200);

    echo json_encode([
      'message' => 'Язык изменен',
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}
