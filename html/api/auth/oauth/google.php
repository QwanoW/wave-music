<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$params = array(
  'client_id' => getenv('GOOGLE_CLIENT_ID'),
  'response_type' => 'code',
  'redirect_uri' => getenv('GOOGLE_REDIRECT_URI'),
  'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
  'state' => '123'
);

$url = 'https://accounts.google.com/o/oauth2/auth?' . urldecode(http_build_query($params));

header('Location: ' . $url);
