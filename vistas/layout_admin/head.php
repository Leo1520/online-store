<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($titulo ?? 'Admin — Electrohogar'); ?></title>

    <!-- Font Awesome 5 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- AdminLTE 3 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        :root {
            --azul:       #1B3A6B;
            --azul-claro: #2751a3;
            --amarillo:   #F5A623;
        }
        .brand-link { background: #1B3A6B !important; border-bottom: 1px solid rgba(255,255,255,.1) !important; }
        .brand-text  { color: #F5A623 !important; font-size: 17px !important; font-weight: 800 !important; letter-spacing: 1px; }
        .main-sidebar { background: #1B3A6B !important; }
        .sidebar .nav-link { color: #c8d3ea !important; font-size: 13px; font-weight: 500; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #F5A623 !important; background: rgba(255,255,255,.07) !important; }
        .sidebar .nav-link i { color: #8898c4; }
        .sidebar .nav-link:hover i, .sidebar .nav-link.active i { color: #F5A623; }
        .sidebar .nav-treeview .nav-link { padding-left: 2rem; font-size: 12px; }
        .nav-sidebar .nav-header { color: #8898c4; font-size: 10px; letter-spacing: 1.5px; padding: 8px 16px 4px; font-weight: 700; }
        .user-panel { border-bottom: 1px solid rgba(255,255,255,.07) !important; }
        .user-panel .info a { color: #c8d3ea !important; font-size: 13px; }
        .user-panel .info small { color: #8898c4; font-size: 11px; }
        .main-header.navbar { background: #fff; border-bottom: 1px solid #e8ecf8; }
        .navbar-badge { font-size: 9px; }
        .content-wrapper { background: #f4f6fa; }
        .content-header h1 { font-size: 20px; font-weight: 700; color: #1B3A6B; }
        .breadcrumb-item.active { color: #888; font-size: 12px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(27,58,107,.08); }
        .card-header { border-radius: 12px 12px 0 0 !important; }
        .sidebar-mini.sidebar-collapse .main-sidebar { width: 4.6rem !important; }
        .badge-amarillo { background: #F5A623; color: #333; }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

<?php
$ap = $_GET['page'] ?? 'dashboard';
function adminLink($page, $extra = '') {
    return '/admin/index.php?page=' . $page . ($extra ? '&' . $extra : '');
}
function isActive($page, $current) {
    return $page === $current ? 'active' : '';
}
function isOpen($pages, $current) {
    return in_array($current, $pages) ? 'menu-open' : '';
}
?>

<!-- ══ TOP NAVBAR ══ -->
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button" title="Colapsar menú">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="/admin/index.php" class="nav-link font-weight-bold" style="color:#1B3A6B;">
                <i class="fas fa-th-large mr-1"></i>Admin Panel
            </a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="/" target="_blank" title="Ver tienda pública">
                <i class="fas fa-store"></i>
                <span class="d-none d-md-inline ml-1" style="font-size:12px;">Ver Tienda</span>
            </a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="userMenu" role="button" data-toggle="dropdown">
                <i class="fas fa-user-shield mr-1" style="color:#1B3A6B;"></i>
                <span style="font-size:13px;font-weight:600;"><?php echo htmlspecialchars($_SESSION['usuario'] ?? 'Admin'); ?></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right" style="min-width:200px;">
                <div class="px-3 py-2 border-bottom">
                    <small class="text-muted d-block">Conectado como</small>
                    <strong><?php echo htmlspecialchars($_SESSION['usuario'] ?? ''); ?></strong>
                    <span class="badge badge-warning badge-sm ml-1">Admin</span>
                </div>
                <a class="dropdown-item" href="/index.php?pagina=inicio" target="_blank">
                    <i class="fas fa-store fa-sm mr-2 text-muted"></i>Ir a la tienda
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="/admin/logout.php">
                    <i class="fas fa-sign-out-alt fa-sm mr-2"></i>Cerrar sesión
                </a>
            </div>
        </li>
    </ul>
</nav>
<!-- ══ END TOP NAVBAR ══ -->

<!-- ══ SIDEBAR ══ -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/admin/index.php" class="brand-link px-3 py-3">
        <span class="brand-text">⚡ ELECTROHOGAR</span>
    </a>

    <div class="sidebar">
        <!-- Usuario -->
        <div class="user-panel mt-3 pb-3 mb-2 d-flex align-items-center px-3">
            <div class="image">
                <div style="width:34px;height:34px;border-radius:50%;background:#F5A623;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:14px;color:#1B3A6B;">
                    <?php echo strtoupper(substr($_SESSION['usuario'] ?? 'A', 0, 1)); ?>
                </div>
            </div>
            <div class="info ml-2">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['usuario'] ?? 'Admin'); ?></a>
                <small>Administrador</small>
            </div>
        </div>

        <!-- Nav -->
        <nav class="mt-1">
            <ul class="nav nav-pills nav-sidebar flex-column nav-compact nav-child-indent"
                data-widget="treeview" role="menu" data-accordion="false">

                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="<?php echo adminLink('dashboard'); ?>" class="nav-link <?php echo isActive('dashboard', $ap); ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">GESTIÓN</li>

                <!-- Productos -->
                <li class="nav-item <?php echo isOpen(['productos'], $ap); ?>">
                    <a href="<?php echo adminLink('productos'); ?>" class="nav-link <?php echo isActive('productos', $ap); ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Productos</p>
                    </a>
                </li>

                <!-- Catálogos -->
                <li class="nav-item <?php echo isOpen(['catalogos'], $ap); ?>">
                    <a href="<?php echo adminLink('catalogos'); ?>" class="nav-link <?php echo isActive('catalogos', $ap); ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Catálogos</p>
                    </a>
                </li>

                <!-- Sucursales -->
                <li class="nav-item">
                    <a href="<?php echo adminLink('sucursales'); ?>" class="nav-link <?php echo isActive('sucursales', $ap); ?>">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Sucursales</p>
                    </a>
                </li>

                <li class="nav-header">PERSONAS</li>

                <!-- Clientes -->
                <li class="nav-item">
                    <a href="<?php echo adminLink('clientes'); ?>" class="nav-link <?php echo isActive('clientes', $ap); ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Clientes</p>
                    </a>
                </li>

                <!-- Vendedores -->
                <li class="nav-item">
                    <a href="<?php echo adminLink('vendedores'); ?>" class="nav-link <?php echo isActive('vendedores', $ap); ?>">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>Vendedores</p>
                    </a>
                </li>

                <li class="nav-header">OPERACIONES</li>

                <!-- Ventas -->
                <li class="nav-item">
                    <a href="<?php echo adminLink('ventas'); ?>" class="nav-link <?php echo isActive('ventas', $ap); ?>">
                        <i class="nav-icon fas fa-receipt"></i>
                        <p>Ventas</p>
                    </a>
                </li>

                <!-- Almacén -->
                <li class="nav-item has-treeview <?php echo isOpen(['almacen'], $ap); ?>">
                    <a href="<?php echo adminLink('almacen'); ?>" class="nav-link <?php echo isActive('almacen', $ap); ?>">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Almacén / Kardex
                        </p>
                    </a>
                </li>

                <li class="nav-header">SISTEMA</li>
                <li class="nav-item">
                    <a href="/admin/logout.php" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt" style="color:#e07070;"></i>
                        <p style="color:#e07070;">Cerrar sesión</p>
                    </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>
<!-- ══ END SIDEBAR ══ -->

<!-- ══ CONTENT WRAPPER ══ -->
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo htmlspecialchars($titulo ?? 'Panel Admin'); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin/index.php">Inicio</a></li>
                        <li class="breadcrumb-item active"><?php echo htmlspecialchars($titulo ?? ''); ?></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
