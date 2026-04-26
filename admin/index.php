<?php
session_start();

// ── Auth: solo admins ──────────────────────────────────────────
if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    header('Location: login.php');
    exit();
}

// ── Router ────────────────────────────────────────────────────
$page = trim($_GET['page'] ?? 'inicio');

$paginas = [
    'inicio', 'dashboard',
    'sucursales',
    'productos', 'productos_crear', 'productos_editar',
    'categorias', 'categorias_crear', 'categorias_editar',
    'marcas', 'marcas_crear', 'marcas_editar',
    'industrias', 'industrias_crear', 'industrias_editar',
    'pedidos',
    'ventas', 'ventas_detalle',
    'clientes', 'clientes_crear', 'clientes_editar',
    'vendedores', 'vendedores_crear', 'vendedores_editar',
    'almacen', 'almacen_kardex', 'almacen_traspasos', 'almacen_ajustes', 'almacen_critico',
    'roles', 'permisos',
    'usuarios_internos',
];

if (!in_array($page, $paginas)) {
    $page = 'dashboard';
}

require_once __DIR__ . '/../config/permisos.php';

// ── Cargar controladores ──────────────────────────────────────
require_once __DIR__ . '/../controladores/admin/DashboardControlador.php';
require_once __DIR__ . '/../controladores/admin/CatalogoControlador.php';
require_once __DIR__ . '/../controladores/admin/ProductoControlador.php';
require_once __DIR__ . '/../controladores/admin/ClienteControlador.php';
require_once __DIR__ . '/../controladores/admin/VendedorControlador.php';
require_once __DIR__ . '/../controladores/admin/VentaControlador.php';
require_once __DIR__ . '/../controladores/admin/SucursalControlador.php';
require_once __DIR__ . '/../controladores/admin/AlmacenControlador.php';
require_once __DIR__ . '/../controladores/admin/RolControlador.php';
require_once __DIR__ . '/../controladores/admin/UsuarioInternoControlador.php';

// ── Despachar ─────────────────────────────────────────────────
switch ($page) {
    case 'inicio':
        (new DashboardControlador())->inicio();
        break;

    case 'dashboard':
        (new DashboardControlador())->dashboard();
        break;

    case 'marcas':         (new CatalogoControlador())->marcas();           break;
    case 'marcas_crear':   (new CatalogoControlador())->marcasCrear();      break;
    case 'marcas_editar':  (new CatalogoControlador())->marcasEditar();     break;

    case 'categorias':        (new CatalogoControlador())->categorias();        break;
    case 'categorias_crear':  (new CatalogoControlador())->categoriasCrear();   break;
    case 'categorias_editar': (new CatalogoControlador())->categoriasEditar();  break;

    case 'industrias':        (new CatalogoControlador())->industrias();        break;
    case 'industrias_crear':  (new CatalogoControlador())->industriasCrear();   break;
    case 'industrias_editar': (new CatalogoControlador())->industriasEditar();  break;

    case 'productos':        (new ProductoControlador())->productos();        break;
    case 'productos_crear':  (new ProductoControlador())->productosCrear();   break;
    case 'productos_editar': (new ProductoControlador())->productosEditar();  break;

    case 'clientes':       (new ClienteControlador())->clientes();       break;
    case 'clientes_crear': (new ClienteControlador())->clientesCrear();  break;
    case 'clientes_editar':(new ClienteControlador())->clientesEditar(); break;

    case 'vendedores':        (new VendedorControlador())->vendedores();       break;
    case 'vendedores_crear':  (new VendedorControlador())->vendedoresCrear();  break;
    case 'vendedores_editar': (new VendedorControlador())->vendedoresEditar(); break;

    case 'pedidos':       (new VentaControlador())->pedidos();       break;
    case 'ventas':        (new VentaControlador())->ventas();        break;
    case 'ventas_detalle':(new VentaControlador())->ventasDetalle(); break;

    case 'sucursales': (new SucursalControlador())->sucursales(); break;

    case 'almacen':           (new AlmacenControlador())->stockActual();  break;
    case 'almacen_kardex':    (new AlmacenControlador())->kardex();       break;
    case 'almacen_traspasos': (new AlmacenControlador())->traspasos();    break;
    case 'almacen_ajustes':   (new AlmacenControlador())->ajustes();      break;
    case 'almacen_critico':   (new AlmacenControlador())->stockCritico(); break;

    case 'roles':    (new RolControlador())->roles();    break;
    case 'permisos': (new RolControlador())->permisos(); break;

    case 'usuarios_internos': (new UsuarioInternoControlador())->usuariosInternos(); break;
}
