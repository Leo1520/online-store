<?php require_once __DIR__ . '/layout_admin/head.php'; ?>
<div class="container mt-4">
    <h1 class="mb-4">Reporte de Ventas</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-dark">
                <tr>
                    <th>Nro</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Items</th>
                    <th>Total $</th>
                    <th>Estado</th>
                    <th>Detalle</th>
                    <th>Comprobante</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <?php
                        $estado = $venta['estado'] ?? 'pendiente';
                        $badgeClases = [
                            'pendiente'  => 'badge-secondary',
                            'procesando' => 'badge-warning',
                            'enviado'    => 'badge-info',
                            'entregado'  => 'badge-success',
                            'cancelado'  => 'badge-danger',
                            'facturado'  => 'badge-primary',
                        ];
                        $badgeClase = $badgeClases[$estado] ?? 'badge-secondary';
                        $ci  = $venta['ciCliente'];
                        $cli = $clientesExtra[$ci] ?? [];
                    ?>
                    <tr>
                        <td><strong>#<?php echo (int)$venta['nro']; ?></strong></td>
                        <td><small><?php echo htmlspecialchars($venta['fechaHora']); ?></small></td>
                        <td>
                            <?php echo htmlspecialchars($venta['cliente']); ?>
                            <small class="text-muted d-block"><?php echo htmlspecialchars($ci); ?></small>
                        </td>
                        <td class="text-center"><?php echo (int)$venta['totalItems']; ?></td>
                        <td><strong>$<?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?></strong></td>
                        <td style="min-width:140px;">
                            <span class="badge <?php echo $badgeClase; ?>"><?php echo ucfirst($estado); ?></span>
                            <?php if ($estado !== 'facturado'): ?>
                            <form method="POST" action="/admin/index.php?page=ventas" class="d-flex align-items-center mt-1">
                                <input type="hidden" name="accion" value="cambiar_estado">
                                <input type="hidden" name="nro" value="<?php echo (int)$venta['nro']; ?>">
                                <select name="estado" class="form-control form-control-sm mr-1" style="width:120px;">
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
                                <ul class="mb-0 pl-3">
                                    <?php foreach ($detalles[$venta['nro']] as $d): ?>
                                        <li>
                                            <?php echo htmlspecialchars($d['producto']); ?>
                                            — Cant: <?php echo (int)$d['cant']; ?>
                                            — $<?php echo number_format((float)$d['precio'], 2); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <span class="text-muted">Sin detalle</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-dark"
                                data-toggle="modal"
                                data-target="#modalComprobante"
                                data-nro="<?php echo (int)$venta['nro']; ?>"
                                data-fecha="<?php echo htmlspecialchars($venta['fechaHora']); ?>"
                                data-cliente="<?php echo htmlspecialchars($venta['cliente']); ?>"
                                data-ci="<?php echo htmlspecialchars($ci); ?>"
                                data-correo="<?php echo htmlspecialchars($cli['correo'] ?? '—'); ?>"
                                data-direccion="<?php echo htmlspecialchars($cli['direccion'] ?? '—'); ?>"
                                data-celular="<?php echo htmlspecialchars($cli['nroCelular'] ?? '—'); ?>"
                                data-total="<?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?>"
                                data-estado="<?php echo htmlspecialchars($estado); ?>">
                                <i class="bi bi-receipt"></i> Comprobante
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="8" class="text-center text-muted">No hay ventas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title"><i class="bi bi-receipt"></i> Comprobante de Pago</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="cuerpoComprobante">
                <!-- Contenido generado por JS -->
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" onclick="imprimirComprobante()">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
                <button type="button" class="btn btn-dark btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('[data-target="#modalComprobante"]').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var d = btn.dataset;
        var metodoTexto = (d.estado === 'facturado') ? 'Facturado' : 'Tarjeta / QR Demo';
        document.getElementById('cuerpoComprobante').innerHTML =
            '<div style="font-family:Arial,sans-serif;font-size:13px;">' +
            '<div style="text-align:center;border-bottom:2px solid #333;padding-bottom:10px;margin-bottom:15px;">' +
            '<h5 style="margin:0;font-size:18px;">⚡ Electrohogar</h5>' +
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
            '<tr><td style="font-size:15px;font-weight:bold;">TOTAL:</td><td style="font-size:15px;font-weight:bold;color:#1a73e8;">$' + d.total + '</td></tr>' +
            '</table>' +
            '<p style="text-align:center;font-size:10px;color:#aaa;margin-top:15px;border-top:1px solid #eee;padding-top:10px;">Solo visible para administradores — Electrohogar S.R.L.</p>' +
            '</div>';
    });
});

function imprimirComprobante() {
    var contenido = document.getElementById('cuerpoComprobante').innerHTML;
    var ventana = window.open('', '_blank', 'width=400,height=600');
    ventana.document.write('<html><head><title>Comprobante</title></head><body style="margin:20px;font-family:Arial">' + contenido + '</body></html>');
    ventana.document.close();
    ventana.print();
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
