<?php
declare(strict_types=1);

class ClienteController
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function verificarSesion(): void
    {
        if (empty($_SESSION['user']) || (($_SESSION['user']['role_name'] ?? '') !== 'cliente' && (string)($_SESSION['user']['role'] ?? '') !== 'cliente')) {
            header('Location: /vetsmart/login');
            exit;
        }
    }

    private function renderView(string $view, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include APP_ROOT . '/views/' . $view . '.php';
        return ob_get_clean();
    }

    public function dashboard(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        $nombre = trim((string)($_SESSION['user']['nombre'] ?? ''));
        $apellido = trim((string)($_SESSION['user']['apellido'] ?? ''));

        $mascotasTotal = 0;
        $proximasCitas = 0;
        $citasPendientes = 0;
        $proximaCitaFecha = null;
        $consultasTotales = 0;
        $pagosRealizados = 0;
        $perfilCompleto = 0;
        $reportesDisponibles = 1;

        try {
            $st = $this->pdo->prepare('SELECT COUNT(*) FROM mascotas WHERE dueno_id = :id');
            $st->execute([':id' => $clienteId]);
            $mascotasTotal = (int)$st->fetchColumn();

            $st = $this->pdo->prepare('SELECT COUNT(*) FROM citas WHERE cliente_id = :id AND fecha >= NOW()');
            $st->execute([':id' => $clienteId]);
            $proximasCitas = (int)$st->fetchColumn();

            $st = $this->pdo->prepare("SELECT COUNT(*) FROM citas WHERE cliente_id = :id AND estado = 'pendiente'");
            $st->execute([':id' => $clienteId]);
            $citasPendientes = (int)$st->fetchColumn();

            $st = $this->pdo->prepare('SELECT fecha FROM citas WHERE cliente_id = :id AND fecha >= NOW() ORDER BY fecha ASC LIMIT 1');
            $st->execute([':id' => $clienteId]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            $proximaCitaFecha = $row['fecha'] ?? null;

            $st = $this->pdo->prepare('SELECT COUNT(*) FROM consultas c INNER JOIN mascotas m ON m.id = c.mascota_id WHERE m.dueno_id = :id');
            $st->execute([':id' => $clienteId]);
            $consultasTotales = (int)$st->fetchColumn();

            try {
                $st = $this->pdo->prepare("SELECT COUNT(*) FROM movimientos WHERE tipo='ingreso' AND (concepto LIKE :tag OR creado_por = :cid)");
                $st->execute([':tag' => '%cliente:' . $clienteId . '%', ':cid' => $clienteId]);
                $pagosRealizados = (int)$st->fetchColumn();
            } catch (Throwable $e) { $pagosRealizados = 0; }

            // Perfil completo: nombre, apellido, email, telefono, direccion (5 campos)
            $filled = 0; $total = 5;
            $st = $this->pdo->prepare('SELECT u.nombre,u.apellido,u.email,u.telefono,u.direccion, cd.telefono t2, cd.direccion d2 FROM usuarios u LEFT JOIN cliente_detalles cd ON cd.idusu=u.id WHERE u.id = :id');
            $st->execute([':id'=>$clienteId]);
            $u = $st->fetch(PDO::FETCH_ASSOC) ?: [];
            if (!empty($u['nombre'])) $filled++;
            if (!empty($u['apellido'])) $filled++;
            if (!empty($u['email'])) $filled++;
            $tel = $u['telefono'] ?? $u['t2'] ?? '';
            if (!empty($tel)) $filled++;
            $dir = $u['direccion'] ?? $u['d2'] ?? '';
            if (!empty($dir)) $filled++;
            $perfilCompleto = (int)round(($filled / $total) * 100);
        } catch (Throwable $e) {
            // leave defaults
        }

        $content = $this->renderView('cliente/dashboard', [
            'nombre' => $nombre,
            'apellido' => $apellido,
            'mascotasTotal' => $mascotasTotal,
            'proximasCitas' => $proximasCitas,
            'citasPendientes' => $citasPendientes,
            'proximaCitaFecha' => $proximaCitaFecha,
            'consultasTotales' => $consultasTotales,
            'pagosRealizados' => $pagosRealizados,
            'perfilCompleto' => $perfilCompleto,
            'reportesDisponibles' => $reportesDisponibles,
        ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    public function perfil(): void
    {
        $this->verificarSesion();
        require_once APP_ROOT . '/models/Cliente.php';
        $clienteModel = new Cliente($this->pdo);
        $id = (int)($_SESSION['user']['id'] ?? 0);
        $cliente = $clienteModel->getByIdWithDetails($id);
        $mensaje = $_SESSION['mensaje'] ?? null;
        unset($_SESSION['mensaje']);
        $content = $this->renderView('cliente/perfil', [
            'cliente' => $cliente,
            'mensaje' => $mensaje,
        ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    public function actualizarPerfil(): void
    {
        $this->verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/cliente/perfil');
            exit;
        }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'SesiÃƒÂ³n expirada. Intenta nuevamente.'];
            header('Location: /vetsmart/cliente/perfil');
            exit;
        }

        $id = (int)($_SESSION['user']['id'] ?? 0);
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $apellido = trim((string)($_POST['apellido'] ?? ''));
        $email = trim((string)($_POST['email'] ?? ''));
        $telefono = trim((string)($_POST['telefono'] ?? ''));
        $direccion = trim((string)($_POST['direccion'] ?? ''));
        $password = (string)($_POST['password'] ?? '');

        require_once APP_ROOT . '/models/Cliente.php';
        $clienteModel = new Cliente($this->pdo);

        // Validar duplicidad de email en otro usuario
        try {
            $chk = $this->pdo->prepare('SELECT id FROM usuarios WHERE email = :e AND id <> :id LIMIT 1');
            $chk->execute([':e' => $email, ':id' => $id]);
            if ($chk->fetch()) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'El correo ya estÃƒÂ¡ registrado por otro usuario.'];
                header('Location: /vetsmart/cliente/perfil');
                exit;
            }
        } catch (Throwable $e) {
            // si falla la validaciÃƒÂ³n, continuar pero registrar log
            error_log('Cliente perfil: validaciÃƒÂ³n email duplicado fallÃƒÂ³: ' . $e->getMessage());
        }

        // Manejo de imagen (validaciÃƒÂ³n tipo, tamaÃƒÂ±o y dimensiones) + limpieza de anterior
        $prevFoto = null; $fotoNombre = null;
        // Obtener foto anterior
        try {
            $pf = $this->pdo->prepare('SELECT foto FROM perfil WHERE usuario_id = :id');
            $pf->execute([':id'=>$id]);
            $row = $pf->fetch(PDO::FETCH_ASSOC);
            $prevFoto = $row['foto'] ?? null;
        } catch (Throwable $e) { /* ignore */ }

        if (!empty($_FILES['foto']['name'] ?? '')) {
            $maxSize = 2 * 1024 * 1024; // 2MB
            $tmp = (string)($_FILES['foto']['tmp_name'] ?? '');
            if (($_FILES['foto']['size'] ?? 0) > $maxSize) {
                $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'La imagen excede 2MB.'];
                header('Location: /vetsmart/cliente/perfil');
                exit;
            }
            $info = @getimagesize($tmp);
            if ($info === false) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Archivo de imagen invÃƒÂ¡lido.'];
                header('Location: /vetsmart/cliente/perfil'); exit;
            }
            $mime = (string)($info['mime'] ?? '');
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Formato no permitido. Usa JPG o PNG.'];
                header('Location: /vetsmart/cliente/perfil'); exit;
            }
            $w = (int)($info[0] ?? 0); $h = (int)($info[1] ?? 0);
            if ($w > 2000 || $h > 2000) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'La imagen supera 2000x2000 pÃƒÂ­xeles.'];
                header('Location: /vetsmart/cliente/perfil'); exit;
            }
            $ext = strtolower((string)pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $uploadDir = APP_ROOT . '/public/assets/uploads/clientes';
            if (!is_dir($uploadDir)) {@mkdir($uploadDir, 0777, true);}            
            $fotoNombre = 'cliente_' . $id . '_' . time() . '.' . $ext;
            $dest = $uploadDir . '/' . $fotoNombre;
            @move_uploaded_file($tmp, $dest);
        }

        try {
            $this->pdo->beginTransaction();
            $clienteModel->updatePerfilBasico($id, [
                'nombre' => $nombre,
                'apellido' => $apellido,
                'email' => $email,
                'telefono' => $telefono,
                'direccion' => $direccion,
            ]);
            if (!empty($password)) {
                $clienteModel->updatePassword($id, password_hash($password, PASSWORD_BCRYPT));
            }
            if ($fotoNombre) {
                $clienteModel->updateFotoPerfil($id, $fotoNombre);
            }
            $this->pdo->commit();
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Perfil actualizado correctamente.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {$this->pdo->rollBack();}
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error al actualizar: ' . $e->getMessage()];
        }
        // Limpiar archivo anterior si se subiÃƒÂ³ uno nuevo y es distinto
        if (!empty($fotoNombre) && !empty($prevFoto) && $prevFoto !== $fotoNombre) {
            $old = APP_ROOT . '/public/assets/uploads/clientes/' . basename((string)$prevFoto);
            if (@is_file($old)) { @unlink($old); }
        }
        header('Location: /vetsmart/cliente/perfil');
        exit;
    }

    public function mascotas(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        require_once APP_ROOT . '/models/Mascota.php';
        $mascotaModel = new Mascota($this->pdo);
        $mascotas = $mascotaModel->getByDueno($clienteId);
        $mensaje = $_SESSION['mensaje'] ?? null; unset($_SESSION['mensaje']);
        $content = $this->renderView('cliente/mascotas', [
            'mascotas' => $mascotas,
            'mensaje' => $mensaje,
        ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    public function guardarMascota(): void
    {
        $this->verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /vetsmart/cliente/mascotas'); exit;
        }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'SesiÃƒÂ³n expirada.'];
            header('Location: /vetsmart/cliente/mascotas'); exit;
        }
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $especie = trim((string)($_POST['especie'] ?? ''));
        $raza = trim((string)($_POST['raza'] ?? ''));
        $edad = $_POST['edad'] !== '' ? (int)$_POST['edad'] : null;
        $sexo = $_POST['sexo'] ?? null;
        $fotoNombre = null;
        if (!empty($_FILES['foto']['name'] ?? '')) {
            $maxSize = 2 * 1024 * 1024; // 2MB
            $tmp = (string)($_FILES['foto']['tmp_name'] ?? '');
            if (($_FILES['foto']['size'] ?? 0) > $maxSize) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'La imagen excede 2MB.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $info = @getimagesize($tmp);
            if ($info === false) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Archivo de imagen invÃƒÂ¡lido.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $mime = (string)($info['mime'] ?? '');
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Formato no permitido. Usa JPG o PNG.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $w = (int)($info[0] ?? 0); $h = (int)($info[1] ?? 0);
            if ($w > 2000 || $h > 2000) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'La imagen supera 2000x2000 pÃƒÂ­xeles.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $ext = strtolower((string)pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $dir = APP_ROOT . '/public/assets/uploads/mascotas';
            if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
            $fotoNombre = 'mascota_' . $clienteId . '_' . time() . '.' . $ext;
            @move_uploaded_file($tmp, $dir . '/' . $fotoNombre);
        }
        require_once APP_ROOT . '/models/Mascota.php';
        $mascotaModel = new Mascota($this->pdo);
        try {
            $this->pdo->beginTransaction();
            $stmt = $this->pdo->prepare("INSERT INTO mascotas (dueno_id, nombre, especie, raza, edad, sexo, foto, creado_en) VALUES (:d,:n,:e,:r,:ed,:s,:f, NOW())");
            $stmt->execute([
                ':d' => $clienteId,
                ':n' => $nombre,
                ':e' => $especie ?: null,
                ':r' => $raza ?: null,
                ':ed' => $edad,
                ':s' => $sexo ?: null,
                ':f' => $fotoNombre,
            ]);
            $this->pdo->commit();
            $_SESSION['mensaje'] = ['tipo' => 'success', 'texto' => 'Mascota registrada.'];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {$this->pdo->rollBack();}
            $_SESSION['mensaje'] = ['tipo' => 'danger', 'texto' => 'Error al registrar mascota.'];
        }
        header('Location: /vetsmart/cliente/mascotas');
        exit;
    }

    public function editarMascota(): void
    {
        $this->verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /vetsmart/cliente/mascotas'); exit; }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) { $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'SesiÃƒÂ³n expirada.']; header('Location: /vetsmart/cliente/mascotas'); exit; }
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim((string)($_POST['nombre'] ?? ''));
        $especie = trim((string)($_POST['especie'] ?? ''));
        $raza = trim((string)($_POST['raza'] ?? ''));
        $edad = $_POST['edad'] !== '' ? (int)$_POST['edad'] : null;
        $sexo = $_POST['sexo'] ?? null;
        // Verificar pertenencia
        $owner = $this->pdo->prepare('SELECT dueno_id, foto FROM mascotas WHERE id = :id');
        $owner->execute([':id' => $id]);
        $m = $owner->fetch(PDO::FETCH_ASSOC);
        if (!$m || (int)$m['dueno_id'] !== $clienteId) {
            $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Mascota no encontrada.'];
            header('Location: /vetsmart/cliente/mascotas'); exit;
        }
        $fotoNombre = $m['foto'] ?? null;
        if (!empty($_FILES['foto']['name'] ?? '')) {
            $maxSize = 2 * 1024 * 1024; // 2MB
            $tmp = (string)($_FILES['foto']['tmp_name'] ?? '');
            if (($_FILES['foto']['size'] ?? 0) > $maxSize) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'La imagen excede 2MB.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $info = @getimagesize($tmp);
            if ($info === false) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Archivo de imagen invÃƒÂ¡lido.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $mime = (string)($info['mime'] ?? '');
            if (!in_array($mime, ['image/jpeg','image/png'], true)) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Formato no permitido. Usa JPG o PNG.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $w = (int)($info[0] ?? 0); $h = (int)($info[1] ?? 0);
            if ($w > 2000 || $h > 2000) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'La imagen supera 2000x2000 pÃƒÂ­xeles.'];
                header('Location: /vetsmart/cliente/mascotas'); exit;
            }
            $ext = strtolower((string)pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            $dir = APP_ROOT . '/public/assets/uploads/mascotas';
            if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
            $newFoto = 'mascota_' . $clienteId . '_' . time() . '.' . $ext;
            if (@move_uploaded_file($tmp, $dir . '/' . $newFoto)) {
                $fotoNombre = $newFoto;
            }
        }
        $upd = $this->pdo->prepare('UPDATE mascotas SET nombre=:n, especie=:e, raza=:r, edad=:ed, sexo=:s, foto=:f WHERE id=:id AND dueno_id=:d');
        $ok = $upd->execute([':n'=>$nombre, ':e'=>$especie?:null, ':r'=>$raza?:null, ':ed'=>$edad, ':s'=>$sexo?:null, ':f'=>$fotoNombre, ':id'=>$id, ':d'=>$clienteId]);
        // Limpiar foto anterior si se actualizÃƒÂ³
        if ($ok && !empty($fotoNombre) && !empty($m['foto']) && $m['foto'] !== $fotoNombre) {
            $old = APP_ROOT . '/public/assets/uploads/mascotas/' . basename((string)$m['foto']);
            if (@is_file($old)) { @unlink($old); }
        }
        $_SESSION['mensaje'] = $ok ? ['tipo'=>'success','texto'=>'Mascota actualizada.'] : ['tipo'=>'danger','texto'=>'No se pudo actualizar.'];
        header('Location: /vetsmart/cliente/mascotas');
        exit;
    }

    public function eliminarMascota(): void
    {
        $this->verificarSesion();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: /vetsmart/cliente/mascotas'); exit; }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) { $_SESSION['mensaje']=['tipo'=>'danger','texto'=>'SesiÃƒÂ³n expirada.']; header('Location: /vetsmart/cliente/mascotas'); exit; }
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        $del = $this->pdo->prepare('DELETE FROM mascotas WHERE id = :id AND dueno_id = :d');
        $ok = $del->execute([':id'=>$id, ':d'=>$clienteId]);
        $_SESSION['mensaje'] = $ok ? ['tipo'=>'success','texto'=>'Mascota eliminada.'] : ['tipo'=>'danger','texto'=>'No se pudo eliminar.'];
        header('Location: /vetsmart/cliente/mascotas');
        exit;
    }

    public function citas(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
            $token = $_POST['_csrf'] ?? '';
            if (!CSRF::validate($token)) {
                $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'SesiÃƒÂ³n expirada.'];
                header('Location: /vetsmart/cliente/citas'); exit;
            }
            $accion = $_POST['accion'] ?? '';
            if ($accion === 'cancelar') {
                $citaId = (int)($_POST['id'] ?? 0);
                $st = $this->pdo->prepare("UPDATE citas SET estado='cancelada' WHERE id=:id AND cliente_id=:cid AND estado='pendiente'");
                $ok = $st->execute([':id'=>$citaId, ':cid'=>$clienteId]);
                $_SESSION['mensaje'] = $ok ? ['tipo'=>'success','texto'=>'Cita cancelada.'] : ['tipo'=>'danger','texto'=>'No se pudo cancelar la cita.'];
                header('Location: /vetsmart/cliente/citas'); exit;
            }
        }

        $sql = "SELECT c.*, m.nombre AS mascota, s.nombre AS servicio,
                       CONCAT(v.nombre,' ',v.apellido) AS veterinario
                FROM citas c
                LEFT JOIN mascotas m ON m.id = c.mascota_id
                LEFT JOIN servicios s ON s.id = c.servicio_id
                LEFT JOIN usuarios v ON v.id = c.empleado_id
                WHERE c.cliente_id = :cid
                ORDER BY c.fecha DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute([':cid'=>$clienteId]);
        $citas = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $resumen = ['pendiente'=>0,'confirmada'=>0,'completada'=>0,'cancelada'=>0,'no_show'=>0];
        try {
            $q = $this->pdo->prepare('SELECT estado, COUNT(*) total FROM citas WHERE cliente_id = :id GROUP BY estado');
            $q->execute([':id'=>$clienteId]);
            foreach ($q->fetchAll(PDO::FETCH_ASSOC) as $r) {
                $k = strtolower((string)($r['estado'] ?? ''));
                if (isset($resumen[$k])) { $resumen[$k] = (int)$r['total']; }
            }
        } catch (Throwable $e) { /* ignore */ }

        $mensaje = $_SESSION['mensaje'] ?? null; unset($_SESSION['mensaje']);
        $content = $this->renderView('cliente/citas', [ 'citas'=>$citas, 'mensaje'=>$mensaje, 'resumen'=>$resumen ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    // Formulario para que el cliente agende una cita nueva
    public function agendar(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        // Cargar mascotas del cliente y servicios disponibles; empleados opcional
        require_once APP_ROOT . '/models/Mascota.php';
        require_once APP_ROOT . '/models/Servicio.php';
        $mascotaModel = new Mascota($this->pdo);
        $servicioModel = new Servicio($this->pdo);
        $mascotas = $mascotaModel->getByDueno($clienteId);
        $servicios = $servicioModel->getAll();
        $empleados = [];
        try {
            // Mostrar solo roles operativos correctos: 4 = veterinario, 5 = peluquero
            $q = $this->pdo->query("SELECT id, nombre, apellido FROM usuarios WHERE role_id IN (4,5) ORDER BY nombre");
            $empleados = $q->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) {}
        $content = $this->renderView('cliente/citas_agendar', [ 'mascotas'=>$mascotas, 'servicios'=>$servicios, 'empleados'=>$empleados ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    // Guardar cita agendada por el cliente
    public function guardarCita(): void
    {
        $this->verificarSesion();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { header('Location: /vetsmart/cliente/citas/agendar'); exit; }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) {
            $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'SesiÃƒÂ³n expirada.'];
            header('Location: /vetsmart/cliente/citas/agendar'); exit;
        }
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        $mascotaId = (int)($_POST['mascota_id'] ?? 0);
        $servicioId = (int)($_POST['servicio_id'] ?? 0);
        $empleadoId = $_POST['empleado_id'] !== '' ? (int)$_POST['empleado_id'] : null;
        $fecha = trim((string)($_POST['fecha'] ?? ''));
        $notas = trim((string)($_POST['notas'] ?? ''));

        // Validaciones bÃƒÂ¡sicas
        if ($mascotaId <= 0 || $servicioId <= 0 || $fecha === '') {
            $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Completa todos los campos requeridos.'];
            header('Location: /vetsmart/cliente/citas/agendar'); exit;
        }
        // Verificar pertenencia de mascota
        $stm = $this->pdo->prepare('SELECT COUNT(*) FROM mascotas WHERE id = :m AND dueno_id = :d');
        $stm->execute([':m'=>$mascotaId, ':d'=>$clienteId]);
        if ((int)$stm->fetchColumn() === 0) {
            $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Mascota invÃƒÂ¡lida.'];
            header('Location: /vetsmart/cliente/citas/agendar'); exit;
        }
        // Anti-duplicados: misma fecha y hora para el mismo empleado o la misma mascota
        $check = $this->pdo->prepare(
            "SELECT COUNT(*) FROM citas 
             WHERE DATE(fecha)=DATE(:f1) AND TIME(fecha)=TIME(:f2)
               AND (mascota_id = :mid OR (empleado_id IS NOT NULL AND empleado_id = :eid))"
        );
        $check->execute([
            ':f1' => $fecha,
            ':f2' => $fecha,
            ':mid' => $mascotaId,
            ':eid' => $empleadoId ?? 0,
        ]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Ya existe una cita para esa fecha y hora (mascota o empleado).'];
            header('Location: /vetsmart/cliente/citas/agendar'); exit;
        }

        $sql = "INSERT INTO citas (fecha, cliente_id, mascota_id, empleado_id, servicio_id, estado, notas, creado_por)
                VALUES (:fecha, :cliente_id, :mascota_id, :empleado_id, :servicio_id, 'pendiente', :notas, :creado_por)";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':fecha'=>$fecha,
            ':cliente_id'=>$clienteId,
            ':mascota_id'=>$mascotaId,
            ':empleado_id'=>$empleadoId,
            ':servicio_id'=>$servicioId,
            ':notas'=>$notas,
            ':creado_por'=>$clienteId,
        ]);
        $_SESSION['mensaje'] = ['tipo'=>'success','texto'=>'Cita agendada correctamente.'];
        header('Location: /vetsmart/cliente/citas');
        exit;
    }

    // Reagendar (actualizar fecha/hora) por el cliente
    public function reagendar(): void
    {
        $this->verificarSesion();
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') { header('Location: /vetsmart/cliente/citas'); exit; }
        $token = $_POST['_csrf'] ?? '';
        if (!CSRF::validate($token)) { $_SESSION['mensaje']=['tipo'=>'danger','texto'=>'SesiÃƒÂ³n expirada.']; header('Location: /vetsmart/cliente/citas'); exit; }
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        $id = (int)($_POST['id'] ?? 0);
        $nuevaFecha = trim((string)($_POST['fecha'] ?? ''));

        // Verificar cita del cliente y estado permitido
        $q = $this->pdo->prepare("SELECT id, empleado_id, mascota_id, estado FROM citas WHERE id = :id AND cliente_id = :cid");
        $q->execute([':id'=>$id, ':cid'=>$clienteId]);
        $c = $q->fetch(PDO::FETCH_ASSOC);
        if (!$c) { $_SESSION['mensaje']=['tipo'=>'danger','texto'=>'Cita no encontrada.']; header('Location: /vetsmart/cliente/citas'); exit; }
        if (!in_array(strtolower((string)$c['estado']), ['pendiente','confirmada'], true)) {
            $_SESSION['mensaje']=['tipo'=>'danger','texto'=>'No se puede reagendar esta cita.']; header('Location: /vetsmart/cliente/citas'); exit;
        }
        if ($nuevaFecha === '') { $_SESSION['mensaje']=['tipo'=>'danger','texto'=>'Fecha/hora invÃƒÂ¡lida.']; header('Location: /vetsmart/cliente/citas'); exit; }

        // Anti-duplicados
        $check = $this->pdo->prepare(
            "SELECT COUNT(*) FROM citas 
             WHERE DATE(fecha)=DATE(:f1) AND TIME(fecha)=TIME(:f2)
               AND id <> :id
               AND (mascota_id = :mid OR (empleado_id IS NOT NULL AND empleado_id = :eid))"
        );
        $check->execute([
            ':f1'=>$nuevaFecha,
            ':f2'=>$nuevaFecha,
            ':id'=>$id,
            ':mid'=>$c['mascota_id'],
            ':eid'=>$c['empleado_id'] ?? 0,
        ]);
        if ((int)$check->fetchColumn() > 0) {
            $_SESSION['mensaje'] = ['tipo'=>'danger','texto'=>'Ya existe una cita a esa hora (mascota o empleado).'];
            header('Location: /vetsmart/cliente/citas'); exit;
        }

        $up = $this->pdo->prepare("UPDATE citas SET fecha = :f, fecha_actualizacion = NOW(), actualizado_por = :u WHERE id = :id AND cliente_id = :cid");
        $ok = $up->execute([':f'=>$nuevaFecha, ':u'=>$clienteId, ':id'=>$id, ':cid'=>$clienteId]);
        $_SESSION['mensaje'] = $ok ? ['tipo'=>'success','texto'=>'Cita reagendada correctamente.'] : ['tipo'=>'danger','texto'=>'No se pudo reagendar.'];
        header('Location: /vetsmart/cliente/citas');
        exit;
    }

    public function historial(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);

        // Filtros: desde, hasta (YYYY-mm-dd) y mascota_id
        $desde = isset($_GET['desde']) ? trim((string)$_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? trim((string)$_GET['hasta']) : '';
        $mascotaId = isset($_GET['mascota_id']) ? (int)$_GET['mascota_id'] : 0;

        $validDesde = $this->validDate($desde);
        $validHasta = $this->validDate($hasta);

        // Consulta base + filtros
        $sql = "SELECT c.id, c.motivo, c.examen, c.diagnostico, c.tratamiento, c.recomendaciones, c.creado_en,
                       m.nombre AS mascota,
                       CONCAT(v.nombre,' ',v.apellido) AS veterinario
                FROM consultas c
                INNER JOIN mascotas m ON m.id = c.mascota_id
                LEFT JOIN usuarios v ON v.id = c.empleado_id
                WHERE m.dueno_id = :cid";

        $params = [':cid' => $clienteId];
        if ($validDesde) { $sql .= ' AND DATE(c.creado_en) >= :desde'; $params[':desde'] = $desde; }
        if ($validHasta) { $sql .= ' AND DATE(c.creado_en) <= :hasta'; $params[':hasta'] = $hasta; }
        if ($mascotaId > 0) { $sql .= ' AND m.id = :mid'; $params[':mid'] = $mascotaId; }
        $sql .= ' ORDER BY c.creado_en DESC';

        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        $registros = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Listado de mascotas del cliente para el filtro
        $mascotas = [];
        try {
            $q = $this->pdo->prepare('SELECT id, nombre FROM mascotas WHERE dueno_id = :id ORDER BY nombre ASC');
            $q->execute([':id' => $clienteId]);
            $mascotas = $q->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e) { $mascotas = []; }

        $content = $this->renderView('cliente/historial', [
            'registros' => $registros,
            'mascotas' => $mascotas,
            'filtros' => [
                'desde' => $validDesde ? $desde : '',
                'hasta' => $validHasta ? $hasta : '',
                'mascota_id' => $mascotaId > 0 ? $mascotaId : 0,
            ],
        ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    public function historialExportar(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        // Filtros igual que en historial
        $desde = isset($_GET['desde']) ? trim((string)$_GET['desde']) : '';
        $hasta = isset($_GET['hasta']) ? trim((string)$_GET['hasta']) : '';
        $mascotaId = isset($_GET['mascota_id']) ? (int)$_GET['mascota_id'] : 0;
        $validDesde = $this->validDate($desde);
        $validHasta = $this->validDate($hasta);

        $sql = "SELECT c.creado_en, m.nombre AS mascota, CONCAT(v.nombre,' ',v.apellido) AS veterinario,
                       c.diagnostico, c.tratamiento
                FROM consultas c
                INNER JOIN mascotas m ON m.id = c.mascota_id
                LEFT JOIN usuarios v ON v.id = c.empleado_id
                WHERE m.dueno_id = :cid";
        $params = [':cid' => $clienteId];
        if ($validDesde) { $sql .= ' AND DATE(c.creado_en) >= :desde'; $params[':desde'] = $desde; }
        if ($validHasta) { $sql .= ' AND DATE(c.creado_en) <= :hasta'; $params[':hasta'] = $hasta; }
        if ($mascotaId > 0) { $sql .= ' AND m.id = :mid'; $params[':mid'] = $mascotaId; }
        $sql .= ' ORDER BY c.creado_en DESC';

        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Si Dompdf estÃƒÂ¡ disponible, exportar PDF; si no, exportar CSV
        

        // CSV fallback
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=historial_clinico.csv');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Fecha','Mascota','Veterinario','DiagnÃƒÂ³stico','Tratamiento']);
        foreach ($rows as $r) {
            fputcsv($out, [
                (string)($r['creado_en'] ?? ''),
                (string)($r['mascota'] ?? ''),
                (string)($r['veterinario'] ?? ''),
                (string)($r['diagnostico'] ?? ''),
                (string)($r['tratamiento'] ?? ''),
            ]);
        }
        fclose($out);
        exit;
    }

    private function validDate(string $d): bool
    {
        if ($d === '') return false;
        $dt = \DateTime::createFromFormat('Y-m-d', $d);
        return $dt && $dt->format('Y-m-d') === $d;
    }



    public function reportes(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);
        // Datos
        $cli = $this->pdo->prepare("SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.direccion,
                                           p.foto
                                    FROM usuarios u
                                    LEFT JOIN perfil p ON p.usuario_id = u.id
                                    WHERE u.id = :id");
        $cli->execute([':id'=>$clienteId]);
        $cliente = $cli->fetch(PDO::FETCH_ASSOC) ?: [];

        $mas = $this->pdo->prepare("SELECT id, nombre, especie, raza, edad, sexo FROM mascotas WHERE dueno_id = :id ORDER BY nombre ASC");
        $mas->execute([':id'=>$clienteId]);
        $mascotas = $mas->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $cit = $this->pdo->prepare("SELECT c.fecha, c.estado, m.nombre AS mascota, s.nombre AS servicio
                                     FROM citas c
                                     LEFT JOIN mascotas m ON m.id = c.mascota_id
                                     LEFT JOIN servicios s ON s.id = c.servicio_id
                                     WHERE c.cliente_id = :id ORDER BY c.fecha DESC");
        $cit->execute([':id'=>$clienteId]);
        $citas = $cit->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $hist = $this->pdo->prepare("SELECT c.creado_en, m.nombre AS mascota, c.diagnostico, c.tratamiento
                                      FROM consultas c
                                      INNER JOIN mascotas m ON m.id = c.mascota_id
                                      WHERE m.dueno_id = :id ORDER BY c.creado_en DESC");
        $hist->execute([':id'=>$clienteId]);
        $historial = $hist->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $content = $this->renderView('cliente/reportes', [
            'cliente' => $cliente,
            'mascotas' => $mascotas,
            'citas' => $citas,
            'historial' => $historial,
        ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
    }

    public function reportesPdf(): void
    {
        $this->verificarSesion();
        $clienteId = (int)($_SESSION['user']['id'] ?? 0);

        // Reutilizar consultas de reportes()
        $cli = $this->pdo->prepare("SELECT u.id, u.nombre, u.apellido, u.email, u.telefono, u.direccion FROM usuarios u WHERE u.id = :id");
        $cli->execute([':id'=>$clienteId]);
        $cliente = $cli->fetch(PDO::FETCH_ASSOC) ?: [];

        $mas = $this->pdo->prepare("SELECT id, nombre, especie, raza, edad, sexo FROM mascotas WHERE dueno_id = :id ORDER BY nombre ASC");
        $mas->execute([':id'=>$clienteId]);
        $mascotas = $mas->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $cit = $this->pdo->prepare("SELECT c.fecha, c.estado, m.nombre AS mascota, s.nombre AS servicio
                                     FROM citas c
                                     LEFT JOIN mascotas m ON m.id = c.mascota_id
                                     LEFT JOIN servicios s ON s.id = c.servicio_id
                                     WHERE c.cliente_id = :id ORDER BY c.fecha DESC");
        $cit->execute([':id'=>$clienteId]);
        $citas = $cit->fetchAll(PDO::FETCH_ASSOC) ?: [];

        $hist = $this->pdo->prepare("SELECT c.creado_en, m.nombre AS mascota, c.diagnostico, c.tratamiento
                                      FROM consultas c
                                      INNER JOIN mascotas m ON m.id = c.mascota_id
                                      WHERE m.dueno_id = :id ORDER BY c.creado_en DESC");
        $hist->execute([':id'=>$clienteId]);
        $historial = $hist->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Intentar Dompdf (plantilla profesional)
        
        $content = $this->renderView('cliente/reportes', [
            'cliente' => $cliente,
            'mascotas' => $mascotas,
            'citas' => $citas,
            'historial' => $historial,
        ]);
        require APP_ROOT . '/views/layouts/main_cliente.php';
        return;
    }
}
