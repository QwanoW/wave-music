<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$queryParams = $_GET;

$db = new \App\Database();
$trackDB = new \App\Objects\Genre($db->getConnection());

if (isset($queryParams['limit'])) {
  $genres = $trackDB->getAllLimit($queryParams['limit']);
} else {
  $genres = $trackDB->getAll();
}


echo json_encode($genres);