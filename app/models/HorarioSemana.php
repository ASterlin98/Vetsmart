<?php
// app/models/HorarioSemana.php

declare(strict_types=1);

class HorarioSemana
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    /** Listar todos los horarios */
    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT hs.*, u.nombre, u.apellido
            FROM horarios_semana hs
            JOIN usuarios u ON hs.empleado_id = u.id
            ORDER BY FIELD(dia, 'Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'), hora_inicio
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Guardar nuevo horario */
    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO horarios_semana (empleado_id, dia, hora_inicio, hora_fin)
            VALUES (:empleado_id, :dia, :hora_inicio, :hora_fin)
        ");
        return $stmt->execute([
            ':empleado_id' => $data['empleado_id'],
            ':dia'         => $data['dia'],
            ':hora_inicio' => $data['hora_inicio'],
            ':hora_fin'    => $data['hora_fin'],
        ]);
    }

    /** Eliminar */
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM horarios_semana WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
