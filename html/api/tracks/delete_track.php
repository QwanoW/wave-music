<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id'])) {
  $id = $_POST['id'];

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $contentDB = new \App\Objects\Content($pdo);
    $trackDB = new \App\Objects\Track($pdo);

    $track = $trackDB->getById($id);
    if ($track) {
      $contentDB->delete($track['content_id']);
      $trackDB->delete($id);

      $pdo->commit();

      http_response_code(200);

      echo json_encode([
        'message' => 'Трек удалён',
      ]);
    } else {
      http_response_code(404);

      echo json_encode([
        'message' => 'Трек не найден',
      ]);
    }
  } catch (\Exception $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось удалить трек'
    ]);
  }
} else {
  http_response_code(400);
  echo json_encode([
    'message' => 'Ошибка запроса'
  ]);
}