<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

use App\Services\AudioService;

if (
  isset($_POST['title']) && isset($_POST['genre_id'])
  && isset($_POST['language_id']) && isset($_POST['artists'])
  && isset($_FILES['audio']) && isset($_FILES['cover'])
) {

  $title = $_POST['title'];
  $genre_id = $_POST['genre_id'];
  $language_id = $_POST['language_id'];
  $albums = gettype($_POST['artists']) === 'array' ? $_POST['artists'] : json_decode($_POST['artists'], true);

  $audio = $_FILES['audio'];
  $photo = $_FILES['cover'];

  AudioService::validate_audio($audio);

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $duration = AudioService::get_audio_duration($audio['tmp_name']);

    $track_uri = AudioService::save_audio_segments("song", $audio['tmp_name']);

    $photo_uri = App\Services\ImageService::save_image($photo);

    $contentDB = new \App\Objects\Content($pdo);
    $trackDB = new \App\Objects\Track($pdo);
    $trackArtistDB = new \App\Objects\TrackArtist($pdo);

    $content_id = $contentDB->insert(['uri' => $track_uri, 'duration' => $duration, 'contentType' => 'TRACK']);

    $track_id = $trackDB->insert(['title' => $title, 'cover_uri' => $photo_uri, 'genre_id' => $genre_id, 'content_id' => $content_id, 'language_id' => $language_id]);

    foreach ($albums as $track) {
      $trackArtistDB->insert(['track_id' => $track_id, 'artist_id' => $track]);
    }

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Трек добавлен',
    ]);
  } catch (\Exception $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => $e->getMessage()
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}
