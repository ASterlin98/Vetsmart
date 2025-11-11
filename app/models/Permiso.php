<?php
// app/models/Permiso.php

class Permiso {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Obtiene todos los permisos, ordenados por módulo y luego por orden de visualización.
     * @return array
     */
    public function getAll() {
        $sql = "SELECT id, modulo, nombre, descripcion, accion, orden, activo
                FROM permisos
                ORDER BY modulo, orden, nombre";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un permiso específico por su ID.
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT * FROM permisos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un nuevo permiso en la base de datos.
     * @param array $data - Datos del permiso.
     * @return bool
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO permisos (modulo, nombre, descripcion, accion, orden, activo)
                    VALUES (:modulo, :nombre, :descripcion, :accion, :orden, :activo)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':modulo'       => $data['modulo'],
                ':nombre'       => $data['nombre'],
                ':descripcion'  => $data['descripcion'],
                ':accion'       => $data['accion'],
                ':orden'        => $data['orden'] ?? 0,
                ':activo'       => $data['activo'] ?? 1
            ]);
        } catch (PDOException $e) {
            // Manejar error de nombre de permiso duplicado (UNIQUE KEY `idx_nombre_unico`)
            if ($e->errorInfo[1] == 1062) {
                throw new Exception("El nombre del permiso '{$data['nombre']}' ya existe.");
            }
            throw $e; // Re-lanzar otras excepciones
        }
    }

    /**
     * Actualiza un permiso existente.
     * @param int $id
     * @param array $data - Datos a actualizar.
     * @return bool
     */
    public function update($id, $data) {
        try {
            $sql = "UPDATE permisos SET
                        modulo = :modulo,
                        nombre = :nombre,
                        descripcion = :descripcion,
                        accion = :accion,
                        orden = :orden,
                        activo = :activo
                    WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                ':id'           => $id,
                ':modulo'       => $data['modulo'],
                ':nombre'       => $data['nombre'],
                ':descripcion'  => $data['descripcion'],
                ':accion'       => $data['accion'],
                ':orden'        => $data['orden'] ?? 0,
                ':activo'       => $data['activo'] ?? 1
            ]);
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                throw new Exception("El nombre del permiso '{$data['nombre']}' ya está en uso por otro permiso.");
            }
            throw $e;
        }
    }

    /**
     * Elimina un permiso de la base de datos.
     * La restricción FOREIGN KEY en `rol_permisos` con ON DELETE CASCADE
     * se encargará de eliminar las asignaciones automáticamente.
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $sql = "DELETE FROM permisos WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Obtiene una lista de todos los módulos únicos existentes.
     * @return array
     */
    public function getModules() {
        $sql = "SELECT DISTINCT modulo FROM permisos ORDER BY modulo";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}