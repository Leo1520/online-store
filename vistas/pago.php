<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5" style="max-width:700px;">
    <h1 class="mb-4"><i class="bi bi-credit-card"></i> Proceso de Pago</h1>

    <?php
    $sinStock = array_filter($items, function($i) {
        return (int)($i['producto']['stock'] ?? 0) < (int)$i['cantidad'];
    });
    ?>

    <!-- Resumen del pedido -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light font-weight-bold">
            <i class="bi bi-bag"></i> Resumen del pedido
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Producto</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr <?php echo (int)($item['producto']['stock'] ?? 0) < (int)$item['cantidad'] ? 'class="table-danger"' : ''; ?>>
                            <td><?php echo htmlspecialchars($item['producto']['nombre']); ?></td>
                            <td class="text-center"><?php echo (int)$item['cantidad']; ?></td>
                            <td class="text-right">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="2" class="text-right">Total:</td>
                        <td class="text-right text-success">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if (!empty($sinStock)): ?>
        <div class="alert alert-warning">
            <strong><i class="bi bi-exclamation-triangle"></i> Sin stock suficiente:</strong>
            <ul class="mb-0 mt-1">
                <?php foreach ($sinStock as $i): ?>
                    <li>
                        <?php echo htmlspecialchars($i['producto']['nombre']); ?>
                        — pedido: <?php echo (int)$i['cantidad']; ?>,
                        disponible: <?php echo (int)($i['producto']['stock'] ?? 0); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a href="index.php?pagina=carrito" class="btn btn-warning btn-block">
            <i class="bi bi-pencil"></i> Editar carrito
        </a>
    <?php elseif ($stripeConfigurado): ?>

        <!-- Botón Stripe -->
        <div class="card shadow-sm mb-3">
            <div class="card-body text-center">
                <p class="text-muted mb-3">
                    <i class="bi bi-shield-lock-fill text-success"></i>
                    Pago seguro procesado por <strong>Stripe</strong>
                </p>
                <button id="btnStripe" class="btn btn-primary btn-lg btn-block">
                    <i class="bi bi-credit-card"></i>
                    Pagar $<?php echo number_format($total, 2); ?> con tarjeta
                </button>
                <div id="stripeError" class="alert alert-danger mt-3" style="display:none;"></div>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-lock"></i> Tus datos están cifrados. No almacenamos información de tarjetas.
                </small>
            </div>
        </div>

        <?php if (!isset($_SESSION['usuario'])): ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Debes <a href="index.php?pagina=login">iniciar sesión</a> para completar el pago.
            </div>
        <?php endif; ?>

        <script>
        document.getElementById('btnStripe').addEventListener('click', function () {
            var btn = this;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Redirigiendo a Stripe...';
            document.getElementById('stripeError').style.display = 'none';

            fetch('api/stripe_checkout.php')
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.error) {
                        document.getElementById('stripeError').textContent = data.error;
                        document.getElementById('stripeError').style.display = '';
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-credit-card"></i> Pagar $<?php echo number_format($total, 2); ?> con tarjeta';
                        if (data.login) window.location.href = 'index.php?pagina=login';
                        return;
                    }
                    window.location.href = data.url;
                })
                .catch(function() {
                    document.getElementById('stripeError').textContent = 'Error de conexión. Intenta nuevamente.';
                    document.getElementById('stripeError').style.display = '';
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-credit-card"></i> Pagar $<?php echo number_format($total, 2); ?> con tarjeta';
                });
        });
        </script>

    <?php else: ?>

        <!-- Modo simulado (Stripe no configurado) -->
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-circle"></i>
            <strong>Modo simulado:</strong> Stripe no está configurado.
            Edita <code>config/stripe.php</code> con tus claves de
            <a href="https://dashboard.stripe.com/apikeys" target="_blank">dashboard.stripe.com</a>.
        </div>
        <form method="POST" action="index.php?pagina=pago">
            <button type="submit" class="btn btn-secondary btn-block">
                <i class="bi bi-check2-circle"></i> Completar compra (simulado)
            </button>
        </form>

    <?php endif; ?>

    <a href="index.php?pagina=carrito" class="btn btn-link btn-block mt-2 text-muted">
        <i class="bi bi-arrow-left"></i> Volver al carrito
    </a>
</div>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
