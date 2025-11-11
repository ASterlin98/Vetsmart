<?php
declare(strict_types=1);

class Comision
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function calcularPorAtencion(int $empleadoId, int $atencionId, float $porcentaje): array
    {
        $st = $this->pdo->prepare('SELECT precio_final FROM atenciones_peluqueria WHERE id = ?');
        $st->execute([$atencionId]);
        $precio = (float)($st->fetchColumn() ?: 0);
        $valor = round($precio * ($porcentaje/100), 2);
        return ['empleado_id'=>$empleadoId,'atencion_id'=>$atencionId,'porcentaje'=>$porcentaje,'valor'=>$valor];
    }
}

