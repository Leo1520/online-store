<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header">
    <h4 class="mb-0 fw-bold" style="color:var(--primary)">
        <i class="bi bi-tags me-2"></i>Categorías / Marcas / Industrias
    </h4>
    <small class="text-muted">Gestión del catálogo de clasificaciones</small>
</div>

<div class="row g-4">
    <!-- Marcas -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="background:var(--primary);">
                <h6 class="mb-0 text-white"><i class="bi bi-patch-check me-2"></i>Marcas</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/index.php?page=catalogos" class="mb-3">
                    <input type="hidden" name="accion" value="<?php echo ($edicion['tipo'] === 'marca') ? 'editar' : 'crear'; ?>">
                    <input type="hidden" name="tipo" value="marca">
                    <input type="hidden" name="cod" value="<?php echo ($edicion['tipo'] === 'marca') ? (int)$edicion['cod'] : 0; ?>">
                    <div class="input-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre de marca"
                            value="<?php echo ($edicion['tipo'] === 'marca') ? htmlspecialchars($edicion['nombre']) : ''; ?>" required>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <?php echo ($edicion['tipo'] === 'marca') ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-plus-lg"></i>'; ?>
                        </button>
                        <?php if ($edicion['tipo'] === 'marca'): ?>
                            <a href="/admin/index.php?page=catalogos" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                <ul class="list-group list-group-flush">
                    <?php foreach ($marcas as $fila): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <?php echo htmlspecialchars($fila['nombre']); ?>
                            <div class="d-flex gap-1">
                                <a href="/admin/index.php?page=catalogos&editar_tipo=marca&cod=<?php echo (int)$fila['cod']; ?>"
                                   class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete('la marca <?php echo htmlspecialchars($fila['nombre'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=catalogos&eliminar_tipo=marca&cod=<?php echo (int)$fila['cod']; ?>';
                                    })"><i class="bi bi-trash"></i></button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Categorías -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="background:var(--accent);color:#333;">
                <h6 class="mb-0"><i class="bi bi-grid me-2"></i>Categorías</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/index.php?page=catalogos" class="mb-3">
                    <input type="hidden" name="accion" value="<?php echo ($edicion['tipo'] === 'categoria') ? 'editar' : 'crear'; ?>">
                    <input type="hidden" name="tipo" value="categoria">
                    <input type="hidden" name="cod" value="<?php echo ($edicion['tipo'] === 'categoria') ? (int)$edicion['cod'] : 0; ?>">
                    <div class="input-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre de categoría"
                            value="<?php echo ($edicion['tipo'] === 'categoria') ? htmlspecialchars($edicion['nombre']) : ''; ?>" required>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <?php echo ($edicion['tipo'] === 'categoria') ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-plus-lg"></i>'; ?>
                        </button>
                        <?php if ($edicion['tipo'] === 'categoria'): ?>
                            <a href="/admin/index.php?page=catalogos" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                <ul class="list-group list-group-flush">
                    <?php foreach ($categorias as $fila): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <?php echo htmlspecialchars($fila['nombre']); ?>
                            <div class="d-flex gap-1">
                                <a href="/admin/index.php?page=catalogos&editar_tipo=categoria&cod=<?php echo (int)$fila['cod']; ?>"
                                   class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete('la categoría <?php echo htmlspecialchars($fila['nombre'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=catalogos&eliminar_tipo=categoria&cod=<?php echo (int)$fila['cod']; ?>';
                                    })"><i class="bi bi-trash"></i></button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <!-- Industrias -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="background:#f8f9fa;">
                <h6 class="mb-0 fw-bold" style="color:var(--primary)"><i class="bi bi-briefcase me-2"></i>Industrias</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="/admin/index.php?page=catalogos" class="mb-3">
                    <input type="hidden" name="accion" value="<?php echo ($edicion['tipo'] === 'industria') ? 'editar' : 'crear'; ?>">
                    <input type="hidden" name="tipo" value="industria">
                    <input type="hidden" name="cod" value="<?php echo ($edicion['tipo'] === 'industria') ? (int)$edicion['cod'] : 0; ?>">
                    <div class="input-group">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre de industria"
                            value="<?php echo ($edicion['tipo'] === 'industria') ? htmlspecialchars($edicion['nombre']) : ''; ?>" required>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <?php echo ($edicion['tipo'] === 'industria') ? '<i class="bi bi-check-lg"></i>' : '<i class="bi bi-plus-lg"></i>'; ?>
                        </button>
                        <?php if ($edicion['tipo'] === 'industria'): ?>
                            <a href="/admin/index.php?page=catalogos" class="btn btn-secondary btn-sm">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
                <ul class="list-group list-group-flush">
                    <?php foreach ($industrias as $fila): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <?php echo htmlspecialchars($fila['nombre']); ?>
                            <div class="d-flex gap-1">
                                <a href="/admin/index.php?page=catalogos&editar_tipo=industria&cod=<?php echo (int)$fila['cod']; ?>"
                                   class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                <button class="btn btn-sm btn-outline-danger"
                                    onclick="confirmDelete('la industria <?php echo htmlspecialchars($fila['nombre'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=catalogos&eliminar_tipo=industria&cod=<?php echo (int)$fila['cod']; ?>';
                                    })"><i class="bi bi-trash"></i></button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('form[method="POST"]').forEach(function (form) {
        Validacion.iniciar(form, {
            nombre: [Validacion.reglas.requerido, Validacion.reglas.minLen(2), Validacion.reglas.maxLen(30)],
        });
    });
});
</script>
<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
