<?php
// TODO: ПЕРЕПИСАТЬ ПО ЧЕЛОВЕЧЕСКИ

namespace App\Objects;

use PDO;

class User
{
  // Подключение к БД таблице "user"
  private $conn;
  private $table_name = "user";

  // Свойства
  public $id;
  public $username;
  public $email;
  public $password;
  public $role;
  public $isArtist;
  public $created_at;
  public $auth_type;

  // Конструктор класса User
  public function __construct($db)
  {
    $this->conn = $db;
  }

  // Метод для создания нового пользователя
  function create()
  {

    // Запрос для добавления нового пользователя в БД
    $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password";

    // Подготовка запроса
    $stmt = $this->conn->prepare($query);

    // Инъекция
    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->password = htmlspecialchars(strip_tags($this->password));

    // Привязываем значения
    $stmt->bindParam(":username", $this->username);
    $stmt->bindParam(":email", $this->email);

    // Для защиты пароля
    // Хешируем пароль перед сохранением в базу данных
    $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
    $stmt->bindParam(":password", $password_hash);

    // Выполняем запрос
    // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
    if ($stmt->execute()) {
      return true;
    }

    return false;
  }

  function createOauth()
  {
    // Запрос для добавления нового пользователя в БД
    $query = "INSERT INTO " . $this->table_name . "
        SET
            username = :username,
            email = :email,
            auth_type = :auth_type";

    // Подготовка запроса
    $stmt = $this->conn->prepare($query);

    // Инъекция
    $this->username = htmlspecialchars(strip_tags($this->username));
    $this->email = htmlspecialchars(strip_tags($this->email));
    $this->auth_type = htmlspecialchars(strip_tags($this->auth_type));

    // Привязываем значения
    $stmt->bindParam(":username", $this->username);
    $stmt->bindParam(":email", $this->email);
    $stmt->bindParam(":auth_type", $this->auth_type);

    // Выполняем запрос
    // Если выполнение успешно, то информация о пользователе будет сохранена в базе данных
    if ($stmt->execute()) {
      $this->id = $this->conn->lastInsertId();

      return true;
    }

    return false;
  }

  function readOne()
  {

    $query = "SELECT id, username, email, role, isArtist, created_at, auth_type FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(1, $this->id);

    if ($stmt->execute()) {
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      $this->username = $row['username'];
      $this->email = $row['email'];
      $this->role = $row['role'];
      $this->isArtist = $row['isArtist'];
      $this->created_at = $row['created_at'];
      $this->auth_type = $row['auth_type'];

      return true;
    } else {
      return false;
    }
  }

  // Проверка, существует ли электронная почта в нашей базе данных
  function emailExists()
  {

    // Запрос, чтобы проверить, существует ли электронная почта
    $query = "SELECT id, username, password, role, isArtist, created_at, auth_type
          FROM " . $this->table_name . "
          WHERE email = ?
          LIMIT 0,1";

    // Подготовка запроса
    $stmt = $this->conn->prepare($query);

    // Инъекция
    $this->email = htmlspecialchars(strip_tags($this->email));

    // Привязываем значение e-mail
    $stmt->bindParam(1, $this->email);

    // Выполняем запрос
    $stmt->execute();

    // Получаем количество строк
    $num = $stmt->rowCount();

    // Если электронная почта существует,
    // Присвоим значения свойствам объекта для легкого доступа и использования для php сессий
    if ($num > 0) {

      // Получаем значения
      $row = $stmt->fetch(PDO::FETCH_ASSOC);

      // Присвоим значения свойствам объекта
      $this->id = $row["id"];
      $this->username = $row["username"];
      $this->password = $row["password"];
      $this->role = $row["role"];
      $this->isArtist = $row["isArtist"];
      $this->created_at = $row["created_at"];
      $this->auth_type = $row["auth_type"];

      // Вернём "true", потому что в базе данных существует электронная почта
      return true;
    }

    // Вернём "false", если адрес электронной почты не существует в базе данных
    return false;
  }
}
