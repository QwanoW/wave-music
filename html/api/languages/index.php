<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

$db = new \App\Database();
$languageDB = new \App\Objects\Language($db->getConnection());

$languages = $languageDB->getAll();

echo json_encode($languages);