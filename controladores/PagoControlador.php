<?php
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Venta.php';

class PagoControlador {

    public function index() {
        if (empty($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit();
        }

        if (empty($_SESSION['carrito'])) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        // POST = pago simulado (tarjeta demo o QR demo)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $metodo   = trim($_GET['metodo'] ?? 'tarjeta');
            $venta    = new Venta();
            $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);
            if ($nroVenta !== false) {
                $_SESSION['carrito'] = [];
                header('Location: index.php?pagina=pago_exitoso&nro=' . (int)$nroVenta . '&metodo=' . $metodo);
            } else {
                header('Location: index.php?pagina=pago_exitoso&error=1');
            }
            exit();
        }

        $modelo = new Producto();
        $items  = [];
        $total  = 0;

        foreach ($_SESSION['carrito'] as $itemCarrito) {
            $producto = $modelo->obtenerPorId($itemCarrito['id_producto']);
            if (!$producto) continue;

            $cantidad = (int)$itemCarrito['cantidad'];
            $subtotal = (float)$producto['precio'] * $cantidad;
            $total   += $subtotal;

            $items[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ];
        }

        $titulo = 'Proceso de Pago';
        require_once __DIR__ . '/../vistas/pago.php';
    }

    public function exitoso() {
        $nroVenta = null;
        $error    = null;

        if (empty($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit();
        }

        if (!empty($_GET['error'])) {
            $error = 'No se pudo registrar la venta. Intenta nuevamente.';
        } else {
            $nroVenta = isset($_GET['nro']) ? (int)$_GET['nro'] : null;
        }

        $titulo = 'Compra Exitosa';
        require_once __DIR__ . '/../vistas/pago_exitoso.php';
    }
}
