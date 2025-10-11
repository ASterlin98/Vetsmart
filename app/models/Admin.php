<?php
// app/models/Admin.php

class Admin {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getEstadisticas() {
        $stats = [];

        // Citas para hoy
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE DATE(fecha) = CURDATE()");
        $stmt->execute();
        $stats['citas_hoy'] = $stmt->fetchColumn();

        // Citas pendientes
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM citas WHERE estado = 'pendiente'");
        $stmt->execute();
        $stats['citas_pendientes'] = $stmt->fetchColumn();

        // Nuevos clientes (este mes)
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM usuarios WHERE role_id = (SELECT id FROM roles WHERE nombre = 'cliente') AND MONTH(creado_en) = MONTH(CURDATE()) AND YEAR(creado_en) = YEAR(CURDATE())");
        $stmt->execute();
        $stats['nuevos_clientes_mes'] = $stmt->fetchColumn();

        // Ingresos totales (este mes, de citas completadas)
        $stmt = $this->db->prepare("SELECT SUM(s.precio) FROM citas c JOIN servicios s ON c.servicio_id = s.id WHERE c.estado = 'completada' AND MONTH(c.fecha) = MONTH(CURDATE()) AND YEAR(c.fecha) = YEAR(CURDATE())");
        $stmt->execute();
        $stats['ingresos_mes'] = $stmt->fetchColumn() ?? 0;

        return $stats;
    }
}