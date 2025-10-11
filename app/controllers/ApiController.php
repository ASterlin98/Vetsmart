<?php

require_once APP_ROOT . '/core/Controller.php';

class ApiController extends Controller
{
    public function getCitaDetalles($id)
    {
        header('Content-Type: application/json');
        try {
            $stmt = $this->db->prepare("
                SELECT
                    c.id, c.fecha, c.estado, c.notas,
                    s.nombre as servicio_nombre, s.precio as servicio_precio,
                    m.nombre as mascota_nombre, m.especie as mascota_especie, m.raza as mascota_raza,
                    cli.nombre as cliente_nombre, cli.apellido as cliente_apellido,
                    emp.nombre as empleado_nombre, emp.apellido as empleado_apellido,
                    uc.nombre as creador_nombre, uc.apellido as creador_apellido
                FROM citas c
                LEFT JOIN servicios s ON c.servicio_id = s.id
                LEFT JOIN mascotas m ON c.mascota_id = m.id
                LEFT JOIN usuarios cli ON c.cliente_id = cli.id
                LEFT JOIN usuarios emp ON c.empleado_id = emp.id
                LEFT JOIN usuarios uc ON c.creado_por = uc.id
                WHERE c.id = :id
            ");
            $stmt->execute([':id' => $id]);
            $cita = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$cita) {
                http_response_code(404);
                echo json_encode(['error' => 'Cita no encontrada.']);
                return;
            }

            echo json_encode($cita);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error al obtener los detalles de la cita.']);
        }
    }
}