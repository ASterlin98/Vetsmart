<?php
// app/models/TurnoEmpleado.php

declare(strict_types=1);

class TurnoEmpleado
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Listar turnos */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT t.*, u.nombre, u.apellido
            FROM turnos_empleado t
            JOIN usuarios u ON t.empleado_id = u.id
            ORDER BY t.inicio DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Guardar turno */
    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO turnos_empleado (empleado_id, inicio, fin, tipo, notas, creado_por)
            VALUES (:empleado_id, :inicio, :fin, :tipo, :notas, :creado_por)
        ");
        return $stmt->execute([
            ':empleado_id' => $data['empleado_id'],
            ':inicio'      => $data['inicio'],
            ':fin'         => $data['fin'],
            ':tipo'        => $data['tipo'],
            ':notas'       => $data['notas'],
            ':creado_por'  => $data['creado_por'],
        ]);
    }

    /** Eliminar turno */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM turnos_empleado WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
