<?php
require_once __DIR__ . '/../modelos/Producto.php';

class CarritoControlador {
    public function index() {
        $this->procesarAccion();

        $modelo = new Producto();
        $items  = [];
        $total  = 0;

        if (!empty($_SESSION['carrito'])) {
            foreach ($_SESSION['carrito'] as $item) {
                $producto = $modelo->obtenerPorId($item['id_producto']);
                if ($producto) {
                    $subtotal = $producto['precio'] * $item['cantidad'];
                    $total   += $subtotal;
                    $items[]  = [
                        'producto' => $producto,
                        'cantidad' => $item['cantidad'],
                        'subtotal' => $subtotal,
                    ];
                }
            }
        }

        $titulo = "Carrito de Compras";
        require_once __DIR__ . '/../vistas/carrito.php';
    }

    private function procesarAccion() {
        if (!isset($_GET['accion'], $_GET['id'])) return;

        $id = (int)$_GET['id'];

        if ($_GET['accion'] === 'agregar') {
            $encontrado = false;
            foreach ($_SESSION['carrito'] as &$item) {
                if ($item['id_producto'] == $id) {
                    $item['cantidad']++;
                    $encontrado = true;
                    break;
                }
            }
            if (!$encontrado) {
                $_SESSION['carrito'][] = ['id_producto' => $id, 'cantidad' => 1];
            }
        } elseif ($_GET['accion'] === 'eliminar') {
            foreach ($_SESSION['carrito'] as $i => $item) {
                if ($item['id_producto'] == $id) {
                    unset($_SESSION['carrito'][$i]);
                    break;
                }
            }
            $_SESSION['carrito'] = array_values($_SESSION['carrito']);
        }
    }
}
