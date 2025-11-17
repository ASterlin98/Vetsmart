<?php
// app/controllers/AdminController.php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AdminController extends Controller
{
    /**
     * Desbloquear usuario por ID
     */
    public function desbloquearUsuario($id)
    {
        // Solo permitir desbloquear roles 3,4,5,6
        $stmt = $this->pdo->prepare("UPDATE usuarios SET is_blocked = 0 WHERE id = :id AND role_id IN (3,4,5,6)");
        $stmt->execute([':id' => $id]);
        // Redirigir de vuelta a la lista de bloqueados
        header("Location: /vetsmart/admin/locked_users");
        exit;
    }
    private $pdo;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
    }

    /**
     * Mostrar usuarios bloqueados
     */
    public function lockedUsers()
    {
    $sql = "SELECT u.id, u.nombre, u.apellido, u.docusu, u.email, u.telefono, u.role_id, r.nombre AS rol, u.is_blocked
        FROM usuarios u
        JOIN roles r ON u.role_id = r.id
        WHERE u.is_blocked = 1 AND u.role_id IN (3,4,5,6)
        ORDER BY u.id DESC";
        $stmt = $this->pdo->query($sql);
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->view("admin/empleados/locked_users", [
            'empleados' => $empleados
        ], "main_admin");
    }

    // --- Compatibilidad: alias publicos que tu router puede llamar ---
    public function empleados() { return $this->empleadosIndex(); }
    public function empleadosCrear() { return $this->crearEmpleado(); }
    public function empleadosEditar($id) { return $this->editarEmpleado($id); }
    public function empleadosActualizar($id) { return $this->actualizarEmpleado($id); }
    public function empleadosEliminar($id) { return $this->eliminarEmpleado($id); }
    // ---------------------------------------------------------------
    

    /** LISTAR EMPLEADOS */
    public function empleadosIndex()
    {
        // Seleccionamos telefono y nombre del rol; excluimos super_admin (1) y cliente (6)
        $sql = "
            SELECT u.id, u.nombre, u.apellido, u.docusu, u.email, u.telefono, u.role_id, r.nombre AS rol,
                   e.especialidad, e.salario, e.fecha_ingreso, e.activo
            FROM usuarios u
            JOIN roles r ON u.role_id = r.id
            LEFT JOIN emp_det e ON u.id = e.usuario_id
            WHERE u.role_id NOT IN (1,6)
            ORDER BY u.id DESC
        ";
        $stmt = $this->pdo->query($sql);
        $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Traer roles disponibles (sin super_admin ni cliente) para el select en el modal/form
        $roles = $this->pdo->query("SELECT id, nombre FROM roles WHERE id NOT IN (1,6) ORDER BY nombre")
                           ->fetchAll(PDO::FETCH_ASSOC);

        $this->view("admin/empleados/index", [
            'empleados' => $empleados,
            'roles' => $roles
        ], "main_admin");
    }

    /** CREAR EMPLEADO (FORM) */
    public function crearEmpleado()
    {
        $roles = $this->pdo->query("SELECT id, nombre FROM roles WHERE id NOT IN (1,6) ORDER BY nombre")
                           ->fetchAll(PDO::FETCH_ASSOC);
        $this->view("admin/empleados/crear", ['roles' => $roles], "main_admin");
    }

    /** GUARDAR NUEVO EMPLEADO */
    public function guardarEmpleado()
    {
        $this->pdo->beginTransaction();
        try {
            // Validaciones minimas (puedes expandir)
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $docusu = trim($_POST['docusu'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role_id = (int)($_POST['role_id'] ?? 0);

            if ($nombre === '' || $apellido === '' || $email === '' || $password === '' || $role_id === 0) {
                throw new Exception("Faltan campos obligatorios.");
            }

            // Insert en usuarios (incluye telefono si existe)
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (nombre, apellido, docusu, email, password, role_id, telefono)
                VALUES (:nombre, :apellido, :docusu, :email, :password, :role_id, :telefono)
            ");
            $stmt->execute([
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
                ':docusu'   => $_POST['docusu'] ?? null,
                ':email'    => $email,
                ':password' => password_hash($password, PASSWORD_DEFAULT),
                ':role_id'  => $role_id,
                ':telefono' => trim($_POST['telefono'] ?? '')
            ]);
            $usuarioId = (int)$this->pdo->lastInsertId();

            // Insert en emp_det (si corresponde)
            $stmt = $this->pdo->prepare("
                INSERT INTO emp_det (usuario_id, especialidad, salario, fecha_ingreso, activo)
                VALUES (:usuario_id, :especialidad, :salario, :fecha_ingreso, :activo)
            ");
            $stmt->execute([
                ':usuario_id'    => $usuarioId,
                ':especialidad'  => $_POST['especialidad'] ?? null,
                ':salario'       => $_POST['salario'] !== '' ? $_POST['salario'] : null,
                ':fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d'),
                ':activo'        => isset($_POST['activo']) ? 1 : 0
            ]);

            $this->pdo->commit();
            header("Location: /vetsmart/admin/empleados");
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            // En dev muestra el error; en produccion deberas loguear y mostrar mensaje generico
            die("Error guardando empleado: " . $e->getMessage());
        }
    }

    /** EDITAR EMPLEADO (FORM) */
    public function editarEmpleado($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.nombre, u.apellido, u.docusu, u.email, u.telefono, u.role_id,
                   e.especialidad, e.salario, e.fecha_ingreso, e.activo
            FROM usuarios u
            LEFT JOIN emp_det e ON u.id = e.usuario_id
            WHERE u.id = :id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id]);
        $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$empleado) {
            http_response_code(404);
            echo "Empleado no encontrado";
            exit;
        }

        $roles = $this->pdo->query("SELECT id, nombre FROM roles WHERE id NOT IN (1,6) ORDER BY nombre")
                           ->fetchAll(PDO::FETCH_ASSOC);

        $this->view("admin/empleados/editar", [
            'empleado' => $empleado,
            'roles'    => $roles
        ], "main_admin");
    }

    /** ACTUALIZAR EMPLEADO */
    public function actualizarEmpleado($id)
    {
        $this->pdo->beginTransaction();
        try {
            $stmt = $this->pdo->prepare("
                UPDATE usuarios
                SET nombre = :nombre,
                    apellido = :apellido,
                    docusu = :docusu,
                    email = :email,
                    role_id = :role_id,
                    telefono = :telefono
                WHERE id = :id
            ");
            $stmt->execute([
                ':nombre'   => $_POST['nombre'] ?? '',
                ':apellido' => $_POST['apellido'] ?? '',
                ':docusu'   => $_POST['docusu'] ?? null,
                ':email'    => $_POST['email'] ?? '',
                ':role_id'  => (int)($_POST['role_id'] ?? 0),
                ':telefono' => trim($_POST['telefono'] ?? ''),
                ':id'       => $id
            ]);

            // Asegurarse de que haya fila en emp_det; si no, insertarla
            $check = $this->pdo->prepare("SELECT COUNT(*) FROM emp_det WHERE usuario_id = :id");
            $check->execute([':id' => $id]);
            $has = (int)$check->fetchColumn();

            if ($has === 0) {
                $ins = $this->pdo->prepare("
                    INSERT INTO emp_det (usuario_id, especialidad, salario, fecha_ingreso, activo)
                    VALUES (:usuario_id, :especialidad, :salario, :fecha_ingreso, :activo)
                ");
                $ins->execute([
                    ':usuario_id'    => $id,
                    ':especialidad'  => $_POST['especialidad'] ?? null,
                    ':salario'       => $_POST['salario'] !== '' ? $_POST['salario'] : null,
                    ':fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d'),
                    ':activo'        => isset($_POST['activo']) ? 1 : 0
                ]);
            } else {
                $upd = $this->pdo->prepare("
                    UPDATE emp_det
                    SET especialidad = :especialidad,
                        salario = :salario,
                        fecha_ingreso = :fecha_ingreso,
                        activo = :activo
                    WHERE usuario_id = :id
                ");
                $upd->execute([
                    ':especialidad'  => $_POST['especialidad'] ?? null,
                    ':salario'       => $_POST['salario'] !== '' ? $_POST['salario'] : null,
                    ':fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d'),
                    ':activo'        => isset($_POST['activo']) ? 1 : 0,
                    ':id'            => $id
                ]);
            }

            $this->pdo->commit();
            header("Location: /vetsmart/admin/empleados");
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error actualizando empleado: " . $e->getMessage());
        }
    }

    /** ELIMINAR EMPLEADO */
    public function eliminarEmpleado($id)
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare("DELETE FROM emp_det WHERE usuario_id = :id")->execute([':id' => $id]);
            $this->pdo->prepare("DELETE FROM usuarios WHERE id = :id")->execute([':id' => $id]);
            $this->pdo->commit();
            header("Location: /vetsmart/admin/empleados");
            exit;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            die("Error eliminando empleado: " . $e->getMessage());
        }
    }

    /* ==================================================
 *  GUARDAR HORARIO SEMANAL
 * ================================================== */
public function guardarHorarioSemana()
{
    try {
        if (empty($_POST['empleado_id']) || $_POST['dia'] === '' || empty($_POST['hora_inicio']) || empty($_POST['hora_fin'])) {
            throw new Exception("Faltan campos obligatorios para horario semanal.");
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO horarios_semana (empleado_id, dia, hora_inicio, hora_fin)
            VALUES (:empleado_id, :dia, :hora_inicio, :hora_fin)
        ");
        $stmt->execute([
            ':empleado_id' => (int)$_POST['empleado_id'],
            ':dia'         => $_POST['dia'],
            ':hora_inicio' => $_POST['hora_inicio'],
            ':hora_fin'    => $_POST['hora_fin'],
        ]);

        $_SESSION['flash_success'] = "Horario semanal guardado correctamente.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error guardando horario: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

/* ==================================================
 *  GUARDAR TURNO EXTRA
 * ================================================== */
public function guardarTurno()
{
    try {
        if (empty($_POST['empleado_id']) || empty($_POST['tipo']) || empty($_POST['inicio']) || empty($_POST['fin'])) {
            throw new Exception("Faltan campos obligatorios para turno.");
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO turnos_empleado (empleado_id, inicio, fin, tipo, notas, creado_por)
            VALUES (:empleado_id, :inicio, :fin, :tipo, :notas, :creado_por)
        ");
        $stmt->execute([
            ':empleado_id' => (int)$_POST['empleado_id'],
            ':inicio'      => $_POST['inicio'],
            ':fin'         => $_POST['fin'],
            ':tipo'        => $_POST['tipo'],
            ':notas'       => $_POST['notas'] ?? null,
            ':creado_por'  => $_SESSION['user']['id'] ?? null,
        ]);

        $_SESSION['flash_success'] = "Turno guardado correctamente.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error guardando turno: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

/* ==================================================
 *  GUARDAR SOLICITUD
 * ================================================== */
public function guardarSolicitud()
{
    try {
        if (empty($_POST['usuario_id']) || empty($_POST['tipo']) || empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
            throw new Exception("Faltan campos obligatorios para solicitud.");
        }

        $stmt = $this->pdo->prepare("
            INSERT INTO solicitudes (usuario_id, tipo, fecha_inicio, fecha_fin, motivo)
            VALUES (:usuario_id, :tipo, :fecha_inicio, :fecha_fin, :motivo)
        ");
        $stmt->execute([
            ':usuario_id'   => (int)$_POST['usuario_id'],
            ':tipo'         => $_POST['tipo'],
            ':fecha_inicio' => $_POST['fecha_inicio'],
            ':fecha_fin'    => $_POST['fecha_fin'],
            ':motivo'       => $_POST['motivo'] ?? null,
        ]);

        $_SESSION['flash_success'] = "Solicitud registrada correctamente.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error guardando solicitud: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

/* ==================================================
 *  LISTAR HORARIOS
 * ================================================== */
public function horariosIndex()
{
    // Horarios semanales
    $stmt = $this->pdo->query("
        SELECT hs.id, hs.empleado_id, hs.dia, hs.hora_inicio, hs.hora_fin,
               CONCAT(u.nombre, ' ', u.apellido) AS empleado
        FROM horarios_semana hs
        INNER JOIN usuarios u ON hs.empleado_id = u.id
        ORDER BY u.nombre, hs.dia
    ");
    $horariosSemana = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Turnos adicionales
    $stmt = $this->pdo->query("
        SELECT te.id, te.empleado_id, te.inicio, te.fin, te.tipo, te.notas,
               CONCAT(u.nombre, ' ', u.apellido) AS empleado
        FROM turnos_empleado te
        INNER JOIN usuarios u ON te.empleado_id = u.id
        ORDER BY te.inicio DESC
    ");
    $turnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Solicitudes
    $stmt = $this->pdo->query("
        SELECT s.id, s.usuario_id, s.tipo, s.fecha_inicio, s.fecha_fin, s.motivo,
               CONCAT(u.nombre, ' ', u.apellido) AS empleado
        FROM solicitudes s
        INNER JOIN usuarios u ON s.usuario_id = u.id
        ORDER BY s.fecha_inicio DESC
    ");
    $solicitudes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Empleados disponibles para asignar horarios
    $stmt = $this->pdo->query("
        SELECT id, nombre, apellido
        FROM usuarios
        WHERE role_id NOT IN (1,6) -- sin super_admin ni cliente
        ORDER BY nombre
    ");
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Render vista
    $this->view("admin/horarios/index", [
        'horariosSemana' => $horariosSemana,
        'turnos'         => $turnos,
        'solicitudes'    => $solicitudes,
        'empleados'      => $empleados,
    ], "main_admin");
}
public function editarHorarioSemana($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM horarios_semana WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $horario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$horario) {
        $_SESSION['flash_error'] = "Horario no encontrado.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    }

    $empleados = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id NOT IN (1,6)")->fetchAll(PDO::FETCH_ASSOC);

    $this->view("admin/horarios/editarSemana", [
        'horario' => $horario,
        'empleados' => $empleados
    ], "main_admin");
}

public function actualizarHorarioSemana($id) {
    try {
        if (empty($_POST['empleado_id']) || empty($_POST['dia']) || empty($_POST['hora_inicio']) || empty($_POST['hora_fin'])) {
            throw new Exception("Todos los campos son obligatorios.");
        }

        $stmt = $this->pdo->prepare("
            UPDATE horarios_semana
            SET empleado_id=:empleado_id, dia=:dia, hora_inicio=:hora_inicio, hora_fin=:hora_fin
            WHERE id=:id
        ");

        $stmt->bindValue(':empleado_id', (int)$_POST['empleado_id'], PDO::PARAM_INT);
        $stmt->bindValue(':dia', $_POST['dia']);
        $stmt->bindValue(':hora_inicio', $_POST['hora_inicio']);
        $stmt->bindValue(':hora_fin', $_POST['hora_fin']);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['flash_success'] = "Horario actualizado correctamente.";
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al actualizar el horario: " . $e->getMessage();
    }

    header("Location: /vetsmart/admin/horarios");
    exit;
}

public function eliminarHorarioSemana($id) {
    $stmt = $this->pdo->prepare("DELETE FROM horarios_semana WHERE id = :id");
    $stmt->execute([':id' => $id]);

    $_SESSION['flash_success'] = "Horario eliminado.";
    header("Location: /vetsmart/admin/horarios");
    exit;
}
public function editarTurno($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM turnos_empleado WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $turno = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$turno) {
        $_SESSION['flash_error'] = "Turno no encontrado.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    }

    $empleados = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id NOT IN (1,6)")->fetchAll(PDO::FETCH_ASSOC);

    $this->view("admin/horarios/editarTurno", [
        'turno' => $turno,
        'empleados' => $empleados
    ], "main_admin");
}

public function actualizarTurno($id)
{
    try {
        $stmt = $this->pdo->prepare("
            UPDATE turnos_empleado
            SET empleado_id = :empleado_id, inicio = :inicio, fin = :fin, tipo = :tipo, notas = :notas
            WHERE id = :id
        ");
        $stmt->bindValue(':empleado_id', (int)$_POST['empleado_id'], PDO::PARAM_INT);
        $stmt->bindValue(':inicio', $_POST['inicio']);
        $stmt->bindValue(':fin', $_POST['fin']);
        $stmt->bindValue(':tipo', $_POST['tipo']);
        $stmt->bindValue(':notas', $_POST['notas'] ?? null);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['flash_success'] = "Turno actualizado.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al actualizar turno: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

public function eliminarTurno($id)
{
    try {
        $stmt = $this->pdo->prepare("DELETE FROM turnos_empleado WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['flash_success'] = "Turno eliminado.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al eliminar turno: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

public function editarSolicitud($id) {
    $stmt = $this->pdo->prepare("SELECT * FROM solicitudes WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $solicitud = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$solicitud) {
        $_SESSION['flash_error'] = "Solicitud no encontrada.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    }

    $empleados = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id NOT IN (1,6)")->fetchAll(PDO::FETCH_ASSOC);

    $this->view("admin/horarios/editarSolicitud", [
        'solicitud' => $solicitud,
        'empleados' => $empleados
    ], "main_admin");
}

public function actualizarSolicitud($id)
{
    try {
        $stmt = $this->pdo->prepare("
            UPDATE solicitudes
            SET usuario_id = :usuario_id, tipo = :tipo, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, motivo = :motivo
            WHERE id = :id
        ");
        $stmt->bindValue(':usuario_id', (int)$_POST['usuario_id'], PDO::PARAM_INT);
        $stmt->bindValue(':tipo', $_POST['tipo']);
        $stmt->bindValue(':fecha_inicio', $_POST['fecha_inicio']);
        $stmt->bindValue(':fecha_fin', $_POST['fecha_fin']);
        $stmt->bindValue(':motivo', $_POST['motivo'] ?? null);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['flash_success'] = "Solicitud actualizada.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al actualizar solicitud: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

public function eliminarSolicitud($id)
{
    try {
        $stmt = $this->pdo->prepare("DELETE FROM solicitudes WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['flash_success'] = "Solicitud eliminada.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al eliminar solicitud: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}


public function eliminarSemana($id)
{
    try {
        $stmt = $this->pdo->prepare("DELETE FROM horarios_semana WHERE id = :id");
        $stmt->execute([':id' => $id]);

        $_SESSION['flash_success'] = "Horario eliminado.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al eliminar horario: " . $e->getMessage();
        header("Location: /vetsmart/admin/horarios");
        exit;
    }
}

public function agenda()
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $user = $_SESSION['user'] ?? null;
    if (!$user) { header('Location: /vetsmart/login'); exit; }

    // filtros
    $desde = $_GET['desde'] ?? date('Y-m-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-t');
    $empleado_id = $_GET['empleado_id'] ?? null;
    $servicio_id = $_GET['servicio_id'] ?? null;
    $estado = $_GET['estado'] ?? null;

    // consulta citas
    $sql = "
        SELECT ci.id, DATE(ci.fecha) AS fecha, TIME(ci.fecha) AS hora, ci.estado, ci.notas,
               m.nombre AS mascota_nombre,
               CONCAT(u_cli.nombre,' ',u_cli.apellido) AS cliente_nombre,
               CONCAT(u_emp.nombre,' ',u_emp.apellido) AS empleado_nombre,
               s.nombre AS servicio_nombre
        FROM citas ci
        LEFT JOIN mascotas m ON ci.mascota_id = m.id
        LEFT JOIN usuarios u_cli ON ci.cliente_id = u_cli.id
        LEFT JOIN usuarios u_emp ON ci.empleado_id = u_emp.id
        LEFT JOIN servicios s ON ci.servicio_id = s.id
        WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
    ";
    $params = [':desde' => $desde, ':hasta' => $hasta];

    if ($empleado_id) {
        $sql .= " AND ci.empleado_id = :empleado_id";
        $params[':empleado_id'] = $empleado_id;
    }
    if ($servicio_id) {
        $sql .= " AND ci.servicio_id = :servicio_id";
        $params[':servicio_id'] = $servicio_id;
    }
    if ($estado) {
        $sql .= " AND ci.estado = :estado";
        $params[':estado'] = $estado;
    }

    $sql .= " ORDER BY ci.fecha ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // traer servicios y usuarios (para filtros)
    $servicios = $this->pdo->query("SELECT id, nombre, precio FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
    $usuarios = $this->pdo->query("
    SELECT u.id, u.nombre, u.apellido, u.role_id, r.nombre AS role_nombre
    FROM usuarios u
    LEFT JOIN roles r ON u.role_id = r.id
    WHERE u.role_id IN (3,4,5)
    ORDER BY u.nombre
")->fetchAll(PDO::FETCH_ASSOC);

    // layout segun rol
    $layout = ($user['role_id'] == 3) ? 'main_recepcionista' : 'main_admin';

    $this->view("admin/agenda", [
        'citas'     => $citas,
        'servicios' => $servicios,
        'usuarios'  => $usuarios
    ], $layout);
}


public function listarCitasJson()
{
    header('Content-Type: application/json; charset=utf-8');

    $start = $_GET['start'] ?? null;
    $end = $_GET['end'] ?? null;
    $servicio_id = $_GET['servicio_id'] ?? null;
    $empleado_id = $_GET['empleado_id'] ?? null;
    $estado = $_GET['estado'] ?? null;

    try {
        $sql = "
            SELECT ci.*,
                   COALESCE(ci.precio, s.precio, 0) AS precio_final,
                   m.nombre AS nombre_mascota, m.raza AS raza_mascota,
                   u_emp.id AS empleado_id, u_emp.nombre AS empleado_nombre, u_emp.apellido AS empleado_apellido, u_emp.role_id AS empleado_role,
                   u_cli.id AS cliente_id, u_cli.nombre AS cliente_nombre, u_cli.apellido AS cliente_apellido,
                   s.id AS servicio_id, s.nombre AS servicio_nombre
            FROM citas ci
            LEFT JOIN mascotas m ON ci.mascota_id = m.id
            LEFT JOIN servicios s ON ci.servicio_id = s.id
            LEFT JOIN usuarios u_emp ON ci.empleado_id = u_emp.id
            LEFT JOIN usuarios u_cli ON ci.cliente_id = u_cli.id
            WHERE 1=1
        ";

        $params = [];

        if ($start && $end) {
            $sql .= " AND DATE(ci.fecha) BETWEEN :start AND :end ";
            $params[':start'] = $start;
            $params[':end'] = $end;
        }

        if ($servicio_id) {
            $sql .= " AND ci.servicio_id = :servicio_id ";
            $params[':servicio_id'] = $servicio_id;
        }
        if ($empleado_id) {
            $sql .= " AND ci.empleado_id = :empleado_id ";
            $params[':empleado_id'] = $empleado_id;
        }
        if ($estado) {
            $sql .= " AND ci.estado = :estado ";
            $params[':estado'] = $estado;
        }

        $sql .= " ORDER BY ci.fecha ASC ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $out = array_map(function($r) {
            return [
                'id' => $r['id'],
                'title' => ($r['nombre_mascota'] ?? 'Cita') . (isset($r['servicio_nombre']) ? "a” {$r['servicio_nombre']}" : ''),
                'start' => $r['fecha'],
                'allDay' => false,
                'precio' => (float) $r['precio_final'],
                'mascota_id' => $r['mascota_id'] ?? null,
                'nombre_mascota' => $r['nombre_mascota'] ?? '',
                'raza_mascota' => $r['raza_mascota'] ?? '',
                'servicio_id' => $r['servicio_id'] ?? null,
                'servicio_nombre' => $r['servicio_nombre'] ?? null,
                'empleado_id' => $r['empleado_id'] ?? null,
                'empleado_nombre' => trim(($r['empleado_nombre'] ?? '') . ' ' . ($r['empleado_apellido'] ?? '')),
                'empleado_role' => $r['empleado_role'] ?? null,
                'cliente_id' => $r['cliente_id'] ?? null,
                'cliente_nombre' => trim(($r['cliente_nombre'] ?? '') . ' ' . ($r['cliente_apellido'] ?? '')),
                'estado' => $r['estado'] ?? 'programada',
                'notas' => $r['notas'] ?? null
            ];
        }, $rows);

        echo json_encode($out);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error DB', 'msg' => $e->getMessage()]);
        exit;
    }
}

// --------------------------------------------------
// Estadi­sticas entre dos fechas (JSON)
// GET: desde, hasta
public function estadisticasAgendaJson()
{
    header('Content-Type: application/json; charset=utf-8');
    $desde = $_GET['desde'] ?? null;
    $hasta = $_GET['hasta'] ?? null;
    if (!$desde || !$hasta) {
        http_response_code(400);
        echo json_encode(['error' => 'Parametros desde y hasta requeridos.']);
        exit;
    }

    try {
        // Totales
        $sqlTotals = "
            SELECT COUNT(*) AS total_citas, 
                COALESCE(SUM(s.precio),0) AS total_recaudo,
                COALESCE(AVG(s.precio),0) AS promedio_valor
            FROM citas ci
            LEFT JOIN servicios s ON ci.servicio_id = s.id
            WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
            AND ci.estado = 'completado'
        ";
        $stmt = $this->pdo->prepare($sqlTotals);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        $tot = $stmt->fetch(PDO::FETCH_ASSOC);

        // Top servicios
        $sqlTopServicios = "
            SELECT s.id, s.nombre, COUNT(*) AS veces, COALESCE(SUM(s.precio),0) AS total
            FROM citas ci
            LEFT JOIN servicios s ON ci.servicio_id = s.id
            WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
            AND ci.estado = 'completado'
            GROUP BY s.id, s.nombre
            ORDER BY veces DESC
            LIMIT 8
        ";
        $stmt = $this->pdo->prepare($sqlTopServicios);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        $topServicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Top razas
        $sqlTopRazas = "
            SELECT m.raza AS raza, COUNT(*) AS veces
            FROM citas ci
            LEFT JOIN mascotas m ON ci.mascota_id = m.id
            WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
            AND ci.estado = 'completado'
            GROUP BY m.raza
            ORDER BY veces DESC
            LIMIT 8
        ";
        $stmt = $this->pdo->prepare($sqlTopRazas);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        $topRazas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Por rol (4 veterinario, 3 recepcionista, 5 peluquero)
        $sqlByRole = "
            SELECT u.role_id, COUNT(*) AS cnt, COALESCE(SUM(s.precio),0) AS total
            FROM citas ci
            LEFT JOIN usuarios u ON ci.empleado_id = u.id
            LEFT JOIN servicios s ON ci.servicio_id = s.id
            WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
            AND ci.estado = 'completado'
            GROUP BY u.role_id
        ";
        $stmt = $this->pdo->prepare($sqlByRole);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        $byRoleRaw = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $roleMap = [4 => 'Veterinario', 3 => 'Recepcionista', 5 => 'Peluquero'];
        $byRole = [];
        foreach ($byRoleRaw as $r) {
            $rid = (int)$r['role_id'];
            $byRole[] = [
                'role_id' => $rid,
                'role_name' => $roleMap[$rid] ?? ("role_{$rid}"),
                'count' => (int)$r['cnt'],
                'revenue' => (float)$r['total']
            ];
        }

        // Por estado
        $sqlEstado = "
            SELECT estado, COUNT(*) AS cnt
            FROM citas
            WHERE DATE(fecha) BETWEEN :desde AND :hasta
            GROUP BY estado
        ";
        $stmt = $this->pdo->prepare($sqlEstado);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        $byEstado = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resp = [
            'totales' => [
                'total_citas' => (int)($tot['total_citas'] ?? 0),
                'total_recaudo' => (float)($tot['total_recaudo'] ?? 0),
                'promedio_valor' => (float)($tot['promedio_valor'] ?? 0)
            ],
            'top_servicios' => $topServicios,
            'top_razas' => $topRazas,
            'por_rol' => $byRole,
            'por_estado' => $byEstado
        ];

        echo json_encode($resp);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error DB', 'msg' => $e->getMessage()]);
        exit;
    }
}

public function exportarExcel()
{
    // Obtener filtros de la URL
    $desde = $_GET['desde'] ?? date('Y-m-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-t');
    $empleado_id = $_GET['empleado_id'] ?? null;
    $servicio_id = $_GET['servicio_id'] ?? null;
    $estado = $_GET['estado'] ?? null;

    // Construcción de la consulta
    $sql = "
        SELECT 
            c.id,
            DATE(c.fecha) AS fecha,
            TIME(c.fecha) AS hora,
            s.nombre AS servicio,
            m.nombre AS mascota,
            CONCAT(cli.nombre, ' ', cli.apellido) AS cliente,
            CONCAT(u.nombre, ' ', u.apellido) AS empleado,
            c.estado
        FROM citas c
        LEFT JOIN servicios s ON c.servicio_id = s.id
        LEFT JOIN mascotas m ON c.mascota_id = m.id
        LEFT JOIN usuarios cli ON c.cliente_id = cli.id
        LEFT JOIN usuarios u ON c.empleado_id = u.id
        WHERE DATE(c.fecha) BETWEEN :desde AND :hasta
    ";

    $params = [
        ':desde' => $desde,
        ':hasta' => $hasta
    ];

    if ($empleado_id) {
        $sql .= " AND c.empleado_id = :empleado_id";
        $params[':empleado_id'] = $empleado_id;
    }
    if ($servicio_id) {
        $sql .= " AND c.servicio_id = :servicio_id";
        $params[':servicio_id'] = $servicio_id;
    }
    if ($estado) {
        $sql .= " AND c.estado = :estado";
        $params[':estado'] = $estado;
    }

    $sql .= " ORDER BY c.fecha ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
    $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Agenda");

    // Encabezados
    $headers = ["ID", "Fecha", "Hora", "Servicio", "Mascota", "Cliente", "Empleado"];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col.'1', $h);
        $sheet->getStyle($col.'1')->getFont()->setBold(true);
        $sheet->getStyle($col.'1')->getFill()
              ->setFillType(Fill::FILL_SOLID)
              ->getStartColor()->setARGB('FFD9E1F2');
        $sheet->getColumnDimension($col)->setAutoSize(true);
        $col++;
    }

    // Datos
    $row = 2;
    foreach ($citas as $c) {
        $sheet->setCellValue('A'.$row, $c['id']);
        $sheet->setCellValue('B'.$row, $c['fecha']);
        $sheet->setCellValue('C'.$row, $c['hora']);
        $sheet->setCellValue('D'.$row, $c['servicio']);
        $sheet->setCellValue('E'.$row, $c['mascota']);
        $sheet->setCellValue('F'.$row, $c['cliente']);
        $sheet->setCellValue('G'.$row, $c['empleado']);
        $row++;
    }

    // Bordes
    $sheet->getStyle('A1:G'.($row-1))->getBorders()->getAllBorders()
          ->setBorderStyle(Border::BORDER_THIN);

    // Descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=agenda_{$desde}_{$hasta}.xlsx");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

public function soporte()
{
    require_once APP_ROOT . '/models/Ticket.php';
    $ticketModel = new Ticket($this->pdo);
    $admin_id = $_SESSION['user']['id'];
    $tickets = $ticketModel->getTicketsByAdminId($admin_id);
    $this->view('admin/soporte/index', ['tickets' => $tickets], 'main_admin');
}

public function verTicket($id)
{
    require_once APP_ROOT . '/models/Ticket.php';
    $ticketModel = new Ticket($this->pdo);
    $ticket = $ticketModel->getById((int)$id);

    if ($ticket && $ticket['usuario_id'] == $_SESSION['user']['id']) {
        $ticketModel->markAsSeenByAdmin($id);
        $mensajes = $ticketModel->getMessagesByTicketId($id);
        $this->view('admin/soporte/ver', ['ticket' => $ticket, 'mensajes' => $mensajes], 'main_admin');
    } else {
        header('Location: /vetsmart/admin/soporte');
        exit;
    }
}


/* ==================================================
 *  FINANZAS
 * ================================================== */
public function finanzasIndex()
{
    $desde = $_GET['desde'] ?? date('Y-m-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-t');

    $sql = "
        SELECT 
            ci.id,
            DATE(ci.fecha) AS fecha,
            ci.estado,
            s.nombre AS servicio,
            s.precio AS valor,
            CONCAT(u_cli.nombre,' ',u_cli.apellido) AS cliente,
            CONCAT(u_emp.nombre,' ',u_emp.apellido) AS empleado
        FROM citas ci
        LEFT JOIN servicios s ON ci.servicio_id = s.id
        LEFT JOIN usuarios u_cli ON ci.cliente_id = u_cli.id
        LEFT JOIN usuarios u_emp ON ci.empleado_id = u_emp.id
        WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
          AND ci.estado IN ('completado','confirmada','finalizada')  -- estados que generan ingresos
        ORDER BY ci.fecha DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $finanzas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Totales (sumar valores de los servicios)
    $total = array_sum(array_column($finanzas, 'valor'));

    $this->view("admin/finanzas/index", [
        'finanzas' => $finanzas,
        'total'    => $total,
        'desde'    => $desde,
        'hasta'    => $hasta
    ], "main_admin");
}


public function exportarFinanzasExcel()
{
    $desde = $_GET['desde'] ?? date('Y-m-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-t');

    $sql = "
        SELECT ci.id, DATE(ci.fecha) AS fecha, s.nombre AS servicio, 
            CONCAT(u_cli.nombre,' ',u_cli.apellido) AS cliente,
            COALESCE(ci.precio, s.precio, 0) AS valor
        FROM citas ci
        LEFT JOIN servicios s ON ci.servicio_id = s.id
        LEFT JOIN usuarios u_cli ON ci.cliente_id = u_cli.id
        WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
        AND ci.estado = 'completado'
        ORDER BY ci.fecha ASC
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Finanzas");

    $headers = ["ID","Fecha","Servicio","Cliente","Valor"];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col.'1', $h);
        $sheet->getStyle($col.'1')->getFont()->setBold(true);
        $col++;
    }

    $row=2;
    foreach($rows as $r){
        $sheet->setCellValue('A'.$row,$r['id']);
        $sheet->setCellValue('B'.$row,$r['fecha']);
        $sheet->setCellValue('C'.$row,$r['servicio']);
        $sheet->setCellValue('D'.$row,$r['cliente']);
        $sheet->setCellValue('E'.$row,$r['valor']);
        $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=finanzas_{$desde}_{$hasta}.xlsx");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

/* ==================================================
 *  REPORTES
 * ================================================== */
public function reportesIndex()
{
    $desde = $_GET['desde'] ?? date('Y-m-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-t');

    // Servicios mas solicitados
    $sqlTopServicios = "
        SELECT s.nombre, COUNT(*) AS cantidad
        FROM citas ci
        LEFT JOIN servicios s ON ci.servicio_id = s.id
        WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
        GROUP BY s.id
        ORDER BY cantidad DESC
        LIMIT 5
    ";
    $stmt = $this->pdo->prepare($sqlTopServicios);
    $stmt->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $topServicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Empleados con mas citas
    $sqlTopEmpleados = "
        SELECT CONCAT(u.nombre,' ',u.apellido) AS empleado, COUNT(*) AS cantidad
        FROM citas ci
        LEFT JOIN usuarios u ON ci.empleado_id = u.id
        WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
        GROUP BY u.id
        ORDER BY cantidad DESC
        LIMIT 5
    ";
    $stmt = $this->pdo->prepare($sqlTopEmpleados);
    $stmt->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $topEmpleados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->view("admin/reportes/index", [
        'topServicios' => $topServicios,
        'topEmpleados' => $topEmpleados,
        'desde' => $desde,
        'hasta' => $hasta
    ], "main_admin");
}

    public function reportesSoporte()
    {
        $this->view("admin/reportes/soporte", [], "main_admin");
    }

    public function guardarTicket()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'usuario_id'   => $_SESSION['user']['id'],
                'asunto'       => $_POST['asunto'],
                'descripcion'  => $_POST['descripcion'],
                'rol_problema' => $_POST['rol_problema'],
                'prioridad'    => $_POST['prioridad']
            ];

            $ticketModel = new Ticket($this->pdo);
            if ($ticketModel->create($data)) {
                header('Location: /vetsmart/admin/reportes');
            } else {
                // Handle error
                header('Location: /vetsmart/admin/reportesSoporte');
            }
        }
    }

public function exportarReportesExcel()
{
    $desde = $_GET['desde'] ?? date('Y-m-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-t');

    $sql = "
        SELECT ci.id, DATE(ci.fecha) AS fecha, s.nombre AS servicio,
               CONCAT(u_cli.nombre,' ',u_cli.apellido) AS cliente,
               CONCAT(u_emp.nombre,' ',u_emp.apellido) AS empleado
        FROM citas ci
        LEFT JOIN servicios s ON ci.servicio_id = s.id
        LEFT JOIN usuarios u_cli ON ci.cliente_id = u_cli.id
        LEFT JOIN usuarios u_emp ON ci.empleado_id = u_emp.id
        WHERE DATE(ci.fecha) BETWEEN :desde AND :hasta
        ORDER BY ci.fecha ASC
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':desde'=>$desde, ':hasta'=>$hasta]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle("Reportes");

    $headers = ["ID","Fecha","Servicio","Cliente","Empleado"];
    $col = 'A';
    foreach ($headers as $h) {
        $sheet->setCellValue($col.'1',$h);
        $sheet->getStyle($col.'1')->getFont()->setBold(true);
        $col++;
    }

    $row=2;
    foreach($rows as $r){
        $sheet->setCellValue('A'.$row,$r['id']);
        $sheet->setCellValue('B'.$row,$r['fecha']);
        $sheet->setCellValue('C'.$row,$r['servicio']);
        $sheet->setCellValue('D'.$row,$r['cliente']);
        $sheet->setCellValue('E'.$row,$r['empleado']);
        $row++;
    }

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=reportes_{$desde}_{$hasta}.xlsx");
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

public function actualizarMascota(int $cliente_id, int $mascota_id)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: /vetsmart/admin/clientes/{$cliente_id}/mascotas/{$mascota_id}/editar");
        exit;
    }

    // Sanitizar / validar
    $nombre  = trim($_POST['nombre'] ?? '');
    $especie = trim($_POST['especie'] ?? null);
    $raza    = trim($_POST['raza'] ?? null);
    $edad    = $_POST['edad'] !== '' ? (int)$_POST['edad'] : null;
    $peso    = $_POST['peso'] !== '' ? (float)$_POST['peso'] : null;
    $notas   = trim($_POST['notas'] ?? null);

    if ($nombre === '') {
        $_SESSION['flash_error'] = 'El nombre es obligatorio.';
        header("Location: /vetsmart/admin/clientes/{$cliente_id}/mascotas/{$mascota_id}/editar");
        exit;
    }

    try {
        $stmt = $this->pdo->prepare("
            UPDATE mascotas
            SET nombre = :nombre,
                especie = :especie,
                raza = :raza,
                edad = :edad,
                peso = :peso,
                notas = :notas
            WHERE id = :id
              AND (cliente_id = :cliente_id OR dueno_id = :cliente_id OR owner_id = :cliente_id)
        ");
        $stmt->execute([
            ':nombre'     => $nombre,
            ':especie'    => $especie,
            ':raza'       => $raza,
            ':edad'       => $edad,
            ':peso'       => $peso,
            ':notas'      => $notas,
            ':id'         => $mascota_id,
            ':cliente_id' => $cliente_id
        ]);

        $_SESSION['flash_success'] = 'Mascota actualizada correctamente.';
        header("Location: /vetsmart/admin/clientes/{$cliente_id}");
        exit;
    } catch (PDOException $e) {
        error_log("actualizarMascota error: " . $e->getMessage());
        $_SESSION['flash_error'] = 'Error actualizando mascota. Revisa logs.';
        header("Location: /vetsmart/admin/clientes/{$cliente_id}/mascotas/{$mascota_id}/editar");
        exit;
    }
}

}
