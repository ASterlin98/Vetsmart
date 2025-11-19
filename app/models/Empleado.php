<?php
declare(strict_types=1);

class Empleado
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmpleadoId(int $empleadoId): array
    {
        $st = $this->pdo->prepare(
            "SELECT 
                u.id AS empleado_id,
                u.id AS usuario_id,
                CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS nombre_completo,
                u.docusu AS documento,
                u.email,
                u.telefono,
                COALESCE(e.especialidad,'Peluquero') AS cargo,
                COALESCE(e.activo,1) AS estado,
                e.fecha_ingreso,
                NULL AS salon
             FROM usuarios u
             LEFT JOIN emp_det e ON e.usuario_id = u.id
             WHERE u.id = ?"
        );
        $st->execute([$empleadoId]);
        return $st->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    public function findByUsuarioId(int $usuarioId): array
    {
        return $this->findByEmpleadoId($usuarioId);
    }
}