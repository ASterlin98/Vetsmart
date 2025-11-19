<?php
// app/models/SuperAdmin.php

class SuperAdmin {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function countTotalUsuarios() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM usuarios");
        return $stmt->fetchColumn();
    }

    public function countTicketsAbiertos() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM tickets WHERE estado = 'Abierto'");
        return $stmt->fetchColumn();
    }

    public function countCitasHoy() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE DATE(fecha) = CURDATE()");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function countUsuariosPorRol() {
        $sql = "SELECT r.nombre, COUNT(u.id) as total
                FROM roles r
                LEFT JOIN usuarios u ON r.id = u.role_id
                GROUP BY r.nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTicketsPorPrioridad() {
        $sql = "SELECT prioridad, COUNT(*) as total
                FROM tickets
                GROUP BY prioridad";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentActivity($limit, $offset) {
        $sql = "SELECT 'cita' AS tipo, c.id AS entidad_id,
                       CONCAT(COALESCE(u_creo.nombre,''),' ',COALESCE(u_creo.apellido,'')) AS actor,
                       'Cita creada' AS accion,
                       COALESCE(c.creado_en, c.fecha, NOW()) AS creado_en,
                       CONCAT('Mascota: ', COALESCE(m.nombre,''), ' — Servicio: ', COALESCE(s.nombre,'')) AS detalle
                FROM citas c
                LEFT JOIN usuarios u_creo ON c.creado_por = u_creo.id
                LEFT JOIN mascotas m ON c.mascota_id = m.id
                LEFT JOIN servicios s ON c.servicio_id = s.id

                UNION ALL

                SELECT 'usuario' AS tipo, u.id AS entidad_id,
                       CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS actor,
                       'Usuario registrado' AS accion,
                       u.creado_en AS creado_en,
                       CONCAT('Email: ', COALESCE(u.email,'')) AS detalle
                FROM usuarios u

                ORDER BY creado_en DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTotalActivities() {
        $sql = "SELECT (SELECT COUNT(*) FROM citas) + (SELECT COUNT(*) FROM usuarios)";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }

    public function getRecentTickets($limit) {
        $sql = "SELECT t.*, CONCAT(u.nombre, ' ', u.apellido) as creador_nombre
                FROM tickets t
                JOIN usuarios u ON t.usuario_id = u.id
                ORDER BY t.creado_en DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}