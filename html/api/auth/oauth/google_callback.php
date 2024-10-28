<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

if (empty($_GET['code'])) {
  $error = "Не удалось получить код";
  header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
}

try {
  $database = new App\Database();
  $db = $database->getConnection();

  $userDB = new App\Objects\User($db);

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

  if (empty($data['access_token'])) {
    $error = "Не удалось получить токен";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
  }

  $params = array(
    'access_token' => $data['access_token'],
    'id_token' => $data['id_token'],
    'token_type' => 'Bearer',
    'expires_in' => 3599
  );

  $info = file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo?' . urldecode(http_build_query($params)));
  $info = json_decode($info, true);

  $user = $userDB->getByEmail($info['email']);

  // Если пользователь уже существует и зарегистрирован другим способом, 
  // то выдаем ошибку
  if ($user && $user['auth_type'] != "google") {
    $error = "Пользователь с такой почтой уже существует";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
    exit();
  }

  if (!$user) {
    $user_id = $userDB->insert($info['name'], $info['email'], null, 'google');

    if (!$user_id) {
      $error = "Не удалось создать пользователя";
      header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
      exit();
    }
    $user = $userDB->getById($user_id);
  }

  $token = array(
    "exp" => time() + 7 * 24 * 3600,
    "data" => array(
      "id" => $user['id'],
      "username" => $user['username'],
      "email" => $user['email'],
      "role" => $user['role'],
      "created_at" => $user['created_at'],
      "auth_type" => $user['auth_type'],
      "is_subscribed" => (bool) $user['is_subscribed'],
      "trial_expires_at" => $user['trial_expires_at'],
    )
  );

  // Создание jwt
  $jwt = App\Services\TokenService::encode_jwt($token);

  if ($jwt) {
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#jwt=$jwt");
  } else {
    $error = "Не удалось создать токен";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
  }
} catch (Exception $e) {
  $error = $e->getMessage();
  header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
}
