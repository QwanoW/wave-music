<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use \App\Services\TokenService;

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  exit();
}

if (isset($_POST['email']) && isset($_POST['password'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  try {
    $database = new \App\Database();
    $db = $database->getConnection();

    $user = new \App\Objects\User($db);

    $user = $user->getByEmail($email);

    if (!$user || !password_verify($password, $user['password'])) {
      http_response_code(401);

      echo json_encode(
        ['message' => 'Неверный логин или пароль']
      );
      exit;
    }
    ;

    $token = [
      "exp" => time() + 7 * 24 * 3600,
      "data" =>
        [
          "id" => $user['id'],
          "username" => $user['username'],
          "email" => $user['email'],
          "role" => $user['role'],
          "created_at" => $user['created_at'],
          "auth_type" => $user['auth_type'],
          "is_subscribed" => (bool) $user['is_subscribed'],
          "trial_expires_at" => $user['trial_expires_at'],
        ],
    ];

    $jwt = TokenService::encode_jwt($token);

    if ($jwt) {
      http_response_code(200);

      echo json_encode(
        [
          "message" => "Успешный вход в систему",
          "jwt" => $jwt
        ]
      );
    } else {
      http_response_code(500);

      echo json_encode(
        ['message' => 'Не удалось создать токен']
      );
    }
  } catch (Exception $e) {
    http_response_code(500);

    echo json_encode(
      ['message' => $e->getMessage()]
    );
  }
} else {
  http_response_code(400);

  echo json_encode(
    ['message' => 'Неверные данные']
  );
}
