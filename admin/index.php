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
    'productos', 'ventas', 'vendedores', 'almacen'
];

if (!in_array($page, $paginas)) {
    $page = 'dashboard';
}

// Requiere controladores desde la raíz del proyecto
require_once __DIR__ . '/../controladores/AdminControlador.php';

$ctrl = new AdminControlador();

switch ($page) {
    case 'dashboard':  $ctrl->dashboard();  break;
    case 'catalogos':  $ctrl->catalogos();  break;
    case 'sucursales': $ctrl->sucursales(); break;
    case 'clientes':   $ctrl->clientes();   break;
    case 'productos':  $ctrl->productos();  break;
    case 'ventas':     $ctrl->ventas();     break;
    case 'vendedores': $ctrl->vendedores(); break;
    case 'almacen':    $ctrl->almacen();    break;
}
