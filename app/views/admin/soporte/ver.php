<?php
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
        <li class="breadcrumb-item"><a href="/vetsmart/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/vetsmart/admin/soporte">Soporte</a></li>
        <li class="breadcrumb-item active">Ver Ticket</li>
    </ol>

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

        <!-- Columna de Detalles -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-1"></i>
                    Detalles del Ticket
                </div>
                <div class="card-body">
                    <h5><?= htmlspecialchars($ticket['asunto']) ?></h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Estado:</strong> <?= htmlspecialchars($ticket['estado']) ?></li>
                        <li class="list-group-item"><strong>Prioridad:</strong> <?= htmlspecialchars($ticket['prioridad']) ?></li>
                        <li class="list-group-item"><strong>Fecha Creación:</strong> <?= date('d/m/Y H:i', strtotime($ticket['creado_en'])) ?></li>
                        <li class="list-group-item"><strong>Última Actividad:</strong> <?= date('d/m/Y H:i', strtotime($ticket['actualizado_en'])) ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>