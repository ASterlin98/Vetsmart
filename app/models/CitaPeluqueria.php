<?php
declare(strict_types=1);

class CitaPeluqueria
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function ultimasPorPeluquero(int $peluqueroId, int $limit = 50): array
    {
        $sql = "SELECT c.*, s.nombre AS servicio, m.nombre AS mascota,
                       CONCAT(COALESCE(u.nombre,''),' ',COALESCE(u.apellido,'')) AS cliente
                FROM cpeluq c
                JOIN servicios_peluqueria s ON s.id=c.servicio_id
                JOIN mascotas m ON m.id=c.mascota_id
                JOIN usuarios u ON u.id=c.cliente_id
                WHERE c.peluquero_id=?
                ORDER BY c.fecha DESC, c.hora DESC
                LIMIT ?";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(1, $peluqueroId, PDO::PARAM_INT);
        $st->bindValue(2, $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
