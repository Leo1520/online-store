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
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php?pagina=inicio">Tienda</a></li>
            <li class="breadcrumb-item active"><?php echo htmlspecialchars($producto['nombre']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-5 text-center">
            <div class="bg-white border rounded d-flex align-items-center justify-content-center"
                 style="height:350px;overflow:hidden;">
                <img src="recursos/imagenes/<?php echo htmlspecialchars($producto['imagen']); ?>"
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                     class="img-fluid" style="max-height:340px;object-fit:contain;">
            </div>
        </div>

        <div class="col-md-7">
            <h1 class="mb-2"><?php echo htmlspecialchars($producto['nombre']); ?></h1>

            <p class="text-muted mb-1">
                <small>
                    Categoría: <strong><?php echo htmlspecialchars($producto['categoria'] ?? 'N/D'); ?></strong>
                    &nbsp;|&nbsp;
                    Marca: <strong><?php echo htmlspecialchars($producto['marca'] ?? 'N/D'); ?></strong>
                    &nbsp;|&nbsp;
                    Industria: <strong><?php echo htmlspecialchars($producto['industria'] ?? 'N/D'); ?></strong>
                </small>
            </p>

            <hr>

            <p class="lead"><?php echo htmlspecialchars($producto['descripcion']); ?></p>

            <h2 class="text-success my-3">$<?php echo number_format((float)$producto['precio'], 2); ?></h2>

            <?php $stock = (int)$producto['stock']; ?>
            <?php if ($stock > 0): ?>
                <p class="text-success">
                    <i class="bi bi-check-circle-fill"></i>
                    En stock: <strong><?php echo $stock; ?> unidades disponibles</strong>
                </p>
            <?php else: ?>
                <p class="text-danger">
                    <i class="bi bi-x-circle-fill"></i> Sin stock disponible
                </p>
            <?php endif; ?>

            <div class="mt-3">
                <button id="btnAgregar" class="btn btn-primary btn-lg"
                        data-id="<?php echo (int)$producto['id_producto']; ?>"
                        <?php echo $stock <= 0 ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus"></i> Agregar al carrito
                </button>
                <a href="index.php?pagina=inicio" class="btn btn-outline-secondary btn-lg ml-2">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var btn    = document.getElementById('btnAgregar');
    var toast  = document.getElementById('toastCarrito');
    var toastMsg = document.getElementById('toastMensaje');

    function actualizarBadge(cantidad) {
        var badge = document.getElementById('carritoContador');
        if (!badge) return;
        badge.textContent   = cantidad;
        badge.style.display = cantidad > 0 ? 'inline-block' : 'none';
    }

    fetch('api/carrito.php?accion=obtener')
        .then(function(r){return r.json();})
        .then(function(d){ actualizarBadge(d.cantidad); });

    if (btn) {
        btn.addEventListener('click', function () {
            var id = btn.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Agregando...';

            fetch('api/carrito.php?accion=agregar&id=' + id)
                .then(function(r){ return r.json(); })
                .then(function(data) {
                    toastMsg.textContent = data.mensaje;
                    toast.querySelector('.toast-header').className =
                        'toast-header ' + (data.ok ? 'bg-success' : 'bg-danger') + ' text-white';
                    $(toast).toast('show');
                    if (data.ok) actualizarBadge(data.cantidad);
                })
                .catch(function(){ toastMsg.textContent = 'Error al conectar.'; $(toast).toast('show'); })
                .finally(function(){
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-cart-plus"></i> Agregar al carrito';
                });
        });
    }
}());
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
