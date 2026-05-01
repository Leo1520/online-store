<?php
session_start();

$pagina = $_GET['pagina'] ?? 'inicio';

$paginasPermitidas = [
    'inicio',
    'carrito',
    'pago',
    'pago_exitoso',
    'factura',
    'login',
    'logout',
    'registro',
    'mi_cuenta',
    'producto',
    'vendedor_panel',
    'admin_catalogos',
    'admin_sucursales',
    'admin_clientes',
    'admin_productos',
    'admin_ventas',
    'admin_vendedores',
    'admin_almacen'
];

if (!in_array($pagina, $paginasPermitidas)) {
    $pagina = 'inicio';
}

// Validar acceso a páginas administrativas
$paginasAdmin = ['admin_catalogos', 'admin_sucursales', 'admin_clientes', 'admin_productos', 'admin_ventas', 'admin_vendedores', 'admin_almacen'];
if (in_array($pagina, $paginasAdmin)) {
    if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
        header('Location: index.php?pagina=login');
        exit();
    }
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
    case 'factura':
        require_once __DIR__ . '/controladores/FacturaControlador.php';
        (new FacturaControlador())->ver();
        break;
    case 'login':
        require_once __DIR__ . '/controladores/AutenticacionControlador.php';
        (new AutenticacionControlador())->login();
        break;
    case 'logout':
        require_once __DIR__ . '/controladores/AutenticacionControlador.php';
        (new AutenticacionControlador())->logout();
        break;
    case 'registro':
        require_once __DIR__ . '/controladores/RegistroControlador.php';
        (new RegistroControlador())->index();
        break;
    case 'mi_cuenta':
        require_once __DIR__ . '/controladores/MiCuentaControlador.php';
        (new MiCuentaControlador())->index();
        break;
    case 'vendedor_panel':
        require_once __DIR__ . '/controladores/VendedorControlador.php';
        (new VendedorControlador())->panel();
        break;
    case 'producto':
        require_once __DIR__ . '/controladores/ProductoControlador.php';
        (new ProductoControlador())->detalle();
        break;
    case 'admin_catalogos':
        header('Location: admin/index.php?page=categorias'); exit();
    case 'admin_sucursales':
        header('Location: admin/index.php?page=sucursales'); exit();
    case 'admin_clientes':
        header('Location: admin/index.php?page=clientes'); exit();
    case 'admin_productos':
        header('Location: admin/index.php?page=productos'); exit();
    case 'admin_ventas':
        header('Location: admin/index.php?page=ventas'); exit();
    case 'admin_vendedores':
        header('Location: admin/index.php?page=vendedores'); exit();
    case 'admin_almacen':
        header('Location: admin/index.php?page=almacen'); exit();
}
