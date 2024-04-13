<?php
// TODO: ПЕРЕПИСАТЬ ПО ЧЕЛОВЕСКИ

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
    'client_id' => getenv('GITHUB_CLIENT_ID'),
    'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
    'code' => $_GET['code']
  );

  $ch = curl_init('https://github.com/login/oauth/access_token');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $data = curl_exec($ch);
  curl_close($ch);
  parse_str($data, $data);

  if (!empty($data['access_token'])) {
    $ch = curl_init('https://api.github.com/user');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . $data['access_token']));
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $info = curl_exec($ch);
    curl_close($ch);
    $info = json_decode($info, true);

    if (empty($info['email'])) {
      $ch = curl_init('https://api.github.com/user/emails');
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: token ' . $data['access_token']));
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $email = curl_exec($ch);
      curl_close($ch);
      $email = json_decode($email, true);

      $info['email'] = $email[0]['email'];
    }

    // Устанавливаем значения
    $user->username = $info['login'];
    $user->email = $info['email'];

    $email_exists = $user->emailExists();

    // Если пользователь уже существует и зарегистрирован другим способом, 
    // то выдаем ошибку
    if ($email_exists == 1 && is_null($user->password) && $user->auth_type != "github") {
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
      $user->auth_type = "github";
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
