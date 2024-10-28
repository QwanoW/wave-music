<?php
namespace App\Objects;

class User extends Table
{
  private $table_name = "user";
  public function __construct(private $db)
  {
    parent::__construct($db);
  }

  public function getById(int $id)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";

    return $this->fetchOne($query, ["id" => $id]);
  }

  public function getByEmail(string $email)
  {
    $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";

    return $this->fetchOne($query, ["email" => $email]);
  }

  public function getAll()
  {
    $query = "SELECT id, username, email, created_at, role, is_subscribed, trial_expires_at FROM " . $this->table_name;

    return $this->fetchAll($query);
  }

  public function insert(string $username, string $email, string|null $password, string $auth_type = 'credentials')
  {
    $query = "INSERT INTO " . $this->table_name . "(username, email, password, auth_type) VALUES (:username, :email, :password, :auth_type)";

    return $this->executeWithId($query, ["username" => $username, "email" => $email, "password" => $password, "auth_type" => $auth_type]);
  }

  public function update($data)
  {
    $query = "UPDATE " . $this->table_name . " SET username = :username, email = :email, password = :password WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function updateSubscription($data)
  {
    $query = "UPDATE " . $this->table_name . " SET is_subscribed = :is_subscribed, trial_expires_at = :trial_expires_at WHERE id = :id";

    return $this->execute($query, $data);
  }

  public function delete(int $id)
  {
    $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";

    return $this->execute($query, ["id" => $id]);
  }
}
