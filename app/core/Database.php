<?php
class Database {

    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Valores desde .env (o fallback locales)
        $dbHost = getenv("DB_HOST") ?: "localhost";
        $dbPort = getenv("DB_PORT") ?: "3306";
        $dbName = getenv("DB_NAME") ?: "u113289098_vetsmart";
        $dbUser = getenv("DB_USER") ?: "u113289098_vetsmart";
        $dbPass = getenv("DB_PASS") ?: "Vetsmar12345";

        // Configuración estricta para MySQL (Hostinger)
        $dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=utf8mb4";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $dbUser, $dbPass, $options);
        } catch (PDOException $e) {
            error_log("DB connection error: " . $e->getMessage());
            die("Error de conexión a la base de datos: " . $e->getMessage() . "<br>Asegúrate de que la BD y usuario existan y que el host sea correcto.");
        }
    }

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
}