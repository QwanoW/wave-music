<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class TrackArtist extends Table
{
  private $table_name = "trackArtist";

  public function __construct(private $db)
  {
    parent::__construct($db);
  }

  public function getById(int $id)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

    return $this->fetchOne($query, ["id" => $id]);
  }
  public function getAllByTrackId(int $id)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE track_id = :track_id";

    return $this->fetchAll($query, ["track_id" => $id]);
  }

  public function getAll()
  {
    $query = "SELECT * FROM " . $this->table_name;

    return $this->fetchAll($query);
  }

  public function insert($data)
  {
    $query = "INSERT INTO " . $this->table_name . "(track_id, artist_id) VALUES (:track_id, :artist_id)";

    return $this->execute($query, $data);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET track_id = :track_id, artist_id = :artist_id WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }

  public function deleteAllByTrackId(int $id) {
    $query = "DELETE FROM " . $this->table_name . " WHERE track_id = :track_id";

    return $this->execute($query, ["track_id" => $id]);
  }
}
