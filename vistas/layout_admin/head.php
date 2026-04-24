<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo ?? 'Panel Admin'); ?> — ElectroHogar</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w: 260px;
            --primary:   #1B3A6B;
            --accent:    #F5A623;
        }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* ══ SIDEBAR ══ */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            position: fixed; top: 0; left: 0; z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            transition: transform .3s;
            display: flex;
            flex-direction: column;
        }
        /* Scrollbar fina dentro del sidebar */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }

        .sidebar-brand {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            flex-shrink: 0;
        }
        .sidebar-brand span { color: var(--accent); }

        /* ── Módulo (botón colapsable) ── */
        .sidebar .menu-module {
            width: 100%;
            text-align: left;
            background: none;
            border: none;
            color: rgba(255,255,255,.85);
            padding: .65rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
            letter-spacing: .01em;
        }
        .sidebar .menu-module:hover { background: rgba(255,255,255,.08); color: #fff; }
        .sidebar .menu-module.open  { background: rgba(255,255,255,.1); color: #fff; }
        .sidebar .menu-module .chevron {
            margin-left: auto;
            font-size: .75rem;
            transition: transform .25s;
            opacity: .7;
        }
        .sidebar .menu-module.open .chevron { transform: rotate(180deg); }
        .sidebar .menu-module .mod-icon { font-size: 1rem; opacity: .9; }

        /* ── Sub-ítems ── */
        .sidebar .submenu { background: rgba(0,0,0,.18); }
        .sidebar .submenu .nav-link {
            color: rgba(255,255,255,.7);
            padding: .5rem 1.5rem .5rem 2.8rem;
            display: flex;
            align-items: center;
            gap: .55rem;
            font-size: .845rem;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .sidebar .submenu .nav-link:hover { color: #fff; background: rgba(255,255,255,.07); border-left-color: rgba(255,255,255,.3); }
        .sidebar .submenu .nav-link.active { color: #fff; background: rgba(255,255,255,.13); border-left-color: var(--accent); font-weight: 600; }

        /* ── Separador de secciones ── */
        .sidebar .nav-section {
            color: rgba(255,255,255,.35);
            font-size: .65rem;
            text-transform: uppercase;
            padding: .9rem 1.5rem .25rem;
            letter-spacing: .1em;
        }

        /* ── Dashboard (ítem directo sin colapso) ── */
        .sidebar .nav-link-direct {
            color: rgba(255,255,255,.75);
            padding: .6rem 1.5rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: .9rem;
            text-decoration: none;
            transition: background .15s;
        }
        .sidebar .nav-link-direct:hover,
        .sidebar .nav-link-direct.active { color: #fff; background: rgba(255,255,255,.12); }

        /* ── Main content ── */
        .main-content { margin-left: var(--sidebar-w); min-height: 100vh; }
        .topbar {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: .75rem 1.5rem; position: sticky; top: 0; z-index: 999;
        }
        .page-header {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
        }

        /* ── Cards ── */
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: .6rem; }
        .stat-card { border-left: 4px solid var(--accent); }

        /* ── Tables ── */
        .table th {
            font-size: .78rem; text-transform: uppercase;
            letter-spacing: .04em; color: #6b7280; font-weight: 600;
        }

        /* ── Status badges ── */
        .badge-status-active    { background: #d1fae5; color: #065f46; }
        .badge-status-inactive  { background: #f3f4f6; color: #374151; }
        .badge-status-blocked   { background: #fee2e2; color: #991b1b; }
        .badge-status-pending   { background: #fef3c7; color: #92400e; }
        .badge-status-approved  { background: #dbeafe; color: #1e40af; }
        .badge-status-overdue   { background: #fee2e2; color: #991b1b; }
        .badge-status-completed { background: #d1fae5; color: #065f46; }
        .badge-status-procesando{ background: #fff7ed; color: #9a3412; }
        .badge-status-enviado   { background: #eff6ff; color: #1d4ed8; }
        .badge-status-entregado { background: #d1fae5; color: #065f46; }
        .badge-status-cancelado { background: #fee2e2; color: #991b1b; }
        .badge-status-facturado { background: #ede9fe; color: #5b21b6; }

        /* ── Responsive ── */
        @media(max-width:768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>

<?php
$ap = $_GET['page'] ?? 'dashboard';

/* Retorna 'active' si la página actual pertenece al grupo del ítem */
function isAct($page) {
    global $ap;
    $grupos = [
        'inicio'            => ['inicio'],
        'productos'         => ['productos',  'productos_crear',  'productos_editar'],
        'categorias'        => ['categorias', 'categorias_crear', 'categorias_editar'],
        'marcas'            => ['marcas',     'marcas_crear',     'marcas_editar'],
        'industrias'        => ['industrias', 'industrias_crear', 'industrias_editar'],
        'clientes'          => ['clientes',   'clientes_crear',   'clientes_editar'],
        'vendedores'        => ['vendedores', 'vendedores_crear', 'vendedores_editar'],
        'pedidos'           => ['pedidos',    'ventas_detalle'],
        'ventas'            => ['ventas'],
        'almacen'           => ['almacen'],
        'almacen_kardex'    => ['almacen_kardex'],
        'almacen_traspasos' => ['almacen_traspasos'],
        'almacen_ajustes'   => ['almacen_ajustes'],
        'almacen_critico'   => ['almacen_critico'],
    ];
    $grupo = $grupos[$page] ?? [$page];
    return in_array($ap, $grupo) ? 'active' : '';
}

/* Retorna 'show' si alguna página del array es la actual (para auto-abrir el colapso) */
function menuAbierto(array $paginas): string {
    global $ap;
    return in_array($ap, $paginas) ? 'show' : '';
}

/* Retorna 'open' para el botón módulo cuando su submenú está abierto */
function moduloActivo(array $paginas): string {
    global $ap;
    return in_array($ap, $paginas) ? 'open' : '';
}

function aUrl($page, $extra = '') {
    return '/admin/index.php?page=' . $page . ($extra ? '&' . $extra : '');
}

// Grupos de páginas por módulo
$pCatalogo = ['productos','productos_crear','productos_editar','categorias','categorias_crear','categorias_editar','marcas','marcas_crear','marcas_editar','industrias','industrias_crear','industrias_editar','sucursales'];
$pVentas   = ['pedidos','ventas','ventas_detalle','clientes','clientes_crear','clientes_editar','vendedores','vendedores_crear','vendedores_editar'];
$pAlmacen  = ['almacen','almacen_kardex','almacen_traspasos','almacen_ajustes','almacen_critico'];
?>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-brand">
        <a href="/admin/index.php?page=inicio" class="text-white text-decoration-none fw-bold fs-5">
            <i class="bi bi-lightning-charge-fill text-warning me-2"></i>Electro<span>Hogar</span>
        </a>
        <div class="text-white-50 small mt-1">Panel Administración</div>
    </div>

    <nav class="mt-1 pb-3">

        <!-- ── Inicio / Dashboard ── -->
        <div class="nav-section">Principal</div>
        <a href="<?php echo aUrl('inicio'); ?>" class="nav-link-direct <?php echo isAct('inicio'); ?>">
            <i class="bi bi-house-door"></i> Inicio
        </a>
        <a href="<?php echo aUrl('dashboard'); ?>" class="nav-link-direct <?php echo isAct('dashboard'); ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <!-- ══ MÓDULO: CATÁLOGO ══ -->
        <div class="nav-section">Catálogo</div>
        <button class="menu-module <?php echo moduloActivo($pCatalogo); ?>"
                data-bs-toggle="collapse" data-bs-target="#menu-catalogo"
                aria-expanded="<?php echo menuAbierto($pCatalogo) ? 'true' : 'false'; ?>">
            <i class="bi bi-grid mod-icon"></i>
            Catálogo
            <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse submenu <?php echo menuAbierto($pCatalogo); ?>" id="menu-catalogo">
            <a href="<?php echo aUrl('productos'); ?>" class="nav-link <?php echo isAct('productos'); ?>">
                <i class="bi bi-box-seam"></i> Productos
            </a>
            <a href="<?php echo aUrl('categorias'); ?>" class="nav-link <?php echo isAct('categorias'); ?>">
                <i class="bi bi-tag"></i> Categorías
            </a>
            <a href="<?php echo aUrl('marcas'); ?>" class="nav-link <?php echo isAct('marcas'); ?>">
                <i class="bi bi-patch-check"></i> Marcas
            </a>
            <a href="<?php echo aUrl('industrias'); ?>" class="nav-link <?php echo isAct('industrias'); ?>">
                <i class="bi bi-gear"></i> Industrias
            </a>
            <a href="<?php echo aUrl('sucursales'); ?>" class="nav-link <?php echo isAct('sucursales'); ?>">
                <i class="bi bi-shop"></i> Sucursales
            </a>
        </div>

        <!-- ══ MÓDULO: VENTAS ══ -->
        <div class="nav-section">Comercial</div>
        <button class="menu-module <?php echo moduloActivo($pVentas); ?>"
                data-bs-toggle="collapse" data-bs-target="#menu-ventas"
                aria-expanded="<?php echo menuAbierto($pVentas) ? 'true' : 'false'; ?>">
            <i class="bi bi-bag-check mod-icon"></i>
            Ventas
            <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse submenu <?php echo menuAbierto($pVentas); ?>" id="menu-ventas">
            <a href="<?php echo aUrl('pedidos'); ?>" class="nav-link <?php echo isAct('pedidos'); ?>">
                <i class="bi bi-clock-history"></i> Pedidos
            </a>
            <a href="<?php echo aUrl('ventas'); ?>" class="nav-link <?php echo isAct('ventas'); ?>">
                <i class="bi bi-receipt"></i> Historial de Ventas
            </a>
            <a href="<?php echo aUrl('clientes'); ?>" class="nav-link <?php echo isAct('clientes'); ?>">
                <i class="bi bi-people"></i> Clientes
            </a>
            <a href="<?php echo aUrl('vendedores'); ?>" class="nav-link <?php echo isAct('vendedores'); ?>">
                <i class="bi bi-person-badge"></i> Vendedores
            </a>
        </div>

        <!-- ══ MÓDULO: ALMACÉN ══ -->
        <div class="nav-section">Almacén</div>
        <button class="menu-module <?php echo moduloActivo($pAlmacen); ?>"
                data-bs-toggle="collapse" data-bs-target="#menu-almacen"
                aria-expanded="<?php echo menuAbierto($pAlmacen) ? 'true' : 'false'; ?>">
            <i class="bi bi-archive mod-icon"></i>
            Almacén
            <?php if (isset($totalCriticos) && $totalCriticos > 0): ?>
                <span class="badge bg-danger ms-1" style="font-size:.6rem;"><?php echo $totalCriticos; ?></span>
            <?php endif; ?>
            <i class="bi bi-chevron-down chevron"></i>
        </button>
        <div class="collapse submenu <?php echo menuAbierto($pAlmacen); ?>" id="menu-almacen">
            <a href="<?php echo aUrl('almacen'); ?>" class="nav-link <?php echo isAct('almacen'); ?>">
                <i class="bi bi-table"></i> Stock Actual
            </a>
            <a href="<?php echo aUrl('almacen_kardex'); ?>" class="nav-link <?php echo isAct('almacen_kardex'); ?>">
                <i class="bi bi-journal-text"></i> Kardex
            </a>
            <a href="<?php echo aUrl('almacen_traspasos'); ?>" class="nav-link <?php echo isAct('almacen_traspasos'); ?>">
                <i class="bi bi-arrow-left-right"></i> Traspasos
            </a>
            <a href="<?php echo aUrl('almacen_ajustes'); ?>" class="nav-link <?php echo isAct('almacen_ajustes'); ?>">
                <i class="bi bi-pencil-square"></i> Ajustes
            </a>
            <a href="<?php echo aUrl('almacen_critico'); ?>" class="nav-link <?php echo isAct('almacen_critico'); ?>">
                <i class="bi bi-exclamation-diamond"></i> Stock Crítico
                <?php if (isset($totalCriticos) && $totalCriticos > 0): ?>
                    <span class="badge bg-danger ms-auto" style="font-size:.6rem;"><?php echo $totalCriticos; ?></span>
                <?php endif; ?>
            </a>
        </div>

        <!-- Cerrar sesión -->
        <div class="px-3 mt-4">
            <a href="/admin/logout.php" class="btn btn-sm btn-outline-light w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Cerrar Sesión
            </a>
        </div>

    </nav>
</aside>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content">

    <!-- TOPBAR -->
    <div class="topbar d-flex align-items-center justify-content-between">
        <button class="btn btn-sm btn-outline-secondary d-md-none"
                onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="/admin/index.php">Admin</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($titulo ?? ''); ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted">
                <i class="bi bi-person-circle me-1"></i>
                <?php echo htmlspecialchars($_SESSION['usuario'] ?? 'Administrador'); ?>
            </span>
            <a href="/index.php?pagina=inicio" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="bi bi-shop me-1"></i>Ver tienda
            </a>
        </div>
    </div>

    <!-- FLASH MESSAGES -->
    <?php if (!empty($mensaje)): ?>
    <div class="px-4 pt-3">
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
            <i class="bi bi-check-circle me-2"></i><?php echo htmlspecialchars($mensaje); ?>
            <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <!-- PAGE CONTENT -->
    <script src="/recursos/js/validacion.js"></script>
    <div class="p-4">
