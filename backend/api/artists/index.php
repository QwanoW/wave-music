<?php
// TODO: REWRITE WITH TRY CATCH

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

$db = new \App\Database();
$trackDB = new \App\Objects\Artist($db->getConnection());

$artists = $trackDB->getAll();

echo json_encode($artists);