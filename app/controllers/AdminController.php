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
}
