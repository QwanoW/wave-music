<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

try {
  $db = new \App\Database();
  $pdo = $db->getConnection();

  $userDB = new \App\Objects\User($pdo);

  $existing_user = $userDB->getById($user->id);

  if (!$existing_user) {
    http_response_code(404);

    echo json_encode(["message" => "Пользователь не найден"]);
    exit();
  }

  $token = [
    "exp" => time() + 7 * 24 * 3600,
    "data" =>
      [
        "id" => $existing_user['id'],
        "username" => $existing_user['username'],
        "email" => $existing_user['email'],
        "role" => $existing_user['role'],
        "created_at" => $existing_user['created_at'],
        "auth_type" => $existing_user['auth_type'],
        "is_subscribed" => (bool) $existing_user['is_subscribed'],
        "trial_expires_at" => $existing_user['trial_expires_at'],
      ],
  ];

  $jwt = App\Services\TokenService::encode_jwt($token);

  if ($jwt) {
    http_response_code(200);

    echo json_encode(
      [
        "jwt" => $jwt
      ]
    );
  } else {
    http_response_code(500);

    echo json_encode(["message" => "Не удалось создать токен"]);
  }

} catch (Exception $e) {
  http_response_code(500);

  echo json_encode(["message" => $e->getMessage()]);
}