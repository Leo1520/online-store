<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<style>
.dash-card { border-radius: 14px; padding: 20px 22px; color: #fff; display: flex; align-items: center; gap: 16px; box-shadow: 0 4px 20px rgba(0,0,0,.12); }
.dash-card-ico { font-size: 36px; opacity: .85; }
.dash-card-num { font-size: 30px; font-weight: 900; line-height: 1; }
.dash-card-lbl { font-size: 12px; opacity: .85; margin-top: 3px; }
.dash-card.azul   { background: linear-gradient(135deg,#1B3A6B,#2751a3); }
.dash-card.verde  { background: linear-gradient(135deg,#28a745,#20c997); }
.dash-card.amari  { background: linear-gradient(135deg,#F5A623,#f7c96a); color:#333; }
.dash-card.rojo   { background: linear-gradient(135deg,#dc3545,#e07070); }
.section-title { font-size: 14px; font-weight: 700; color: #1B3A6B; margin-bottom: 14px; border-left: 3px solid #F5A623; padding-left: 10px; }
</style>

<!-- Tarjetas métricas -->
<div class="row mb-4">
    <div class="col-sm-6 col-xl-3 mb-3">
        <div class="dash-card azul">
            <div class="dash-card-ico"><i class="fas fa-shopping-cart"></i></div>
            <div>
                <div class="dash-card-num"><?php echo (int)($dash['ventasHoy'] ?? 0); ?></div>
                <div class="dash-card-lbl">Ventas hoy</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 mb-3">
        <div class="dash-card verde">
            <div class="dash-card-ico"><i class="fas fa-calendar-week"></i></div>
            <div>
                <div class="dash-card-num"><?php echo (int)($dash['ventasSemana'] ?? 0); ?></div>
                <div class="dash-card-lbl">Ventas esta semana</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 mb-3">
        <div class="dash-card amari">
            <div class="dash-card-ico"><i class="fas fa-calendar-alt"></i></div>
            <div>
                <div class="dash-card-num"><?php echo (int)($dash['ventasMes'] ?? 0); ?></div>
                <div class="dash-card-lbl">Ventas este mes</div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3 mb-3">
        <div class="dash-card azul" style="background:linear-gradient(135deg,#6610f2,#6f42c1);">
            <div class="dash-card-ico"><i class="fas fa-dollar-sign"></i></div>
            <div>
                <div class="dash-card-num">Bs. <?php echo number_format((float)($dash['ingresosMes'] ?? 0), 0, '.', ','); ?></div>
                <div class="dash-card-lbl">Ingresos del mes</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Últimas ventas -->
    <div class="col-lg-7 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#1B3A6B;">
                <h6 class="mb-0 text-white"><i class="fas fa-receipt mr-2"></i>Últimas Ventas</h6>
                <a href="/admin/index.php?page=ventas" class="btn btn-sm btn-outline-light" style="font-size:11px;">Ver todas</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0" style="font-size:13px;">
                    <thead class="thead-light">
                        <tr><th>#</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Estado</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($ultimasVentas)): ?>
                            <tr><td colspan="5" class="text-center text-muted py-3">Sin ventas</td></tr>
                        <?php else: ?>
                            <?php foreach ($ultimasVentas as $v):
                                $badges = ['pendiente'=>'secondary','procesando'=>'warning','enviado'=>'info','entregado'=>'success','cancelado'=>'danger','facturado'=>'primary'];
                                $b = $badges[$v['estado'] ?? 'pendiente'] ?? 'secondary';
                            ?>
                            <tr>
                                <td><strong>#<?php echo (int)$v['nro']; ?></strong></td>
                                <td><small><?php echo htmlspecialchars(substr($v['fechaHora'],0,16)); ?></small></td>
                                <td><?php echo htmlspecialchars($v['cliente'] ?? $v['ciCliente']); ?></td>
                                <td><strong>Bs. <?php echo number_format((float)($v['totalMonto'] ?? 0),2); ?></strong></td>
                                <td><span class="badge badge-<?php echo $b; ?>"><?php echo ucfirst($v['estado']??''); ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Columna derecha -->
    <div class="col-lg-5 mb-4">
        <!-- Top productos -->
        <div class="card mb-3">
            <div class="card-header" style="background:#F5A623;">
                <h6 class="mb-0" style="color:#333;"><i class="fas fa-trophy mr-2"></i>Productos Más Vendidos</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($topProductos)): ?>
                    <p class="text-center text-muted py-3 mb-0" style="font-size:13px;">Sin datos</p>
                <?php else: ?>
                    <?php foreach ($topProductos as $p): ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div style="font-size:13px;"><strong><?php echo htmlspecialchars($p['nombre']); ?></strong></div>
                        <div class="text-right">
                            <span class="badge badge-primary"><?php echo (int)$p['totalVendido']; ?> uds</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Stock crítico -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center" style="background:#dc3545;">
                <h6 class="mb-0 text-white"><i class="fas fa-exclamation-triangle mr-2"></i>Stock Crítico</h6>
                <a href="/admin/index.php?page=almacen" class="btn btn-sm btn-outline-light" style="font-size:11px;">Ver almacén</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($stockCritico)): ?>
                    <p class="text-center text-muted py-3 mb-0" style="font-size:13px;"><i class="fas fa-check-circle text-success mr-1"></i>Sin alertas</p>
                <?php else: ?>
                    <?php foreach (array_slice($stockCritico, 0, 6) as $c): ?>
                    <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                        <div style="font-size:12px;"><?php echo htmlspecialchars($c['producto']); ?></div>
                        <span class="badge badge-<?php echo $c['alerta'] === 'agotado' ? 'danger' : 'warning'; ?>">
                            <?php echo (int)$c['stockTotal']; ?> uds
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Total ingresos -->
<div class="row">
    <div class="col-12">
        <div class="callout callout-info" style="border-radius:10px;">
            <h5 style="font-size:13px;color:#888;margin:0;">Ingresos totales acumulados</h5>
            <h3 style="font-size:24px;font-weight:900;color:#1B3A6B;margin:4px 0 0;">
                Bs. <?php echo number_format((float)($dash['ingresosTotal'] ?? 0), 2); ?>
            </h3>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
