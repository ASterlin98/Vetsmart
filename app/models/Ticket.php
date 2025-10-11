<?php
// app/models/Ticket.php

class Ticket {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Crea un nuevo ticket de soporte.
     * @param array $data - Datos del ticket (usuario_id, asunto, descripcion, etc.)
     * @return int|false - El ID del nuevo ticket o false si falla.
     */
    public function create($data) {
        try {
            $sql = "INSERT INTO tickets (usuario_id, asunto, descripcion, prioridad, asignado_a)
                    VALUES (:usuario_id, :asunto, :descripcion, :prioridad, :asignado_a)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':usuario_id'   => $data['usuario_id'],
                ':asunto'       => $data['asunto'],
                ':descripcion'  => $data['descripcion'],
                ':prioridad'    => $data['prioridad'] ?? 'Media',
                ':asignado_a'   => $data['asignado_a'] ?? null
            ]);
            return (int)$this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error al crear ticket: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Añade un mensaje a un ticket existente.
     * @param int $ticket_id
     * @param int $usuario_id
     * @param string $mensaje
     * @return bool
     */
    public function addMessage($ticket_id, $usuario_id, $mensaje) {
        try {
            $sql = "INSERT INTO ticket_mensajes (ticket_id, usuario_id, mensaje)
                    VALUES (:ticket_id, :usuario_id, :mensaje)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':ticket_id'  => $ticket_id,
                ':usuario_id' => $usuario_id,
                ':mensaje'    => $mensaje
            ]);

            // Actualizar la fecha de 'actualizado_en' del ticket principal
            $stmt_update = $this->db->prepare("UPDATE tickets SET actualizado_en = CURRENT_TIMESTAMP WHERE id = :ticket_id");
            $stmt_update->execute([':ticket_id' => $ticket_id]);

            return true;
        } catch (PDOException $e) {
            error_log("Error al añadir mensaje al ticket $ticket_id: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los tickets, uniendo información del creador y asignado.
     * @return array
     */
    public function getAll() {
        $sql = "SELECT
                    t.id, t.asunto, t.estado, t.prioridad, t.creado_en, t.actualizado_en,
                    CONCAT(u_creador.nombre, ' ', u_creador.apellido) as creador_nombre,
                    CONCAT(u_asignado.nombre, ' ', u_asignado.apellido) as asignado_nombre
                FROM tickets t
                JOIN usuarios u_creador ON t.usuario_id = u_creador.id
                LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                ORDER BY t.actualizado_en DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene un ticket específico por su ID.
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $sql = "SELECT
                    t.*,
                    CONCAT(u_creador.nombre, ' ', u_creador.apellido) as creador_nombre,
                    u_creador.email as creador_email,
                    CONCAT(u_asignado.nombre, ' ', u_asignado.apellido) as asignado_nombre
                FROM tickets t
                JOIN usuarios u_creador ON t.usuario_id = u_creador.id
                LEFT JOIN usuarios u_asignado ON t.asignado_a = u_asignado.id
                WHERE t.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene todos los mensajes de un ticket específico.
     * @param int $ticket_id
     * @return array
     */
    public function getMessagesByTicketId($ticket_id) {
        $sql = "SELECT
                    m.mensaje, m.creado_en,
                    u.id as usuario_id,
                    CONCAT(u.nombre, ' ', u.apellido) as autor_nombre
                FROM ticket_mensajes m
                JOIN usuarios u ON m.usuario_id = u.id
                WHERE m.ticket_id = :ticket_id
                ORDER BY m.creado_en ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ticket_id' => $ticket_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Actualiza el estado, prioridad o asignación de un ticket.
     * @param int $id
     * @param string $estado
     * @param string $prioridad
     * @param int|null $asignado_a
     * @return bool
     */
    public function updateTicketMeta($id, $estado, $prioridad, $asignado_a) {
        try {
            $sql = "UPDATE tickets SET estado = :estado, prioridad = :prioridad, asignado_a = :asignado_a WHERE id = :id";
            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->bindValue(':estado', $estado, PDO::PARAM_STR);
            $stmt->bindValue(':prioridad', $prioridad, PDO::PARAM_STR);

            if (empty($asignado_a)) {
                $stmt->bindValue(':asignado_a', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':asignado_a', (int)$asignado_a, PDO::PARAM_INT);
            }

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error al actualizar metadatos del ticket {$id}: " . $e->getMessage());
            return false;
        }
    }
}