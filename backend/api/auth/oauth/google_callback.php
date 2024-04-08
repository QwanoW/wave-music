<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Database;
use App\Objects\User;

use Firebase\JWT\JWT;

if (!empty($_GET['code'])) {
  // Получаем соединение с базой данных
  $database = new Database();
  $db = $database->getConnection();

  // Создание объекта "User"
  $user = new User($db);


  $params = array(
    'client_id' => getenv('GOOGLE_CLIENT_ID'),
    'client_secret' => getenv('GOOGLE_CLIENT_SECRET'),
    'redirect_uri' => getenv('GOOGLE_REDIRECT_URI'),
    'grant_type' => 'authorization_code',
    'code' => $_GET['code']
  );

  $ch = curl_init('https://accounts.google.com/o/oauth2/token');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $data = curl_exec($ch);
  curl_close($ch);

  $data = json_decode($data, true);

  if (!empty($data['access_token'])) {
    $params = array(
      'access_token' => $data['access_token'],
      'id_token' => $data['id_token'],
      'token_type' => 'Bearer',
      'expires_in' => 3599
    );

    $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
    $info = json_decode($info, true);

    // Устанавливаем значения
    $user->username = $info['name'];
    $user->email = $info['email'];

    $email_exists = $user->emailExists();

    // Если пользователь уже существует и зарегистрирован другим способом, 
    // то выдаем ошибку
    if ($email_exists == 1 && $user->auth_type != "google") {
      $error = "Пользователь с такой почтой уже существует";
      header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
      exit();
    }

    // Создание пользователя
    if (
      !empty($user->username) &&
      !empty($user->email) &&
      $email_exists == 0
    ) {
      $user->auth_type = "google";
      $user->createOauth();

      $user->readOne();
    }


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

    // Создание jwt
    $jwt = JWT::encode($token, getenv('JWT_SECRET'), 'HS256');

    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#jwt=$jwt");
  } else {
    $error = "Не удалось получить токен";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
  }
} else {
  $error = "Не удалось получить код";
  header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
}
