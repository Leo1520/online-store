<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-bag-check me-2"></i>Pedidos / Ventas
        </h4>
        <small class="text-muted">Historial de ventas y gestión de estados</small>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr>
                        <th>#</th><th>Fecha</th><th>Cliente</th><th>Items</th>
                        <th>Total</th><th>Estado</th><th>Detalle</th><th>Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ventas as $venta): ?>
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
                            $badgeClase = $badgeMap[$estado] ?? 'badge-status-pending';
                            $ci  = $venta['ciCliente'];
                            $cli = $clientesExtra[$ci] ?? [];
                        ?>
                        <tr>
                            <td><strong>#<?php echo (int)$venta['nro']; ?></strong></td>
                            <td><small><?php echo htmlspecialchars(substr($venta['fechaHora'],0,16)); ?></small></td>
                            <td>
                                <?php echo htmlspecialchars($venta['cliente']); ?>
                                <small class="text-muted d-block"><?php echo htmlspecialchars($ci); ?></small>
                            </td>
                            <td class="text-center"><?php echo (int)$venta['totalItems']; ?></td>
                            <td><strong>Bs. <?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?></strong></td>
                            <td style="min-width:160px;">
                                <span class="badge rounded-pill <?php echo $badgeClase; ?>"><?php echo ucfirst($estado); ?></span>
                                <?php if ($estado !== 'facturado'): ?>
                                <form method="POST" action="/admin/index.php?page=ventas" class="d-flex align-items-center mt-1 gap-1">
                                    <input type="hidden" name="accion" value="cambiar_estado">
                                    <input type="hidden" name="nro" value="<?php echo (int)$venta['nro']; ?>">
                                    <select name="estado" class="form-select form-select-sm" style="width:130px;">
                                        <?php foreach (['pendiente','procesando','enviado','entregado','cancelado','facturado'] as $op): ?>
                                            <option value="<?php echo $op; ?>" <?php echo $estado === $op ? 'selected' : ''; ?>>
                                                <?php echo ucfirst($op); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Guardar">
                                        <i class="bi bi-check2"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (!empty($detalles[$venta['nro']])): ?>
                                    <ul class="mb-0 ps-3" style="font-size:.8rem;">
                                        <?php foreach ($detalles[$venta['nro']] as $d): ?>
                                            <li>
                                                <?php echo htmlspecialchars($d['producto']); ?>
                                                — <?php echo (int)$d['cant']; ?> u.
                                                — Bs. <?php echo number_format((float)$d['precio'], 2); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="text-muted small">Sin detalle</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-dark"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalComprobante"
                                    data-nro="<?php echo (int)$venta['nro']; ?>"
                                    data-fecha="<?php echo htmlspecialchars($venta['fechaHora']); ?>"
                                    data-cliente="<?php echo htmlspecialchars($venta['cliente']); ?>"
                                    data-ci="<?php echo htmlspecialchars($ci); ?>"
                                    data-correo="<?php echo htmlspecialchars($cli['correo'] ?? '—'); ?>"
                                    data-direccion="<?php echo htmlspecialchars($cli['direccion'] ?? '—'); ?>"
                                    data-celular="<?php echo htmlspecialchars($cli['nroCelular'] ?? '—'); ?>"
                                    data-total="<?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?>"
                                    data-estado="<?php echo htmlspecialchars($estado); ?>">
                                    <i class="bi bi-receipt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($ventas)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">No hay ventas registradas.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);">
                <h5 class="modal-title text-white"><i class="bi bi-receipt me-2"></i>Comprobante de Pago</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="cuerpoComprobante"></div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" onclick="imprimirComprobante()">
                    <i class="bi bi-printer me-1"></i>Imprimir
                </button>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('modalComprobante').addEventListener('show.bs.modal', function (e) {
    var btn = e.relatedTarget;
    var d   = btn.dataset;
    var metodoTexto = (d.estado === 'facturado') ? 'Facturado' : 'Tarjeta / QR Demo';
    document.getElementById('cuerpoComprobante').innerHTML =
        '<div style="font-family:Arial,sans-serif;font-size:13px;">' +
        '<div style="text-align:center;border-bottom:2px solid #1B3A6B;padding-bottom:10px;margin-bottom:15px;">' +
        '<h5 style="margin:0;font-size:18px;color:#1B3A6B;">⚡ Electrohogar</h5>' +
        '<p style="margin:2px 0;color:#666;font-size:11px;">Comprobante de Pago</p>' +
        '</div>' +
        '<table style="width:100%;font-size:12px;border-collapse:collapse;">' +
        '<tr><td style="padding:4px 0;color:#888;width:40%;">Nro. Pedido:</td><td><strong>#' + d.nro + '</strong></td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">Fecha:</td><td>' + d.fecha + '</td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">Método de pago:</td><td><strong>' + metodoTexto + '</strong></td></tr>' +
        '<tr><td colspan="2" style="padding:10px 0 4px;font-weight:bold;border-top:1px solid #eee;color:#333;">Datos del Cliente</td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">Nombre:</td><td>' + d.cliente + '</td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">CI:</td><td>' + d.ci + '</td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">Correo:</td><td>' + d.correo + '</td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">Dirección:</td><td>' + d.direccion + '</td></tr>' +
        '<tr><td style="padding:4px 0;color:#888;">Celular:</td><td>' + d.celular + '</td></tr>' +
        '<tr><td colspan="2" style="padding:12px 0 4px;border-top:1px solid #eee;"></td></tr>' +
        '<tr><td style="font-size:15px;font-weight:bold;">TOTAL:</td><td style="font-size:15px;font-weight:bold;color:#1B3A6B;">Bs. ' + d.total + '</td></tr>' +
        '</table>' +
        '<p style="text-align:center;font-size:10px;color:#aaa;margin-top:15px;border-top:1px solid #eee;padding-top:10px;">Solo visible para administradores — Electrohogar S.R.L.</p>' +
        '</div>';
});

function imprimirComprobante() {
    var contenido = document.getElementById('cuerpoComprobante').innerHTML;
    var ventana = window.open('', '_blank', 'width=420,height=620');
    ventana.document.write('<html><head><title>Comprobante</title></head><body style="margin:20px;font-family:Arial">' + contenido + '</body></html>');
    ventana.document.close();
    ventana.print();
}
</script>
<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
