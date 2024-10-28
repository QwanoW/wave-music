<?php
namespace App\Services;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use FFMpeg;

class AudioService
{
  static function save_audio_segments(string $type, $file_name)
  {
    $unique_name = uniqid();

    $ffmpeg = FFMpeg\FFMpeg::create();
    $directory = getenv('LANDO_MOUNT') . '/html/api/content/' . $type . "/" . $unique_name;

    if (!file_exists($directory)) {
      mkdir($directory, 0755, true);
    }

    $file = $ffmpeg->open($file_name);
    $file->addFilter(new \FFMpeg\Filters\Audio\SimpleFilter([
      "-vn",
      "-acodec", "aac", // Кодирование аудио в AAC
      "-hls_list_size", "0",
      "-hls_time", "30", // Длительность сегмента HLS
      "-preset", "fast" // Добавление быстрого пресета
    ]));

    $audio_format = new FFMpeg\Format\Audio\Mp3();
    $file->save($audio_format, $directory . '/index.m3u8');

    return str_replace(getenv('LANDO_MOUNT') . '/html/api', '', $directory) . '/index.m3u8';
  }

  static function get_audio_duration($file_name)
  {
    $ffprobe = FFMpeg\FFProbe::create();
    $duration = $ffprobe->streams($file_name)
      ->audios()
      ->first()
      ->get('duration');

    return $duration;
  }

  static function validate_audio($file)
  {
    if ($file['error'] !== UPLOAD_ERR_OK) {
      http_response_code(400);
      echo json_encode(["message" => "Файл не существует"]);
      exit;
    }

    $size = $file['size'];

    if ($size === 0) {
      http_response_code(400);
      echo json_encode(["message" => "Файл пуст"]);
      exit;
    }

    if ($size > 15 * 1024 * 1024) {
      http_response_code(400);
      echo json_encode(["message" => "Файл слишком большой(>15 Мб)"]);
      exit;
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    if ($ext !== "mp3" && $ext !== "wav" && $ext && "flac") {
      http_response_code(400);
      echo json_encode(["message" => "Неподдерживаемый формат"]);
      exit;
    }
  }
}