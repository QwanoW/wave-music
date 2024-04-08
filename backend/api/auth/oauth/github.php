<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$params = array(
  'client_id' => getenv('GITHUB_CLIENT_ID'),
  'scope' => 'user,user:email',
  'response_type' => 'code',
  'state' => ''
);

$url = 'https://github.com/login/oauth/authorize?' . http_build_query($params);

header('Location: ' . $url);
