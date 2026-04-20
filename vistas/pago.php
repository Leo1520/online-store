<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5" style="max-width:700px;">
    <h1 class="mb-4"><i class="bi bi-credit-card"></i> Proceso de Pago</h1>

    <?php
    $sinStock = array_filter($items, function($i) {
        return (int)($i['producto']['stock'] ?? 0) < (int)$i['cantidad'];
    });
    $stripeConfigurado = defined('STRIPE_PUBLISHABLE_KEY') && strpos(STRIPE_PUBLISHABLE_KEY, 'REEMPLAZA') === false;
    $mpConfigurado     = defined('MP_ACCESS_TOKEN')        && strpos(MP_ACCESS_TOKEN, 'REEMPLAZA') === false;
    ?>

    <!-- Resumen del pedido -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light font-weight-bold">
            <i class="bi bi-bag"></i> Resumen del pedido
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="thead-light">
                    <tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-right">Subtotal</th></tr>
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
                    <li><?php echo htmlspecialchars($i['producto']['nombre']); ?>
                        — pedido: <?php echo (int)$i['cantidad']; ?>,
                        disponible: <?php echo (int)($i['producto']['stock'] ?? 0); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <a href="index.php?pagina=carrito" class="btn btn-warning btn-block">
            <i class="bi bi-pencil"></i> Editar carrito
        </a>

    <?php else: ?>

        <!-- Opciones de pago -->
        <div class="row">

            <!-- ── Stripe (tarjeta) ── -->
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <i class="bi bi-credit-card-fill"></i> Pagar con tarjeta
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                        <?php if ($stripeConfigurado): ?>
                            <p class="text-muted small mb-3">Visa, Mastercard y más.<br>Procesado por <strong>Stripe</strong>.</p>
                            <button id="btnStripe" class="btn btn-primary btn-block">
                                <i class="bi bi-credit-card"></i>
                                Pagar $<?php echo number_format($total, 2); ?>
                            </button>
                            <div id="stripeError" class="alert alert-danger mt-2 w-100" style="display:none;"></div>
                        <?php else: ?>
                            <p class="text-muted small">Stripe no configurado.<br>Edita <code>config/stripe.php</code>.</p>
                            <form method="POST" action="index.php?pagina=pago" class="w-100">
                                <button type="submit" class="btn btn-secondary btn-block">Pago simulado</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ── MercadoPago QR ── -->
            <div class="col-md-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <i class="bi bi-qr-code"></i> Pagar con QR
                    </div>
                    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center" id="mpPanel">
                        <?php if ($mpConfigurado): ?>
                            <p class="text-muted small mb-3">Escanea el QR con tu app de<br><strong>MercadoPago</strong>.</p>
                            <button id="btnGenerarQR" class="btn btn-success btn-block">
                                <i class="bi bi-qr-code-scan"></i> Generar QR
                            </button>
                            <!-- QR se inyecta aquí -->
                            <div id="qrContainer" style="display:none;" class="mt-3 w-100">
                                <img id="qrImg" src="" alt="QR de pago" class="img-fluid border rounded mb-2" style="max-width:200px;">
                                <p class="text-muted small mb-1">Escanea con MercadoPago</p>
                                <div id="qrEstado" class="badge badge-secondary">Esperando pago...</div>
                                <div class="mt-2">
                                    <a id="linkMP" href="#" target="_blank" class="btn btn-outline-success btn-sm">
                                        Abrir en navegador
                                    </a>
                                </div>
                            </div>
                            <div id="mpError" class="alert alert-danger mt-2 w-100" style="display:none;"></div>
                        <?php else: ?>
                            <p class="text-muted small">MercadoPago no configurado.<br>Edita <code>config/stripe.php</code> con tu <code>MP_ACCESS_TOKEN</code>.</p>
                            <a href="https://www.mercadopago.com/developers/panel/app" target="_blank" class="btn btn-outline-success btn-sm">Obtener credenciales</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div><!-- /row -->

    <?php endif; ?>

    <a href="index.php?pagina=carrito" class="btn btn-link btn-block mt-2 text-muted">
        <i class="bi bi-arrow-left"></i> Volver al carrito
    </a>
</div>

<script>
(function () {
    // ── Stripe ──
    var btnStripe = document.getElementById('btnStripe');
    if (btnStripe) {
        btnStripe.addEventListener('click', function () {
            btnStripe.disabled = true;
            btnStripe.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Redirigiendo...';
            document.getElementById('stripeError').style.display = 'none';

            fetch('api/stripe_checkout.php')
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.error) {
                        document.getElementById('stripeError').textContent = d.error;
                        document.getElementById('stripeError').style.display = '';
                        btnStripe.disabled = false;
                        btnStripe.innerHTML = '<i class="bi bi-credit-card"></i> Pagar $<?php echo number_format($total, 2); ?>';
                        if (d.login) window.location.href = 'index.php?pagina=login';
                        return;
                    }
                    window.location.href = d.url;
                })
                .catch(function () {
                    document.getElementById('stripeError').textContent = 'Error de conexión.';
                    document.getElementById('stripeError').style.display = '';
                    btnStripe.disabled = false;
                    btnStripe.innerHTML = '<i class="bi bi-credit-card"></i> Pagar $<?php echo number_format($total, 2); ?>';
                });
        });
    }

    // ── MercadoPago QR ──
    var btnQR      = document.getElementById('btnGenerarQR');
    var qrContainer = document.getElementById('qrContainer');
    var pollingId  = null;

    if (btnQR) {
        btnQR.addEventListener('click', function () {
            btnQR.disabled = true;
            btnQR.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Generando QR...';
            document.getElementById('mpError').style.display = 'none';

            fetch('api/mp_checkout.php')
                .then(function(r) { return r.json(); })
                .then(function(d) {
                    if (d.error) {
                        document.getElementById('mpError').textContent = d.error;
                        document.getElementById('mpError').style.display = '';
                        btnQR.disabled = false;
                        btnQR.innerHTML = '<i class="bi bi-qr-code-scan"></i> Generar QR';
                        if (d.login) window.location.href = 'index.php?pagina=login';
                        return;
                    }

                    // Generar imagen QR con API pública (sin librerías)
                    var qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' + encodeURIComponent(d.qr_url);
                    document.getElementById('qrImg').src = qrUrl;
                    document.getElementById('linkMP').href = d.init_point;
                    btnQR.style.display = 'none';
                    qrContainer.style.display = '';

                    // Polling cada 4 segundos
                    pollingId = setInterval(verificarPago, 4000);
                })
                .catch(function () {
                    document.getElementById('mpError').textContent = 'Error al generar QR.';
                    document.getElementById('mpError').style.display = '';
                    btnQR.disabled = false;
                    btnQR.innerHTML = '<i class="bi bi-qr-code-scan"></i> Generar QR';
                });
        });
    }

    function verificarPago() {
        fetch('api/mp_verificar.php')
            .then(function(r) { return r.json(); })
            .then(function(d) {
                var badge = document.getElementById('qrEstado');
                if (d.status === 'pagado') {
                    clearInterval(pollingId);
                    badge.className = 'badge badge-success';
                    badge.textContent = '¡Pago confirmado!';
                    setTimeout(function () {
                        window.location.href = 'index.php?pagina=pago_exitoso&metodo=mp&status=approved&payment_id=0&nro=' + (d.nroVenta || '');
                    }, 1200);
                } else if (d.status === 'pendiente') {
                    badge.className = 'badge badge-warning';
                    badge.textContent = 'Pago pendiente...';
                } else if (d.status === 'esperando') {
                    badge.className = 'badge badge-secondary';
                    badge.textContent = 'Esperando pago...';
                }
            });
    }
}());
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
