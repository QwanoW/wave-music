<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $pdo->beginTransaction();

    $albumDB = new \App\Objects\Album($pdo);

    $track = $albumDB->getById($id);

    if ($track) {
      $albumDB->delete($id);

      $pdo->commit();

      http_response_code(200);

      echo json_encode([
        'message' => 'Альбом удалён',
      ]);
    } else {
      http_response_code(404);

      echo json_encode([
        'message' => 'Альбом не найден',
      ]);
    }
  } catch (\Exception $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось удалить альбом'
    ]);
  }
} else {
  http_response_code(400);
  echo json_encode([
    'message' => 'Ошибка запроса'
  ]);
}