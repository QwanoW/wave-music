<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class AlbumTrack extends Table
{
  private $table_name = "albumTrack";

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

  public function getAllByAlbumId(int $album_id)
  {
    $query = "SELECT * FROM $this->table_name INNER JOIN album ON $this->table_name.album_id = album.id WHERE album_id = :album_id";

    return $this->fetchAll($query, ["album_id" => $album_id]);
  }

  public function insert($data)
  {
    $query = "INSERT INTO " . $this->table_name . "(track_id, album_id, `order`) VALUES (:track_id, :album_id, :order)";

    return $this->execute($query, $data);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET track_id = :track_id, album_id = :album_id, order = :order WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }

  public function deleteAllByAlbumId(int $album_id)
  {
    $query = "DELETE FROM $this->table_name WHERE album_id = :album_id";

    return $this->execute($query, ['album_id' => $album_id]);
  }
}
