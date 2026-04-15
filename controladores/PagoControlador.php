<?php
class PagoControlador {
    public function index() {
        if (empty($_SESSION['carrito'])) {
            header("Location: index.php?pagina=carrito");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $_SESSION['carrito'] = [];
            header("Location: index.php?pagina=pago_exitoso");
            exit();
        }

        $titulo = "Proceso de Pago";
        require_once __DIR__ . '/../vistas/pago.php';
    }

    public function exitoso() {
        $titulo = "Compra Exitosa";
        require_once __DIR__ . '/../vistas/pago_exitoso.php';
    }
}
