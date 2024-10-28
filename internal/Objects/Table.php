<?php

namespace App\Objects;

class Table
{
  public function __construct(private \PDO $db)
  {
  }

  public function fetchAll(string $query, array $params = []): array
  {
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll();
  }

  public function fetchOne(string $query, array $params = []): array
  {
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    $result = $stmt->fetch();
    
    if ($result === false) {
      return [];
    }

    return $result;
  }

  public function execute(string $query, array $params = []): bool
  {
    $stmt = $this->db->prepare($query);
    return $stmt->execute($params);
  }

  public function executeWithId(string $query, array $params = []): int
  {
    $stmt = $this->db->prepare($query);
    $stmt->execute($params);
    return $this->db->lastInsertId();
  }
}