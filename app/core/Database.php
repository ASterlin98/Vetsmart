<?php
// app/core/Database.php
class Database {
    private static $instance = null;
    private $pdo;

    public function __construct() {

        $dbHost = '127.0.0.1';
        $dbName = 'vetsmart';
        $dbUser = 'root';
        $dbPass = '';
        $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}
