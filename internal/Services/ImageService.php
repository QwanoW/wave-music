<?php
namespace App\Services;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

class ImageService
{
  static function validate_image($file)
  {
    $error = null;

    if ($file['error'] !== UPLOAD_ERR_OK) {
      $error = "Файл не существует";
    }

    $size = $file['size'];

    if ($size === 0) {
      $error = "Файл пуст";
    }

    if ($size > 5000000) {
      $error = "Файл слишком большой";
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

    if ($ext !== 'jpg' && $ext !== 'jpeg' && $ext !== 'png' && $ext !== 'webp') {
      $error = "Неподдерживаемый формат изображения";
    }

    return $error;
  }

  static function save_image($file)
  {
    $error = self::validate_image($file);

    if ($error) {
      http_response_code(400);

      echo json_encode([
        'message' => $error,
      ]);

      exit();
    }

    $unique_name = uniqid();

    $new_file = getenv('LANDO_MOUNT') . '/html/api/content/images/' . $unique_name . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);

    if (move_uploaded_file($file['tmp_name'], $new_file)) {
      return str_replace(getenv('LANDO_MOUNT') . '/html/api', '', $new_file);
    } else {
      return null;
    }
  }
}