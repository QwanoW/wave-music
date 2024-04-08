<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
  exit();
}

use App\Database;
use App\Objects\User;

// Получаем соединение с базой данных
$database = new Database();
$db = $database->getConnection();

// Создание объекта "User"
$user = new User($db);

// Получаем данные
$data = json_decode(file_get_contents("php://input"));

// Устанавливаем значения
$user->username = $data->username;
$user->email = $data->email;
$user->password = $data->password;

$email_exists = $user->emailExists();

// Создание пользователя
if (
  !empty($user->username) &&
  !empty($user->email) &&
  $email_exists == 0 &&
  !empty($user->password) &&
  $user->create()
) {
  // Устанавливаем код ответа
  http_response_code(200);

  // Покажем сообщение о том, что пользователь был создан
  echo json_encode(array("message" => "Пользователь был создан"));
}

// Сообщение, если не удаётся создать пользователя
else {

  if ($email_exists) {
    http_response_code(409);
    echo json_encode(array("message" => "Пользователь с такой почтой уже существует"));
  } else {
    // Устанавливаем код ответа
    http_response_code(400);

    // Покажем сообщение о том, что создать пользователя не удалось
    echo json_encode(array("message" => "Невозможно создать пользователя"));
  }
}
