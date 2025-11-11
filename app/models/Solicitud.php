<?php
// app/models/Solicitud.php

declare(strict_types=1);

class Solicitud
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Listar solicitudes */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT s.*, u.nombre, u.apellido
            FROM solicitudes s
            JOIN usuarios u ON s.usuario_id = u.id
            ORDER BY s.fecha_inicio DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Crear solicitud */
    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO solicitudes (usuario_id, tipo, fecha_inicio, fecha_fin, motivo)
            VALUES (:usuario_id, :tipo, :fecha_inicio, :fecha_fin, :motivo)
        ");
        return $stmt->execute([
            ':usuario_id'  => $data['usuario_id'],
            ':tipo'        => $data['tipo'],
            ':fecha_inicio'=> $data['fecha_inicio'],
            ':fecha_fin'   => $data['fecha_fin'],
            ':motivo'      => $data['motivo'] ?? null,
        ]);
    }

    /** Eliminar solicitud */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM solicitudes WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
