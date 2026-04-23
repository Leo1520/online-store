<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-receipt me-2"></i>Pedido #<?php echo (int)$venta['nro']; ?>
        </h4>
        <small class="text-muted"><?php echo htmlspecialchars(substr($venta['fechaHora'], 0, 16)); ?></small>
    </div>
    <a href="/admin/index.php?page=ventas" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<?php
$estado = $venta['estado'] ?? 'pendiente';
$badgeMap = [
    'pendiente'  => 'badge-status-pending',
    'procesando' => 'badge-status-procesando',
    'enviado'    => 'badge-status-enviado',
    'entregado'  => 'badge-status-entregado',
    'cancelado'  => 'badge-status-cancelado',
    'facturado'  => 'badge-status-facturado',
];
$badge = $badgeMap[$estado] ?? 'badge-status-pending';
?>

<div class="row g-4">

    <!-- ══ COLUMNA PRINCIPAL (8) ══ -->
    <div class="col-lg-8">

        <!-- Items del pedido -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold py-3 border-bottom d-flex align-items-center justify-content-between">
                <span><i class="bi bi-list-ul me-2" style="color:var(--primary)"></i>Productos del Pedido</span>
                <span class="badge rounded-pill <?php echo $badge; ?>"><?php echo ucfirst($estado); ?></span>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cant.</th>
                            <th class="text-end">Precio unit.</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $d): ?>
                        <tr>
                            <td class="small fw-semibold"><?php echo htmlspecialchars($d['producto']); ?></td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?php echo (int)$d['cant']; ?></span>
                            </td>
                            <td class="text-end small">Bs. <?php echo number_format((float)$d['precio'], 2); ?></td>
                            <td class="text-end small fw-semibold">Bs. <?php echo number_format((float)$d['precio'] * (int)$d['cant'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total:</td>
                            <td class="text-end fw-bold" style="color:var(--primary)">
                                Bs. <?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Datos del cliente -->
        <div class="card">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-person me-2" style="color:var(--primary)"></i>Datos del Cliente
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="small text-muted">Nombre</div>
                        <div class="fw-semibold"><?php echo htmlspecialchars($venta['cliente']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">CI</div>
                        <div class="font-monospace"><?php echo htmlspecialchars($venta['ciCliente']); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Correo</div>
                        <div><?php echo htmlspecialchars($clienteExtra['correo'] ?? '—'); ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="small text-muted">Celular</div>
                        <div><?php echo htmlspecialchars($clienteExtra['nroCelular'] ?? '—'); ?></div>
                    </div>
                    <div class="col-12">
                        <div class="small text-muted">Dirección</div>
                        <div><?php echo htmlspecialchars($clienteExtra['direccion'] ?? '—'); ?></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- ══ COLUMNA LATERAL (4) ══ -->
    <div class="col-lg-4">

        <!-- Cambiar estado -->
        <?php if ($estado !== 'facturado'): ?>
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-arrow-repeat me-2" style="color:var(--primary)"></i>Cambiar Estado
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/index.php?page=ventas_detalle&id=<?php echo (int)$venta['nro']; ?>">
                    <input type="hidden" name="accion" value="cambiar_estado">
                    <input type="hidden" name="nro" value="<?php echo (int)$venta['nro']; ?>">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Estado actual</label>
                        <select name="estado" class="form-select">
                            <?php foreach (['pendiente','procesando','enviado','entregado','cancelado','facturado'] as $op): ?>
                                <option value="<?php echo $op; ?>" <?php echo $estado === $op ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($op); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn w-100 fw-semibold text-white" style="background:var(--primary);">
                        <i class="bi bi-check-lg me-1"></i>Guardar estado
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <!-- Resumen -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-info-circle me-2" style="color:var(--primary)"></i>Resumen
            </div>
            <div class="card-body">
                <div class="mb-2 small"><span class="text-muted">Pedido:</span>
                    <strong class="ms-1">#<?php echo (int)$venta['nro']; ?></strong></div>
                <div class="mb-2 small"><span class="text-muted">Fecha:</span>
                    <span class="ms-1"><?php echo htmlspecialchars(substr($venta['fechaHora'], 0, 16)); ?></span></div>
                <div class="mb-2 small"><span class="text-muted">Items:</span>
                    <strong class="ms-1"><?php echo (int)$venta['totalItems']; ?></strong></div>
                <div class="mb-2 small"><span class="text-muted">Total:</span>
                    <strong class="ms-1" style="color:var(--primary)">Bs. <?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?></strong></div>
                <hr>
                <div class="mb-2 small"><span class="text-muted">Estado:</span>
                    <span class="badge rounded-pill ms-1 <?php echo $badge; ?>"><?php echo ucfirst($estado); ?></span></div>
            </div>
        </div>

        <!-- Comprobante -->
        <div class="card">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-printer me-2" style="color:var(--primary)"></i>Comprobante
            </div>
            <div class="card-body d-grid gap-2">
                <button class="btn btn-outline-secondary btn-sm" onclick="imprimirComprobante()">
                    <i class="bi bi-printer me-1"></i>Imprimir comprobante
                </button>
            </div>
        </div>

    </div>
</div>

<!-- Zona oculta para impresión -->
<div id="zonaImpresion" style="display:none">
    <div style="font-family:Arial,sans-serif;font-size:13px;max-width:380px;margin:0 auto;">
        <div style="text-align:center;border-bottom:2px solid #1B3A6B;padding-bottom:10px;margin-bottom:15px;">
            <h5 style="margin:0;font-size:18px;color:#1B3A6B;">⚡ Electrohogar</h5>
            <p style="margin:2px 0;color:#666;font-size:11px;">Comprobante de Pago</p>
        </div>
        <table style="width:100%;font-size:12px;border-collapse:collapse;">
            <tr><td style="padding:4px 0;color:#888;width:40%;">Nro. Pedido:</td><td><strong>#<?php echo (int)$venta['nro']; ?></strong></td></tr>
            <tr><td style="padding:4px 0;color:#888;">Fecha:</td><td><?php echo htmlspecialchars($venta['fechaHora']); ?></td></tr>
            <tr><td style="padding:4px 0;color:#888;">Estado:</td><td><?php echo ucfirst($estado); ?></td></tr>
            <tr><td colspan="2" style="padding:10px 0 4px;font-weight:bold;border-top:1px solid #eee;color:#333;">Datos del Cliente</td></tr>
            <tr><td style="padding:4px 0;color:#888;">Nombre:</td><td><?php echo htmlspecialchars($venta['cliente']); ?></td></tr>
            <tr><td style="padding:4px 0;color:#888;">CI:</td><td><?php echo htmlspecialchars($venta['ciCliente']); ?></td></tr>
            <tr><td style="padding:4px 0;color:#888;">Correo:</td><td><?php echo htmlspecialchars($clienteExtra['correo'] ?? '—'); ?></td></tr>
            <tr><td style="padding:4px 0;color:#888;">Celular:</td><td><?php echo htmlspecialchars($clienteExtra['nroCelular'] ?? '—'); ?></td></tr>
            <tr><td style="padding:4px 0;color:#888;">Dirección:</td><td><?php echo htmlspecialchars($clienteExtra['direccion'] ?? '—'); ?></td></tr>
            <tr><td colspan="2" style="padding:10px 0 4px;border-top:1px solid #eee;font-weight:bold;">Productos</td></tr>
            <?php foreach ($detalles as $d): ?>
            <tr>
                <td style="padding:3px 0;color:#555;"><?php echo htmlspecialchars($d['producto']); ?> x<?php echo (int)$d['cant']; ?></td>
                <td style="text-align:right;">Bs. <?php echo number_format((float)$d['precio'] * (int)$d['cant'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td style="padding:8px 0 0;font-size:15px;font-weight:bold;border-top:2px solid #1B3A6B;">TOTAL:</td>
                <td style="padding:8px 0 0;font-size:15px;font-weight:bold;color:#1B3A6B;text-align:right;">Bs. <?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?></td>
            </tr>
        </table>
        <p style="text-align:center;font-size:10px;color:#aaa;margin-top:15px;border-top:1px solid #eee;padding-top:10px;">Electrohogar S.R.L.</p>
    </div>
</div>

<script>
function imprimirComprobante() {
    var contenido = document.getElementById('zonaImpresion').innerHTML;
    var ventana = window.open('', '_blank', 'width=440,height=680');
    ventana.document.write('<html><head><title>Comprobante #<?php echo (int)$venta['nro']; ?></title></head><body style="margin:20px;">' + contenido + '</body></html>');
    ventana.document.close();
    ventana.print();
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
