<?php
namespace App;

use App\Services\TokenService;

enum UserRole
{
  case ADMIN;
  case USER;
  case MODERATOR;
}

class Middleware
{
  static function cors(string $method)
  {

    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Origin: " . getenv('CLIENT_HOST'));
    header("Access-Control-Allow-Credentials: true");
    header("Access-Control-Allow-Methods: $method, OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      exit();
    }
  }

  // Возвращает данные пользователя, если у него есть токен
  static function protect_route()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      exit();
    }

    $jwt = Utils::get_bearer_token();

    if (!$jwt) {
      http_response_code(401);
      echo json_encode(["message" => "Доступ запрещён"]);
      exit;
    }

    $decoded = TokenService::decode_jwt($jwt);

    if (!$decoded) {
      http_response_code(401);
      echo json_encode(["message" => "Невалидный токен либо истёк срок его действия"]);
      exit;
    }

    return $decoded->data;
  }

  static function admin_route()
  {
    $user = Middleware::protect_route();

    if ($user->role !== UserRole::ADMIN->name) {
      http_response_code(403);
      echo json_encode(["message" => "Доступ запрещён"]);
      exit;
    }

    return $user;
  }
}