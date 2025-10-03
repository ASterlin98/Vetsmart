<?php
// app/controllers/AdminController.php

declare(strict_types=1);

class AdminController extends Controller
{
    private $pdo;

    public function __construct($pdo)
    {
        parent::__construct($pdo);
        $this->pdo = $pdo;
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
            SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.role_id, r.nombre AS rol,
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
            // Validaciones mínimas (puedes expandir)
            $nombre = trim($_POST['nombre'] ?? '');
            $apellido = trim($_POST['apellido'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role_id = (int)($_POST['role_id'] ?? 0);

            if ($nombre === '' || $apellido === '' || $email === '' || $password === '' || $role_id === 0) {
                throw new Exception("Faltan campos obligatorios.");
            }

            // Insert en usuarios (incluye telefono si existe)
            $stmt = $this->pdo->prepare("
                INSERT INTO usuarios (nombre, apellido, email, password, role_id, telefono)
                VALUES (:nombre, :apellido, :email, :password, :role_id, :telefono)
            ");
            $stmt->execute([
                ':nombre'   => $nombre,
                ':apellido' => $apellido,
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
            // En dev muestra el error; en producción deberías loguear y mostrar mensaje genérico
            die("Error guardando empleado: " . $e->getMessage());
        }
    }

    /** EDITAR EMPLEADO (FORM) */
    public function editarEmpleado($id)
    {
        $stmt = $this->pdo->prepare("
            SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.role_id,
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
                    email = :email,
                    role_id = :role_id,
                    telefono = :telefono
                WHERE id = :id
            ");
            $stmt->execute([
                ':nombre'   => $_POST['nombre'] ?? '',
                ':apellido' => $_POST['apellido'] ?? '',
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
    $stmt = $this->pdo->prepare("UPDATE horarios_semana SET empleado_id=:empleado_id, dia=:dia, hora_inicio=:hora_inicio, hora_fin=:hora_fin WHERE id=:id");
    $stmt->execute([
        ':empleado_id' => $_POST['empleado_id'],
        ':dia' => $_POST['dia'],
        ':hora_inicio' => $_POST['hora_inicio'],
        ':hora_fin' => $_POST['hora_fin'],
        ':id' => $id
    ]);

    $_SESSION['flash_success'] = "Horario actualizado.";
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
            SET inicio = :inicio, fin = :fin, tipo = :tipo, notas = :notas
            WHERE id = :id
        ");
        $stmt->execute([
            ':inicio' => $_POST['inicio'],
            ':fin'    => $_POST['fin'],
            ':tipo'   => $_POST['tipo'],
            ':notas'  => $_POST['notas'] ?? null,
            ':id'     => $id
        ]);

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
            SET tipo = :tipo, fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, motivo = :motivo
            WHERE id = :id
        ");
        $stmt->execute([
            ':tipo'         => $_POST['tipo'],
            ':fecha_inicio' => $_POST['fecha_inicio'],
            ':fecha_fin'    => $_POST['fecha_fin'],
            ':motivo'       => $_POST['motivo'] ?? null,
            ':id'           => $id
        ]);

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

/* ==================================================
 *  ACTUALIZAR HORARIO SEMANAL
 * ================================================== */
public function actualizarSemana($id)
{
    try {
        $stmt = $this->pdo->prepare("
            UPDATE horarios_semana
            SET dia = :dia, hora_inicio = :hora_inicio, hora_fin = :hora_fin
            WHERE id = :id
        ");
        $stmt->execute([
            ':dia'         => $_POST['dia'],
            ':hora_inicio' => $_POST['hora_inicio'],
            ':hora_fin'    => $_POST['hora_fin'],
            ':id'          => $id
        ]);

        $_SESSION['flash_success'] = "Horario semanal actualizado.";
        header("Location: /vetsmart/admin/horarios");
        exit;
    } catch (Exception $e) {
        $_SESSION['flash_error'] = "Error al actualizar horario: " . $e->getMessage();
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

}
