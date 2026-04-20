<?php
require_once __DIR__ . '/../modelos/NotaVenta.php';
require_once __DIR__ . '/../modelos/Cuenta.php';

class MiCuentaControlador {
    public function index() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit();
        }

        if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $notaModel = new NotaVenta();
        $cuentaModel = new Cuenta();
        $mensaje     = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $tipoMensaje = isset($_GET['tipo']) ? trim($_GET['tipo']) : 'success';

        $cliente  = $notaModel->obtenerClientePorUsuario($_SESSION['usuario']);
        $historial = $notaModel->obtenerHistorialCliente($_SESSION['usuario']);

        $detalles = [];
        foreach ($historial as $compra) {
            $detalles[$compra['nro']] = $notaModel->obtenerDetallesPorNota((int)$compra['nro']);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'actualizar_perfil' && $cliente) {
                $correo     = trim($_POST['correo']     ?? '');
                $direccion  = trim($_POST['direccion']  ?? '');
                $nroCelular = trim($_POST['nroCelular'] ?? '');

                if ($correo && $direccion && $nroCelular) {
                    $ok = $notaModel->actualizarPerfilCliente($cliente['ci'], $_SESSION['usuario'], $correo, $direccion, $nroCelular);
                    $msg  = $ok ? 'Perfil actualizado correctamente.' : 'No se pudo actualizar el perfil.';
                    $tipo = $ok ? 'success' : 'danger';
                    header('Location: index.php?pagina=mi_cuenta&msg=' . urlencode($msg) . '&tipo=' . $tipo);
                    exit();
                }
            }

            if ($accion === 'cambiar_password') {
                $passwordActual = trim($_POST['password_actual'] ?? '');
                $passwordNuevo  = trim($_POST['password_nuevo']  ?? '');

                if ($passwordActual && $passwordNuevo && strlen($passwordNuevo) >= 4) {
                    $cuenta = $cuentaModel->verificarCredenciales($_SESSION['usuario'], $passwordActual);
                    if ($cuenta) {
                        $hash = password_hash($passwordNuevo, PASSWORD_DEFAULT);
                        $ok   = $cuentaModel->actualizarPassword($_SESSION['usuario'], $hash);
                        $msg  = $ok ? 'Contraseña actualizada.' : 'No se pudo cambiar la contraseña.';
                        $tipo = $ok ? 'success' : 'danger';
                    } else {
                        $msg  = 'La contraseña actual es incorrecta.';
                        $tipo = 'danger';
                    }
                    header('Location: index.php?pagina=mi_cuenta&msg=' . urlencode($msg) . '&tipo=' . $tipo);
                    exit();
                }
            }
        }

        $titulo = 'Mi Cuenta';
        require_once __DIR__ . '/../vistas/mi_cuenta.php';
    }
}
