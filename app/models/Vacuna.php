<?php
class Vacuna {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getPorMascota($mascota_id) {
        $stmt = $this->db->prepare("SELECT * FROM vacunas WHERE mascota_id = ? ORDER BY fecha_aplicacion DESC");
        $stmt->execute([$mascota_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear($data) {
        $stmt = $this->db->prepare("INSERT INTO vacunas (mascota_id, nombre, fecha_aplicacion, proxima_dosis, descripcion) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $data['mascota_id'],
            $data['nombre'],
            $data['fecha_aplicacion'],
            $data['proxima_dosis'],
            $data['descripcion']
        ]);
    }

    public function eliminar($id) {
        $stmt = $this->db->prepare("DELETE FROM vacunas WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM vacunas WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $data)
    {
        $stmt = $this->db->prepare("UPDATE vacunas SET nombre = ?, fecha_aplicacion = ?, proxima_dosis = ?, descripcion = ? WHERE id = ?");
        $stmt->execute([
            $data['nombre'],
            $data['fecha_aplicacion'],
            $data['proxima_dosis'],
            $data['descripcion'],
            $id
        ]);
    }

}
