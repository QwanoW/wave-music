<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (isset($_POST['username']) && isset($_POST['email'])) {
  $username = $_POST['username'];
  $email = $_POST['email'];

  $old_password = $_POST['oldPassword'] ?? null;
  $password = $_POST['password'] ?? null;

  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $userDB = new \App\Objects\User($pdo);

    $existing_user = $userDB->getById($user->id);

    if (!$existing_user) {
      http_response_code(404);

      echo json_encode([
        'message' => 'Пользователь не найден'
      ]);
      exit;
    }

    if ($old_password && $password) {
      if (!password_verify($old_password, $existing_user['password'])) {
        http_response_code(400);

        echo json_encode([
          'message' => 'Неверный пароль'
        ]);
        exit;
      } else {
        $password = password_hash($password, PASSWORD_BCRYPT);

        $userDB->update(['id' => $user->id, 'username' => $username, 'email' => $email, 'password' => $password]);

        http_response_code(200);

        echo json_encode([
          'message' => 'Пользователь обновлен'
        ]);
      }
    } else {
      $userDB->update(['id' => $user->id, 'username' => $username, 'email' => $email, 'password' => $existing_user['password']]);

      http_response_code(200);

      echo json_encode([
        'message' => 'Пользователь обновлен'
      ]);
    }
  } catch (Exception $e) {
    http_response_code(500);

    echo json_encode([
      'message' => 'Ошибка сервера'
    ]);
  }
} else {
  http_response_code(400);
  echo json_encode([
    'message' => 'Ошибка запроса'
  ]);
}

