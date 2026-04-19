<?php
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Venta.php';

class PagoControlador {
    public function index() {
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?pagina=carrito");
            exit();
        }

        $modelo = new Producto();
        $items = [];
        $total = 0;

        foreach ($_SESSION['carrito'] as $itemCarrito) {
            $producto = $modelo->obtenerPorId($itemCarrito['id_producto']);
            if (!$producto) {
                continue;
            }

            $cantidad = (int)$itemCarrito['cantidad'];
            $subtotal = (float)$producto['precio'] * $cantidad;
            $total += $subtotal;

            $items[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ];
        }

        $errorPago = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_SESSION['usuario'])) {
                header('Location: index.php?pagina=login');
                exit();
            }
            $venta = new Venta();
            $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);

            if ($nroVenta !== false) {
                $_SESSION['carrito'] = [];
                header("Location: index.php?pagina=pago_exitoso&nro=" . (int)$nroVenta);
                exit();
            }

            $errorPago = "No se pudo registrar la compra en la base de datos. Intenta nuevamente.";
        }

        $titulo = "Proceso de Pago";
        require_once __DIR__ . '/../vistas/pago.php';
    }

    public function exitoso() {
        $nroVenta = isset($_GET['nro']) ? (int)$_GET['nro'] : null;
        $titulo = "Compra Exitosa";
        require_once __DIR__ . '/../vistas/pago_exitoso.php';
    }
}
