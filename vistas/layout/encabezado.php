<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo ?? 'Electrohogar'); ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        :root {
            --azul:       #1B3A6B;
            --azul-claro: #2751a3;
            --amarillo:   #F5A623;
            --amarillo-hover: #e09310;
            --gris-bg:    #F4F6FA;
        }
        body { background: var(--gris-bg); font-family: 'Segoe UI', sans-serif; }

        /* Barra superior */
        .barra-top {
            background: var(--azul);
            color: #cdd9f0;
            font-size: 12px;
            padding: 5px 0;
        }
        .barra-top a { color: #cdd9f0; text-decoration: none; }
        .barra-top a:hover { color: var(--amarillo); }

        /* Header principal */
        .header-main {
            background: var(--azul);
            padding: 12px 0;
        }
        .header-main .logo-text {
            font-size: 22px;
            font-weight: 800;
            color: #fff;
            text-decoration: none;
            letter-spacing: 1px;
        }
        .header-main .logo-text span { color: var(--amarillo); }
        .header-main .logo-text:hover { text-decoration: none; }

        /* Buscador */
        .search-box { max-width: 460px; width: 100%; }
        .search-box .form-control {
            border: none;
            border-radius: 25px 0 0 25px;
            padding: 8px 18px;
            font-size: 14px;
        }
        .search-box .btn-search {
            background: var(--amarillo);
            border: none;
            border-radius: 0 25px 25px 0;
            color: #fff;
            padding: 8px 18px;
            font-weight: 600;
        }
        .search-box .btn-search:hover { background: var(--amarillo-hover); }

        /* Íconos header derecha */
        .header-actions a {
            color: #fff;
            text-decoration: none;
            font-size: 13px;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: 20px;
        }
        .header-actions a i { font-size: 22px; }
        .header-actions a:hover { color: var(--amarillo); }
        .header-actions .badge-carrito {
            position: absolute;
            top: -4px;
            right: -6px;
            background: var(--amarillo);
            color: #fff;
            font-size: 10px;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Navbar categorías */
        .navbar-cats {
            background: var(--azul-claro);
            padding: 0;
            min-height: 42px;
        }
        .navbar-cats .nav-link {
            color: #e0e9ff !important;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 14px !important;
            border-right: 1px solid rgba(255,255,255,.1);
        }
        .navbar-cats .nav-link:hover,
        .navbar-cats .nav-link.active { color: #fff !important; background: rgba(0,0,0,.2); }
        .navbar-cats .dropdown-menu { border-radius: 0 0 6px 6px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,.15); }
        .navbar-cats .dropdown-item:hover { background: var(--amarillo); color: #fff; }

        /* Botones globales */
        .btn-amarillo { background: var(--amarillo); color: #fff; border: none; font-weight: 600; }
        .btn-amarillo:hover { background: var(--amarillo-hover); color: #fff; }
        .btn-azul { background: var(--azul); color: #fff; border: none; font-weight: 600; }
        .btn-azul:hover { background: var(--azul-claro); color: #fff; }

        /* Cards productos */
        .card-producto {
            border: none;
            border-radius: 10px;
            transition: transform .2s, box-shadow .2s;
            background: #fff;
        }
        .card-producto:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(27,58,107,.15) !important; }
        .card-producto .precio-tag { color: var(--azul); font-size: 18px; font-weight: 700; }
        .card-producto .badge-cat { background: var(--azul); color: #fff; font-size: 10px; }

        /* Sección beneficios */
        .beneficios { background: #fff; border-bottom: 3px solid var(--amarillo); }
        .beneficio-item i { color: var(--amarillo); font-size: 28px; }
        .beneficio-item h6 { color: var(--azul); font-weight: 700; margin: 6px 0 2px; }
        .beneficio-item p { font-size: 12px; color: #888; margin: 0; }

        /* Filtros */
        .filtros-box { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.06); }
        .filtros-box .form-control:focus { border-color: var(--azul); box-shadow: 0 0 0 .2rem rgba(27,58,107,.2); }

        /* Paginación */
        .page-item.active .page-link { background: var(--azul); border-color: var(--azul); }
        .page-link { color: var(--azul); }
        .page-link:hover { color: var(--azul-claro); }

        /* Título sección */
        .seccion-titulo {
            border-left: 4px solid var(--amarillo);
            padding-left: 12px;
            color: var(--azul);
            font-weight: 700;
        }
    </style>
</head>
<body>

<!-- Barra superior -->
<div class="barra-top d-none d-md-block">
    <div class="container d-flex justify-content-between">
        <span><i class="bi bi-telephone-fill mr-1"></i> +591 7000-0000 &nbsp;|&nbsp; <i class="bi bi-envelope-fill mr-1"></i> electrohogar@gmail.com</span>
        <span>
            <?php if (isset($_SESSION['usuario'])): ?>
                Hola, <strong style="color:#fff;"><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong>
                &nbsp;|&nbsp; <a href="index.php?pagina=logout">Cerrar sesión</a>
            <?php else: ?>
                <a href="index.php?pagina=login">Iniciar sesión</a> &nbsp;|&nbsp;
                <a href="index.php?pagina=registro">Crear cuenta</a>
            <?php endif; ?>
        </span>
    </div>
</div>

<!-- Header principal -->
<div class="header-main">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap">

            <!-- Logo -->
            <a href="index.php?pagina=inicio" class="logo-text mr-3">
                <i class="bi bi-lightning-charge-fill" style="color:var(--amarillo);"></i>
                Electro<span>hogar</span>
            </a>

            <!-- Buscador (solo en inicio) -->
            <form class="search-box d-none d-md-flex flex-grow-1 mx-3" onsubmit="return false;">
                <input type="text" id="headerBusqueda" class="form-control" placeholder="¿Qué estás buscando? Ej: televisor, lavadora...">
                <button class="btn-search" type="button" onclick="buscarDesdeHeader()">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <!-- Acciones derecha -->
            <div class="header-actions d-flex align-items-center">
                <?php if (isset($_SESSION['usuario'])): ?>
                    <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']): ?>
                        <a href="index.php?pagina=admin_ventas">
                            <i class="bi bi-shield-check"></i>
                            <span>Admin</span>
                        </a>
                    <?php elseif (isset($_SESSION['rol']) && $_SESSION['rol'] === 'vendedor'): ?>
                        <a href="index.php?pagina=vendedor_panel">
                            <i class="bi bi-speedometer2"></i>
                            <span>Mi Panel</span>
                        </a>
                    <?php else: ?>
                        <a href="index.php?pagina=mi_cuenta">
                            <i class="bi bi-person-circle"></i>
                            <span>Mi Cuenta</span>
                        </a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="index.php?pagina=login">
                        <i class="bi bi-person-circle"></i>
                        <span>Ingresar</span>
                    </a>
                <?php endif; ?>

                <a href="index.php?pagina=carrito" style="position:relative;">
                    <i class="bi bi-cart3"></i>
                    <span id="carritoContador" class="badge-carrito" style="display:none;">0</span>
                    <span>Carrito</span>
                </a>
            </div>

        </div>
    </div>
</div>

<!-- Navbar categorías -->
<nav class="navbar navbar-expand-lg navbar-dark p-0" style="background:var(--azul-claro);">
    <div class="container">
        <button class="navbar-toggler my-1" type="button" data-toggle="collapse" data-target="#navCats">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navCats">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php?pagina=inicio" style="font-size:13px;">
                        <i class="bi bi-house-fill mr-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?pagina=inicio" style="font-size:13px;">
                        <i class="bi bi-grid mr-1"></i>Todos los productos
                    </a>
                </li>
                <?php if (isset($_SESSION['es_admin']) && $_SESSION['es_admin']): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminMenu"
                       role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
                       style="font-size:13px; color:var(--amarillo) !important; font-weight:700;">
                        <i class="bi bi-gear-fill mr-1"></i>Administración
                    </a>
                    <div class="dropdown-menu" aria-labelledby="adminMenu">
                        <a class="dropdown-item" href="index.php?pagina=admin_catalogos"><i class="bi bi-tags mr-2 text-primary"></i>Catálogos</a>
                        <a class="dropdown-item" href="index.php?pagina=admin_productos"><i class="bi bi-box mr-2 text-primary"></i>Productos</a>
                        <a class="dropdown-item" href="index.php?pagina=admin_sucursales"><i class="bi bi-building mr-2 text-primary"></i>Sucursales</a>
                        <a class="dropdown-item" href="index.php?pagina=admin_clientes"><i class="bi bi-people mr-2 text-primary"></i>Clientes</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="index.php?pagina=admin_ventas"><i class="bi bi-receipt mr-2 text-success"></i>Ventas</a>
                        <a class="dropdown-item" href="index.php?pagina=admin_vendedores"><i class="bi bi-person-badge mr-2 text-success"></i>Vendedores</a>
                    </div>
                </li>
                <?php endif; ?>
                <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'vendedor'): ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?pagina=vendedor_panel" style="font-size:13px;">
                        <i class="bi bi-speedometer2 mr-1"></i>Mi Panel
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link font-weight-bold" href="index.php?pagina=pago"
                       style="font-size:13px; color:var(--amarillo) !important;">
                        <i class="bi bi-bag-check-fill mr-1"></i>Finalizar compra
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
function buscarDesdeHeader() {
    var q = document.getElementById('headerBusqueda');
    if (!q) return;
    var filtroBusqueda = document.getElementById('filtroBusqueda');
    if (filtroBusqueda) {
        filtroBusqueda.value = q.value;
        filtroBusqueda.dispatchEvent(new Event('input'));
    }
}
function filtrarCategoria(id) {
    var sel = document.getElementById('filtroCategoria');
    if (sel) { sel.value = id; sel.dispatchEvent(new Event('change')); }
}
document.addEventListener('DOMContentLoaded', function () {
    var hb = document.getElementById('headerBusqueda');
    if (hb) hb.addEventListener('keydown', function (e) { if (e.key === 'Enter') buscarDesdeHeader(); });
});
</script>
