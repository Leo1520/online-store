<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo ?? 'Panel Admin'); ?> — ElectroHogar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        :root {
            --sidebar-w:      260px;
            --sidebar-mini:   64px;
            --primary:        #1B3A6B;
            --accent:         #F5A623;
            --sidebar-trans:  width .28s cubic-bezier(.4,0,.2,1);
        }
        body { background: #f0f2f5; font-family: 'Segoe UI', sans-serif; }

        /* ══ SIDEBAR BASE ══ */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--primary);
            position: fixed; top: 0; left: 0; z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            transition: var(--sidebar-trans);
            display: flex;
            flex-direction: column;
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.2); border-radius: 4px; }

        /* ══ SIDEBAR MINI (solo iconos) ══ */
        .sidebar.mini { width: var(--sidebar-mini); }

        .sidebar.mini .sidebar-label,
        .sidebar.mini .nav-section,
        .sidebar.mini .collapse,
        .sidebar.mini .submenu  { display: none !important; }

        .sidebar.mini .sidebar-brand {
            padding: .9rem 0;
            text-align: center;
            justify-content: center;
        }
        .sidebar.mini .sidebar-brand .brand-text { display: none; }

        .sidebar.mini .menu-module {
            justify-content: center;
            padding: .75rem 0;
        }
        .sidebar.mini .nav-link-direct {
            justify-content: center;
            padding: .75rem 0;
        }
        .sidebar.mini .logout-wrap {
            padding: .5rem 0;
            display: flex;
            justify-content: center;
        }
        .sidebar.mini .logout-wrap .btn { width: 38px; padding: .3rem 0; }
        .sidebar.mini .logout-wrap .logout-text { display: none; }

        /* ══ MAIN CONTENT ══ */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            transition: margin-left .28s cubic-bezier(.4,0,.2,1);
        }
        .main-content.mini { margin-left: var(--sidebar-mini); }

        /* ══ TOPBAR ══ */
        .topbar {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: .75rem 1.5rem; position: sticky; top: 0; z-index: 999;
        }
        .page-header {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: 1.25rem 1.5rem; margin-bottom: 1.5rem;
        }

        /* ══ SIDEBAR BRAND ══ */
        .sidebar-brand {
            padding: 1.2rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
            flex-shrink: 0;
            display: flex;
            align-items: center;
            gap: .5rem;
        }
        .sidebar-brand span { color: var(--accent); }

        /* ══ MÓDULO BUTTON ══ */
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
        .sidebar .menu-module.open  { background: rgba(255,255,255,.1);  color: #fff; }
        .sidebar .menu-module .chevron {
            margin-left: auto;
            font-size: .75rem;
            transition: transform .25s;
            opacity: .7;
        }
        .sidebar .menu-module.open .chevron { transform: rotate(180deg); }
        .sidebar .menu-module .mod-icon { font-size: 1rem; opacity: .9; flex-shrink: 0; }

        /* ══ SUBMENU ══ */
        .sidebar .submenu { background: rgba(0,0,0,.18); }
        .sidebar .submenu .nav-link {
            color: rgba(255,255,255,.7);
            padding: .5rem 1.5rem .5rem 2.8rem;
            display: flex; align-items: center; gap: .55rem;
            font-size: .845rem; text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .15s;
        }
        .sidebar .submenu .nav-link:hover  { color: #fff; background: rgba(255,255,255,.07); border-left-color: rgba(255,255,255,.3); }
        .sidebar .submenu .nav-link.active { color: #fff; background: rgba(255,255,255,.13); border-left-color: var(--accent); font-weight: 600; }

        /* ══ DIRECT LINK ══ */
        .sidebar .nav-link-direct {
            color: rgba(255,255,255,.75);
            padding: .6rem 1.5rem;
            display: flex; align-items: center; gap: .6rem;
            font-size: .9rem; text-decoration: none;
            transition: background .15s;
        }
        .sidebar .nav-link-direct:hover,
        .sidebar .nav-link-direct.active { color: #fff; background: rgba(255,255,255,.12); }

        /* ══ SECCIÓN LABEL ══ */
        .sidebar .nav-section {
            color: rgba(255,255,255,.35);
            font-size: .65rem;
            text-transform: uppercase;
            padding: .9rem 1.5rem .25rem;
            letter-spacing: .1em;
        }

        /* ══ FLYOUT (aparece al hacer hover sobre icono en modo mini) ══ */
        .sidebar-flyout {
            position: fixed;
            left: var(--sidebar-mini);
            background: #162f58;
            border-radius: 0 10px 10px 0;
            box-shadow: 6px 4px 20px rgba(0,0,0,.3);
            min-width: 200px;
            z-index: 1200;
            display: none;
            padding: 6px 0;
        }
        .flyout-header {
            color: rgba(255,255,255,.45);
            font-size: .62rem;
            text-transform: uppercase;
            letter-spacing: .1em;
            padding: 7px 16px 5px;
            border-bottom: 1px solid rgba(255,255,255,.08);
            margin-bottom: 3px;
        }
        .sidebar-flyout .nav-link {
            color: rgba(255,255,255,.8);
            padding: .44rem 1.1rem;
            font-size: .845rem;
            display: flex; align-items: center; gap: .5rem;
            text-decoration: none;
            border-left: 3px solid transparent;
            transition: all .12s;
        }
        .sidebar-flyout .nav-link:hover  { color: #fff; background: rgba(255,255,255,.1);  border-left-color: rgba(255,255,255,.3); }
        .sidebar-flyout .nav-link.active { color: #fff; background: rgba(255,255,255,.13); border-left-color: var(--accent); font-weight: 600; }

        /* ══ SIDEBAR GROUP (envuelve icono + submenu + flyout) ══ */
        .sidebar-group { position: relative; }

        /* ══ CARDS / TABLES / BADGES ══ */
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,.08); border-radius: .6rem; }
        .stat-card { border-left: 4px solid var(--accent); }
        .table th { font-size: .78rem; text-transform: uppercase; letter-spacing: .04em; color: #6b7280; font-weight: 600; }

        .badge-status-active    { background:#d1fae5; color:#065f46; }
        .badge-status-inactive  { background:#f3f4f6; color:#374151; }
        .badge-status-blocked   { background:#fee2e2; color:#991b1b; }
        .badge-status-pending   { background:#fef3c7; color:#92400e; }
        .badge-status-approved  { background:#dbeafe; color:#1e40af; }
        .badge-status-overdue   { background:#fee2e2; color:#991b1b; }
        .badge-status-completed { background:#d1fae5; color:#065f46; }
        .badge-status-procesando{ background:#fff7ed; color:#9a3412; }
        .badge-status-enviado   { background:#eff6ff; color:#1d4ed8; }
        .badge-status-entregado { background:#d1fae5; color:#065f46; }
        .badge-status-cancelado { background:#fee2e2; color:#991b1b; }
        .badge-status-facturado { background:#ede9fe; color:#5b21b6; }

        /* ══ RESPONSIVE ══ */
        @media(max-width:768px) {
            .sidebar { transform: translateX(-100%); width: var(--sidebar-w) !important; }
            .sidebar.show { transform: translateX(0); }
            .main-content, .main-content.mini { margin-left: 0; }
            .sidebar-flyout { display: none !important; }
        }
    </style>
</head>
<body>

<?php
$ap = $_GET['page'] ?? 'inicio';

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

function menuAbierto(array $paginas): string {
    global $ap;
    return in_array($ap, $paginas) ? 'show' : '';
}

function moduloActivo(array $paginas): string {
    global $ap;
    return in_array($ap, $paginas) ? 'open' : '';
}

function aUrl($page, $extra = '') {
    return '/admin/index.php?page=' . $page . ($extra ? '&' . $extra : '');
}

$pCatalogo = ['productos','productos_crear','productos_editar','categorias','categorias_crear','categorias_editar','marcas','marcas_crear','marcas_editar','industrias','industrias_crear','industrias_editar','sucursales'];
$pVentas   = ['pedidos','ventas','ventas_detalle','clientes','clientes_crear','clientes_editar','vendedores','vendedores_crear','vendedores_editar'];
$pAlmacen  = ['almacen','almacen_kardex','almacen_traspasos','almacen_ajustes','almacen_critico'];
?>

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar" id="sidebar">

    <!-- Brand -->
    <div class="sidebar-brand">
        <a href="/admin/index.php?page=inicio" class="text-white text-decoration-none fw-bold fs-5 d-flex align-items-center gap-2">
            <i class="bi bi-lightning-charge-fill text-warning flex-shrink-0"></i>
            <span class="brand-text sidebar-label">Electro<span style="color:var(--accent)">Hogar</span></span>
        </a>
        <div class="text-white-50 small mt-1 sidebar-label" style="line-height:1.1;">Panel Admin</div>
    </div>

    <nav class="mt-1 pb-3 flex-grow-1">

        <!-- Principal -->
        <div class="nav-section">Principal</div>

        <div class="sidebar-group">
            <a href="<?php echo aUrl('inicio'); ?>" class="nav-link-direct <?php echo isAct('inicio'); ?>">
                <i class="bi bi-house-door flex-shrink-0"></i>
                <span class="sidebar-label">Inicio</span>
            </a>
            <div class="sidebar-flyout" id="fly-inicio">
                <div class="flyout-header">Principal</div>
                <a href="<?php echo aUrl('inicio'); ?>" class="nav-link <?php echo isAct('inicio'); ?>">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
            </div>
        </div>

        <div class="sidebar-group">
            <a href="<?php echo aUrl('dashboard'); ?>" class="nav-link-direct <?php echo isAct('dashboard'); ?>">
                <i class="bi bi-speedometer2 flex-shrink-0"></i>
                <span class="sidebar-label">Dashboard</span>
            </a>
            <div class="sidebar-flyout" id="fly-dashboard">
                <div class="flyout-header">Principal</div>
                <a href="<?php echo aUrl('dashboard'); ?>" class="nav-link <?php echo isAct('dashboard'); ?>">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </div>
        </div>

        <!-- Catálogo -->
        <div class="nav-section sidebar-label">Catálogo</div>

        <div class="sidebar-group">
            <button class="menu-module <?php echo moduloActivo($pCatalogo); ?>"
                    data-bs-toggle="collapse" data-bs-target="#menu-catalogo"
                    aria-expanded="<?php echo menuAbierto($pCatalogo) ? 'true' : 'false'; ?>">
                <i class="bi bi-grid mod-icon"></i>
                <span class="sidebar-label">Catálogo</span>
                <i class="bi bi-chevron-down chevron sidebar-label"></i>
            </button>
            <div class="collapse submenu <?php echo menuAbierto($pCatalogo); ?>" id="menu-catalogo">
                <a href="<?php echo aUrl('productos'); ?>"  class="nav-link <?php echo isAct('productos'); ?>"><i class="bi bi-box-seam"></i> Productos</a>
                <a href="<?php echo aUrl('categorias'); ?>" class="nav-link <?php echo isAct('categorias'); ?>"><i class="bi bi-tag"></i> Categorías</a>
                <a href="<?php echo aUrl('marcas'); ?>"     class="nav-link <?php echo isAct('marcas'); ?>"><i class="bi bi-patch-check"></i> Marcas</a>
                <a href="<?php echo aUrl('industrias'); ?>" class="nav-link <?php echo isAct('industrias'); ?>"><i class="bi bi-gear"></i> Industrias</a>
                <a href="<?php echo aUrl('sucursales'); ?>" class="nav-link <?php echo isAct('sucursales'); ?>"><i class="bi bi-shop"></i> Sucursales</a>
            </div>
            <!-- Flyout catálogo -->
            <div class="sidebar-flyout" id="fly-catalogo">
                <div class="flyout-header">Catálogo</div>
                <a href="<?php echo aUrl('productos'); ?>"  class="nav-link <?php echo isAct('productos'); ?>"><i class="bi bi-box-seam"></i> Productos</a>
                <a href="<?php echo aUrl('categorias'); ?>" class="nav-link <?php echo isAct('categorias'); ?>"><i class="bi bi-tag"></i> Categorías</a>
                <a href="<?php echo aUrl('marcas'); ?>"     class="nav-link <?php echo isAct('marcas'); ?>"><i class="bi bi-patch-check"></i> Marcas</a>
                <a href="<?php echo aUrl('industrias'); ?>" class="nav-link <?php echo isAct('industrias'); ?>"><i class="bi bi-gear"></i> Industrias</a>
                <a href="<?php echo aUrl('sucursales'); ?>" class="nav-link <?php echo isAct('sucursales'); ?>"><i class="bi bi-shop"></i> Sucursales</a>
            </div>
        </div>

        <!-- Ventas -->
        <div class="nav-section sidebar-label">Comercial</div>

        <div class="sidebar-group">
            <button class="menu-module <?php echo moduloActivo($pVentas); ?>"
                    data-bs-toggle="collapse" data-bs-target="#menu-ventas"
                    aria-expanded="<?php echo menuAbierto($pVentas) ? 'true' : 'false'; ?>">
                <i class="bi bi-bag-check mod-icon"></i>
                <span class="sidebar-label">Ventas</span>
                <i class="bi bi-chevron-down chevron sidebar-label"></i>
            </button>
            <div class="collapse submenu <?php echo menuAbierto($pVentas); ?>" id="menu-ventas">
                <a href="<?php echo aUrl('pedidos'); ?>"   class="nav-link <?php echo isAct('pedidos'); ?>"><i class="bi bi-clock-history"></i> Pedidos</a>
                <a href="<?php echo aUrl('ventas'); ?>"    class="nav-link <?php echo isAct('ventas'); ?>"><i class="bi bi-receipt"></i> Historial de Ventas</a>
                <a href="<?php echo aUrl('clientes'); ?>"  class="nav-link <?php echo isAct('clientes'); ?>"><i class="bi bi-people"></i> Clientes</a>
                <a href="<?php echo aUrl('vendedores'); ?>" class="nav-link <?php echo isAct('vendedores'); ?>"><i class="bi bi-person-badge"></i> Vendedores</a>
            </div>
            <!-- Flyout ventas -->
            <div class="sidebar-flyout" id="fly-ventas">
                <div class="flyout-header">Comercial</div>
                <a href="<?php echo aUrl('pedidos'); ?>"   class="nav-link <?php echo isAct('pedidos'); ?>"><i class="bi bi-clock-history"></i> Pedidos</a>
                <a href="<?php echo aUrl('ventas'); ?>"    class="nav-link <?php echo isAct('ventas'); ?>"><i class="bi bi-receipt"></i> Historial de Ventas</a>
                <a href="<?php echo aUrl('clientes'); ?>"  class="nav-link <?php echo isAct('clientes'); ?>"><i class="bi bi-people"></i> Clientes</a>
                <a href="<?php echo aUrl('vendedores'); ?>" class="nav-link <?php echo isAct('vendedores'); ?>"><i class="bi bi-person-badge"></i> Vendedores</a>
            </div>
        </div>

        <!-- Almacén -->
        <div class="nav-section sidebar-label">Almacén</div>

        <div class="sidebar-group">
            <button class="menu-module <?php echo moduloActivo($pAlmacen); ?>"
                    data-bs-toggle="collapse" data-bs-target="#menu-almacen"
                    aria-expanded="<?php echo menuAbierto($pAlmacen) ? 'true' : 'false'; ?>">
                <i class="bi bi-archive mod-icon"></i>
                <span class="sidebar-label">Almacén</span>
                <?php if (!empty($totalCriticos)): ?>
                    <span class="badge bg-danger sidebar-label ms-1" style="font-size:.6rem;"><?php echo $totalCriticos; ?></span>
                <?php endif; ?>
                <i class="bi bi-chevron-down chevron sidebar-label"></i>
            </button>
            <div class="collapse submenu <?php echo menuAbierto($pAlmacen); ?>" id="menu-almacen">
                <a href="<?php echo aUrl('almacen'); ?>"           class="nav-link <?php echo isAct('almacen'); ?>"><i class="bi bi-table"></i> Stock Actual</a>
                <a href="<?php echo aUrl('almacen_kardex'); ?>"    class="nav-link <?php echo isAct('almacen_kardex'); ?>"><i class="bi bi-journal-text"></i> Kardex</a>
                <a href="<?php echo aUrl('almacen_traspasos'); ?>" class="nav-link <?php echo isAct('almacen_traspasos'); ?>"><i class="bi bi-arrow-left-right"></i> Traspasos</a>
                <a href="<?php echo aUrl('almacen_ajustes'); ?>"   class="nav-link <?php echo isAct('almacen_ajustes'); ?>"><i class="bi bi-pencil-square"></i> Ajustes</a>
                <a href="<?php echo aUrl('almacen_critico'); ?>"   class="nav-link <?php echo isAct('almacen_critico'); ?>">
                    <i class="bi bi-exclamation-diamond"></i> Stock Crítico
                    <?php if (!empty($totalCriticos)): ?>
                        <span class="badge bg-danger ms-auto" style="font-size:.6rem;"><?php echo $totalCriticos; ?></span>
                    <?php endif; ?>
                </a>
            </div>
            <!-- Flyout almacén -->
            <div class="sidebar-flyout" id="fly-almacen">
                <div class="flyout-header">Almacén</div>
                <a href="<?php echo aUrl('almacen'); ?>"           class="nav-link <?php echo isAct('almacen'); ?>"><i class="bi bi-table"></i> Stock Actual</a>
                <a href="<?php echo aUrl('almacen_kardex'); ?>"    class="nav-link <?php echo isAct('almacen_kardex'); ?>"><i class="bi bi-journal-text"></i> Kardex</a>
                <a href="<?php echo aUrl('almacen_traspasos'); ?>" class="nav-link <?php echo isAct('almacen_traspasos'); ?>"><i class="bi bi-arrow-left-right"></i> Traspasos</a>
                <a href="<?php echo aUrl('almacen_ajustes'); ?>"   class="nav-link <?php echo isAct('almacen_ajustes'); ?>"><i class="bi bi-pencil-square"></i> Ajustes</a>
                <a href="<?php echo aUrl('almacen_critico'); ?>"   class="nav-link <?php echo isAct('almacen_critico'); ?>"><i class="bi bi-exclamation-diamond"></i> Stock Crítico</a>
            </div>
        </div>

    </nav>

    <!-- Cerrar sesión -->
    <div class="logout-wrap px-3 pb-3">
        <a href="/admin/logout.php" class="btn btn-sm btn-outline-light w-100 d-flex align-items-center justify-content-center gap-1">
            <i class="bi bi-box-arrow-right"></i>
            <span class="logout-text sidebar-label">Cerrar Sesión</span>
        </a>
    </div>

</aside>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content" id="mainContent">

    <!-- TOPBAR -->
    <div class="topbar d-flex align-items-center justify-content-between">

        <!-- Hamburguesa (siempre visible) -->
        <button class="btn btn-sm btn-outline-secondary" id="sidebarToggle" title="Expandir/Contraer menú">
            <i class="bi bi-list fs-5"></i>
        </button>

        <nav aria-label="breadcrumb" class="d-none d-md-block ms-3 flex-grow-1">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="/admin/index.php">Admin</a></li>
                <li class="breadcrumb-item active"><?php echo htmlspecialchars($titulo ?? ''); ?></li>
            </ol>
        </nav>

        <div class="d-flex align-items-center gap-3">
            <span class="small text-muted d-none d-sm-inline">
                <i class="bi bi-person-circle me-1"></i>
                <?php echo htmlspecialchars($_SESSION['usuario'] ?? 'Administrador'); ?>
            </span>
            <a href="/index.php?pagina=inicio" class="btn btn-sm btn-outline-primary" target="_blank">
                <i class="bi bi-shop me-1"></i><span class="d-none d-sm-inline">Ver tienda</span>
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

    <script src="/recursos/js/validacion.js"></script>
    <div class="p-4">

<script>
(function () {
    const STORAGE_KEY  = 'ehSidebarMini';
    const FULL_PAGES   = ['inicio', 'dashboard', ''];
    const currentPage  = '<?php echo addslashes($ap); ?>';
    const sidebar      = document.getElementById('sidebar');
    const mainContent  = document.getElementById('mainContent');

    /* ── Determinar estado inicial ── */
    const saved = localStorage.getItem(STORAGE_KEY);
    let isMini  = (saved !== null) ? saved === '1' : !FULL_PAGES.includes(currentPage);

    function applyState(mini, animate) {
        if (!animate) {
            sidebar.style.transition     = 'none';
            mainContent.style.transition = 'none';
        }
        sidebar.classList.toggle('mini', mini);
        mainContent.classList.toggle('mini', mini);
        if (!animate) {
            setTimeout(() => {
                sidebar.style.transition     = '';
                mainContent.style.transition = '';
            }, 50);
        }
    }

    applyState(isMini, false);

    /* ── Hamburguesa ── */
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle('show');
            return;
        }
        isMini = !isMini;
        localStorage.setItem(STORAGE_KEY, isMini ? '1' : '0');
        applyState(isMini, true);
        hideAllFlyouts();
    });

    /* ── Guardar estado mini al hacer clic en cualquier enlace del sidebar ── */
    sidebar.querySelectorAll('a.nav-link, a.nav-link-direct').forEach(link => {
        link.addEventListener('click', function () {
            if (window.innerWidth > 768) {
                localStorage.setItem(STORAGE_KEY, '1');
            }
        });
    });

    /* ══ FLYOUTS ══ */
    let flyoutTimer   = null;
    let activeFlyout  = null;

    function hideAllFlyouts() {
        document.querySelectorAll('.sidebar-flyout').forEach(f => f.style.display = 'none');
        activeFlyout = null;
    }

    function showFlyout(flyout, groupEl) {
        clearTimeout(flyoutTimer);
        if (activeFlyout && activeFlyout !== flyout) {
            activeFlyout.style.display = 'none';
        }
        const rect    = groupEl.getBoundingClientRect();
        flyout.style.top     = rect.top + 'px';
        flyout.style.display = 'block';
        activeFlyout = flyout;
    }

    function scheduleHide() {
        flyoutTimer = setTimeout(() => {
            if (activeFlyout) {
                activeFlyout.style.display = 'none';
                activeFlyout = null;
            }
        }, 130);
    }

    document.querySelectorAll('.sidebar-group').forEach(function (group) {
        const flyout = group.querySelector('.sidebar-flyout');
        if (!flyout) return;

        group.addEventListener('mouseenter', function () {
            if (window.innerWidth <= 768) return;
            if (!sidebar.classList.contains('mini'))  return;
            showFlyout(flyout, group);
        });

        group.addEventListener('mouseleave', function () {
            scheduleHide();
        });

        flyout.addEventListener('mouseenter', function () {
            clearTimeout(flyoutTimer);
        });

        flyout.addEventListener('mouseleave', function () {
            scheduleHide();
        });
    });

    /* Esconder flyouts si el sidebar se expande */
    const observer = new MutationObserver(function () {
        if (!sidebar.classList.contains('mini')) hideAllFlyouts();
    });
    observer.observe(sidebar, { attributes: true, attributeFilter: ['class'] });
})();
</script>
