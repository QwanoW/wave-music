<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Middleware;
use App\Services\AudioService;

Middleware::protect_route();

if (isset($_FILES['file']) && isset($_POST['name'])) {

  $file = $_FILES['file'];

  AudioService::validate_audio($file);

  $duration = AudioService::get_audio_duration($file['tmp_name']);

  $path = AudioService::save_audio_segments("song", $file['tmp_name']);

  http_response_code(200);

  echo json_encode([
    'message' => 'Файл успешно загружен',
    'duration' => $duration,
    'path' => $path
  ]);
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Файл не был загружен'
  ]);
}