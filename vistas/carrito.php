<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<div class="container mt-5">
    <h1 class="text-center mb-4">Carrito de Compras</h1>

    <div id="carritoContenido">
        <!-- Se renderiza via AJAX -->
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Cargando carrito...</p>
        </div>
    </div>
</div>

<script>
(function () {
    var contenedor = document.getElementById('carritoContenido');

    function actualizarContadorNavbar(cantidad) {
        var badge = document.getElementById('carritoContador');
        if (!badge) return;
        badge.textContent = cantidad;
        badge.style.display = cantidad > 0 ? 'inline-block' : 'none';
    }

    function renderCarrito(data) {
        if (!data.items || data.items.length === 0) {
            contenedor.innerHTML =
                '<p class="text-center text-muted py-4">El carrito esta vacio.</p>' +
                '<div class="text-center"><a href="index.php?pagina=inicio" class="btn btn-primary">Ver productos</a></div>';
            actualizarContadorNavbar(0);
            return;
        }

        var html = '<div class="table-responsive">' +
            '<table class="table table-bordered" id="tablaCarrito">' +
            '<thead class="thead-dark"><tr>' +
            '<th>Producto</th><th>Precio</th><th>Cantidad</th><th>Subtotal</th><th>Accion</th>' +
            '</tr></thead><tbody>';

        data.items.forEach(function (item) {
            html += '<tr id="fila-' + item.id_producto + '">' +
                '<td>' +
                    '<a href="index.php?pagina=producto&id=' + item.id_producto + '" class="text-dark font-weight-bold">' +
                    escHtml(item.nombre) + '</a>' +
                '</td>' +
                '<td>Bs. ' + (parseFloat(item.precioVigente)||0).toFixed(2) + '</td>' +
                '<td>' +
                    '<div class="d-flex align-items-center">' +
                    '<button class="btn btn-outline-secondary btn-sm btn-menos" data-id="' + item.id_producto + '" data-cant="' + (item.cantidad - 1) + '">-</button>' +
                    '<span class="mx-2 cantidad-valor" id="cant-' + item.id_producto + '">' + item.cantidad + '</span>' +
                    '<button class="btn btn-outline-secondary btn-sm btn-mas" data-id="' + item.id_producto + '" data-cant="' + (item.cantidad + 1) + '">+</button>' +
                    '</div>' +
                '</td>' +
                '<td id="sub-' + item.id_producto + '">$' + item.subtotal.toFixed(2) + '</td>' +
                '<td><button class="btn btn-danger btn-sm btn-eliminar" data-id="' + item.id_producto + '">' +
                '<i class="bi bi-trash"></i></button></td>' +
                '</tr>';
        });

        html += '</tbody></table></div>' +
            '<h4 class="text-right">Total: $<span id="totalCarrito">' + data.total.toFixed(2) + '</span></h4>' +
            '<div class="text-right mt-3">' +
            '<a href="index.php?pagina=inicio" class="btn btn-secondary mr-2">Seguir comprando</a>' +
            '<a href="index.php?pagina=pago" class="btn btn-success">Proceder al Pago</a>' +
            '</div>';

        contenedor.innerHTML = html;
        actualizarContadorNavbar(data.cantidad);

        function actualizarFila(data, id) {
            if (!data.ok) return;
            if (data.items.length === 0) { renderCarrito(data); return; }
            var item = data.items.find(function(i){ return i.id_producto == id; });
            if (item) {
                var cantEl = document.getElementById('cant-' + id);
                var subEl  = document.getElementById('sub-'  + id);
                var filaEl = document.getElementById('fila-' + id);
                if (cantEl) cantEl.textContent = item.cantidad;
                if (subEl)  subEl.textContent  = '$' + item.subtotal.toFixed(2);
                // Actualizar data-cant de los botones
                if (filaEl) {
                    var btnM = filaEl.querySelector('.btn-menos');
                    var btnP = filaEl.querySelector('.btn-mas');
                    if (btnM) btnM.dataset.cant = item.cantidad - 1;
                    if (btnP) btnP.dataset.cant = item.cantidad + 1;
                }
            } else {
                var filaEl = document.getElementById('fila-' + id);
                if (filaEl) filaEl.remove();
            }
            var totalEl = document.getElementById('totalCarrito');
            if (totalEl) totalEl.textContent = data.total.toFixed(2);
            actualizarContadorNavbar(data.cantidad);
        }

        function ajaxActualizar(id, cantidad) {
            fetch('api/carrito.php?accion=actualizar&id=' + id + '&cantidad=' + cantidad)
                .then(function(r){ return r.json(); })
                .then(function(data){ actualizarFila(data, id); })
                .catch(function(){});
        }

        // Eventos +/-
        document.querySelectorAll('.btn-menos, .btn-mas').forEach(function (btn) {
            btn.addEventListener('click', function () {
                ajaxActualizar(this.dataset.id, parseInt(this.dataset.cant) || 0);
            });
        });

        // Eventos de eliminar
        document.querySelectorAll('.btn-eliminar').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var id   = this.dataset.id;
                var fila = document.getElementById('fila-' + id);
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                fetch('api/carrito.php?accion=eliminar&id=' + id)
                    .then(function (r) { return r.json(); })
                    .then(function (data) {
                        if (!data.ok) return;
                        if (data.items.length === 0) {
                            renderCarrito(data);
                        } else {
                            if (fila) fila.remove();
                            var totalEl = document.getElementById('totalCarrito');
                            if (totalEl) totalEl.textContent = data.total.toFixed(2);
                            actualizarContadorNavbar(data.cantidad);
                        }
                    })
                    .catch(function () { btn.disabled = false; btn.innerHTML = '<i class="bi bi-trash"></i>'; });
            });
        });
    }

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(str));
        return d.innerHTML;
    }

    // Cargar carrito via AJAX
    fetch('api/carrito.php?accion=obtener')
        .then(function (r) { return r.json(); })
        .then(renderCarrito)
        .catch(function () {
            contenedor.innerHTML = '<p class="text-danger text-center">Error al cargar el carrito.</p>';
        });
}());
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
