<?php
// app/models/Servicio.php

class Servicio {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Obtener todos los servicios
    public function getAll() {
        $sql = "SELECT * FROM servicios ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener servicios activos (nombre solicitado por el controlador)
    public function getAllActivos() {
        $sql = "SELECT * FROM servicios WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Alias por compatibilidad (en caso de que en otro lado se llame getActivos)
    public function getActivos() {
        return $this->getAllActivos();
    }

    // Obtener un servicio por su ID
    public function getById($id) {
        $sql = "SELECT * FROM servicios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo servicio
    public function crear($data) {
        $sql = "INSERT INTO servicios (nombre, descripcion, precio, duracion_min, activo, creado_en)
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['duracion_min'],
            $data['activo'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    // Actualizar un servicio existente
    public function actualizar($id, $data) {
        $sql = "UPDATE servicios SET nombre = ?, descripcion = ?, precio = ?, duracion_min = ?, activo = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['nombre'],
            $data['descripcion'],
            $data['precio'],
            $data['duracion_min'],
            $data['activo'],
            $id
        ]);
    }

    // Eliminar un servicio
    public function eliminar($id) {
        $sql = "DELETE FROM servicios WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
