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

<div class="container mt-5">
    <h1 class="text-center mb-4">Bienvenido a nuestra tienda en línea</h1>

    <!-- Indicador de carga -->
    <div id="cargando" class="text-center py-5">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 text-muted">Cargando productos...</p>
    </div>

    <!-- Grid de productos (se llena via AJAX) -->
    <div id="gridProductos" class="row" style="display:none;"></div>

    <!-- Plantilla de tarjeta (oculta) -->
    <template id="plantillaProducto">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
                <a class="producto-imagen-link" href="#" target="_blank">
                    <div class="card-img-top d-flex align-items-center justify-content-center bg-white" style="height:200px;overflow:hidden;">
                        <img class="producto-imagen img-fluid" alt="" style="max-height:200px;object-fit:contain;">
                    </div>
                </a>
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title producto-nombre"></h5>
                    <p class="card-text text-muted small producto-descripcion"></p>
                    <p class="card-text mb-1"><small>Marca: <span class="producto-marca"></span></small></p>
                    <p class="card-text mb-2"><small>Categoría: <span class="producto-categoria"></span></small></p>
                    <p class="card-text"><strong>Precio: $<span class="producto-precio"></span></strong></p>
                    <p class="card-text"><small>Stock: <span class="producto-stock"></span></small></p>
                    <div class="mt-auto">
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
    var grid      = document.getElementById('gridProductos');
    var cargando  = document.getElementById('cargando');
    var plantilla = document.getElementById('plantillaProducto');
    var toast     = document.getElementById('toastCarrito');
    var toastMsg  = document.getElementById('toastMensaje');

    function mostrarToast(mensaje, exito) {
        toastMsg.textContent = mensaje;
        toast.querySelector('.toast-header').className =
            'toast-header ' + (exito ? 'bg-success' : 'bg-danger') + ' text-white';
        $(toast).toast('show');
    }

    function actualizarContadorNavbar(cantidad) {
        var badge = document.getElementById('carritoContador');
        if (!badge) return;
        badge.textContent = cantidad;
        badge.style.display = cantidad > 0 ? 'inline-block' : 'none';
    }

    function renderProductos(productos) {
        grid.innerHTML = '';
        if (!productos || productos.length === 0) {
            grid.innerHTML = '<p class="text-center w-100">No hay productos disponibles.</p>';
            return;
        }

        productos.forEach(function (p) {
            var nodo   = document.importNode(plantilla.content, true);
            var imgSrc = 'recursos/imagenes/' + p.imagen;

            nodo.querySelector('.producto-imagen-link').href = imgSrc;
            nodo.querySelector('.producto-imagen').src        = imgSrc;
            nodo.querySelector('.producto-imagen').alt        = p.nombre;
            nodo.querySelector('.producto-nombre').textContent      = p.nombre;
            nodo.querySelector('.producto-descripcion').textContent = p.descripcion;
            nodo.querySelector('.producto-marca').textContent       = p.marca    || 'N/D';
            nodo.querySelector('.producto-categoria').textContent   = p.categoria || 'N/D';
            nodo.querySelector('.producto-precio').textContent      = parseFloat(p.precio).toFixed(2);
            nodo.querySelector('.producto-stock').textContent       = parseInt(p.stock) || 0;

            var btn = nodo.querySelector('.btn-agregar');
            if (p.estado === 'activo') {
                btn.dataset.id = p.id_producto;
            } else {
                btn.textContent = 'No disponible';
                btn.disabled    = true;
                btn.classList.replace('btn-primary', 'btn-secondary');
            }

            grid.appendChild(nodo);
        });

        // Delegacion de eventos para botones agregar
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
        }, { once: false });
    }

    // Cargar productos via AJAX
    fetch('api/productos.php')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            cargando.style.display = 'none';
            grid.style.display     = '';
            renderProductos(data.productos);
        })
        .catch(function () {
            cargando.innerHTML = '<p class="text-danger">Error al cargar los productos. Recarga la pagina.</p>';
        });

    // Cargar contador inicial del carrito
    fetch('api/carrito.php?accion=obtener')
        .then(function (r) { return r.json(); })
        .then(function (data) { actualizarContadorNavbar(data.cantidad); });
}());
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
