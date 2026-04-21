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

        /* Carrito drawer */
        .carrito-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1040;
        }
        .carrito-overlay.activo { display: block; }

        .carrito-drawer {
            position: fixed;
            top: 0; right: 0;
            width: 360px; max-width: 95vw;
            height: 100vh;
            background: #fff;
            z-index: 1050;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform .3s ease;
            box-shadow: -4px 0 24px rgba(0,0,0,.18);
        }
        .carrito-drawer.activo { transform: translateX(0); }

        .carrito-drawer-header {
            background: var(--azul);
            color: #fff;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }
        .carrito-drawer-header h5 { margin: 0; font-weight: 700; font-size: 16px; }
        .carrito-drawer-close {
            background: none; border: none; color: #fff;
            font-size: 22px; cursor: pointer; line-height: 1; opacity: .8;
        }
        .carrito-drawer-close:hover { opacity: 1; }

        .carrito-drawer-body {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
        }

        .carrito-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .carrito-item img {
            width: 56px; height: 56px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #eee;
            background: #fafafa;
        }
        .carrito-item-info { flex: 1; min-width: 0; }
        .carrito-item-info .nombre {
            font-size: 13px; font-weight: 600;
            color: var(--azul);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .carrito-item-info .precio { font-size: 12px; color: #888; }
        .carrito-item-controls {
            display: flex; align-items: center; gap: 4px; margin-top: 5px;
        }
        .carrito-item-controls button {
            width: 24px; height: 24px;
            border: 1px solid #ddd; background: #f8f8f8;
            border-radius: 4px; font-size: 14px; line-height: 1;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .carrito-item-controls button:hover { background: var(--azul); color: #fff; border-color: var(--azul); }
        .carrito-item-controls span {
            width: 28px; text-align: center; font-size: 13px; font-weight: 600;
        }
        .carrito-item-subtotal { font-size: 13px; font-weight: 700; color: var(--azul); white-space: nowrap; }
        .btn-eliminar-item {
            background: none; border: none; color: #ccc;
            font-size: 16px; cursor: pointer; padding: 0 2px;
        }
        .btn-eliminar-item:hover { color: #dc3545; }

        .carrito-drawer-footer {
            padding: 16px;
            border-top: 2px solid #f0f0f0;
            flex-shrink: 0;
            background: #fff;
        }
        .carrito-total-row {
            display: flex; justify-content: space-between;
            font-size: 16px; font-weight: 700;
            color: var(--azul); margin-bottom: 12px;
        }
        .carrito-empty {
            text-align: center; padding: 40px 20px; color: #aaa;
        }
        .carrito-empty i { font-size: 48px; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>

<!-- Cabecera fija -->
<div id="cabecera-fija" style="position:sticky;top:0;z-index:1030;">

<!-- Barra superior -->
<div class="barra-top d-none d-md-block">
    <div class="container d-flex justify-content-between">
        <span><i class="bi bi-telephone-fill mr-1"></i> +591 7000-0000 &nbsp;|&nbsp; <i class="bi bi-envelope-fill mr-1"></i> electrohogar@gmail.com</span>
        <span>
            <?php if (isset($_SESSION['usuario'])): ?>
                Hola, <strong style="color:#fff;"><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong>
                &nbsp;|&nbsp; <a href="index.php?pagina=logout">Cerrar sesión</a>
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
                    <a href="#" data-toggle="modal" data-target="#modalLogin">
                        <i class="bi bi-person-circle"></i>
                        <span>Ingresar</span>
                    </a>
                    <a href="#" data-toggle="modal" data-target="#modalRegistro">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Registrarse</span>
                    </a>
                <?php endif; ?>

                <a href="#" onclick="abrirCarrito(); return false;" style="position:relative;">
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
        </div>
    </div>
</nav>

</div><!-- fin cabecera-fija -->

<!-- Carrito Overlay -->
<div class="carrito-overlay" id="carritoOverlay" onclick="cerrarCarrito()"></div>

<!-- Carrito Drawer -->
<div class="carrito-drawer" id="carritoDrawer">
    <div class="carrito-drawer-header">
        <h5><i class="bi bi-cart3 mr-2"></i>Mi Carrito</h5>
        <button class="carrito-drawer-close" onclick="cerrarCarrito()">&times;</button>
    </div>
    <div class="carrito-drawer-body" id="carritoDrawerBody">
        <div class="carrito-empty"><i class="bi bi-cart-x"></i>Tu carrito está vacío</div>
    </div>
    <div class="carrito-drawer-footer" id="carritoDrawerFooter" style="display:none;">
        <div class="carrito-total-row">
            <span>Total</span>
            <span id="carritoDrawerTotal">Bs. 0.00</span>
        </div>
        <?php if (isset($_SESSION['usuario'])): ?>
            <a href="index.php?pagina=pago" class="btn btn-amarillo btn-block font-weight-bold">
                <i class="bi bi-credit-card mr-1"></i>Finalizar compra
            </a>
        <?php else: ?>
            <button type="button" class="btn btn-amarillo btn-block font-weight-bold"
                    onclick="cerrarCarrito(); $('#modalLogin').modal('show');">
                <i class="bi bi-lock-fill mr-1"></i>Inicia sesión para pagar
            </button>
        <?php endif; ?>
        <button onclick="cerrarCarrito()" class="btn btn-outline-secondary btn-block btn-sm mt-2">
            Seguir comprando
        </button>
    </div>
</div>

<!-- Modal Login -->
<div class="modal fade" id="modalLogin" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;">

            <!-- Header -->
            <div class="modal-header border-0 pb-0" style="background:var(--azul);">
                <div class="w-100 text-center py-3">
                    <i class="bi bi-lightning-charge-fill" style="font-size:2rem;color:var(--amarillo);"></i>
                    <h5 class="text-white font-weight-bold mt-1 mb-0">Bienvenido a Electrohogar</h5>
                    <p class="text-white-50 small mb-0">Ingresa a tu cuenta</p>
                </div>
                <button type="button" class="close" data-dismiss="modal" style="position:absolute;top:12px;right:16px;color:#fff;opacity:.7;">
                    <span>&times;</span>
                </button>
            </div>

            <!-- Body -->
            <div class="modal-body px-4 py-4">
                <div id="loginError" class="alert alert-danger py-2" style="display:none;font-size:13px;"></div>

                <form id="formLoginModal">
                    <div class="form-group">
                        <label class="small font-weight-bold" style="color:var(--azul);">
                            <i class="bi bi-person mr-1"></i>Usuario
                        </label>
                        <input type="text" id="loginUsuario" name="usuario" class="form-control"
                               placeholder="Tu nombre de usuario" autocomplete="username"
                               style="border-radius:8px;">
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold" style="color:var(--azul);">
                            <i class="bi bi-lock mr-1"></i>Contraseña
                        </label>
                        <div class="input-group">
                            <input type="password" id="loginPassword" name="password" class="form-control"
                                   placeholder="Tu contraseña" autocomplete="current-password"
                                   style="border-radius:8px 0 0 8px;">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                        style="border-radius:0 8px 8px 0;"
                                        onclick="togglePassword()">
                                    <i class="bi bi-eye" id="iconOjo"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="btnLoginModal" class="btn btn-block font-weight-bold mt-3"
                            style="background:var(--azul);color:#fff;border-radius:8px;padding:10px;">
                        <i class="bi bi-box-arrow-in-right mr-1"></i>Ingresar
                    </button>
                </form>

                <hr class="my-3">
                <p class="text-center text-muted small mb-0">
                    ¿No tenés cuenta?
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#modalRegistro"
                       style="color:var(--azul);font-weight:700;">
                        Registrate gratis
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    var inp  = document.getElementById('loginPassword');
    var icon = document.getElementById('iconOjo');
    if (inp.type === 'password') { inp.type = 'text';     icon.className = 'bi bi-eye-slash'; }
    else                         { inp.type = 'password'; icon.className = 'bi bi-eye'; }
}

document.addEventListener('DOMContentLoaded', function () {
    var form   = document.getElementById('formLoginModal');
    var errDiv = document.getElementById('loginError');
    var btn    = document.getElementById('btnLoginModal');

    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errDiv.style.display = 'none';

        var usuario  = document.getElementById('loginUsuario').value.trim();
        var password = document.getElementById('loginPassword').value.trim();

        if (!usuario || !password) {
            errDiv.textContent = 'Ingresa usuario y contraseña.';
            errDiv.style.display = '';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Ingresando...';

        var fd = new FormData();
        fd.append('usuario',  usuario);
        fd.append('password', password);

        fetch('api/login.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.ok) {
                    window.location.reload();
                } else {
                    errDiv.textContent   = d.mensaje;
                    errDiv.style.display = '';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-box-arrow-in-right mr-1"></i>Ingresar';
                }
            })
            .catch(function () {
                errDiv.textContent   = 'Error de conexión. Intenta nuevamente.';
                errDiv.style.display = '';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-box-arrow-in-right mr-1"></i>Ingresar';
            });
    });

    // Abrir modal si viene de redirect de login
    <?php if (isset($_GET['pagina']) && $_GET['pagina'] === 'login'): ?>
    $('#modalLogin').modal('show');
    <?php endif; ?>
});
</script>

<!-- Modal Registro -->
<div class="modal fade" id="modalRegistro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width:580px;">
        <div class="modal-content" style="border-radius:14px;overflow:hidden;border:none;">

            <div class="modal-header border-0 pb-0" style="background:var(--azul);">
                <div class="w-100 text-center py-3">
                    <i class="bi bi-person-plus-fill" style="font-size:2rem;color:var(--amarillo);"></i>
                    <h5 class="text-white font-weight-bold mt-1 mb-0">Crear cuenta en Electrohogar</h5>
                    <p class="text-white-50 small mb-0">Es gratis y rápido</p>
                </div>
                <button type="button" class="close" data-dismiss="modal"
                        style="position:absolute;top:12px;right:16px;color:#fff;opacity:.7;">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body px-4 py-4">
                <div id="registroError"   class="alert alert-danger  py-2" style="display:none;font-size:13px;"></div>
                <div id="registroExito"   class="alert alert-success py-2" style="display:none;font-size:13px;"></div>

                <form id="formRegistroModal">
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="small font-weight-bold" style="color:var(--azul);">Usuario</label>
                            <input type="text" name="usuario" class="form-control" placeholder="nombreusuario" style="border-radius:8px;">
                        </div>
                        <div class="form-group col-6">
                            <label class="small font-weight-bold" style="color:var(--azul);">Contraseña</label>
                            <input type="password" name="password" class="form-control" placeholder="Mínimo 4 caracteres" style="border-radius:8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-4">
                            <label class="small font-weight-bold" style="color:var(--azul);">CI</label>
                            <input type="text" name="ci" class="form-control" placeholder="12345678" style="border-radius:8px;">
                        </div>
                        <div class="form-group col-8">
                            <label class="small font-weight-bold" style="color:var(--azul);">Nombres</label>
                            <input type="text" name="nombres" class="form-control" placeholder="Tu nombre" style="border-radius:8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="small font-weight-bold" style="color:var(--azul);">Apellido Paterno</label>
                            <input type="text" name="apPaterno" class="form-control" placeholder="Apellido" style="border-radius:8px;">
                        </div>
                        <div class="form-group col-6">
                            <label class="small font-weight-bold" style="color:var(--azul);">Apellido Materno</label>
                            <input type="text" name="apMaterno" class="form-control" placeholder="Apellido" style="border-radius:8px;">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-6">
                            <label class="small font-weight-bold" style="color:var(--azul);">Correo electrónico</label>
                            <input type="email" name="correo" class="form-control" placeholder="correo@ejemplo.com" style="border-radius:8px;">
                        </div>
                        <div class="form-group col-6">
                            <label class="small font-weight-bold" style="color:var(--azul);">Celular</label>
                            <input type="text" name="nroCelular" class="form-control" placeholder="70000000" style="border-radius:8px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="small font-weight-bold" style="color:var(--azul);">Dirección</label>
                        <input type="text" name="direccion" class="form-control" placeholder="Tu dirección" style="border-radius:8px;">
                    </div>

                    <button type="submit" id="btnRegistroModal" class="btn btn-block font-weight-bold"
                            style="background:var(--amarillo);color:#fff;border-radius:8px;padding:10px;">
                        <i class="bi bi-person-check-fill mr-1"></i>Crear mi cuenta
                    </button>
                </form>

                <hr class="my-3">
                <p class="text-center text-muted small mb-0">
                    ¿Ya tenés cuenta?
                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#modalLogin"
                       style="color:var(--azul);font-weight:700;">Iniciá sesión</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form    = document.getElementById('formRegistroModal');
    var errDiv  = document.getElementById('registroError');
    var okDiv   = document.getElementById('registroExito');
    var btn     = document.getElementById('btnRegistroModal');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        errDiv.style.display = 'none';
        okDiv.style.display  = 'none';

        var fd = new FormData(form);
        var vacio = false;
        fd.forEach(function (v) { if (!v.trim()) vacio = true; });
        if (vacio) { errDiv.textContent = 'Todos los campos son obligatorios.'; errDiv.style.display = ''; return; }

        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Creando cuenta...';

        fetch('api/registro.php', { method: 'POST', body: fd })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.ok) {
                    okDiv.textContent   = '¡Cuenta creada! Bienvenido a Electrohogar.';
                    okDiv.style.display = '';
                    setTimeout(function () { window.location.reload(); }, 1200);
                } else {
                    errDiv.textContent   = d.mensaje;
                    errDiv.style.display = '';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-person-check-fill mr-1"></i>Crear mi cuenta';
                }
            })
            .catch(function () {
                errDiv.textContent = 'Error de conexión.';
                errDiv.style.display = '';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-person-check-fill mr-1"></i>Crear mi cuenta';
            });
    });
});
</script>

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

<script>
/* ====== Carrito Drawer ====== */
function abrirCarrito() {
    document.getElementById('carritoOverlay').classList.add('activo');
    document.getElementById('carritoDrawer').classList.add('activo');
    cargarCarritoDrawer();
}

function cerrarCarrito() {
    document.getElementById('carritoOverlay').classList.remove('activo');
    document.getElementById('carritoDrawer').classList.remove('activo');
}

function cargarCarritoDrawer() {
    fetch('api/carrito.php?accion=obtener')
        .then(function(r) { return r.json(); })
        .then(function(d) { renderCarritoDrawer(d); })
        .catch(function() {});
}

function renderCarritoDrawer(d) {
    var body   = document.getElementById('carritoDrawerBody');
    var footer = document.getElementById('carritoDrawerFooter');
    var badge  = document.getElementById('carritoContador');

    if (d.cantidad > 0) {
        badge.textContent   = d.cantidad;
        badge.style.display = '';
    } else {
        badge.style.display = 'none';
    }

    if (!d.items || d.items.length === 0) {
        body.innerHTML      = '<div class="carrito-empty"><i class="bi bi-cart-x"></i>Tu carrito está vacío</div>';
        footer.style.display = 'none';
        return;
    }

    var html = '';
    d.items.forEach(function(item) {
        var img = item.imagen
            ? 'recursos/imagenes/' + item.imagen
            : 'recursos/imagenes/no-image.png';
        var precio   = parseFloat(item.precio).toFixed(2);
        var subtotal = parseFloat(item.subtotal).toFixed(2);
        html += '<div class="carrito-item" id="citem-' + item.id_producto + '">'
            + '<img src="' + img + '" alt="' + item.nombre + '" onerror="this.src=\'recursos/imagenes/no-image.png\'">'
            + '<div class="carrito-item-info">'
            +   '<div class="nombre">' + item.nombre + '</div>'
            +   '<div class="precio">Bs. ' + precio + ' c/u</div>'
            +   '<div class="carrito-item-controls">'
            +     '<button onclick="cambiarCantidadDrawer(' + item.id_producto + ',' + (item.cantidad - 1) + ')">&#8722;</button>'
            +     '<span>' + item.cantidad + '</span>'
            +     '<button onclick="cambiarCantidadDrawer(' + item.id_producto + ',' + (item.cantidad + 1) + ')">&#43;</button>'
            +   '</div>'
            + '</div>'
            + '<div class="d-flex flex-column align-items-end">'
            +   '<span class="carrito-item-subtotal">Bs. ' + subtotal + '</span>'
            +   '<button class="btn-eliminar-item mt-1" onclick="eliminarItemDrawer(' + item.id_producto + ')" title="Eliminar">'
            +     '<i class="bi bi-trash"></i>'
            +   '</button>'
            + '</div>'
            + '</div>';
    });

    body.innerHTML = html;
    document.getElementById('carritoDrawerTotal').textContent = 'Bs. ' + parseFloat(d.total).toFixed(2);
    footer.style.display = '';
}

function cambiarCantidadDrawer(id, nuevaCantidad) {
    fetch('api/carrito.php?accion=actualizar&id=' + id + '&cantidad=' + nuevaCantidad)
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (!d.ok) {
                mostrarAlertaDrawer(d.mensaje);
            } else {
                renderCarritoDrawer(d);
            }
        })
        .catch(function() {});
}

function mostrarAlertaDrawer(msg) {
    var body = document.getElementById('carritoDrawerBody');
    var alerta = document.createElement('div');
    alerta.className = 'alert alert-danger py-2 px-3 mx-0 mb-2';
    alerta.style.fontSize = '13px';
    alerta.textContent = msg;
    body.insertAdjacentElement('afterbegin', alerta);
    setTimeout(function() { if (alerta.parentNode) alerta.parentNode.removeChild(alerta); }, 3000);
}

function eliminarItemDrawer(id) {
    fetch('api/carrito.php?accion=eliminar&id=' + id)
        .then(function(r) { return r.json(); })
        .then(function(d) { renderCarritoDrawer(d); })
        .catch(function() {});
}

/* Actualizar badge al cargar la página */
document.addEventListener('DOMContentLoaded', function () {
    fetch('api/carrito.php?accion=obtener')
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var badge = document.getElementById('carritoContador');
            if (badge && d.cantidad > 0) {
                badge.textContent   = d.cantidad;
                badge.style.display = '';
            }
        })
        .catch(function() {});
});
</script>
