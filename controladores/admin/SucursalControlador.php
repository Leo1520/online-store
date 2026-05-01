<?php
require_once __DIR__ . '/../../modelos/Sucursal.php';

class SucursalControlador {

    public function sucursales() {
        requierePermiso('ver_sucursales');
        $sucursalModel = new Sucursal();
        $mensaje       = null;
        $sucursalEditar = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion      = $_POST['accion']      ?? 'crear';
            $cod         = (int)($_POST['cod']   ?? 0);
            $nombre      = trim($_POST['nombre']      ?? '');
            $direccion   = trim($_POST['direccion']   ?? '');
            $nroTelefono = trim($_POST['nroTelefono'] ?? '');

            if ($nombre !== '' && $direccion !== '' && $nroTelefono !== '') {
                if ($accion === 'editar' && $cod > 0) {
                    $sucursalModel->actualizar($cod, $nombre, $direccion, $nroTelefono);
                    $mensaje = 'Sucursal actualizada correctamente.';
                } else {
                    $sucursalModel->crear($nombre, $direccion, $nroTelefono);
                    $mensaje = 'Sucursal creada correctamente.';
                }
            }
        }

        if (isset($_GET['eliminar'])) {
            $sucursalModel->eliminar((int)$_GET['eliminar']);
            header('Location: index.php?page=sucursales');
            exit();
        }

        if (isset($_GET['editar'])) {
            $sucursalEditar = $sucursalModel->obtenerPorCod((int)$_GET['editar']);
        }

        $sucursales = $sucursalModel->obtenerTodas();
        $titulo     = 'Sucursales';
        require_once __DIR__ . '/../../vistas/admin_sucursales.php';
    }
}
