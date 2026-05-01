<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-building me-2"></i>Sucursales
        </h4>
        <small class="text-muted">Gestión de sucursales y puntos de venta</small>
    </div>
</div>

<div class="row g-4">
    <!-- Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header" style="background:var(--primary);">
                <h6 class="mb-0 text-white">
                    <i class="bi bi-<?php echo !empty($sucursalEditar) ? 'pencil' : 'plus-circle'; ?> me-2"></i>
                    <?php echo !empty($sucursalEditar) ? 'Editar sucursal' : 'Nueva sucursal'; ?>
                </h6>
            </div>
            <div class="card-body">
                <form id="formSucursal" method="POST" action="index.php?page=sucursales">
                    <input type="hidden" name="accion" value="<?php echo !empty($sucursalEditar) ? 'editar' : 'crear'; ?>">
                    <input type="hidden" name="cod" value="<?php echo !empty($sucursalEditar) ? (int)$sucursalEditar['cod'] : 0; ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control"
                            value="<?php echo !empty($sucursalEditar) ? htmlspecialchars($sucursalEditar['nombre']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control"
                            value="<?php echo !empty($sucursalEditar) ? htmlspecialchars($sucursalEditar['direccion']) : ''; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="nroTelefono" class="form-control"
                            value="<?php echo !empty($sucursalEditar) ? htmlspecialchars($sucursalEditar['nroTelefono']) : ''; ?>" required>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-save me-1"></i><?php echo !empty($sucursalEditar) ? 'Actualizar' : 'Guardar'; ?>
                        </button>
                        <?php if (!empty($sucursalEditar)): ?>
                            <a href="index.php?page=sucursales" class="btn btn-secondary">Cancelar</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="background:#f8f9fa;">
                <h6 class="mb-0 fw-bold" style="color:var(--primary)"><i class="bi bi-list-ul me-2"></i>Sucursales registradas</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr><th>Cod</th><th>Nombre</th><th>Dirección</th><th>Teléfono</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sucursales as $sucursal): ?>
                                <tr>
                                    <td><?php echo (int)$sucursal['cod']; ?></td>
                                    <td><?php echo htmlspecialchars($sucursal['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($sucursal['direccion']); ?></td>
                                    <td><?php echo htmlspecialchars($sucursal['nroTelefono']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-warning"
                                           href="index.php?page=sucursales&editar=<?php echo (int)$sucursal['cod']; ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDelete('la sucursal <?php echo htmlspecialchars($sucursal['nombre'], ENT_QUOTES); ?>', function(){
                                                window.location='/admin/index.php?page=sucursales&eliminar=<?php echo (int)$sucursal['cod']; ?>';
                                            })">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Validacion.iniciar(document.getElementById('formSucursal'), {
        nombre:      [Validacion.reglas.requerido, Validacion.reglas.minLen(2), Validacion.reglas.maxLen(30)],
        direccion:   [Validacion.reglas.requerido, Validacion.reglas.minLen(5)],
        nroTelefono: [Validacion.reglas.requerido, Validacion.reglas.soloDigitos],
    });
});
</script>
<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
