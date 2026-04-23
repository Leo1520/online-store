<?php
session_start();

// ── Auth: solo admins ──────────────────────────────────────────
if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header('Location: login.php');
    exit();
}

// ── Router ────────────────────────────────────────────────────
$page = trim($_GET['page'] ?? 'dashboard');

$paginas = [
    'dashboard', 'catalogos', 'sucursales', 'clientes',
    'productos', 'productos_crear', 'productos_editar',
    'categorias', 'categorias_crear', 'categorias_editar',
    'marcas', 'marcas_crear', 'marcas_editar',
    'industrias', 'industrias_crear', 'industrias_editar',
    'ventas', 'vendedores', 'vendedores_crear', 'vendedores_editar', 'almacen'
];

if (!in_array($page, $paginas)) {
    $page = 'dashboard';
}

// Requiere controladores desde la raíz del proyecto
require_once __DIR__ . '/../controladores/AdminControlador.php';

$ctrl = new AdminControlador();

switch ($page) {
    case 'dashboard':        $ctrl->dashboard();        break;
    case 'catalogos':        $ctrl->catalogos();        break;
    case 'sucursales':       $ctrl->sucursales();       break;
    case 'clientes':         $ctrl->clientes();         break;
    case 'productos':        $ctrl->productos();        break;
    case 'productos_crear':  $ctrl->productosCrear();   break;
    case 'productos_editar': $ctrl->productosEditar();  break;
    case 'categorias':       $ctrl->categorias();       break;
    case 'categorias_crear': $ctrl->categoriasCrear();  break;
    case 'categorias_editar':$ctrl->categoriasEditar(); break;
    case 'marcas':           $ctrl->marcas();           break;
    case 'marcas_crear':     $ctrl->marcasCrear();      break;
    case 'marcas_editar':    $ctrl->marcasEditar();     break;
    case 'industrias':       $ctrl->industrias();       break;
    case 'industrias_crear': $ctrl->industriasCrear();  break;
    case 'industrias_editar':$ctrl->industriasEditar(); break;
    case 'ventas':           $ctrl->ventas();           break;
    case 'vendedores':         $ctrl->vendedores();        break;
    case 'vendedores_crear':   $ctrl->vendedoresCrear();   break;
    case 'vendedores_editar':  $ctrl->vendedoresEditar();  break;
    case 'almacen':          $ctrl->almacen();          break;
}
