<?php
require_once __DIR__ . '/../../modelos/Empleado.php';

class UsuarioInternoControlador {

    private static array $ROLES_INTERNOS = ['admin', 'almacenero', 'repartidor', 'it'];

    public function usuariosInternos(): void {
        $modelo  = new Empleado();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $error   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = trim($_POST['accion'] ?? '');

            if ($accion === 'crear') {
                $result = $this->procesarCrear($modelo);
                if ($result === true) {
                    header('Location: /admin/index.php?page=usuarios_internos&msg=' . urlencode('Usuario creado correctamente.'));
                    exit();
                }
                $error = $result;
            }

            if ($accion === 'editar') {
                $result = $this->procesarEditar($modelo);
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
                $res = $modelo->eliminar($usuario);
                $msg = $res['ok'] ? 'Usuario eliminado.' : ($res['msg'] ?? 'No se pudo eliminar.');
                header('Location: /admin/index.php?page=usuarios_internos&msg=' . urlencode($msg));
                exit();
            }
        }

        $usuarios = $modelo->listarTodosInternos();
        $titulo   = 'Usuarios Internos';
        require_once __DIR__ . '/../../vistas/admin_usuarios_internos.php';
    }

    private function procesarCrear(Empleado $modelo): bool|string {
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
        if (!in_array($rol, self::$ROLES_INTERNOS)) {
            return 'Rol no válido.';
        }
        if (strlen($password) < 6) {
            return 'La contraseña debe tener al menos 6 caracteres.';
        }

        $hash   = password_hash($password, PASSWORD_DEFAULT);
        $result = $modelo->crearConCuenta($usuario, $hash, $rol, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $cargo);
        return $result['ok'] ? true : ($result['msg'] ?? 'No se pudo crear el usuario.');
    }

    private function procesarEditar(Empleado $modelo): bool|string {
        $usuario    = trim($_POST['usuario']    ?? '');
        $ci         = trim($_POST['ci']         ?? '');
        $nombres    = trim($_POST['nombres']    ?? '');
        $apPaterno  = trim($_POST['apPaterno']  ?? '');
        $apMaterno  = trim($_POST['apMaterno']  ?? '');
        $correo     = trim($_POST['correo']     ?? '');
        $nroCelular = trim($_POST['nroCelular'] ?? '');
        $cargo      = trim($_POST['cargo']      ?? '');
        $rol        = trim($_POST['rol']        ?? '');
        $password   = trim($_POST['password']   ?? '');

        if ($usuario === '' || $ci === '' || $nombres === '' || $apPaterno === '' || $rol === '') {
            return 'Faltan datos obligatorios.';
        }
        if (!in_array($rol, self::$ROLES_INTERNOS)) {
            return 'Rol no válido.';
        }

        $ok = $modelo->actualizar($usuario, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $cargo, $rol);
        if (!$ok) return 'No se pudo actualizar el usuario.';

        if ($password !== '') {
            if (strlen($password) < 6) return 'La contraseña debe tener al menos 6 caracteres.';
            $modelo->actualizarPassword($usuario, password_hash($password, PASSWORD_DEFAULT));
        }

        return true;
    }
}
