<?php

namespace App;

use PDO;
use PDOException;

// Используем для подключения к базе данных MySQL
class Database
{
  // Учётные данные базы данных
  private $host;
  private $db_name;
  private $username;
  private $password;
  public $conn;

  // Конструктор базы данных
  function __construct()
  {

    $this->host = getenv('DB_HOST');
    $this->db_name = getenv('DB_NAME');
    $this->username = getenv('DB_USER');
    $this->password = getenv('DB_PASS');
  }

  // Получаем соединение с базой данных
  public function getConnection()
  {
    $this->conn = null;

    try {
      $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
      ]);
    } catch (PDOException $exception) {
      echo "Ошибка соединения с БД: " . $exception->getMessage();
    }

    return $this->conn;
  }
}
