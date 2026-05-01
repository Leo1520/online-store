<?php
require_once __DIR__ . '/../../modelos/Cliente.php';

class ClienteControlador {

    private $usuariosProtegidos = ['cliente_demo', 'admin'];

    public function clientes() {
        requierePermiso('ver_clientes');
        $clienteModel = new Cliente();
        $mensaje      = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if (isset($_GET['eliminar_ci'], $_GET['eliminar_usuario'])) {
            $ci      = trim($_GET['eliminar_ci']);
            $usuario = trim($_GET['eliminar_usuario']);
            if (in_array($usuario, $this->usuariosProtegidos, true)) {
                header('Location: index.php?page=clientes&msg=' . urlencode('No se puede eliminar la cuenta protegida.'));
                exit();
            }
            $ok  = $clienteModel->eliminarClienteYCuentaSegura($ci, $usuario);
            $msg = $ok ? 'Cliente eliminado correctamente.' : 'No se pudo eliminar el cliente.';
            header('Location: index.php?page=clientes&msg=' . urlencode($msg));
            exit();
        }

        $clientes = $clienteModel->obtenerTodos();
        $titulo   = 'Clientes';
        require_once __DIR__ . '/../../vistas/admin_clientes.php';
    }

    public function clientesCrear() {
        requierePermiso('ver_clientes');
        $clienteModel = new Cliente();
        $esEditar = false;
        $error    = null;
        $cliente  = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario    = trim($_POST['usuario']    ?? '');
            $password   = trim($_POST['password']   ?? '');
            $ci         = trim($_POST['ci']         ?? '');
            $nombres    = trim($_POST['nombres']    ?? '');
            $apPaterno  = trim($_POST['apPaterno']  ?? '');
            $apMaterno  = trim($_POST['apMaterno']  ?? '');
            $correo     = trim($_POST['correo']     ?? '');
            $direccion  = trim($_POST['direccion']  ?? '');
            $nroCelular = trim($_POST['nroCelular'] ?? '');

            if ($usuario === '' || $password === '' || $ci === '' || $nombres === '' || $apPaterno === '' || $apMaterno === '' || $correo === '' || $direccion === '' || $nroCelular === '') {
                $error   = 'Completa todos los campos obligatorios.';
                $cliente = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'direccion', 'nroCelular');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ok   = $clienteModel->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);
                if ($ok) {
                    header('Location: index.php?page=clientes&msg=' . urlencode('Cliente creado correctamente.'));
                    exit();
                }
                $error   = 'No se pudo crear el cliente. El usuario o CI ya existe.';
                $cliente = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'direccion', 'nroCelular');
            }
        }

        $titulo = 'Nuevo Cliente';
        require_once __DIR__ . '/../../vistas/admin_clientes_form.php';
    }

    public function clientesEditar() {
        requierePermiso('ver_clientes');
        $clienteModel = new Cliente();
        $esEditar = true;
        $error    = null;
        $ci       = trim($_GET['ci']      ?? '');
        $usuario  = trim($_GET['usuario'] ?? '');

        if ($ci === '' || $usuario === '') {
            header('Location: index.php?page=clientes'); exit();
        }

        $cliente = $clienteModel->obtenerPorClave($ci, $usuario);
        if (!$cliente) {
            header('Location: index.php?page=clientes&msg=' . urlencode('Cliente no encontrado.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioCuenta = trim($_POST['usuarioCuenta'] ?? '');
            $ciPost        = trim($_POST['ci']            ?? '');
            $password      = trim($_POST['password']      ?? '');
            $nombres       = trim($_POST['nombres']       ?? '');
            $apPaterno     = trim($_POST['apPaterno']     ?? '');
            $apMaterno     = trim($_POST['apMaterno']     ?? '');
            $correo        = trim($_POST['correo']        ?? '');
            $direccion     = trim($_POST['direccion']     ?? '');
            $nroCelular    = trim($_POST['nroCelular']    ?? '');

            if ($nombres === '' || $apPaterno === '' || $apMaterno === '' || $correo === '' || $direccion === '' || $nroCelular === '') {
                $error   = 'Completa todos los campos obligatorios.';
                $cliente = array_merge($cliente, compact('nombres', 'apPaterno', 'apMaterno', 'correo', 'direccion', 'nroCelular'));
            } else {
                $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '';
                $ok   = $clienteModel->actualizarConPassword($ciPost, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $hash);
                if ($ok) {
                    header('Location: index.php?page=clientes&msg=' . urlencode('Cliente actualizado correctamente.'));
                    exit();
                }
                $error   = 'No se pudo actualizar el cliente.';
                $cliente = array_merge($cliente, compact('nombres', 'apPaterno', 'apMaterno', 'correo', 'direccion', 'nroCelular'));
            }
        }

        $titulo = 'Editar Cliente';
        require_once __DIR__ . '/../../vistas/admin_clientes_form.php';
    }
}
