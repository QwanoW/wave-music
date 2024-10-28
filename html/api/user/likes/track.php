<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

if (isset($_GET['id']) && isset($_GET['action'])) {
  $track_id = $_GET['id'];
  $action = $_GET['action'];

  try {
    $db = new \App\Database();
    $pdo = $db->getConnection();

    $userLikedTracksDB = new \App\Objects\UserLikedTracks($pdo);

    $existingLike = $userLikedTracksDB->getByUserAndTrack($user->id, $track_id);

    if ($action === 'like') {
      if ($existingLike) {
        http_response_code(409);

        echo json_encode(["message" => "Вы уже лайкали этот трек"]);
        exit();
      }

      $userLikedTracksDB->insert(['user_id' => $user->id, 'track_id' => $track_id]);
    } else if ($action === 'unlike') {
      $userLikedTracksDB->deleteByUserAndTrack(['user_id' => $user->id, 'track_id' => $track_id]);
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
