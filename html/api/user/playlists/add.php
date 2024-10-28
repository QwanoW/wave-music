<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (
  isset($_POST['title']) && isset($_POST['is_private'])
) {

  $title = $_POST['title'];
  $is_private = $_POST['is_private'] ? 1 : 0;
  $user_id = $user->id;
  $description = $_POST['description'] ?? null;

  $db = new \App\Database();
  $pdo = $db->getConnection();

  $pdo->beginTransaction();

  try {
    $albumDB = new \App\Objects\Playlist($pdo);
    $userDB = new \App\Objects\User($pdo);

    $playlist_id = $albumDB->insert(['title' => $title, 'description' => $description, 'cover_uri' => '/content/images/default/playlist.png', 'user_id' => $user_id, 'is_private' => $is_private]);

    $created_playlist = $albumDB->getById($playlist_id);

    $owner = $userDB->getById($created_playlist['user_id']);

    unset($created_playlist['user_id'], $owner['password'], $owner['auth_type']);

    $created_playlist['owner'] = $owner;
    $created_playlist['tracks'] = [];

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
      'message' => $e->getMessage()
    ]);
  }
} else {
  http_response_code(400);

  echo json_encode([
    'message' => 'Ошибка'
  ]);
}

