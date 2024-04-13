<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

if (isset($_POST['id'])) {
  $id = $_POST['id'];

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $albumDB = new \App\Objects\Playlist($pdo);

    $playlist = $albumDB->getById($id);
    if ($playlist) {
      $albumDB->delete($id);

      $pdo->commit();

      http_response_code(200);

      echo json_encode([
        'message' => 'Плейлист удалён',
      ]);
    } else {
      http_response_code(404);

      echo json_encode([
        'message' => 'Плейлист не найден',
      ]);
    }
  } catch (\Exception $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось удалить плейлист'
    ]);
  }
} else {
  http_response_code(400);
  echo json_encode([
    'message' => 'Ошибка запроса'
  ]);
}