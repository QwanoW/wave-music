<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (isset($_POST['duration'])) {
  $duration = (int) $_POST['duration'];
  $trial_expires_at = date('Y-m-d H:i:s', time() + $duration * 24 * 3600);

  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $userDB = new \App\Objects\User($pdo);

    $userDB->updateSubscription(['id' => $user->id, 'is_subscribed' => 1, 'trial_expires_at' => $trial_expires_at]);

    http_response_code(200);

    echo json_encode([
      'message' => 'Подписка активирована',
    ]);

  } catch (Exception $e) {
    http_response_code(500);

    echo json_encode(["message" => $e->getMessage()]);
  }
} else {
  http_response_code(400);
  echo json_encode([
    'message' => 'Ошибка запроса'
  ]);
}