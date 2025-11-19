<?php
// app/models/Producto.php
class Producto {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function listar(string $q = ''): array {
        $sql = "SELECT * FROM productos";
        $params = [];
        if ($q !== '') {
            // En MySQL (PDO, emulate_prepares desactivado) no se puede reutilizar el mismo placeholder dos veces.
            // Usar placeholders distintos para evitar HY093 Invalid parameter number.
            $sql .= " WHERE nombre LIKE :q1 OR categoria LIKE :q2";
            $like = '%' . $q . '%';
            $params[':q1'] = $like;
            $params[':q2'] = $like;
        }
        $sql .= " ORDER BY nombre ASC";
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function obtener(int $id): ?array {
        $st = $this->db->prepare("SELECT * FROM productos WHERE id = :id");
        $st->execute([':id' => $id]);
        $r = $st->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function crear(array $d): int {
        $st = $this->db->prepare("INSERT INTO productos (nombre, categoria, unidad, stock, costo, precio, sku, notas, creado_en, actualizado_en)
            VALUES (:nombre, :categoria, :unidad, :stock, :costo, :precio, :sku, :notas, NOW(), NOW())");
        $st->execute([
            ':nombre' => $d['nombre'],
            ':categoria' => $d['categoria'] ?? null,
            ':unidad' => $d['unidad'] ?? 'unidad',
            ':stock' => (float)($d['stock'] ?? 0),
            ':costo' => (float)($d['costo'] ?? 0),
            ':precio' => (float)($d['precio'] ?? 0),
            ':sku' => $d['sku'] ?? null,
            ':notas' => $d['notas'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function actualizar(int $id, array $d): bool {
        $st = $this->db->prepare("UPDATE productos SET nombre=:nombre, categoria=:categoria, unidad=:unidad, stock=:stock, costo=:costo, precio=:precio, sku=:sku, notas=:notas, actualizado_en=NOW() WHERE id=:id");
        return $st->execute([
            ':id' => $id,
            ':nombre' => $d['nombre'],
            ':categoria' => $d['categoria'] ?? null,
            ':unidad' => $d['unidad'] ?? 'unidad',
            ':stock' => (float)($d['stock'] ?? 0),
            ':costo' => (float)($d['costo'] ?? 0),
            ':precio' => (float)($d['precio'] ?? 0),
            ':sku' => $d['sku'] ?? null,
            ':notas' => $d['notas'] ?? null,
        ]);
    }

    public function eliminar(int $id): bool {
        $st = $this->db->prepare("DELETE FROM productos WHERE id = :id");
        return $st->execute([':id' => $id]);
    }
}
