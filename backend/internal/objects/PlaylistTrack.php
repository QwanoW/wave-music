<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class PlaylistTrack extends Table
{
  private $table_name = "playlistTrack";

  public function __construct(private $db)
  {
    parent::__construct($db);
  }

  public function getById(int $id)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

    return $this->fetchOne($query, ["id" => $id]);
  }

  public function getAll()
  {
    $query = "SELECT * FROM " . $this->table_name;

    return $this->fetchAll($query);
  }

  public function getAllByPlaylistId(int $playlist_id) {
    $query = "SELECT * FROM $this->table_name INNER JOIN track ON $this->table_name.track_id = track.id WHERE playlist_id = :playlist_id";

    return $this->fetchAll($query, ["playlist_id" => $playlist_id]);
  }

  public function insert($data)
  {
    $query = "INSERT INTO " . $this->table_name . "(playlist_id, track_id) VALUES (:playlist_id, :track_id)";

    return $this->execute($query, $data);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET playlist_id = :playlist_id, track_id = :track_id WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }

  public function deleteAllByPlaylistId(int $playlist_id) {
    $query = "DELETE FROM $this->table_name WHERE playlist_id = :playlist_id";

    return $this->execute($query, ['playlist_id' => $playlist_id]);
  }
}
