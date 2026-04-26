<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<!-- Cabecera -->
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-box-seam me-2"></i>Productos
        </h4>
        <small class="text-muted"><?php echo count($productos); ?> productos registrados</small>
    </div>
    <a href="/admin/index.php?page=productos_crear" class="btn fw-semibold text-white"
       style="background:var(--accent);color:#333!important;">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Producto
    </a>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form method="GET" action="/admin/index.php" class="row g-2 align-items-end">
            <input type="hidden" name="page" value="productos">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control form-control-sm"
                       placeholder="Buscar nombre..."
                       value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <select name="cat" class="form-select form-select-sm">
                    <option value="">Todas las categorías</option>
                    <?php foreach ($categorias as $c): ?>
                        <option value="<?php echo (int)$c['cod']; ?>"
                            <?php echo (isset($_GET['cat']) && (int)$_GET['cat'] === (int)$c['cod']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select form-select-sm">
                    <option value="">Todos los estados</option>
                    <option value="activo"   <?php echo (($_GET['estado'] ?? '') === 'activo')   ? 'selected' : ''; ?>>Activos</option>
                    <option value="inactivo" <?php echo (($_GET['estado'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivos</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="stock_bajo" class="form-select form-select-sm">
                    <option value="">Sin filtro</option>
                    <option value="1" <?php echo (($_GET['stock_bajo'] ?? '') === '1') ? 'selected' : ''; ?>>Stock bajo (≤5)</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<?php
// Aplicar filtros
$lista = $productos;

if (!empty($_GET['q'])) {
    $q = strtolower(trim($_GET['q']));
    $lista = array_filter($lista, fn($p) => str_contains(strtolower($p['nombre']), $q));
}
if (!empty($_GET['cat'])) {
    $lista = array_filter($lista, fn($p) => (int)($p['codCategoria'] ?? 0) === (int)$_GET['cat']);
}
if (!empty($_GET['estado'])) {
    $lista = array_filter($lista, fn($p) => strtolower($p['estado']) === $_GET['estado']);
}
if (!empty($_GET['stock_bajo'])) {
    $lista = array_filter($lista, fn($p) => (int)($p['stock'] ?? 0) <= 5 && strtolower($p['estado']) === 'activo');
}
?>

<!-- Tabla -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">Img</th>
                        <th>Producto</th>
                        <th>Código</th>
                        <th>Categoría</th>
                        <th>Marca</th>
                        <th>P. Propuesto</th>
                        <th>P. Vigente</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($lista)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">
                                <i class="bi bi-box-seam d-block mb-2" style="font-size:2rem;"></i>
                                No hay productos.
                                <a href="/admin/index.php?page=productos_crear">Crear el primero</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($lista as $p): ?>
                        <tr>
                            <td>
                                <img src="<?php echo !empty($p['imagen']) ? '/recursos/imagenes/' . htmlspecialchars($p['imagen']) : '/ups.png'; ?>"
                                     class="rounded" style="width:48px;height:48px;object-fit:contain;background:#f8f9fa;"
                                     onerror="this.onerror=null;this.src='/ups.png';">
                            </td>
                            <td>
                                <div class="fw-semibold small"><?php echo htmlspecialchars($p['nombre']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars(mb_strimwidth($p['descripcion'] ?? '', 0, 50, '…')); ?></small>
                            </td>
                            <td><small class="text-muted font-monospace"><?php echo htmlspecialchars($p['codigo'] ?? '—'); ?></small></td>
                            <td><small><?php echo htmlspecialchars($p['categoria'] ?? '—'); ?></small></td>
                            <td><small><?php echo htmlspecialchars($p['marca'] ?? '—'); ?></small></td>
                            <td>
                                <small class="text-muted">Bs. <?php echo number_format((float)($p['precioPropuesto'] ?? 0), 2); ?></small>
                            </td>
                            <td>
                                <?php
                                    $pv = (float)($p['precioVigente'] ?? 0);
                                    $pp = (float)($p['precioPropuesto'] ?? 0);
                                    $hayDescuento = $pp > 0 && $pv < $pp;
                                ?>
                                <span class="fw-semibold small text-success">Bs. <?php echo number_format($pv, 2); ?></span>
                                <?php if ($hayDescuento): ?>
                                    <br><span class="badge bg-danger" style="font-size:.65rem;">-<?php echo round((($pp - $pv)/$pp)*100); ?>%</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php $s = (int)($p['stock'] ?? 0); ?>
                                <?php if ($s <= 0): ?>
                                    <span class="badge bg-danger rounded-pill">Agotado</span>
                                <?php elseif ($s <= 5): ?>
                                    <span class="badge bg-warning text-dark rounded-pill"><?php echo $s; ?> bajo</span>
                                <?php else: ?>
                                    <span class="badge bg-success rounded-pill"><?php echo $s; ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge rounded-pill <?php echo $p['estado'] === 'activo' ? 'badge-status-active' : 'badge-status-inactive'; ?>">
                                    <?php echo ucfirst($p['estado']); ?>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="/admin/index.php?page=productos_editar&id=<?php echo (int)$p['id_producto']; ?>"
                                   class="btn btn-sm btn-outline-primary py-0 px-2 me-1"
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                    title="Eliminar"
                                    onclick="confirmDelete('el producto <?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=productos&eliminar_producto=<?php echo (int)$p['id_producto']; ?>';
                                    })">
                                    <i class="bi bi-trash"></i>
                                </button>
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
