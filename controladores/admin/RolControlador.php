<?php
require_once __DIR__ . '/../../modelos/Rol.php';
require_once __DIR__ . '/../../modelos/Permiso.php';

class RolControlador {

    /* ══ ROLES ══════════════════════════════════════════════════════ */

    public function roles(): void {
        requierePermiso('gestionar_roles');
        $rolModel  = new Rol();
        $permModel = new Permiso();
        $mensaje   = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $error     = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = trim($_POST['accion'] ?? '');

            if ($accion === 'crear_rol') {
                $nombre      = trim($_POST['nombre']      ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                if ($nombre === '') {
                    $error = 'El nombre del rol es obligatorio.';
                } else {
                    $rolModel->crear($nombre, $descripcion);
                    header('Location: index.php?page=roles&msg=' . urlencode('Rol creado correctamente.'));
                    exit();
                }
            }

            if ($accion === 'editar_rol') {
                $cod         = (int)($_POST['cod']         ?? 0);
                $nombre      = trim($_POST['nombre']       ?? '');
                $descripcion = trim($_POST['descripcion']  ?? '');
                if ($cod <= 0 || $nombre === '') {
                    $error = 'Datos inválidos.';
                } else {
                    $rolModel->actualizar($cod, $nombre, $descripcion);
                    header('Location: index.php?page=roles&msg=' . urlencode('Rol actualizado correctamente.'));
                    exit();
                }
            }

            if ($accion === 'asignar_permisos') {
                $codRol     = (int)($_POST['codRol'] ?? 0);
                $permisos   = array_map('intval', (array)($_POST['permisos'] ?? []));
                if ($codRol > 0) {
                    $rolModel->asignarPermisos($codRol, $permisos);
                }
                header('Location: index.php?page=roles&msg=' . urlencode('Permisos actualizados correctamente.'));
                exit();
            }

            if ($accion === 'cambiar_rol_cuenta') {
                $usuario = trim($_POST['usuario'] ?? '');
                $rol     = trim($_POST['rol']     ?? '');
                if ($usuario !== '' && $rol !== '') {
                    $rolModel->cambiarRolCuenta($usuario, $rol);
                }
                header('Location: index.php?page=roles&msg=' . urlencode('Rol de cuenta actualizado.'));
                exit();
            }
        }

        if (isset($_GET['eliminar'])) {
            $res = $rolModel->eliminar((int)$_GET['eliminar']);
            $msg = $res['ok'] ? 'Rol eliminado.' : ($res['msg'] ?? 'No se pudo eliminar el rol.');
            header('Location: index.php?page=roles&msg=' . urlencode($msg));
            exit();
        }

        $roles    = $rolModel->listar();
        $permisos = $permModel->listar();
        $cuentas  = $rolModel->listarCuentas();
        $titulo   = 'Roles y Permisos';
        require_once __DIR__ . '/../../vistas/admin_roles.php';
    }

    /* ══ PERMISOS ════════════════════════════════════════════════════ */

    public function permisos(): void {
        requierePermiso('gestionar_roles');
        $permModel = new Permiso();
        $mensaje   = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $error     = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = trim($_POST['accion'] ?? '');

            if ($accion === 'crear_permiso') {
                $nombre      = trim($_POST['nombre']      ?? '');
                $descripcion = trim($_POST['descripcion'] ?? '');
                $modulo      = trim($_POST['modulo']      ?? '');
                if ($nombre === '' || $modulo === '') {
                    $error = 'Nombre y módulo son obligatorios.';
                } else {
                    $permModel->crear($nombre, $descripcion, $modulo);
                    header('Location: index.php?page=permisos&msg=' . urlencode('Permiso creado correctamente.'));
                    exit();
                }
            }

            if ($accion === 'editar_permiso') {
                $cod         = (int)($_POST['cod']         ?? 0);
                $nombre      = trim($_POST['nombre']       ?? '');
                $descripcion = trim($_POST['descripcion']  ?? '');
                $modulo      = trim($_POST['modulo']       ?? '');
                if ($cod <= 0 || $nombre === '' || $modulo === '') {
                    $error = 'Datos inválidos.';
                } else {
                    $permModel->actualizar($cod, $nombre, $descripcion, $modulo);
                    header('Location: index.php?page=permisos&msg=' . urlencode('Permiso actualizado correctamente.'));
                    exit();
                }
            }
        }

        if (isset($_GET['eliminar'])) {
            $permModel->eliminar((int)$_GET['eliminar']);
            header('Location: index.php?page=permisos&msg=' . urlencode('Permiso eliminado.'));
            exit();
        }

        $permisos = $permModel->listar();
        $titulo   = 'Permisos';
        require_once __DIR__ . '/../../vistas/admin_permisos.php';
    }
}
