<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-4">
    <div class="d-flex align-items-center mb-4">
        <h1 class="mb-0"><i class="bi bi-speedometer2"></i> Panel de Vendedor</h1>
        <span class="badge badge-info ml-3"><?php echo htmlspecialchars($_SESSION['usuario']); ?></span>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-left-primary shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Ventas hoy</div>
                    <div class="h3 font-weight-bold text-gray-800">
                        <?php echo (int)($resumen['ventasHoy'] ?? 0); ?>
                    </div>
                    <small class="text-muted"><i class="bi bi-calendar-day"></i> <?php echo date('d/m/Y'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-info shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Ventas esta semana</div>
                    <div class="h3 font-weight-bold text-gray-800">
                        <?php echo (int)($resumen['ventasSemana'] ?? 0); ?>
                    </div>
                    <small class="text-muted"><i class="bi bi-calendar-week"></i> Últimos 7 días</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-success shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Ventas este mes</div>
                    <div class="h3 font-weight-bold text-gray-800">
                        <?php echo (int)($resumen['ventasMes'] ?? 0); ?>
                    </div>
                    <small class="text-muted"><i class="bi bi-calendar-month"></i> <?php echo date('F Y'); ?></small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-left-warning shadow-sm h-100">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Ingresos del mes</div>
                    <div class="h4 font-weight-bold text-gray-800">
                        $<?php echo number_format((float)($resumen['ingresosMes'] ?? 0), 2); ?>
                    </div>
                    <small class="text-muted">
                        Total acumulado: <strong>$<?php echo number_format((float)($resumen['ingresosTotal'] ?? 0), 2); ?></strong>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top 5 productos más vendidos -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-trophy-fill"></i> Top 5 productos más vendidos
                </div>
                <div class="card-body p-0">
                    <?php if (empty($topProductos)): ?>
                        <p class="p-3 text-muted mb-0">Sin ventas registradas aún.</p>
                    <?php else: ?>
                        <table class="table table-sm mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Producto</th>
                                    <th>Vendidos</th>
                                    <th>Ingresos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($topProductos as $i => $prod): ?>
                                    <tr>
                                        <td>
                                            <?php if ($i === 0): ?>
                                                <span class="badge badge-warning"><i class="bi bi-trophy"></i></span>
                                            <?php else: ?>
                                                <?php echo $i + 1; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($prod['nombre']); ?></td>
                                        <td><span class="badge badge-success"><?php echo (int)$prod['totalVendido']; ?></span></td>
                                        <td>$<?php echo number_format((float)$prod['totalIngresos'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Últimas 10 ventas -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-receipt"></i> Últimas 10 ventas
                </div>
                <div class="card-body p-0">
                    <?php if (empty($ultimasVentas)): ?>
                        <p class="p-3 text-muted mb-0">Sin ventas registradas aún.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Nro</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        $bcVendedor = ['pendiente'=>'badge-secondary','procesando'=>'badge-warning','enviado'=>'badge-info','entregado'=>'badge-success','cancelado'=>'badge-danger'];
                                    ?>
                                    <?php foreach ($ultimasVentas as $venta): ?>
                                        <?php $est = $venta['estado'] ?? 'pendiente'; ?>
                                        <tr>
                                            <td><strong>#<?php echo (int)$venta['nro']; ?></strong></td>
                                            <td><small><?php echo htmlspecialchars($venta['fechaHora']); ?></small></td>
                                            <td><?php echo htmlspecialchars($venta['cliente']); ?></td>
                                            <td><?php echo (int)$venta['totalItems']; ?></td>
                                            <td class="text-success font-weight-bold">
                                                $<?php echo number_format((float)$venta['totalMonto'], 2); ?>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $bcVendedor[$est] ?? 'badge-secondary'; ?>">
                                                    <?php echo ucfirst($est); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info    { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
</style>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
