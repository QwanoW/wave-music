<?php
// TODO: REWRITE WITH TOKEN SERVICE
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  exit();
}

use App\Database;
use App\Objects\User;

use Firebase\JWT\JWT;

// Получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// Создание объекта "User"
$user = new User($db);

// Получаем данные
$data = json_decode(file_get_contents("php://input"));

// Устанавливаем значения
$user->email = $data->email;
$email_exists = $user->emailExists();

// Существует ли электронная почта и соответствует ли пароль тому, что находится в базе данных
if ($email_exists && password_verify($data->password, $user->password)) {

  $token = array(
    "exp" => time() + 7 * 24 * 3600,
    "data" => array(
      "id" => $user->id,
      "username" => $user->username,
      "email" => $user->email,
      "role" => $user->role,
      "isArtist" => $user->isArtist,
      "created_at" => $user->created_at
    )
  );

  // Код ответа
  http_response_code(200);

  // Создание jwt
  $jwt = JWT::encode($token, getenv('JWT_SECRET'), 'HS256');
  echo json_encode(
    array(
      "message" => "Успешный вход в систему",
      "jwt" => $jwt
    )
  );
}

// Если электронная почта не существует или пароль не совпадает,
// Сообщим пользователю, что он не может войти в систему
else {

  // Код ответа
  http_response_code(401);

  // Скажем пользователю что войти не удалось
  echo json_encode(array("message" => "Неправильный логин или пароль"));
}
