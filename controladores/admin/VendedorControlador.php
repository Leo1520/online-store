<?php
require_once __DIR__ . '/../../modelos/Vendedor.php';

class VendedorControlador {

    public function vendedores() {
        $vendedorModel = new Vendedor();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if (isset($_GET['eliminar_ci'], $_GET['eliminar_usuario'])) {
            $ci      = trim($_GET['eliminar_ci']);
            $usuario = trim($_GET['eliminar_usuario']);
            if ($usuario === 'admin') {
                header('Location: index.php?page=vendedores&msg=' . urlencode('No se puede eliminar la cuenta admin.'));
                exit();
            }
            $ok  = $vendedorModel->eliminarVendedorYCuenta($ci, $usuario);
            $msg = $ok ? 'Vendedor eliminado correctamente.' : 'No se pudo eliminar el vendedor.';
            header('Location: index.php?page=vendedores&msg=' . urlencode($msg));
            exit();
        }

        $vendedores = $vendedorModel->obtenerTodos();
        $titulo     = 'Vendedores';
        require_once __DIR__ . '/../../vistas/admin_vendedores.php';
    }

    public function vendedoresCrear() {
        $vendedorModel = new Vendedor();
        $esEditar = false;
        $error    = null;
        $vendedor = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario    = trim($_POST['usuario']    ?? '');
            $password   = trim($_POST['password']   ?? '');
            $ci         = trim($_POST['ci']         ?? '');
            $nombres    = trim($_POST['nombres']    ?? '');
            $apPaterno  = trim($_POST['apPaterno']  ?? '');
            $apMaterno  = trim($_POST['apMaterno']  ?? '');
            $correo     = trim($_POST['correo']     ?? '');
            $nroCelular = trim($_POST['nroCelular'] ?? '');

            if ($usuario === '' || $password === '' || $ci === '' || $nombres === '' || $apPaterno === '' || $apMaterno === '' || $correo === '' || $nroCelular === '') {
                $error    = 'Completa todos los campos obligatorios.';
                $vendedor = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ok   = $vendedorModel->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular);
                if ($ok) {
                    header('Location: index.php?page=vendedores&msg=' . urlencode('Vendedor creado correctamente.'));
                    exit();
                }
                $error    = 'No se pudo crear el vendedor. El usuario o CI ya existe.';
                $vendedor = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular');
            }
        }

        $titulo = 'Nuevo Vendedor';
        require_once __DIR__ . '/../../vistas/admin_vendedores_form.php';
    }

    public function vendedoresEditar() {
        $vendedorModel = new Vendedor();
        $esEditar = true;
        $error    = null;
        $ci       = trim($_GET['ci']      ?? '');
        $usuario  = trim($_GET['usuario'] ?? '');

        if ($ci === '' || $usuario === '') {
            header('Location: index.php?page=vendedores'); exit();
        }

        $vendedor = $vendedorModel->obtenerPorClave($ci, $usuario);
        if (!$vendedor) {
            header('Location: index.php?page=vendedores&msg=' . urlencode('Vendedor no encontrado.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioCuenta = trim($_POST['usuarioCuenta'] ?? '');
            $ciPost        = trim($_POST['ci']            ?? '');
            $nombres       = trim($_POST['nombres']       ?? '');
            $apPaterno     = trim($_POST['apPaterno']     ?? '');
            $apMaterno     = trim($_POST['apMaterno']     ?? '');
            $correo        = trim($_POST['correo']        ?? '');
            $nroCelular    = trim($_POST['nroCelular']    ?? '');
            $password      = trim($_POST['password']      ?? '');

            if ($nombres === '' || $apPaterno === '' || $apMaterno === '' || $correo === '' || $nroCelular === '') {
                $error    = 'Completa todos los campos obligatorios.';
                $vendedor = array_merge($vendedor, compact('nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular'));
            } else {
                $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '';
                $ok   = $vendedorModel->actualizarConPassword($ciPost, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $hash);
                if ($ok) {
                    header('Location: index.php?page=vendedores&msg=' . urlencode('Vendedor actualizado correctamente.'));
                    exit();
                }
                $error    = 'No se pudo actualizar el vendedor.';
                $vendedor = array_merge($vendedor, compact('nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular'));
            }
        }

        $titulo = 'Editar Vendedor';
        require_once __DIR__ . '/../../vistas/admin_vendedores_form.php';
    }
}
