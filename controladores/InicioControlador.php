<?php
require_once __DIR__ . '/../modelos/Producto.php';

class InicioControlador {
    public function index() {
        $modelo   = new Producto();
        $productos = $modelo->obtenerTodos();
        $titulo   = "Inicio - Tienda en Línea";
        require_once __DIR__ . '/../vistas/inicio.php';
    }
}
