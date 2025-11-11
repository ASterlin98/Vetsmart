<?php
// app/models/NotaMascota.php

class NotaMascota {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function obtenerPorMascota($mascotaId) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notas_mascotas
            WHERE mascota_id = :mascota_id
            ORDER BY creado_en DESC
        ");
        $stmt->execute(['mascota_id' => $mascotaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function guardar($mascotaId, $veterinarioId, $nota) {
        $stmt = $this->db->prepare("
            INSERT INTO notas_mascotas (mascota_id, veterinario_id, nota)
            VALUES (:mascota_id, :veterinario_id, :nota)
        ");
        return $stmt->execute([
            'mascota_id' => $mascotaId,
            'veterinario_id' => $veterinarioId,
            'nota' => $nota
        ]);
    }

    public function obtenerPorId($id) {
        $stmt = $this->db->prepare("SELECT * FROM notas_mascotas WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $nota) {
        $stmt = $this->db->prepare("
            UPDATE notas_mascotas
            SET nota = :nota
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'nota' => $nota
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM notas_mascotas WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function crear($mascota_id, $veterinario_id, $nota): int {
        $sql = "INSERT INTO notas_mascotas (mascota_id, veterinario_id, nota, creado_en, actualizado_en)
                VALUES (?, ?, ?, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mascota_id, $veterinario_id, $nota]);
        return $this->db->lastInsertId();
    }
}       

