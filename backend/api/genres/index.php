<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

$db = new \App\Database();
$genreDB = new \App\Objects\Genre($db->getConnection());

$genres = $genreDB->getAll();

echo json_encode($genres);