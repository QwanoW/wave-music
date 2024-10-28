<?php
namespace App\Services;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class TokenService
{
  static function encode_jwt($data)
  {
    $token = array(
      "exp" => time() + 7 * 24 * 3600,
      "data" => $data
    );

    $jwt = JWT::encode($token, getenv('JWT_SECRET'), 'HS256');

    return $jwt;
  }

  static function decode_jwt($jwt)
  {
    try {
      $decoded = JWT::decode($jwt, new Key(getenv('JWT_SECRET'), 'HS256'));
      $data = $decoded->data;
      return $data;
    } catch (Exception $e) {
      return "";
    }
  }
}