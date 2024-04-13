<?php
namespace App\Objects;

require_once getenv('LANDO_MOUNT') . '/vendor/autoload.php';

use App\Objects\Table;

class Track extends Table
{
  private $table_name = "track";


  public function __construct(private $db)
  {
    parent::__construct($db);
  }

  public function getById(int $id)
  {
    $query = "SELECT * FROM $this->table_name WHERE id = :id";

    return $this->fetchOne($query, ["id" => $id]);
  }

  public function getAll()
  {
    $query = "SELECT * FROM $this->table_name";
    return $this->fetchAll($query);
  }

  public function insert($data)
  {
    $query = "INSERT INTO " . $this->table_name . "(title, cover_uri, genre_id, content_id, language_id) VALUES (:title, :cover_uri, :genre_id, :content_id, :language_id)";

    return $this->executeWithId($query, $data);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET title = :title, cover_uri = :cover_uri, genre_id = :genre_id, content_id = :content_id, language_id = :language_id WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }
}
