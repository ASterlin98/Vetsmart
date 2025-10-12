<?php
// app/controllers/SoporteController.php

require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/models/Ticket.php';
require_once APP_ROOT . '/models/Usuario.php';

class SoporteController extends Controller {
    private $ticketModel;
    private $userModel;

    public function __construct($pdo) {
        parent::__construct($pdo);
        $this->ticketModel = new Ticket($pdo);
        $this->userModel = new Usuario($pdo);
    }

    /**
     * Muestra el listado de todos los tickets de soporte.
     */
    public function index() {
        $tickets = $this->ticketModel->getAll();
        $this->view('soporte/index', ['tickets' => $tickets], 'main_superadmin');
    }

    /**
     * Muestra la vista detallada de un ticket y su historial de mensajes.
     * @param int $id - El ID del ticket.
     */
    public function ver($id) {
        $ticket = $this->ticketModel->getById($id);
        if (!$ticket) {
            http_response_code(404);
            $this->view('errors/404', [], 'main_superadmin');
            return;
        }

        $mensajes = $this->ticketModel->getMessagesByTicketId($id);
        $staff = $this->userModel->getStaff();

        $this->view('soporte/ver', [
            'ticket' => $ticket,
            'mensajes' => $mensajes,
            'staff' => $staff
        ], 'main_superadmin');
    }

    /**
     * Muestra el formulario para crear un nuevo ticket.
     */
    public function crear() {
        $staff = $this->userModel->getStaff();
        $this->view('soporte/crear', ['staff' => $staff], 'main_superadmin');
    }

    /**
     * Procesa la creación de un nuevo ticket.
     */
    public function guardar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/soporte/crear');
            exit;
        }

        $data = [
            'usuario_id'  => $_SESSION['user']['id'],
            'asunto'      => $_POST['asunto'],
            'descripcion' => $_POST['descripcion'],
            'prioridad'   => $_POST['prioridad'],
            'asignado_a'  => !empty($_POST['asignado_a']) ? $_POST['asignado_a'] : null
        ];

        $ticketId = $this->ticketModel->create($data);

        if ($ticketId) {
            $this->ticketModel->addMessage($ticketId, $data['usuario_id'], $data['descripcion']);
            header('Location: /vetsmart/soporte/ver/' . $ticketId);
        } else {
            $_SESSION['flash_error'] = "No se pudo crear el ticket.";
            header('Location: /vetsmart/soporte/crear');
        }
        exit;
    }

    /**
     * Procesa el envío de una nueva respuesta en un ticket.
     */
    public function responder() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido');
        }

        $ticket_id = $_POST['ticket_id'];
        $mensaje = trim($_POST['mensaje']);
        $usuario_id = $_SESSION['user']['id'];

        if (empty($mensaje)) {
            $_SESSION['flash_error'] = "El mensaje no puede estar vacío.";
            header('Location: /vetsmart/soporte/ver/' . $ticket_id);
            exit;
        }

        if ($this->ticketModel->addMessage($ticket_id, $usuario_id, $mensaje)) {
             $_SESSION['flash_success'] = "Respuesta enviada.";
        } else {
            $_SESSION['flash_error'] = "No se pudo enviar la respuesta.";
        }

        header('Location: /vetsmart/soporte/ver/' . $ticket_id);
        exit;
    }

    /**
     * Actualiza los metadatos de un ticket (estado, prioridad, asignado).
     */
    public function actualizarMeta() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit('Método no permitido');
        }

        $ticket_id  = (int)($_POST['ticket_id'] ?? 0);

        if ($ticket_id <= 0) {
            $_SESSION['flash_error'] = "Error: ID de ticket no válido. La operación ha sido cancelada para proteger los datos.";
            header('Location: /vetsmart/soporte');
            exit;
        }

        $estado     = $_POST['estado'];
        $prioridad  = $_POST['prioridad'];
        $asignado_a = !empty($_POST['asignado_a']) ? $_POST['asignado_a'] : null;

        if ($this->ticketModel->updateTicketMeta($ticket_id, $estado, $prioridad, $asignado_a)) {
            $_SESSION['flash_success'] = "El ticket ha sido actualizado.";
        } else {
            $_SESSION['flash_error'] = "No se pudo actualizar el ticket.";
        }

        header('Location: /vetsmart/soporte/ver/' . $ticket_id);
        exit;
    }
}