<?php
require_once __DIR__ . '/../../modelos/Empleado.php';
require_once __DIR__ . '/../../modelos/Vendedor.php';

class UsuarioInternoControlador {

    private static array $ROLES_EMPLEADO = ['admin', 'almacenero', 'repartidor', 'it'];

    public function usuariosInternos(): void {
        if (!tieneAlgunPermiso(['gestionar_usuarios', 'ver_usuarios'])) {
            header('Location: /admin/index.php?page=inicio&msg=' . urlencode('No tienes permiso para acceder a esa sección.'));
            exit();
        }
        $empModelo = new Empleado();
        $venModelo = new Vendedor();
        $mensaje   = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $error     = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = trim($_POST['accion'] ?? '');

            if ($accion === 'crear') {
                $result = $this->procesarCrear($empModelo, $venModelo);
                if ($result === true) {
                    header('Location: /admin/index.php?page=usuarios_internos&msg=' . urlencode('Usuario creado correctamente.'));
                    exit();
                }
                $error = $result;
            }

            if ($accion === 'editar') {
                $result = $this->procesarEditar($empModelo, $venModelo);
                if ($result === true) {
                    header('Location: /admin/index.php?page=usuarios_internos&msg=' . urlencode('Usuario actualizado correctamente.'));
                    exit();
                }
                $error = $result;
            }
        }

        if (isset($_GET['eliminar'])) {
            $usuario = trim($_GET['eliminar']);
            if ($usuario !== '') {
                $res = $empModelo->eliminar($usuario);
                $msg = $res['ok'] ? 'Usuario eliminado.' : ($res['msg'] ?? 'No se pudo eliminar.');
                header('Location: /admin/index.php?page=usuarios_internos&msg=' . urlencode($msg));
                exit();
            }
        }

        $usuarios = $empModelo->listarTodosInternos();
        $titulo   = 'Usuarios Internos';
        require_once __DIR__ . '/../../vistas/admin_usuarios_internos.php';
    }

    private function procesarCrear(Empleado $emp, Vendedor $ven): bool|string {
        $usuario    = trim($_POST['usuario']    ?? '');
        $password   = trim($_POST['password']   ?? '');
        $rol        = trim($_POST['rol']        ?? '');
        $ci         = trim($_POST['ci']         ?? '');
        $nombres    = trim($_POST['nombres']    ?? '');
        $apPaterno  = trim($_POST['apPaterno']  ?? '');
        $apMaterno  = trim($_POST['apMaterno']  ?? '');
        $correo     = trim($_POST['correo']     ?? '');
        $nroCelular = trim($_POST['nroCelular'] ?? '');
        $cargo      = trim($_POST['cargo']      ?? '');

        if ($usuario === '' || $password === '' || $rol === '' || $ci === '' || $nombres === '' || $apPaterno === '') {
            return 'Los campos usuario, contraseña, rol, CI, nombres y apellido paterno son obligatorios.';
        }
        if (strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($rol === 'vendedor') {
            $ok = $ven->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular);
            return $ok ? true : 'No se pudo crear el vendedor. El usuario o CI ya existe.';
        }

        if (!in_array($rol, self::$ROLES_EMPLEADO)) {
            return 'Rol no válido.';
        }

        $result = $emp->crearConCuenta($usuario, $hash, $rol, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $cargo);
        return $result['ok'] ? true : ($result['msg'] ?? 'No se pudo crear el usuario.');
    }

    private function procesarEditar(Empleado $emp, Vendedor $ven): bool|string {
        $usuario    = trim($_POST['usuario']     ?? '');
        $tipoPerfil = trim($_POST['tipo_perfil'] ?? '');
        $ci         = trim($_POST['ci']          ?? '');
        $nombres    = trim($_POST['nombres']     ?? '');
        $apPaterno  = trim($_POST['apPaterno']   ?? '');
        $apMaterno  = trim($_POST['apMaterno']   ?? '');
        $correo     = trim($_POST['correo']      ?? '');
        $nroCelular = trim($_POST['nroCelular']  ?? '');
        $cargo      = trim($_POST['cargo']       ?? '');
        $rol        = trim($_POST['rol']         ?? '');
        $password   = trim($_POST['password']    ?? '');

        if ($usuario === '' || $ci === '' || $nombres === '' || $apPaterno === '') {
            return 'Faltan datos obligatorios.';
        }

        if ($tipoPerfil === 'vendedor') {
            $ok = $ven->actualizar($usuario, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular);
            if (!$ok) return 'No se pudo actualizar el vendedor.';
        } else {
            if (!in_array($rol, self::$ROLES_EMPLEADO)) {
                return 'Rol no válido.';
            }
            $ok = $emp->actualizar($usuario, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $cargo, $rol);
            if (!$ok) return 'No se pudo actualizar el usuario.';
        }

        if ($password !== '') {
            if (strlen($password) < 6) return 'La contraseña debe tener al menos 6 caracteres.';
            $emp->actualizarPassword($usuario, password_hash($password, PASSWORD_DEFAULT));
        }

        return true;
    }
}
