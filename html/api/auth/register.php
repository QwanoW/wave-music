<?php

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  exit();
}

if (
  isset($_POST['username']) &&
  isset($_POST['email']) &&
  isset($_POST['password'])
) {

  $username = $_POST['username'];
  $email = $_POST['email'];
  $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

  try {
    $database = new App\Database();
    $db = $database->getConnection();

    $user = new App\Objects\User($db);

    $existing_user = $user->getByEmail($email);

    if ($existing_user) {
      http_response_code(409);

      echo json_encode(
        ['message' => 'Пользователь с такой почтой уже существует']
      );
      exit;
    }

    if ($user->insert($username, $email, $password)) {
      http_response_code(201);

      echo json_encode(
        ['message' => 'Пользователь успешно создан']
      );
    } else {
      http_response_code(500);

      echo json_encode(
        ['message' => 'Не удалось создать пользователя']
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
    ['message' => 'Недостаточно данных']
  );
}

