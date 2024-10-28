<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (isset($_GET['id']) && isset($_GET['action'])) {
  $artist_id = $_GET['id'];
  $action = $_GET['action'];

  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $userLikedTracksDB = new \App\Objects\UserLikedArtists($pdo);

    $existingLike = $userLikedTracksDB->getByUserAndArtist($user->id, $artist_id);

    if ($action === 'like') {
      if ($existingLike) {
        http_response_code(409);
  
        echo json_encode(["message" => "Вы уже лайкали этого артиста"]);
        exit();
      }

      $userLikedTracksDB->insert(['user_id' => $user->id, 'artist_id' => $artist_id]);
    } else if ($action === 'unlike') {
      $userLikedTracksDB->deleteByUserAndArtist(['user_id' => $user->id, 'artist_id' => $artist_id]);
    } else {
      http_response_code(400);

      echo json_encode(["message" => "Неизвестное действие"]);
      exit();
    }

    http_response_code(200);

    echo json_encode(["message" => "Успешно"]);

  } catch (Exception $e) {
    var_dump($e->getMessage());

    http_response_code(500);

    echo json_encode(["message" => $e->getMessage()]);
  }
}
