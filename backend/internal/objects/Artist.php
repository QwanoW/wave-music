<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class Artist extends Table
{
  private $table_name = "artist";

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

  public function insert(string $name, string $bio, string $photo)
  {
    $query = "INSERT INTO " . $this->table_name . "(name, bio, photo_uri) VALUES (:name, :bio, :photo_uri)";

    return $this->execute($query, ["name" => $name, "bio" => $bio, "photo_uri" => $photo]);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET name = :name, bio = :bio, photo_uri = :photo_uri WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }
}
