<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (isset($_GET['id']) && isset($_GET['action'])) {
  $playlist_id = $_GET['id'];
  $action = $_GET['action'];

  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $userLikedPlaylistsDB = new \App\Objects\UserLikedPlaylists($pdo);

    $existingLike = $userLikedPlaylistsDB->getByUserAndPlaylist($user->id, $playlist_id);

    if ($action === 'like') {
      if ($existingLike) {
        http_response_code(409);
  
        echo json_encode(["message" => "Вы уже лайкали этот плейлист"]);
        exit();
      }

      $userLikedPlaylistsDB->insert(['user_id' => $user->id, 'playlist_id' => $playlist_id]);
    } else if ($action === 'unlike') {
      $userLikedPlaylistsDB->deleteByUserAndPlaylist(['user_id' => $user->id, 'playlist_id' => $playlist_id]);
    } else {
      http_response_code(400);

      echo json_encode(["message" => "Неизвестное действие"]);
      exit();
    }

    http_response_code(200);

    echo json_encode(["message" => "Успешно"]);

  } catch (Exception $e) {
    http_response_code(500);

    echo json_encode(["message" => $e->getMessage()]);
  }
}