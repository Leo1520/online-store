<?php
session_start();

$pagina = $_GET['pagina'] ?? 'inicio';

$paginasPermitidas = ['inicio', 'carrito', 'pago', 'pago_exitoso'];

if (!in_array($pagina, $paginasPermitidas)) {
    $pagina = 'inicio';
}

switch ($pagina) {
    case 'inicio':
        require_once __DIR__ . '/controladores/InicioControlador.php';
        (new InicioControlador())->index();
        break;
    case 'carrito':
        require_once __DIR__ . '/controladores/CarritoControlador.php';
        (new CarritoControlador())->index();
        break;
    case 'pago':
        require_once __DIR__ . '/controladores/PagoControlador.php';
        (new PagoControlador())->index();
        break;
    case 'pago_exitoso':
        require_once __DIR__ . '/controladores/PagoControlador.php';
        (new PagoControlador())->exitoso();
        break;
}
