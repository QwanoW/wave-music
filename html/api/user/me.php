<?php
require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

$user = App\Middleware::protect_route();

try {
  $db = new \App\Database();
  $pdo = $db->getConnection();

  $userLikedTracksDB = new \App\Objects\UserLikedTracks($pdo);
  $userLikedAlbumsDB = new \App\Objects\UserLikedAlbums($pdo);
  $userLikedArtistsDB = new \App\Objects\UserLikedArtists($pdo);
  $userLikedPlaylistsDB = new \App\Objects\UserLikedPlaylists($pdo);

  $liked_tracks = $userLikedTracksDB->getAllByUserId($user->id);
  $liked_albums = $userLikedAlbumsDB->getAllByUserId($user->id);
  $liked_artists = $userLikedArtistsDB->getAllByUserId($user->id);
  $liked_playlists = $userLikedPlaylistsDB->getAllByUserId($user->id);

  http_response_code(200);

  echo json_encode([
    "user" => $user,

    "liked_tracks" => $liked_tracks,
    "liked_albums" => $liked_albums,
    "liked_artists" => $liked_artists,
    "liked_playlists" => $liked_playlists
  ]);

} catch (Exception $e) {
  http_response_code(500);
  
  echo json_encode(["message" => $e->getMessage()]);
}