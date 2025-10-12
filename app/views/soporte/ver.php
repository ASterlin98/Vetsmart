<?php
// app/views/soporte/ver.php

function get_badge_class($status) {
    switch ($status) {
        case 'Abierto': return 'bg-success';
        case 'En Proceso': return 'bg-info';
        case 'Cerrado': return 'bg-secondary';
        default: return 'bg-light text-dark';
    }
}

function get_priority_class($priority) {
    switch ($priority) {
        case 'Urgente': return 'text-danger fw-bold';
        case 'Alta': return 'text-warning fw-bold';
        case 'Media': return 'text-info';
        default: return 'text-muted';
    }
}

$current_user_id = $_SESSION['user']['id'];
?>

<style>
    .chat-container {
        max-height: 500px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 1rem;
        border-radius: 5px;
        margin-bottom: 1rem;
    }
    .chat-message {
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        border-radius: 10px;
        max-width: 80%;
    }
    .chat-message.author {
        background-color: #e9f5ff;
        align-self: flex-start;
        text-align: left;
    }
    .chat-message.recipient {
        background-color: #dcf8c6;
        align-self: flex-end;
        text-align: left;
        margin-left: auto;
    }
    .message-meta {
        font-size: 0.8rem;
        color: #666;
        margin-top: 5px;
    }
</style>

<div class="container-fluid px-4">
    <h1 class="mt-4">Detalle del Ticket #<?= htmlspecialchars($ticket['id']) ?></h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="/vetsmart/super_admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/vetsmart/soporte">Soporte</a></li>
        <li class="breadcrumb-item active">Ver Ticket</li>
    </ol>

    <?php if (isset($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_success']; unset($_SESSION['flash_success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $_SESSION['flash_error']; unset($_SESSION['flash_error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Columna de Chat/Mensajes -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-comments me-1"></i>
                    Historial de Conversación
                </div>
                <div class="card-body">
                    <div class="chat-container d-flex flex-column">
                        <?php foreach ($mensajes as $mensaje): ?>
                            <div class="chat-message <?= ($mensaje['usuario_id'] == $current_user_id) ? 'recipient' : 'author' ?>">
                                <p class="mb-0"><?= nl2br(htmlspecialchars($mensaje['mensaje'])) ?></p>
                                <div class="message-meta">
                                    <strong><?= htmlspecialchars($mensaje['autor_nombre']) ?></strong>
                                    - <small><?= date('d/m/Y H:i', strtotime($mensaje['creado_en'])) ?></small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <form action="/vetsmart/soporte/responder" method="POST">
                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                        <div class="input-group">
                            <textarea name="mensaje" class="form-control" placeholder="Escribe tu respuesta..." rows="3" required></textarea>
                            <button class="btn btn-primary" type="submit">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna de Detalles/Acciones -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Detalles y Acciones
                </div>
                <div class="card-body">
                    <h5><?= htmlspecialchars($ticket['asunto']) ?></h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Creado por:</strong> <?= htmlspecialchars($ticket['creador_nombre']) ?></li>
                        <li class="list-group-item"><strong>Fecha Creación:</strong> <?= date('d/m/Y H:i', strtotime($ticket['creado_en'])) ?></li>
                        <li class="list-group-item"><strong>Última Actividad:</strong> <?= date('d/m/Y H:i', strtotime($ticket['actualizado_en'])) ?></li>
                    </ul>
                    <hr>
                    <form action="/vetsmart/soporte/actualizarMeta" method="POST">
                        <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']) ?>">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="form-select">
                                <option value="Abierto" <?= $ticket['estado'] == 'Abierto' ? 'selected' : '' ?>>Abierto</option>
                                <option value="En Proceso" <?= $ticket['estado'] == 'En Proceso' ? 'selected' : '' ?>>En Proceso</option>
                                <option value="Cerrado" <?= $ticket['estado'] == 'Cerrado' ? 'selected' : '' ?>>Cerrado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="prioridad" class="form-label">Prioridad</label>
                            <select name="prioridad" id="prioridad" class="form-select">
                                <option value="Baja" <?= $ticket['prioridad'] == 'Baja' ? 'selected' : '' ?>>Baja</option>
                                <option value="Media" <?= $ticket['prioridad'] == 'Media' ? 'selected' : '' ?>>Media</option>
                                <option value="Alta" <?= $ticket['prioridad'] == 'Alta' ? 'selected' : '' ?>>Alta</option>
                                <option value="Urgente" <?= $ticket['prioridad'] == 'Urgente' ? 'selected' : '' ?>>Urgente</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="asignado_a" class="form-label">Asignado a</label>
                            <select name="asignado_a" id="asignado_a" class="form-select">
                                <option value="">-- Sin asignar --</option>
                                <?php foreach ($staff as $member): ?>
                                    <option value="<?= $member['id'] ?>" <?= $ticket['asignado_a'] == $member['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($member['nombre'] . ' ' . $member['apellido']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Actualizar Ticket</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>