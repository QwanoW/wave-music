<?php
namespace App;

class Utils
{
  static function get_bearer_token()
  {
    $headers = getallheaders();

    if (!array_key_exists('Authorization', $headers)) {
      http_response_code(401);
      echo json_encode(["message" => "Authorization заголовок отсутствует"]);
      exit;
    }

    if (substr($headers['Authorization'], 0, 7) !== 'Bearer ') {
      echo json_encode(["error" => "Bearer токен отсутствует"]);
      exit;
    }

    $token = trim(substr($headers['Authorization'], 7));
    return $token;
  }
}

