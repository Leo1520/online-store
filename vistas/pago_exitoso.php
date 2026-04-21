<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5" style="max-width:600px;">

    <?php if ($error): ?>
        <div class="card shadow-sm border-danger">
            <div class="card-body text-center py-5">
                <i class="bi bi-x-circle-fill text-danger" style="font-size:4rem;"></i>
                <h2 class="mt-3 text-danger">Error en el pago</h2>
                <p class="text-muted"><?php echo htmlspecialchars($error); ?></p>
                <a href="index.php?pagina=pago" class="btn btn-warning mt-2">
                    <i class="bi bi-arrow-left"></i> Reintentar pago
                </a>
            </div>
        </div>

    <?php else: ?>
        <div class="card shadow-sm border-success">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size:4rem;"></i>
                <h2 class="mt-3 text-success">¡Pago exitoso!</h2>
                <p class="text-muted">Tu pedido ha sido registrado correctamente.</p>

                <?php if ($nroVenta): ?>
                    <div class="alert alert-light border mt-3">
                        <strong>Número de pedido:</strong>
                        <span class="h5 text-primary">#<?php echo (int)$nroVenta; ?></span>
                    </div>
                <?php endif; ?>

                <p class="text-muted small mt-2">
                    Puedes ver el estado de tu pedido en
                    <a href="index.php?pagina=mi_cuenta">Mi cuenta</a>.
                </p>

                <div class="mt-3">
                    <a href="index.php?pagina=inicio" class="btn btn-primary mr-2">
                        <i class="bi bi-shop"></i> Seguir comprando
                    </a>
                    <?php if ($nroVenta): ?>
                    <a href="index.php?pagina=factura&nro=<?php echo (int)$nroVenta; ?>" target="_blank" class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark-text"></i> Factura
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
