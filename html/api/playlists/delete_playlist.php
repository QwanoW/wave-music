<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();



if (isset($_GET['id'])) {
  $id = $_GET['id'];
  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();
  
    $pdo->beginTransaction();

    $albumDB = new \App\Objects\Playlist($pdo);

    $track = $albumDB->getById($id);

    if ($user->role !== App\UserRole::ADMIN->name || $user->id !== $track['user_id']) {
      http_response_code(403);

      echo json_encode([
        'message' => 'Доступ запрещён',
      ]);
      exit();
    }

    if ($track) {
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