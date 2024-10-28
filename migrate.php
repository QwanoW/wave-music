<?php

// Настройки подключения к базе данных
$dbHost = getenv("DB_HOST");
$dbName = getenv("DB_NAME");
$dbUser = getenv("DB_USER");
$dbPass = getenv("DB_PASS");

// Путь к файлу дампа
$dumpFile = __DIR__ . '/dump.sql';  // Замените путь к вашему файлу дампа

try {
    // Подключение к базе данных через PDO
    $pdo = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbName, $dbUser, $dbPass, [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES => false
    ]);

    $pdo->exec("SET foreign_key_checks = 0");

    // Чтение файла дампа
    $sql = file_get_contents($dumpFile);
    if ($sql === false) {
        throw new Exception("Не удалось прочитать файл дампа.");
    }

    // Выполнение SQL команд из дампа
    $pdo->exec($sql);
    echo "Миграция базы данных успешно выполнена.\n";
    
    $pdo->exec("SET foreign_key_checks = 1");

} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Ошибка выполнения миграции: " . $e->getMessage() . "\n";
}