<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (isset($_GET['id']) && isset($_GET['action'])) {
  $album_id = $_GET['id'];
  $action = $_GET['action'];

  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $userLikedTracksDB = new \App\Objects\UserLikedAlbums($pdo);

    if ($action === 'like') {
      $existingLike = $userLikedTracksDB->getByUserAndAlbum($user->id, $album_id);

      if ($existingLike) {
        http_response_code(409);
  
        echo json_encode(["message" => "Вы уже лайкали этот альбом"]);
        exit();
      }

      $userLikedTracksDB->insert(['user_id' => $user->id, 'album_id' => $album_id]);
    } else if ($action === 'unlike') {
      $userLikedTracksDB->deleteByUserAndAlbum(['user_id' => $user->id, 'album_id' => $album_id]);
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
