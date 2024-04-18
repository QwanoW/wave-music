<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class Playlist extends Table
{
  private $table_name = "playlist";

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

  public function insert($data)
  {
    $query = "INSERT INTO " . $this->table_name . "(title, description, cover_uri, user_id, is_private) VALUES (:title, :description, :cover_uri, :user_id, :is_private)";

    return $this->executeWithId($query, $data);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET title = :title, description = :description, cover_uri = :cover_uri, user_id = :user_id, is_private = :is_private WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }
}
