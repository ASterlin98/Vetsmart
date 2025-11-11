<?php
declare(strict_types=1);

class ServicioPeluqueria
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function activos(): array
    {
        $st = $this->pdo->query('SELECT * FROM servicios_peluqueria WHERE activo=1 ORDER BY nombre ASC');
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}

