<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class UserLikedArtists extends Table
{
  private $table_name = "userLikedArtists";

  public function __construct(private $db)
  {
    parent::__construct($db);
  }

  public function getAllByUserId(int $user_id)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id";

    return $this->fetchAll($query, ["user_id" => $user_id]);
  }

  public function getByUserAndArtist(int $user_id, int $artist_id) {
    $query = "SELECT * FROM " . $this->table_name . " WHERE user_id = :user_id AND artist_id = :artist_id";

    return $this->fetchOne($query, ["user_id" => $user_id, "artist_id" => $artist_id]);
  }

  public function getAll()
  {
    $query = "SELECT * FROM " . $this->table_name;

    return $this->fetchAll($query);
  }

  public function insert($data)
  {
    $query = "INSERT INTO " . $this->table_name . "(user_id, artist_id) VALUES (:user_id, :artist_id)";

    return $this->execute($query, $data);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET user_id = :user_id, artist_id = :artist_id WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function deleteByUserAndArtist($data) {
    $query = "DELETE FROM " . $this->table_name . " WHERE user_id = :user_id AND artist_id = :artist_id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }
}
