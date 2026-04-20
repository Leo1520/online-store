<?php
require_once __DIR__ . '/../modelos/Cliente.php';

class RegistroControlador {
    public function index() {
        if (isset($_SESSION['usuario'])) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $mensaje     = null;
        $tipoMensaje = null;
        $datos       = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario    = trim($_POST['usuario']   ?? '');
            $password   = trim($_POST['password']  ?? '');
            $ci         = trim($_POST['ci']        ?? '');
            $nombres    = trim($_POST['nombres']   ?? '');
            $apPaterno  = trim($_POST['apPaterno'] ?? '');
            $apMaterno  = trim($_POST['apMaterno'] ?? '');
            $correo     = trim($_POST['correo']    ?? '');
            $direccion  = trim($_POST['direccion'] ?? '');
            $nroCelular = trim($_POST['nroCelular'] ?? '');

            $datos = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'direccion', 'nroCelular');

            if ($usuario && $password && $ci && $nombres && $apPaterno && $apMaterno && $correo && $direccion && $nroCelular) {
                $clienteModel = new Cliente();
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ok = $clienteModel->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);

                if ($ok) {
                    header('Location: index.php?pagina=login&msg=Registro+exitoso.+Ahora+puedes+iniciar+sesion.');
                    exit();
                }
                $mensaje     = 'No se pudo registrar. El usuario o CI ya existe.';
                $tipoMensaje = 'danger';
            } else {
                $mensaje     = 'Todos los campos son obligatorios.';
                $tipoMensaje = 'warning';
            }
        }

        $titulo = 'Registro - Tienda en Línea';
        require_once __DIR__ . '/../vistas/layout/encabezado.php';
        require_once __DIR__ . '/../vistas/registro.php';
        require_once __DIR__ . '/../vistas/layout/pie.php';
    }
}
