<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

try {
  $db = new \App\Database();
  $pdo = $db->getConnection();

  $user = new \App\Objects\User($pdo);

  $users = $user->getAll();

  http_response_code(200);

  echo json_encode($users);

} catch (Exception $e) {
  http_response_code(500);
  
  echo json_encode(["message" => $e->getMessage()]);
}