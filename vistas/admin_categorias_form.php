<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-tag me-2"></i><?php echo $esEditar ? 'Editar' : 'Nueva'; ?> Categoría
        </h4>
    </div>
    <a href="/admin/index.php?page=categorias" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nombre" name="nombre"
                               value="<?php echo htmlspecialchars($categoria['nombre'] ?? ''); ?>" required
                               placeholder="Ingrese el nombre de la categoría">
                        <div class="form-text">El nombre debe ser único y descriptivo.</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn text-white" style="background:var(--accent);">
                            <i class="bi bi-check-lg me-1"></i><?php echo $esEditar ? 'Actualizar' : 'Crear'; ?> Categoría
                        </button>
                        <a href="/admin/index.php?page=categorias" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
