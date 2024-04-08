<?php

namespace App\Objects;

class Table
{
    public function __construct(private \PDO $db)
    {
    }

    public function fetchAll(string $query, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function fetchOne(string $query, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function execute(string $query, array $params = []): bool
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return true;
        } catch (\PDOException $e) {
            exit($e->getMessage());
        }
    }
}