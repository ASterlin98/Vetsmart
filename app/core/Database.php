<?php
class Database {

    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Valores de Render o locales
        $dbHost = getenv("DB_HOST") ?: "127.0.0.1";
        $dbPort = getenv("DB_PORT") ?: "3306";
        $dbName = getenv("DB_NAME") ?: "vetsmart";
        $dbUser = getenv("DB_USER") ?: "root";
        $dbPass = getenv("DB_PASS") ?: "";

        // Configuración para PostgreSQL (Render)
        if ($dbPort == "5432" || strpos($dbHost, 'render.com') !== false || strpos($dbHost, 'dpg-') !== false) {
            // Se agrega sslmode=require para cumplir con los requisitos de Render
            $dsn = "pgsql:host=$dbHost;port=$dbPort;dbname=$dbName;sslmode=require";
        } else {
            // Configuración para MySQL (Local)
            $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";
        }

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            // Es importante registrar el error en los logs de Render para depuración
            error_log("DB connection error: " . $e->getMessage());
            die("Error de conexión a la base de datos.");
        }
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}