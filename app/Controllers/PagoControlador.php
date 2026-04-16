<?php
require_once __DIR__ . '/Controlador.php';
require_once __DIR__ . '/../Models/NotaVenta.php';
require_once __DIR__ . '/../Models/DetalleNotaVenta.php';

/**
 * Controlador de Pagos
 */
class PagoControlador extends Controlador {

    /**
     * Muestra el formulario de pago
     */
    public function mostrar($nro) {
        $this->verificarAutenticacion();

        $modeloNotaVenta = new NotaVenta($this->conexion);
        $notaVenta = $modeloNotaVenta->obtenerPorNro($nro);

        if (!$notaVenta) {
            $_SESSION['error'] = 'Nota de venta no encontrada.';
            $this->redirigir('?controlador=carrito&accion=mostrar');
        }

        $modeloDetalleNotaVenta = new DetalleNotaVenta($this->conexion);
        $detalles = $modeloDetalleNotaVenta->obtenerPorNotaVenta($nro);
        $total = $modeloDetalleNotaVenta->obtenerTotal($nro);

        $this->cargarVista('Pago/mostrar', [
            'notaVenta' => $notaVenta,
            'detalles' => $detalles,
            'total' => $total
        ]);
    }

    /**
     * Procesa el pago
     */
    public function procesar($nro) {
        $this->verificarAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=pago&accion=mostrar&nro=' . $nro);
        }

        $modeloNotaVenta = new NotaVenta($this->conexion);
        $notaVenta = $modeloNotaVenta->obtenerPorNro($nro);

        if (!$notaVenta) {
            $_SESSION['error'] = 'Nota de venta no encontrada.';
            $this->redirigir('?controlador=carrito&accion=mostrar');
        }

        // Validar datos de pago
        $numeroTarjeta = trim($_POST['numeroTarjeta'] ?? '');
        $nombreTitular = trim($_POST['nombreTitular'] ?? '');
        $fechaExpiracion = trim($_POST['fechaExpiracion'] ?? '');
        $cvv = trim($_POST['cvv'] ?? '');

        if (empty($numeroTarjeta) || empty($nombreTitular) || empty($fechaExpiracion) || empty($cvv)) {
            $_SESSION['error'] = 'Todos los campos de pago son requeridos.';
            $this->redirigir('?controlador=pago&accion=mostrar&nro=' . $nro);
        }

        // Validaciones básicas
        if (strlen($numeroTarjeta) < 13 || strlen($numeroTarjeta) > 19) {
            $_SESSION['error'] = 'Número de tarjeta inválido.';
            $this->redirigir('?controlador=pago&accion=mostrar&nro=' . $nro);
        }

        if (strlen($cvv) < 3 || strlen($cvv) > 4) {
            $_SESSION['error'] = 'CVV inválido.';
            $this->redirigir('?controlador=pago&accion=mostrar&nro=' . $nro);
        }

        // Aquí iría la integración con un gateway de pago real
        // Por ahora, simulamos el pago exitoso
        
        $_SESSION['mensaje'] = 'Pago procesado exitosamente.';
        $this->redirigir('pago_exitoso.php?nro=' . $nro);
    }

    /**
     * Muestra el comprobante de pago
     */
    public function comprobante($nro) {
        $this->verificarAutenticacion();

        $modeloNotaVenta = new NotaVenta($this->conexion);
        $notaVenta = $modeloNotaVenta->obtenerPorNro($nro);

        if (!$notaVenta) {
            $_SESSION['error'] = 'Comprobante no encontrado.';
            $this->redirigir('index.php');
        }

        $modeloDetalleNotaVenta = new DetalleNotaVenta($this->conexion);
        $detalles = $modeloDetalleNotaVenta->obtenerPorNotaVenta($nro);
        $total = $modeloDetalleNotaVenta->obtenerTotal($nro);

        $this->cargarVista('Pago/comprobante', [
            'notaVenta' => $notaVenta,
            'detalles' => $detalles,
            'total' => $total
        ]);
    }
}
?>
