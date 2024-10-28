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
    'client_id' => getenv('GITHUB_CLIENT_ID'),
    'client_secret' => getenv('GITHUB_CLIENT_SECRET'),
    'code' => $_GET['code']
  );

  $ch = curl_init('https://github.com/login/oauth/access_token');
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_HEADER, false);
  $data = curl_exec($ch);
  curl_close($ch);

  parse_str($data, $data);

  if (empty($data['access_token'])) {
    $error = "Не удалось получить токен";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
  }

  $ch = curl_init('https://api.github.com/user');
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

  $user = $userDB->getByEmail($info['email']);

  // Если пользователь уже существует и зарегистрирован другим способом, 
  // то выдаем ошибку
  if ($user && $user['auth_type'] != "github") {
    $error = "Пользователь с такой почтой уже существует";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
    exit();
  }

  if (!$user) {
    $user_id = $userDB->insert($info['login'], $info['email'], null, 'github');

    if (!$user_id) {
      $error = "Не удалось создать пользователя";
      header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
      exit();
    }

    $user = $userDB->getById($user_id);
  }


  if (!$user) {
    $error = "Не удалось получить пользователя";
    header("Location: " . getenv('CLIENT_HOST') . "/auth/callback/?#error=$error");
    exit();
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

