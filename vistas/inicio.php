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

<!-- ═══ PRODUCTOS ═══ -->
<div class="container mt-4" id="productos">

    <h2 class="seccion-titulo mb-4">Nuestros Productos</h2>

    <!-- Filtros -->
    <div class="filtros-box p-3 mb-4">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2">
                <label class="small font-weight-bold text-muted"><i class="bi bi-search mr-1"></i>Buscar</label>
                <input type="text" id="filtroBusqueda" class="form-control" placeholder="Nombre del producto...">
            </div>
            <div class="form-group col-md-3 mb-2">
                <label class="small font-weight-bold text-muted"><i class="bi bi-tag mr-1"></i>Categoría</label>
                <select id="filtroCategoria" class="form-control">
                    <option value="0">Todas las categorías</option>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label class="small font-weight-bold text-muted">Precio mín ($)</label>
                <input type="number" id="filtroPrecioMin" class="form-control" placeholder="0" min="0" step="0.01">
            </div>
            <div class="form-group col-md-2 mb-2">
                <label class="small font-weight-bold text-muted">Precio máx ($)</label>
                <input type="number" id="filtroPrecioMax" class="form-control" placeholder="Sin límite" min="0" step="0.01">
            </div>
            <div class="form-group col-md-1 mb-2 d-flex flex-column">
                <label class="small d-block">&nbsp;</label>
                <button id="btnLimpiar" class="btn btn-outline-secondary btn-block" title="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center flex-wrap mt-1">
            <div id="contadorResultados" class="text-muted small"></div>
            <div class="d-flex align-items-center">
                <label class="text-muted small mr-2 mb-0"><i class="bi bi-sort-down mr-1"></i>Ordenar:</label>
                <select id="filtroOrden" class="form-control form-control-sm mr-3" style="width:170px;">
                    <option value="">Relevancia</option>
                    <option value="precio_asc">Precio: menor a mayor</option>
                    <option value="precio_desc">Precio: mayor a menor</option>
                    <option value="nombre_asc">Nombre: A–Z</option>
                    <option value="nombre_desc">Nombre: Z–A</option>
                </select>
                <label class="text-muted small mr-2 mb-0">Por página:</label>
                <select id="porPagina" class="form-control form-control-sm" style="width:70px;">
                    <option value="6">6</option>
                    <option value="12" selected>12</option>
                    <option value="24">24</option>
                    <option value="48">48</option>
                </select>
            </div>
        </div>
    </div>

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
    var paginaActual      = 1;

    var inputBusqueda   = document.getElementById('filtroBusqueda');
    var selectCategoria = document.getElementById('filtroCategoria');
    var inputMin        = document.getElementById('filtroPrecioMin');
    var inputMax        = document.getElementById('filtroPrecioMax');
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

    [inputBusqueda, inputMin, inputMax].forEach(function (el) {
        el.addEventListener('input', function () {
            clearTimeout(debounceId);
            debounceId = setTimeout(cargarProductos, 400);
        });
    });
    selectCategoria.addEventListener('change', cargarProductos);
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
            nodo.querySelector('.producto-precio').textContent      = parseFloat(p.precio).toFixed(2);
            nodo.querySelector('.producto-stock').textContent       = parseInt(p.stock) || 0;
            nodo.querySelector('.btn-detalle').href                 = url;
            var btn = nodo.querySelector('.btn-agregar');
            btn.dataset.id = p.id_producto;
            if ((parseInt(p.stock) || 0) === 0) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-x-circle mr-1"></i>Sin stock'; }
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
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        fetch('api/carrito.php?accion=agregar&id=' + btn.dataset.id)
            .then(function (r) { return r.json(); })
            .then(function (d) { mostrarToast(d.mensaje, d.ok); if (d.ok) actualizarContadorNavbar(d.cantidad); })
            .catch(function () { mostrarToast('Error al conectar.', false); })
            .finally(function () { btn.disabled = false; btn.innerHTML = '<i class="bi bi-cart-plus mr-1"></i>Agregar al carrito'; });
    });

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
                todosLosProductos = d.productos || []; paginaActual = 1; mostrarPagina(1);
            });
    }

    fetch('api/carrito.php?accion=obtener').then(function (r) { return r.json(); }).then(function (d) { actualizarContadorNavbar(d.cantidad); });
    cargarCategorias();
}());
</script>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
