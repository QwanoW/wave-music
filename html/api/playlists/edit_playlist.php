<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';


App\Middleware::admin_route();

if (
  isset($_POST['id']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['tracks']) && isset($_POST['is_private'])
) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $description = $_POST['description'];
  $user_id = $_POST['user_id'] ?? null;
  $is_private = (bool) $_POST['is_private'];

  $cover = $_FILES['cover'] ?? null;
  $tracks = $_POST['tracks'];

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $albumDB = new \App\Objects\Playlist($pdo);
    $playlistTrackDB = new \App\Objects\PlaylistTrack($pdo);

    // if new cover is present then save it, else use old one
    if ($cover) {
      $cover_uri = App\Services\ImageService::save_image($cover);

      if (is_null($cover_uri)) {
        throw new Exception("Не удалось загрузить обложку");
      }
    } else {
      $track = $albumDB->getById($id);
      $cover_uri = $track['cover_uri'];
    }

    $albumDB->update(['id' => $id, 'title' => $title, 'description' => $description, 'cover_uri' => $cover_uri, 'user_id' => $user_id, 'is_private' => $is_private]);

    // delete previous tracks and insert new
    $playlistTrackDB->deleteAllByPlaylistId($id);
    foreach ($tracks as $track_id) {
      $playlistTrackDB->insert(['playlist_id' => $id, 'track_id' => $track_id]);
    }

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Плейлист обновлен',
    ]);
  } catch (\Exception $e) {
    var_dump($e->getMessage());

    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось обновить плейлист'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

