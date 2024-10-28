<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

App\Middleware::admin_route();

use App\Services\AudioService;

if (
  isset($_POST['id']) && isset($_POST['title']) && isset($_POST['genre_id'])
  && isset($_POST['language_id']) && isset($_POST['artists'])
) {

  $id = $_POST['id'];
  $title = $_POST['title'];
  $genre_id = $_POST['genre_id'];
  $language_id = $_POST['language_id'];
  $albums = $_POST['artists'];

  $audio = $_FILES['audio'] ?? null;
  $photo = $_FILES['cover'] ?? null;

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $contentDB = new \App\Objects\Content($pdo);
    $trackDB = new \App\Objects\Track($pdo);
    $track = $trackDB->getById($id);
    $content = $contentDB->getById($track['content_id']);

    if ($audio) {
      AudioService::validate_audio($audio);
      $duration = AudioService::get_audio_duration($audio['tmp_name']);
      $track_uri = AudioService::save_audio_segments("song", $audio['tmp_name']);
      $contentDB->update(['id' => $id, 'uri' => $track_uri, 'duration' => $duration, 'contentType' => $content_type]);
    }


    if ($photo) {
      $photo_uri = App\Services\ImageService::save_image($photo);
    } else {
      $photo_uri = $track['cover_uri'];
    }

    $data = [
      'id' => $id,
      'title' => $title,
      'cover_uri' => $photo_uri,
      'content_id' => $content['id'],
      'genre_id' => $genre_id,
      'language_id' => $language_id
    ];

    $trackDB->update($data);

    $trackArtistDB = new \App\Objects\TrackArtist($pdo);
    $trackArtistDB->deleteAllByTrackId($id);
    
    foreach ($albums as $track) {
      $trackArtistDB->insert(['track_id' => $id, 'artist_id' => $track]);
    }

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Трек изменен',
    ]);
  } catch (\Exception $e) {
    var_dump($e);

    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось изменить трек'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

