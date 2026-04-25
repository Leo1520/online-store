<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<!-- Toast -->
<div aria-live="polite" aria-atomic="true" style="position:fixed;top:80px;right:20px;z-index:9999;min-width:290px;">
    <div id="toastCarrito" class="toast shadow" role="alert" data-delay="3000">
        <div class="toast-header bg-success text-white">
            <strong class="mr-auto"><i class="bi bi-cart-check"></i> Carrito</strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body" id="toastMensaje"></div>
    </div>
</div>

<!-- ═══ CAROUSEL ═══ -->
<div id="carouselHero" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#carouselHero" data-slide-to="0" class="active"></li>
        <li data-target="#carouselHero" data-slide-to="1"></li>
        <li data-target="#carouselHero" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <!-- Slide 1 -->
        <div class="carousel-item active">
            <div style="background: linear-gradient(135deg, #1B3A6B 0%, #2751a3 60%, #F5A623 100%); height:400px; display:flex; align-items:center;">
                <div class="container text-white">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <span style="background:var(--amarillo);color:#fff;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;letter-spacing:1px;">OFERTA ESPECIAL</span>
                            <h1 style="font-size:42px;font-weight:900;margin-top:12px;line-height:1.1;">Electrodomésticos<br>de Alta Calidad</h1>
                            <p style="font-size:16px;opacity:.85;margin:12px 0 24px;">Las mejores marcas al mejor precio. Envío a todo Bolivia.</p>
                            <a href="#productos" class="btn btn-amarillo btn-lg px-4" style="border-radius:25px;">
                                <i class="bi bi-lightning-charge-fill mr-1"></i> Ver ofertas
                            </a>
                        </div>
                        <div class="col-md-5 text-center d-none d-md-block">
                            <i class="bi bi-tv" style="font-size:120px;opacity:.25;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slide 2 -->
        <div class="carousel-item">
            <div style="background: linear-gradient(135deg, #0f2340 0%, #1B3A6B 50%, #1a6b3a 100%); height:400px; display:flex; align-items:center;">
                <div class="container text-white">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <span style="background:#28a745;color:#fff;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;">NUEVA COLECCIÓN</span>
                            <h1 style="font-size:42px;font-weight:900;margin-top:12px;line-height:1.1;">Tecnología para<br>tu Hogar</h1>
                            <p style="font-size:16px;opacity:.85;margin:12px 0 24px;">Refrigeradoras, lavadoras, cocinas y más con garantía incluida.</p>
                            <a href="#productos" class="btn btn-light btn-lg px-4" style="border-radius:25px;color:var(--azul);font-weight:700;">
                                <i class="bi bi-bag-fill mr-1"></i> Comprar ahora
                            </a>
                        </div>
                        <div class="col-md-5 text-center d-none d-md-block">
                            <i class="bi bi-house-heart" style="font-size:120px;opacity:.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Slide 3 -->
        <div class="carousel-item">
            <div style="background: linear-gradient(135deg, #2c1b6b 0%, #1B3A6B 50%, #6b1b3a 100%); height:400px; display:flex; align-items:center;">
                <div class="container text-white">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <span style="background:#dc3545;color:#fff;padding:4px 14px;border-radius:20px;font-size:12px;font-weight:700;">PAGO SEGURO</span>
                            <h1 style="font-size:42px;font-weight:900;margin-top:12px;line-height:1.1;">Compra con<br>Total Seguridad</h1>
                            <p style="font-size:16px;opacity:.85;margin:12px 0 24px;">Paga con tarjeta demo o QR. Factura inmediata en cada compra.</p>
                            <a href="index.php?pagina=registro" class="btn btn-amarillo btn-lg px-4" style="border-radius:25px;">
                                <i class="bi bi-person-plus-fill mr-1"></i> Crear cuenta
                            </a>
                        </div>
                        <div class="col-md-5 text-center d-none d-md-block">
                            <i class="bi bi-shield-check" style="font-size:120px;opacity:.2;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#carouselHero" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </a>
    <a class="carousel-control-next" href="#carouselHero" role="button" data-slide="next">
        <span class="carousel-control-next-icon"></span>
    </a>
</div>

<!-- ═══ BENEFICIOS ═══ -->
<div class="beneficios py-3 shadow-sm">
    <div class="container">
        <div class="row text-center">
            <div class="col-6 col-md-3 beneficio-item py-2">
                <i class="bi bi-truck"></i>
                <h6>Envío a Bolivia</h6>
                <p>Despacho a todo el país</p>
            </div>
            <div class="col-6 col-md-3 beneficio-item py-2">
                <i class="bi bi-shield-check"></i>
                <h6>Garantía oficial</h6>
                <p>Todos los productos garantizados</p>
            </div>
            <div class="col-6 col-md-3 beneficio-item py-2">
                <i class="bi bi-credit-card"></i>
                <h6>Pago seguro</h6>
                <p>Tarjeta demo y QR disponible</p>
            </div>
            <div class="col-6 col-md-3 beneficio-item py-2">
                <i class="bi bi-headset"></i>
                <h6>Soporte 24/7</h6>
                <p>Siempre listos para ayudarte</p>
            </div>
        </div>
    </div>
</div>

<!-- ═══ OFERTAS DEL DÍA ═══ -->
<div class="oferta-section mt-4 mb-2">
    <div class="container">

        <!-- Cabecera de sección -->
        <div class="oferta-header">
            <div class="oferta-header-left">
                <div class="oferta-fuego">
                    <i class="bi bi-lightning-charge-fill"></i>
                </div>
                <div>
                    <div class="oferta-titulo">OFERTAS DEL DÍA</div>
                    <div class="oferta-subtitulo">¡Ofertas únicas que no vuelven!</div>
                </div>
            </div>
            <div class="oferta-header-right">
                <span class="oc-termina">Termina en:</span>
                <div class="oferta-contador">
                    <div class="oc-bloque">
                        <span class="oc-num" id="ocHoras">00</span>
                        <span class="oc-lbl">Horas</span>
                    </div>
                    <span class="oc-sep">:</span>
                    <div class="oc-bloque">
                        <span class="oc-num" id="ocMinutos">00</span>
                        <span class="oc-lbl">Min</span>
                    </div>
                    <span class="oc-sep">:</span>
                    <div class="oc-bloque">
                        <span class="oc-num" id="ocSegundos">00</span>
                        <span class="oc-lbl">Seg</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas productos -->
        <div class="oferta-grid" id="ofertaProductos">
            <div class="text-center py-4" style="color:#fff;opacity:.7;width:100%;">
                <span class="spinner-border spinner-border-sm"></span> Cargando ofertas...
            </div>
        </div>

    </div>
</div>

<style>
@keyframes pulso {
    0%,100% { transform: scale(1); }
    50%      { transform: scale(1.08); }
}
@keyframes brillar {
    0%   { background-position: -200% center; }
    100% { background-position: 200% center; }
}

.oferta-section {
    background: linear-gradient(135deg, #1B3A6B 0%, #0f2340 50%, #1a3a6b 100%);
    padding: 28px 0 32px;
    position: relative;
    overflow: hidden;
}
.oferta-section::before {
    content: '';
    position: absolute; inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}

/* Cabecera */
.oferta-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 22px; flex-wrap: wrap; gap: 16px;
}
.oferta-header-left { display: flex; align-items: center; gap: 14px; }
.oferta-fuego {
    width: 52px; height: 52px; border-radius: 50%;
    background: var(--amarillo);
    display: flex; align-items: center; justify-content: center;
    font-size: 26px; color: #fff;
    box-shadow: 0 0 20px rgba(245,166,35,.6);
    animation: pulso 2s ease-in-out infinite;
    flex-shrink: 0;
}
.oferta-titulo {
    font-size: 22px; font-weight: 900; color: var(--amarillo);
    letter-spacing: 2px; text-transform: uppercase;
    text-shadow: 0 2px 8px rgba(245,166,35,.4);
}
.oferta-subtitulo { font-size: 13px; color: rgba(255,255,255,.7); margin-top: 2px; }

/* Contador */
.oferta-header-right { display: flex; align-items: center; gap: 12px; }
.oc-termina { font-size: 12px; color: rgba(255,255,255,.6); font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }
.oferta-contador { display: flex; align-items: center; gap: 6px; }
.oc-bloque { display: flex; flex-direction: column; align-items: center; }
.oc-num {
    font-size: 28px; font-weight: 900; color: #fff;
    background: rgba(255,255,255,.15);
    border: 1px solid rgba(255,255,255,.25);
    backdrop-filter: blur(4px);
    border-radius: 8px; padding: 4px 10px;
    min-width: 52px; text-align: center; line-height: 1.2;
}
.oc-lbl { font-size: 9px; color: rgba(255,255,255,.5); margin-top: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; }
.oc-sep { font-size: 24px; font-weight: 900; color: var(--amarillo); margin-bottom: 14px; }

/* Grid de tarjetas */
.oferta-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
}
@media (max-width: 767px) { .oferta-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .oferta-grid { grid-template-columns: 1fr; } }

.oferta-prod-card {
    background: #fff;
    border-radius: 14px;
    overflow: hidden;
    position: relative;
    transition: transform .25s, box-shadow .25s;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
}
.oferta-prod-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(0,0,0,.3);
}

/* Badge descuento */
.op-badge-desc {
    position: absolute; top: 12px; left: 12px;
    background: #e53935; color: #fff;
    font-size: 12px; font-weight: 900;
    padding: 4px 10px; border-radius: 20px;
    z-index: 2; box-shadow: 0 2px 8px rgba(229,57,53,.4);
}

/* Badge stock urgencia */
.op-badge-stock {
    position: absolute; top: 12px; right: 12px;
    background: #fff3e0; color: #e65100;
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 20px;
    z-index: 2; border: 1px solid #ffcc80;
}

.op-img-wrap {
    background: #f8f9ff;
    display: flex; align-items: center; justify-content: center;
    height: 160px; padding: 14px;
}
.op-img-wrap img {
    max-height: 132px; max-width: 100%;
    object-fit: contain; transition: transform .3s;
}
.oferta-prod-card:hover .op-img-wrap img { transform: scale(1.06); }

.op-body { padding: 14px 14px 16px; }
.op-categoria {
    font-size: 10px; font-weight: 700; text-transform: uppercase;
    color: var(--azul-claro); letter-spacing: .8px; margin-bottom: 4px;
}
.op-nombre {
    font-size: 13px; font-weight: 700; color: #222;
    line-height: 1.35; margin-bottom: 10px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.op-precios { display: flex; align-items: baseline; gap: 8px; margin-bottom: 4px; }
.op-precio-ahora { font-size: 20px; font-weight: 900; color: var(--azul); }
.op-precio-antes { font-size: 12px; color: #bbb; text-decoration: line-through; }
.op-cuotas { font-size: 11px; color: #888; margin-bottom: 12px; }

.btn-oferta {
    width: 100%;
    background: linear-gradient(90deg, var(--azul) 0%, var(--azul-claro) 100%);
    color: #fff; border: none; border-radius: 8px;
    padding: 9px; font-size: 13px; font-weight: 700;
    cursor: pointer; transition: opacity .2s, transform .1s;
    display: flex; align-items: center; justify-content: center; gap: 6px;
}
.btn-oferta:hover  { opacity: .9; transform: scale(1.02); }
.btn-oferta:active { transform: scale(.98); }
.btn-oferta:disabled { opacity: .55; cursor: not-allowed; transform: none; }
.btn-oferta.agregado { background: linear-gradient(90deg, #1a6b3a, #28a745); }
.btn-oferta.sin-stock { background: #ccc; }
</style>

<script>
/* Contador regresivo — reinicia cada 24h desde medianoche */
(function () {
    function tick() {
        var ahora     = new Date();
        var manana    = new Date(ahora);
        manana.setHours(24, 0, 0, 0);
        var diff = Math.floor((manana - ahora) / 1000);
        var h = Math.floor(diff / 3600);
        var m = Math.floor((diff % 3600) / 60);
        var s = diff % 60;
        var pad = function(n) { return String(n).padStart(2, '0'); };
        document.getElementById('ocHoras').textContent    = pad(h);
        document.getElementById('ocMinutos').textContent  = pad(m);
        document.getElementById('ocSegundos').textContent = pad(s);
    }
    tick();
    setInterval(tick, 1000);
})();

/* Cargar 3 productos destacados para la sección oferta */
(function () {
    fetch('api/productos.php')
        .then(function(r) { return r.json(); })
        .then(function(d) {
            var activos = (d.productos || []).filter(function(p) {
                return (p.estado || '').toLowerCase() === 'activo' && parseInt(p.stock) > 0;
            });
            // Tomar 3 productos: los de mayor precio (más llamativos como oferta)
            activos.sort(function(a, b) { return parseFloat(b.precioVigente) - parseFloat(a.precioVigente); });
            var seleccion = activos.slice(0, 3);
            var cont = document.getElementById('ofertaProductos');
            if (!seleccion.length) { cont.innerHTML = ''; return; }

            var html = '';
            seleccion.forEach(function(p) {
                var img    = p.imagen ? 'recursos/imagenes/' + p.imagen : 'recursos/imagenes/no-image.png';
                var pv     = parseFloat(p.precioVigente) || 0;
                var pp     = parseFloat(p.precioPropuesto) || 0;
                var hayDesc = pp > 0 && pv < pp;
                var pct    = hayDesc ? Math.round(((pp - pv) / pp) * 100) : 0;
                var ahora  = pv.toFixed(2);
                var cuota  = (pv / 12).toFixed(2);
                var stock  = parseInt(p.stock) || 0;
                html += '<div class="oferta-prod-card">'
                    + (hayDesc ? '<span class="op-badge-desc">−' + pct + '%</span>' : '')
                    + (stock <= 5 && stock > 0 ? '<span class="op-badge-stock">¡Últimas ' + stock + '!</span>' : '')
                    + '<div class="op-img-wrap">'
                    +   '<a href="index.php?pagina=producto&id=' + p.id_producto + '">'
                    +     '<img src="' + img + '" alt="' + p.nombre + '" onerror="this.src=\'recursos/imagenes/no-image.png\'">'
                    +   '</a>'
                    + '</div>'
                    + '<div class="op-body">'
                    +   '<div class="op-categoria">' + (p.categoria || 'Producto') + '</div>'
                    +   '<div class="op-nombre">' + p.nombre + '</div>'
                    +   '<div class="op-precios">'
                    +     '<span class="op-precio-ahora">Bs. ' + ahora + '</span>'
                    +     (hayDesc ? '<span class="op-precio-antes">Bs. ' + pp.toFixed(2) + '</span>' : '')
                    +   '</div>'
                    +   '<div class="op-cuotas"><i class="bi bi-credit-card mr-1"></i>12 cuotas de Bs. ' + cuota + '</div>'
                    +   '<button class="btn-oferta btn-agregar-oferta" data-id="' + p.id_producto + '" data-stock="' + stock + '">'
                    +     '<i class="bi bi-cart-plus"></i> Agregar al carrito'
                    +   '</button>'
                    + '</div>'
                    + '</div>';
            });
            cont.innerHTML = html;

            cont.addEventListener('click', function(e) {
                var btn = e.target.closest('.btn-agregar-oferta');
                if (!btn || btn.disabled) return;
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Agregando...';
                fetch('api/carrito.php?accion=agregar&id=' + btn.dataset.id)
                    .then(function(r) { return r.json(); })
                    .then(function(res) {
                        if (res.ok) {
                            btn.classList.add('agregado');
                            btn.innerHTML = '<i class="bi bi-check-lg"></i> ¡Agregado!';
                            var badge = document.getElementById('carritoContador');
                            if (badge) { badge.textContent = res.cantidad; badge.style.display = 'flex'; }
                            setTimeout(function() {
                                btn.classList.remove('agregado');
                                btn.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al carrito';
                                btn.disabled = false;
                            }, 2000);
                        } else {
                            btn.classList.add('sin-stock');
                            btn.innerHTML = '<i class="bi bi-x-circle"></i> Sin stock';
                        }
                    })
                    .catch(function() { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al carrito'; });
            });
        });
})();
</script>

<!-- ═══ PRODUCTOS ═══ -->
<div class="container mt-4" id="productos">

    <!-- Inputs ocultos — la lógica JS los sigue usando -->
    <input type="hidden" id="filtroBusqueda">
    <select id="filtroCategoria" style="display:none;"><option value="0">Todas</option></select>
    <input type="hidden" id="filtroPrecioMin">
    <input type="hidden" id="filtroPrecioMax">

    <!-- inputs ocultos para limpiar filtros -->
    <span id="contadorResultados" style="display:none;"></span>
    <select id="filtroOrden" style="display:none;"></select>
    <select id="porPagina" style="display:none;"><option value="12" selected>12</option></select>
    <button id="btnLimpiar" style="display:none;"></button>

    <!-- Spinner -->
    <div id="cargando" class="text-center py-5">
        <div class="spinner-border" role="status" style="color:var(--azul);width:3rem;height:3rem;"></div>
        <p class="mt-3 text-muted">Cargando productos...</p>
    </div>

    <!-- Grid -->
    <div id="gridProductos" class="row" style="display:none;"></div>

    <!-- Paginación -->
    <div id="paginacion" class="d-flex justify-content-between align-items-center mt-2 mb-4" style="display:none!important;">
        <div class="text-muted small" id="infoPagina"></div>
        <nav><ul class="pagination pagination-sm mb-0" id="listaPaginas"></ul></nav>
        <div></div>
    </div>

    <!-- Template tarjeta -->
    <template id="plantillaProducto">
        <div class="col-md-4 col-sm-6 mb-4">
            <div class="card card-producto h-100 shadow-sm">
                <a class="producto-imagen-link" href="#" target="_blank">
                    <div class="d-flex align-items-center justify-content-center bg-white"
                         style="height:190px;overflow:hidden;border-radius:10px 10px 0 0;padding:10px;">
                        <img class="producto-imagen img-fluid"
                             style="max-height:170px;object-fit:contain;" alt="">
                    </div>
                </a>
                <div class="card-body d-flex flex-column px-3 pb-3 pt-2">
                    <span class="badge badge-cat mb-1 align-self-start producto-categoria" style="font-size:10px;"></span>
                    <h6 class="card-title producto-nombre mb-1" style="font-size:14px;font-weight:700;color:#222;line-height:1.3;"></h6>
                    <p class="card-text text-muted producto-descripcion mb-2" style="font-size:12px;flex-grow:1;"></p>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="precio-tag">$<span class="producto-precio"></span></span>
                        <span class="text-muted" style="font-size:11px;">Stock: <span class="producto-stock"></span></span>
                    </div>
                    <div>
                        <a class="btn btn-sm btn-outline-secondary btn-block btn-detalle mb-1" href="" style="border-radius:6px;font-size:12px;">
                            <i class="bi bi-eye mr-1"></i>Ver detalle
                        </a>
                        <button class="btn btn-azul btn-sm btn-agregar btn-block" data-id="" style="border-radius:6px;font-size:12px;">
                            <i class="bi bi-cart-plus mr-1"></i>Agregar al carrito
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
(function () {
    var grid         = document.getElementById('gridProductos');
    var cargando     = document.getElementById('cargando');
    var plantilla    = document.getElementById('plantillaProducto');
    var toast        = document.getElementById('toastCarrito');
    var toastMsg     = document.getElementById('toastMensaje');
    var contador     = document.getElementById('contadorResultados');
    var paginacion   = document.getElementById('paginacion');
    var listaPaginas = document.getElementById('listaPaginas');
    var infoPagina   = document.getElementById('infoPagina');
    var selectPP     = document.getElementById('porPagina');
    var debounceId   = null;

    var todosLosProductos = [];
    var carritoActual     = {}; // { id_producto: cantidad_en_carrito }
    var paginaActual      = 1;

    var inputBusqueda   = document.getElementById('filtroBusqueda');       // hidden
    var selectCategoria = document.getElementById('filtroCategoria');      // hidden select
    var inputMin        = document.getElementById('filtroPrecioMin');      // hidden
    var inputMax        = document.getElementById('filtroPrecioMax');      // hidden
    var selectOrden     = document.getElementById('filtroOrden');
    var btnLimpiar      = document.getElementById('btnLimpiar');


    function obtenerFiltros() {
        return new URLSearchParams({
            nombre:    inputBusqueda.value.trim(),
            categoria: selectCategoria.value,
            precioMin: inputMin.value || 0,
            precioMax: inputMax.value || 0,
            orden:     selectOrden.value,
        });
    }

    btnLimpiar.addEventListener('click', function () {
        inputBusqueda.value = ''; selectCategoria.value = '0';
        inputMin.value = ''; inputMax.value = ''; selectOrden.value = '';
        cargarProductos();
    });

    selectOrden.addEventListener('change', cargarProductos);
    selectPP.addEventListener('change', function () { paginaActual = 1; mostrarPagina(1); });

    function mostrarToast(msg, ok) {
        toastMsg.textContent = msg;
        toast.querySelector('.toast-header').className = 'toast-header ' + (ok ? 'bg-success' : 'bg-danger') + ' text-white';
        $(toast).toast('show');
    }

    function actualizarContadorNavbar(n) {
        var b = document.getElementById('carritoContador');
        if (!b) return;
        b.textContent   = n;
        b.style.display = n > 0 ? 'flex' : 'none';
    }

    function renderProductos(productos) {
        grid.innerHTML = '';
        if (!productos || productos.length === 0) {
            grid.innerHTML = '<div class="col-12 text-center py-5"><i class="bi bi-search" style="font-size:3rem;color:#ccc;"></i><p class="mt-3 text-muted">No se encontraron productos.</p></div>';
            paginacion.style.display = 'none';
            return;
        }
        productos.forEach(function (p) {
            var nodo = document.importNode(plantilla.content, true);
            var url  = 'index.php?pagina=producto&id=' + p.id_producto;
            nodo.querySelector('.producto-imagen-link').href        = url;
            nodo.querySelector('.producto-imagen').src              = 'recursos/imagenes/' + p.imagen;
            nodo.querySelector('.producto-imagen').alt              = p.nombre;
            nodo.querySelector('.producto-nombre').textContent      = p.nombre;
            nodo.querySelector('.producto-descripcion').textContent = p.descripcion;
            nodo.querySelector('.producto-categoria').textContent   = p.categoria || '';
            var pv2 = parseFloat(p.precioVigente) || 0;
            var pp2 = parseFloat(p.precioPropuesto) || 0;
            var precioHtml = 'Bs. ' + pv2.toFixed(2);
            if (pp2 > 0 && pv2 < pp2) {
                var pct2 = Math.round(((pp2 - pv2) / pp2) * 100);
                precioHtml = '<span class="badge bg-danger me-1">-' + pct2 + '%</span>'
                           + '<span class="fw-bold">Bs. ' + pv2.toFixed(2) + '</span>'
                           + ' <small class="text-muted text-decoration-line-through">Bs. ' + pp2.toFixed(2) + '</small>';
            }
            nodo.querySelector('.producto-precio').innerHTML = precioHtml;
            var stockReal   = parseInt(p.stock) || 0;
            var enCarrito   = carritoActual[p.id_producto] || 0;
            var disponible  = Math.max(0, stockReal - enCarrito);
            nodo.querySelector('.producto-stock').textContent       = disponible;
            nodo.querySelector('.btn-detalle').href                 = url;
            var btn = nodo.querySelector('.btn-agregar');
            btn.dataset.id = p.id_producto;
            if (disponible === 0) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-x-circle mr-1"></i>Sin stock'; }
            grid.appendChild(nodo);
        });
    }

    function mostrarPagina(pagina) {
        var pp    = parseInt(selectPP.value) || 12;
        var total = todosLosProductos.length;
        var totalPags = Math.ceil(total / pp) || 1;
        paginaActual  = Math.max(1, Math.min(pagina, totalPags));
        var inicio = (paginaActual - 1) * pp;
        renderProductos(todosLosProductos.slice(inicio, inicio + pp));
        infoPagina.textContent = 'Mostrando ' + (inicio + 1) + '–' + Math.min(inicio + pp, total) + ' de ' + total;
        listaPaginas.innerHTML = '';
        function li(txt, pg, act, dis) {
            var el = document.createElement('li');
            el.className = 'page-item' + (act ? ' active' : '') + (dis ? ' disabled' : '');
            var a = document.createElement('a'); a.className = 'page-link'; a.href = '#'; a.innerHTML = txt;
            if (!dis && !act) a.addEventListener('click', function (e) { e.preventDefault(); mostrarPagina(pg); window.scrollTo({top: grid.offsetTop - 80, behavior:'smooth'}); });
            el.appendChild(a); return el;
        }
        listaPaginas.appendChild(li('&laquo;', paginaActual - 1, false, paginaActual === 1));
        var ri = Math.max(1, paginaActual - 2), rf = Math.min(totalPags, ri + 4); ri = Math.max(1, rf - 4);
        for (var i = ri; i <= rf; i++) listaPaginas.appendChild(li(i, i, i === paginaActual, false));
        listaPaginas.appendChild(li('&raquo;', paginaActual + 1, false, paginaActual === totalPags));
        paginacion.style.display = total > 0 ? 'flex' : 'none';
    }

    grid.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-agregar');
        if (!btn || btn.disabled) return;
        var id = parseInt(btn.dataset.id);
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        fetch('api/carrito.php?accion=agregar&id=' + id)
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (d.ok) {
                    carritoActual[id] = (carritoActual[id] || 0) + 1;
                    actualizarContadorNavbar(d.cantidad);
                    actualizarBtnStock(btn, id);
                } else {
                    // Stock agotado: deshabilitar botón directamente
                    btn.disabled = true;
                    btn.innerHTML = '<i class="bi bi-x-circle mr-1"></i>Sin stock';
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-cart-plus mr-1"></i>Agregar al carrito';
            });
    });

    function actualizarBtnStock(btn, id) {
        var producto = todosLosProductos.find(function(p) { return p.id_producto == id; });
        if (!producto) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cart-plus mr-1"></i>Agregar al carrito'; return; }
        var stockReal = parseInt(producto.stock) || 0;
        var enCarrito = carritoActual[id] || 0;
        var disponible = stockReal - enCarrito;
        // Actualizar etiqueta de stock en la tarjeta
        var tarjeta = btn.closest('.col-md-4, .col-sm-6');
        if (tarjeta) {
            var spanStock = tarjeta.querySelector('.producto-stock');
            if (spanStock) spanStock.textContent = Math.max(0, disponible);
        }
        if (disponible <= 0) {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-x-circle mr-1"></i>Sin stock';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cart-plus mr-1"></i>Agregar al carrito';
        }
    }

    function cargarProductos() {
        cargando.style.display = ''; grid.style.display = 'none'; paginacion.style.display = 'none'; contador.textContent = '';
        fetch('api/productos.php?' + obtenerFiltros())
            .then(function (r) { return r.json(); })
            .then(function (d) {
                cargando.style.display = 'none'; grid.style.display = '';
                todosLosProductos = d.productos || []; paginaActual = 1; mostrarPagina(1);
                var filtroActivo = inputBusqueda.value.trim() || selectCategoria.value !== '0' || inputMin.value || inputMax.value || selectOrden.value;
                contador.textContent = filtroActivo ? d.total + ' producto(s) encontrado(s).' : '';
            })
            .catch(function () { cargando.innerHTML = '<p class="text-danger">Error al cargar productos.</p>'; });
    }

    function cargarCategorias() {
        fetch('api/productos.php')
            .then(function (r) { return r.json(); })
            .then(function (d) {
                (d.categorias || []).forEach(function (c) {
                    var o = document.createElement('option'); o.value = c.cod; o.textContent = c.nombre;
                    selectCategoria.appendChild(o);
                });
                cargando.style.display = 'none'; grid.style.display = '';
                todosLosProductos = d.productos || []; paginaActual = 1;

                // Leer ?categoria= de la URL (viene del mega-menú)
                var params = new URLSearchParams(window.location.search);
                var catParam = params.get('categoria');
                if (catParam && parseInt(catParam) > 0) {
                    selectCategoria.value = catParam;
                }
                mostrarPagina(1);
            });
    }

    fetch('api/carrito.php?accion=obtener').then(function (r) { return r.json(); }).then(function (d) {
        actualizarContadorNavbar(d.cantidad);
        (d.items || []).forEach(function(item) { carritoActual[item.id_producto] = item.cantidad; });
    });

    // Leer parámetro ?buscar= de la URL (viene del buscador del encabezado desde otra página)
    (function () {
        var params  = new URLSearchParams(window.location.search);
        var termino = params.get('buscar');
        if (termino && termino.trim().length >= 3) {
            // Pre-llenar buscador del encabezado y el filtro interno
            var hb = document.getElementById('headerBusqueda');
            if (hb) hb.value = termino.trim();
            // esperar a que cargarCategorias() pobló los productos
            var intentos = 0;
            var esperar = setInterval(function () {
                intentos++;
                var fb = document.getElementById('filtroBusqueda');
                if (fb && window.todosLosProductos && todosLosProductos.length > 0) {
                    clearInterval(esperar);
                    fb.value = termino.trim();
                    fb.dispatchEvent(new Event('input'));
                    window.scrollTo({ top: document.getElementById('gridProductos').offsetTop - 100, behavior: 'smooth' });
                }
                if (intentos > 30) clearInterval(esperar);
            }, 100);
        }
    })();

    cargarCategorias();
}());


</script>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
