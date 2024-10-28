<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();


try {
  $db = new \App\Database();
  $pdo = $db->getConnection();

  $userDB = new \App\Objects\User($pdo);

  $userDB->updateSubscription(['id' => $user->id, 'is_subscribed' => 0, 'trial_expires_at' => null]);

  http_response_code(200);

  echo json_encode([
    'message' => 'Подписка отменена',
  ]);

} catch (Exception $e) {
  http_response_code(500);

  echo json_encode(["message" => $e->getMessage()]);
}
