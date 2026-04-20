<?php
require_once __DIR__ . '/../modelos/Producto.php';

class ProductoControlador {
    public function detalle() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $modelo   = new Producto();
        $producto = $modelo->obtenerPorId($id);

        if (!$producto || strtolower($producto['estado'] ?? '') !== 'activo') {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $titulo = htmlspecialchars($producto['nombre']) . ' - Tienda en Línea';
        require_once __DIR__ . '/../vistas/producto_detalle.php';
    }
}
