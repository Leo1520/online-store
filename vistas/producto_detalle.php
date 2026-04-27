<?php require_once __DIR__ . '/layout/encabezado.php'; ?>


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
                <img src="<?php echo !empty($producto['imagen']) ? 'recursos/imagenes/' . htmlspecialchars($producto['imagen']) : 'recursos/imagenes/ups.png'; ?>"
                     alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                     class="img-fluid" style="max-height:340px;object-fit:contain;"
                     onerror="this.onerror=null;this.src='recursos/imagenes/ups.png';">
            </div>
        </div>

        <div class="col-md-7">
            <h1 class="mb-2"><?php echo htmlspecialchars($producto['nombre']); ?></h1>

            <?php if (!empty($producto['codigo'])): ?>
            <p class="text-muted mb-1"><small>Código: <span class="font-monospace fw-semibold"><?php echo htmlspecialchars($producto['codigo']); ?></span></small></p>
            <?php endif; ?>

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

            <?php
                $pv = (float)($producto['precioVigente'] ?? 0);
                $pp = (float)($producto['precioPropuesto'] ?? 0);
                $hayDesc = $pp > 0 && $pv < $pp;
                $pct     = $hayDesc ? round((($pp - $pv) / $pp) * 100) : 0;
            ?>
            <div class="my-3">
                <?php if ($hayDesc): ?>
                    <div class="d-flex align-items-center gap-3 mb-1">
                        <span class="badge bg-danger fs-6">-<?php echo $pct; ?>% OFF</span>
                        <span class="text-muted text-decoration-line-through fs-5">Bs. <?php echo number_format($pp, 2); ?></span>
                    </div>
                <?php endif; ?>
                <h2 class="text-success mb-0">Bs. <?php echo number_format($pv, 2); ?></h2>
                <?php if ($hayDesc): ?>
                    <small class="text-muted">Ahorras Bs. <?php echo number_format($pp - $pv, 2); ?></small>
                <?php endif; ?>
            </div>

            <?php $stockServidor = (int)$producto['stock']; ?>
            <p id="infoStock" class="<?php echo $stockServidor > 0 ? 'text-success' : 'text-danger'; ?>">
                <?php if ($stockServidor > 0): ?>
                    <i class="bi bi-check-circle-fill"></i>
                    En stock: <strong id="stockNumero"><?php echo $stockServidor; ?></strong> unidades disponibles
                <?php else: ?>
                    <i class="bi bi-x-circle-fill"></i> Sin stock disponible
                <?php endif; ?>
            </p>

            <div class="mt-3">
                <button id="btnAgregar" class="btn btn-azul btn-lg"
                        data-id="<?php echo (int)$producto['id_producto']; ?>"
                        data-stock="<?php echo $stockServidor; ?>"
                        <?php echo $stockServidor <= 0 ? 'disabled' : ''; ?>>
                    <i class="bi bi-cart-plus mr-1"></i>
                    <?php echo $stockServidor <= 0 ? 'Sin stock' : 'Agregar al carrito'; ?>
                </button>
                <a href="index.php?pagina=inicio" class="btn btn-outline-secondary btn-lg ml-2">
                    <i class="bi bi-arrow-left mr-1"></i> Volver
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var btn        = document.getElementById('btnAgregar');
    var infoStock  = document.getElementById('infoStock');
    var stockNum   = document.getElementById('stockNumero');
    var stockReal  = btn ? parseInt(btn.dataset.stock) || 0 : 0;
    var enCarrito  = 0;

    function actualizarBadge(cantidad) {
        var badge = document.getElementById('carritoContador');
        if (!badge) return;
        badge.textContent   = cantidad;
        badge.style.display = cantidad > 0 ? 'flex' : 'none';
    }

    function actualizarVistaStock() {
        var disponible = Math.max(0, stockReal - enCarrito);
        if (stockNum) stockNum.textContent = disponible;
        if (disponible <= 0) {
            if (infoStock) {
                infoStock.className = 'text-danger';
                infoStock.innerHTML = '<i class="bi bi-x-circle-fill"></i> Sin stock disponible';
            }
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="bi bi-x-circle mr-1"></i>Sin stock';
            }
        } else {
            if (infoStock) infoStock.className = 'text-success';
        }
    }

    // Al cargar: obtener cantidad actual en carrito para descontar del stock
    fetch('api/carrito.php?accion=obtener')
        .then(function(r) { return r.json(); })
        .then(function(d) {
            actualizarBadge(d.cantidad);
            if (btn) {
                var id = parseInt(btn.dataset.id);
                (d.items || []).forEach(function(item) {
                    if (item.id_producto == id) enCarrito = item.cantidad;
                });
                actualizarVistaStock();
            }
        });

    if (btn) {
        btn.addEventListener('click', function () {
            var id = btn.dataset.id;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-1"></span>Agregando...';

            fetch('api/carrito.php?accion=agregar&id=' + id)
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.ok) {
                        enCarrito++;
                        actualizarBadge(d.cantidad);
                        actualizarVistaStock();
                    } else {
                        // Stock agotado según servidor
                        btn.disabled = true;
                        btn.innerHTML = '<i class="bi bi-x-circle mr-1"></i>Sin stock';
                        if (infoStock) {
                            infoStock.className = 'text-danger';
                            infoStock.innerHTML = '<i class="bi bi-x-circle-fill"></i> Sin stock disponible';
                        }
                    }
                })
                .catch(function() {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-cart-plus mr-1"></i>Agregar al carrito';
                });
        });
    }
}());
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
