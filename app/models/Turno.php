<?php
class Turno {
    private $db;
    public function __construct($db){ $this->db = $db; }

    public function asignar($idusu, $fecha, $hora_inicio, $hora_fin){
        $sql = "INSERT INTO turnos (idusu, fecha, hora_inicio, hora_fin) VALUES (?, ?, ?, ?)";
        return $this->db->prepare($sql)->execute([$idusu, $fecha, $hora_inicio, $hora_fin]);
    }

    public function listarPorEmpleado($idusu){
        $sql = "SELECT * FROM turnos WHERE idusu=?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$idusu]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
