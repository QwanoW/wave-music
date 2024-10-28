<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';


try {
  $db = new \App\Database();
  $userDB = new \App\Objects\User($db->getConnection());

  $users = $userDB->getAll();

  http_response_code(200);

  echo json_encode($users);
} catch (\Exception $e) {
  echo json_encode(['message' => $e->getMessage()]);
}