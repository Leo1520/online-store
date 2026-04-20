<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<!-- Toast de notificacion -->
<div aria-live="polite" aria-atomic="true" style="position:fixed;top:70px;right:20px;z-index:9999;min-width:280px;">
    <div id="toastCarrito" class="toast" role="alert" data-delay="3000">
        <div class="toast-header bg-success text-white">
            <strong class="mr-auto"><i class="bi bi-cart-check"></i> Carrito</strong>
            <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast">&times;</button>
        </div>
        <div class="toast-body" id="toastMensaje"></div>
    </div>
</div>

<div class="container mt-4">
    <h1 class="text-center mb-4">Nuestra Tienda</h1>

    <!-- Panel de busqueda y filtros -->
    <div class="card card-body mb-4 shadow-sm">
        <div class="form-row align-items-end">
            <div class="form-group col-md-4 mb-2">
                <label class="font-weight-bold"><i class="bi bi-search"></i> Buscar producto</label>
                <input type="text" id="filtroBusqueda" class="form-control"
                       placeholder="Nombre del producto...">
            </div>
            <div class="form-group col-md-3 mb-2">
                <label class="font-weight-bold"><i class="bi bi-tag"></i> Categoria</label>
                <select id="filtroCategoria" class="form-control">
                    <option value="0">Todas las categorias</option>
                </select>
            </div>
            <div class="form-group col-md-2 mb-2">
                <label class="font-weight-bold">Precio min ($)</label>
                <input type="number" id="filtroPrecioMin" class="form-control"
                       placeholder="0" min="0" step="0.01">
            </div>
            <div class="form-group col-md-2 mb-2">
                <label class="font-weight-bold">Precio max ($)</label>
                <input type="number" id="filtroPrecioMax" class="form-control"
                       placeholder="Sin limite" min="0" step="0.01">
            </div>
            <div class="form-group col-md-2 mb-2">
                <label class="font-weight-bold"><i class="bi bi-sort-down"></i> Ordenar</label>
                <select id="filtroOrden" class="form-control">
                    <option value="">Relevancia</option>
                    <option value="precio_asc">Precio: menor a mayor</option>
                    <option value="precio_desc">Precio: mayor a menor</option>
                    <option value="nombre_asc">Nombre: A-Z</option>
                    <option value="nombre_desc">Nombre: Z-A</option>
                </select>
            </div>
            <div class="form-group col-md-1 mb-2">
                <label class="d-block">&nbsp;</label>
                <button id="btnLimpiar" class="btn btn-outline-secondary btn-block"
                        title="Limpiar filtros">
                    <i class="bi bi-x-circle"></i>
                </button>
            </div>
        </div>
        <div id="contadorResultados" class="text-muted small"></div>
    </div>

    <!-- Spinner de carga -->
    <div id="cargando" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Cargando productos...</p>
    </div>

    <!-- Grid de productos -->
    <div id="gridProductos" class="row" style="display:none;"></div>

    <!-- Paginacion -->
    <div id="paginacion" class="d-flex justify-content-between align-items-center mt-2 mb-4" style="display:none!important;">
        <div class="text-muted small" id="infoPagina"></div>
        <nav>
            <ul class="pagination pagination-sm mb-0" id="listaPaginas"></ul>
        </nav>
        <div class="d-flex align-items-center">
            <label class="text-muted small mr-2 mb-0">Por página:</label>
            <select id="porPagina" class="form-control form-control-sm" style="width:70px;">
                <option value="6">6</option>
                <option value="12" selected>12</option>
                <option value="24">24</option>
                <option value="48">48</option>
            </select>
        </div>
    </div>

    <!-- Plantilla de tarjeta -->
    <template id="plantillaProducto">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <a class="producto-imagen-link" href="#" target="_blank">
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-white"
                         style="height:200px;overflow:hidden;">
                        <img class="producto-imagen img-fluid" alt=""
                             style="max-height:200px;object-fit:contain;">
                    </div>
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title producto-nombre"></h5>
                    <p class="card-text text-muted small producto-descripcion"></p>
                    <p class="card-text mb-1">
                        <small>Marca: <span class="producto-marca"></span></small>
                    </p>
                    <p class="card-text mb-2">
                        <small>Categoria: <span class="producto-categoria"></span></small>
                    </p>
                    <p class="card-text">
                        <strong>Precio: $<span class="producto-precio"></span></strong>
                    </p>
                    <p class="card-text">
                        <small class="text-muted">Stock: <span class="producto-stock"></span></small>
                    </p>
                    <div class="mt-auto pt-2">
                        <a class="btn btn-outline-secondary btn-block btn-detalle mb-1" href="#">
                            <i class="bi bi-eye"></i> Ver detalle
                        </a>
                        <button class="btn btn-primary btn-agregar btn-block" data-id="">
                            <i class="bi bi-cart-plus"></i> Agregar al carrito
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

    // Estado de paginacion
    var todosLosProductos = [];
    var paginaActual      = 1;

    // --- Filtros ---
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

    function limpiarFiltros() {
        inputBusqueda.value   = '';
        selectCategoria.value = '0';
        inputMin.value        = '';
        inputMax.value        = '';
        selectOrden.value     = '';
        cargarProductos();
    }

    btnLimpiar.addEventListener('click', limpiarFiltros);

    [inputBusqueda, inputMin, inputMax].forEach(function (el) {
        el.addEventListener('input', function () {
            clearTimeout(debounceId);
            debounceId = setTimeout(cargarProductos, 400);
        });
    });

    selectCategoria.addEventListener('change', cargarProductos);
    selectOrden.addEventListener('change', cargarProductos);
    selectPP.addEventListener('change', function () {
        paginaActual = 1;
        mostrarPagina(paginaActual);
    });

    // --- Toast ---
    function mostrarToast(mensaje, exito) {
        toastMsg.textContent = mensaje;
        toast.querySelector('.toast-header').className =
            'toast-header ' + (exito ? 'bg-success' : 'bg-danger') + ' text-white';
        $(toast).toast('show');
    }

    // --- Navbar contador ---
    function actualizarContadorNavbar(cantidad) {
        var badge = document.getElementById('carritoContador');
        if (!badge) return;
        badge.textContent    = cantidad;
        badge.style.display  = cantidad > 0 ? 'inline-block' : 'none';
    }

    // --- Render una pagina de productos ---
    function renderProductos(productos) {
        grid.innerHTML = '';

        if (!productos || productos.length === 0) {
            grid.innerHTML =
                '<div class="col-12 text-center py-5">' +
                '<i class="bi bi-search" style="font-size:3rem;color:#ccc;"></i>' +
                '<p class="mt-3 text-muted">No se encontraron productos con esos filtros.</p>' +
                '</div>';
            paginacion.style.display = 'none';
            return;
        }

        productos.forEach(function (p) {
            var nodo   = document.importNode(plantilla.content, true);
            var imgSrc = 'recursos/imagenes/' + p.imagen;

            var detalleUrl = 'index.php?pagina=producto&id=' + p.id_producto;
            nodo.querySelector('.producto-imagen-link').href         = detalleUrl;
            nodo.querySelector('.producto-imagen').src               = imgSrc;
            nodo.querySelector('.producto-imagen').alt               = p.nombre;
            nodo.querySelector('.producto-nombre').textContent       = p.nombre;
            nodo.querySelector('.producto-descripcion').textContent  = p.descripcion;
            nodo.querySelector('.producto-marca').textContent        = p.marca     || 'N/D';
            nodo.querySelector('.producto-categoria').textContent    = p.categoria || 'N/D';
            nodo.querySelector('.producto-precio').textContent       = parseFloat(p.precio).toFixed(2);
            nodo.querySelector('.producto-stock').textContent        = parseInt(p.stock) || 0;

            nodo.querySelector('.btn-detalle').href = detalleUrl;

            var btn = nodo.querySelector('.btn-agregar');
            btn.dataset.id = p.id_producto;
            if ((parseInt(p.stock) || 0) === 0) {
                btn.disabled = true;
                btn.textContent = 'Sin stock';
            }

            grid.appendChild(nodo);
        });
    }

    // --- Mostrar pagina concreta ---
    function mostrarPagina(pagina) {
        var pp    = parseInt(selectPP.value) || 12;
        var total = todosLosProductos.length;
        var totalPaginas = Math.ceil(total / pp) || 1;

        paginaActual = Math.max(1, Math.min(pagina, totalPaginas));

        var inicio = (paginaActual - 1) * pp;
        var slice  = todosLosProductos.slice(inicio, inicio + pp);

        renderProductos(slice);

        // Info de pagina
        var fin = Math.min(inicio + pp, total);
        infoPagina.textContent = 'Mostrando ' + (inicio + 1) + '-' + fin + ' de ' + total + ' productos';

        // Construir botones de paginas
        listaPaginas.innerHTML = '';

        function crearLi(texto, pg, activo, deshabilitado) {
            var li = document.createElement('li');
            li.className = 'page-item' + (activo ? ' active' : '') + (deshabilitado ? ' disabled' : '');
            var a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = texto;
            if (!deshabilitado && !activo) {
                a.addEventListener('click', function (e) {
                    e.preventDefault();
                    mostrarPagina(pg);
                    window.scrollTo({ top: grid.offsetTop - 20, behavior: 'smooth' });
                });
            }
            li.appendChild(a);
            return li;
        }

        listaPaginas.appendChild(crearLi('&laquo;', paginaActual - 1, false, paginaActual === 1));

        // Rango de botones visible: máximo 5 páginas
        var rangoInicio = Math.max(1, paginaActual - 2);
        var rangoFin    = Math.min(totalPaginas, rangoInicio + 4);
        rangoInicio     = Math.max(1, rangoFin - 4);

        for (var i = rangoInicio; i <= rangoFin; i++) {
            listaPaginas.appendChild(crearLi(i, i, i === paginaActual, false));
        }

        listaPaginas.appendChild(crearLi('&raquo;', paginaActual + 1, false, paginaActual === totalPaginas));

        paginacion.style.display = total > 0 ? 'flex' : 'none';
    }

    // Delegacion de eventos para agregar al carrito
    grid.addEventListener('click', function (e) {
        var btn = e.target.closest('.btn-agregar');
        if (!btn || btn.disabled) return;

        var id = btn.dataset.id;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

        fetch('api/carrito.php?accion=agregar&id=' + id)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                mostrarToast(data.mensaje, data.ok);
                if (data.ok) actualizarContadorNavbar(data.cantidad);
            })
            .catch(function () { mostrarToast('Error al conectar con el servidor.', false); })
            .finally(function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al carrito';
            });
    });

    // --- Cargar productos con filtros ---
    function cargarProductos() {
        cargando.style.display = '';
        grid.style.display     = 'none';
        paginacion.style.display = 'none';
        contador.textContent   = '';

        fetch('api/productos.php?' + obtenerFiltros())
            .then(function (r) { return r.json(); })
            .then(function (data) {
                cargando.style.display = 'none';
                grid.style.display     = '';

                todosLosProductos = data.productos || [];
                paginaActual = 1;
                mostrarPagina(1);

                var hayFiltro = inputBusqueda.value.trim() !== '' ||
                                selectCategoria.value !== '0'    ||
                                inputMin.value !== ''            ||
                                inputMax.value !== ''            ||
                                selectOrden.value !== '';

                contador.textContent = hayFiltro
                    ? data.total + ' producto(s) encontrado(s).'
                    : '';
            })
            .catch(function () {
                cargando.innerHTML =
                    '<p class="text-danger">Error al cargar los productos. Recarga la pagina.</p>';
            });
    }

    // --- Poblar dropdown de categorias ---
    function cargarCategorias() {
        fetch('api/productos.php')
            .then(function (r) { return r.json(); })
            .then(function (data) {
                (data.categorias || []).forEach(function (cat) {
                    var opt    = document.createElement('option');
                    opt.value  = cat.cod;
                    opt.textContent = cat.nombre;
                    selectCategoria.appendChild(opt);
                });

                cargando.style.display = 'none';
                grid.style.display     = '';

                todosLosProductos = data.productos || [];
                paginaActual = 1;
                mostrarPagina(1);
            })
            .catch(function () {
                cargando.innerHTML =
                    '<p class="text-danger">Error al cargar los productos.</p>';
            });
    }

    // --- Contador inicial del carrito ---
    fetch('api/carrito.php?accion=obtener')
        .then(function (r) { return r.json(); })
        .then(function (data) { actualizarContadorNavbar(data.cantidad); });

    // Inicio
    cargarCategorias();
}());
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
