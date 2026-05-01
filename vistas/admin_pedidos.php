<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-clock-history me-2"></i>Pedidos Activos
        </h4>
        <small class="text-muted"><?php echo count($ventas); ?> pedidos en proceso</small>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px;">#</th>
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventas)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i>
                                No hay pedidos activos en este momento.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ventas as $v):
                            $estado = $v['estado'] ?? 'pendiente';
                            $badgeMap = [
                                'pendiente'  => 'badge-status-pending',
                                'procesando' => 'badge-status-procesando',
                                'enviado'    => 'badge-status-enviado',
                            ];
                            $badge = $badgeMap[$estado] ?? 'badge-status-pending';
                        ?>
                        <tr>
                            <td><strong class="text-primary">#<?php echo (int)$v['nro']; ?></strong></td>
                            <td><small class="text-muted"><?php echo htmlspecialchars(substr($v['fechaHora'], 0, 16)); ?></small></td>
                            <td>
                                <div class="small fw-semibold"><?php echo htmlspecialchars($v['cliente']); ?></div>
                                <small class="text-muted font-monospace"><?php echo htmlspecialchars($v['ciCliente']); ?></small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border"><?php echo (int)$v['totalItems']; ?></span>
                            </td>
                            <td><strong>Bs. <?php echo number_format((float)($v['totalMonto'] ?? 0), 2); ?></strong></td>
                            <td>
                                <span class="badge rounded-pill <?php echo $badge; ?>"><?php echo ucfirst($estado); ?></span>
                            </td>
                            <td class="text-end">
                                <a href="index.php?page=ventas_detalle&id=<?php echo (int)$v['nro']; ?>&from=pedidos"
                                   class="btn btn-sm btn-outline-primary py-0 px-2" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
