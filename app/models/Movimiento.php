<?php
// app/models/Movimiento.php
class Movimiento {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function listar($desde = null, $hasta = null, $tipo = null): array {
        $sql = "SELECT * FROM movimientos WHERE 1=1";
        $params = [];
        if ($tipo && in_array($tipo, ['ingreso','egreso'], true)) {
            $sql .= " AND tipo = :tipo";
            $params[':tipo'] = $tipo;
        }
        if ($desde) { $sql .= " AND fecha >= :desde"; $params[':desde'] = $desde; }
        if ($hasta) { $sql .= " AND fecha <= :hasta"; $params[':hasta'] = $hasta; }
        $sql .= " ORDER BY fecha DESC, id DESC";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function crear(array $d): int {
        // Intentar insertar con producto_id si viene y la columna existe
        try {
            if (isset($d['producto_id']) && $d['producto_id']) {
                $st = $this->db->prepare("INSERT INTO movimientos (tipo, concepto, monto, fecha, notas, creado_por, producto_id, creado_en) VALUES (:tipo, :concepto, :monto, :fecha, :notas, :creado_por, :producto_id, NOW())");
                $st->execute([
                    ':tipo' => $d['tipo'],
                    ':concepto' => $d['concepto'],
                    ':monto' => $d['monto'],
                    ':fecha' => $d['fecha'],
                    ':notas' => $d['notas'] ?? null,
                    ':creado_por' => $d['creado_por'] ?? null,
                    ':producto_id' => $d['producto_id'],
                ]);
                return (int)$this->db->lastInsertId();
            }
        } catch (\Throwable $e) {
            // Fallback a inserción sin producto_id
        }
        $st = $this->db->prepare("INSERT INTO movimientos (tipo, concepto, monto, fecha, notas, creado_por, creado_en) VALUES (:tipo, :concepto, :monto, :fecha, :notas, :creado_por, NOW())");
        $st->execute([
            ':tipo' => $d['tipo'],
            ':concepto' => $d['concepto'],
            ':monto' => $d['monto'],
            ':fecha' => $d['fecha'],
            ':notas' => $d['notas'] ?? null,
            ':creado_por' => $d['creado_por'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function obtener(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM movimientos WHERE id = :id");
        $st->execute([':id' => $id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function actualizar(int $id, array $d): bool {
        // Intentar actualizar con producto_id si aplica
        try {
            if (isset($d['producto_id'])) {
                $st = $this->db->prepare("UPDATE movimientos SET tipo=:tipo, concepto=:concepto, monto=:monto, fecha=:fecha, notas=:notas, producto_id=:producto_id WHERE id = :id");
                return $st->execute([
                    ':id' => $id,
                    ':tipo' => $d['tipo'],
                    ':concepto' => $d['concepto'],
                    ':monto' => $d['monto'],
                    ':fecha' => $d['fecha'],
                    ':notas' => $d['notas'] ?? null,
                    ':producto_id' => $d['producto_id'],
                ]);
            }
        } catch (\Throwable $e) {
            // Fallback sin producto_id
        }
        $st = $this->db->prepare("UPDATE movimientos SET tipo=:tipo, concepto=:concepto, monto=:monto, fecha=:fecha, notas=:notas WHERE id = :id");
        return $st->execute([
            ':id' => $id,
            ':tipo' => $d['tipo'],
            ':concepto' => $d['concepto'],
            ':monto' => $d['monto'],
            ':fecha' => $d['fecha'],
            ':notas' => $d['notas'] ?? null,
        ]);
    }

    public function eliminar(int $id): bool {
        $st = $this->db->prepare("DELETE FROM movimientos WHERE id = :id");
        return $st->execute([':id' => $id]);
    }

    // Conceptos predefinidos con montos fijos
    public function conceptos(string $tipo): array {
        try {
            $sql = "SELECT id, tipo, concepto, monto, activo FROM mov_conceptos WHERE tipo = :t AND activo = 1 ORDER BY concepto ASC";
            $st = $this->db->prepare($sql);
            $st->execute([':t' => $tipo]);
            return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) { return []; }
    }

    public function conceptoPorNombre(string $tipo, string $concepto): ?array {
        try {
            $st = $this->db->prepare("SELECT * FROM mov_conceptos WHERE tipo = :t AND concepto = :c AND activo = 1 LIMIT 1");
            $st->execute([':t' => $tipo, ':c' => $concepto]);
            $r = $st->fetch(PDO::FETCH_ASSOC);
            return $r ?: null;
        } catch (Throwable $e) { return null; }
    }
}
