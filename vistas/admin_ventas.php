<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
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
                        ];
                        $badgeClase = $badgeClases[$estado] ?? 'badge-secondary';
                    ?>
                    <tr>
                        <td><strong>#<?php echo (int)$venta['nro']; ?></strong></td>
                        <td><small><?php echo htmlspecialchars($venta['fechaHora']); ?></small></td>
                        <td>
                            <?php echo htmlspecialchars($venta['cliente']); ?>
                            <small class="text-muted d-block"><?php echo htmlspecialchars($venta['ciCliente']); ?></small>
                        </td>
                        <td class="text-center"><?php echo (int)$venta['totalItems']; ?></td>
                        <td><strong>$<?php echo number_format((float)($venta['totalMonto'] ?? 0), 2); ?></strong></td>
                        <td style="min-width:160px;">
                            <span class="badge <?php echo $badgeClase; ?> mb-1"><?php echo ucfirst($estado); ?></span>
                            <form method="POST" action="index.php?pagina=admin_ventas" class="d-flex align-items-center mt-1">
                                <input type="hidden" name="accion" value="cambiar_estado">
                                <input type="hidden" name="nro" value="<?php echo (int)$venta['nro']; ?>">
                                <select name="estado" class="form-control form-control-sm mr-1" style="width:120px;">
                                    <?php foreach (['pendiente','procesando','enviado','entregado','cancelado'] as $op): ?>
                                        <option value="<?php echo $op; ?>" <?php echo $estado === $op ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($op); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Guardar">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
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
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($ventas)): ?>
                    <tr><td colspan="7" class="text-center text-muted">No hay ventas registradas.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
