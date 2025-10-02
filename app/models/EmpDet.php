<?php
// app/models/EmpDet.php
class EmpDet {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Obtener empleados (excepto super_admin y cliente)
    public function getAll() {
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, r.nombre AS rol, e.especialidad, e.salario, e.fecha_ingreso, e.activo
                FROM usuarios u
                JOIN roles r ON u.role_id = r.id
                LEFT JOIN emp_det e ON e.usuario_id = u.id
                WHERE u.role_id NOT IN (1,6)";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Crear empleado
    public function create($data) {
        $this->pdo->beginTransaction();
        try {
            // Crear usuario
            $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, apellido, email, telefono, role_id, password) 
                                         VALUES (:nombre, :apellido, :email, :telefono, :role_id, :password)");
            $stmt->execute([
                ':nombre' => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':email' => $data['email'],
                ':telefono' => $data['telefono'],
                ':role_id' => $data['role_id'],
                ':password' => password_hash($data['password'], PASSWORD_BCRYPT),
            ]);

            $usuario_id = $this->pdo->lastInsertId();

            // Crear detalles de empleado
            $stmt2 = $this->pdo->prepare("INSERT INTO emp_det (usuario_id, especialidad, salario, fecha_ingreso, activo) 
                                          VALUES (:usuario_id, :especialidad, :salario, :fecha_ingreso, :activo)");
            $stmt2->execute([
                ':usuario_id' => $usuario_id,
                ':especialidad' => $data['especialidad'] ?? null,
                ':salario' => $data['salario'] ?? null,
                ':fecha_ingreso' => $data['fecha_ingreso'] ?? date('Y-m-d'),
                ':activo' => $data['activo'] ?? 1,
            ]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    // Obtener roles válidos (excepto super_admin y cliente)
    public function getRoles() {
        $sql = "SELECT id, nombre FROM roles WHERE id NOT IN (1,6)";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
