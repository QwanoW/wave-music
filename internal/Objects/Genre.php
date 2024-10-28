<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class Genre extends Table
{
  private $table_name = "genre";

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
    $query = "SELECT * FROM $this->table_name";

    return $this->fetchAll($query);
  }

  public function getAllLimit(int $limit)
  {
    $query = "SELECT * FROM $this->table_name LIMIT $limit";

    return $this->fetchAll($query);
  }

  public function insert(string $name, string $cover)
  {
    $query = "INSERT INTO " . $this->table_name . "(name, cover_uri) VALUES (:name, :cover_uri)";

    return $this->execute($query, ["name" => $name, "cover_uri" => $cover]);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET name = :name, cover_uri = :cover_uri WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }
}
