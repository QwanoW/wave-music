<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (
  isset($_POST['title']) && isset($_POST['is_private'])
) {

  $title = $_POST['title'];
  $is_private = (int) $_POST['is_private'];
  $user_id = $user->id;
  $description = $_POST['description'] ?? null;

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $playlistDB = new \App\Objects\Playlist($pdo);

    $playlist_id = $playlistDB->insert(['title' => $title, 'description' => $description, 'cover_uri' => null, 'user_id' => $user_id, 'is_private' => $is_private]);

    $created_playlist = $playlistDB->getById($playlist_id);

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
      'message' => 'Плейлист добавлен',
      'data' => $created_playlist
    ]);
  } catch (\Exception $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
      'message' => 'Не удалось загрузить плейлист'
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

