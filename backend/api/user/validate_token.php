<?php

// TODO: REWRITE WITH TOKEN SERVICE
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Utils;
use App\Middleware;

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

Middleware::protect_route();

$jwt = Utils::get_bearer_token();

// Если JWT не пуст
if ($jwt) {

  // Если декодирование выполнено успешно, показать данные пользователя
  try {
    // Декодирование jwt
    $decoded = JWT::decode($jwt, new Key(getenv('JWT_SECRET'), 'HS256'));

    // Код ответа
    http_response_code(200);

    // Покажем детали
    echo json_encode(
      array(
        "message" => "Доступ разрешен",
        "data" => $decoded->data
      )
    );
  }

  // Если декодирование не удалось, это означает, что JWT является недействительным
  catch (Exception $e) {

    // Код ответа
    http_response_code(401);

    // Сообщим пользователю что ему отказано в доступе и покажем сообщение об ошибке
    echo json_encode(
      array(
        "message" => "Вам доступ закрыт",
        "error" => $e->getMessage()
      )
    );
  }
}

// Покажем сообщение об ошибке, если JWT пуст
else {

  // Код ответа
  http_response_code(401);

  // Сообщим пользователю что доступ запрещен
  echo json_encode(array("message" => "Доступ запрещён"));
}
