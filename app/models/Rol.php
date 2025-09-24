<?php
require_once __DIR__ . '/../core/Database.php';

class Rol {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAllRoles() {
        $sql = "SELECT * FROM roles";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


?>