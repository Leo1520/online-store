<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-person-badge me-2"></i>Vendedores
        </h4>
        <small class="text-muted">Gestión del equipo de ventas</small>
    </div>
</div>

<!-- Form -->
<div class="card mb-4">
    <div class="card-header" style="background:var(--primary);">
        <h6 class="mb-0 text-white">
            <i class="bi bi-<?php echo !empty($vendedorEditar) ? 'pencil' : 'plus-circle'; ?> me-2"></i>
            <?php echo !empty($vendedorEditar) ? 'Editar vendedor' : 'Nuevo vendedor'; ?>
        </h6>
    </div>
    <div class="card-body">
        <form method="POST" action="/admin/index.php?page=vendedores">
            <input type="hidden" name="accion" value="<?php echo !empty($vendedorEditar) ? 'editar' : 'crear'; ?>">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Usuario</label>
                    <?php if (!empty($vendedorEditar)): ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vendedorEditar['usuarioCuenta']); ?>" readonly>
                        <input type="hidden" name="usuarioCuenta" value="<?php echo htmlspecialchars($vendedorEditar['usuarioCuenta']); ?>">
                    <?php else: ?>
                        <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control"
                        <?php echo empty($vendedorEditar) ? 'required' : ''; ?>
                        placeholder="<?php echo !empty($vendedorEditar) ? 'Dejar vacío para no cambiar' : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CI</label>
                    <?php if (!empty($vendedorEditar)): ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($vendedorEditar['ci']); ?>" readonly>
                        <input type="hidden" name="ci" value="<?php echo htmlspecialchars($vendedorEditar['ci']); ?>">
                    <?php else: ?>
                        <input type="text" name="ci" class="form-control" placeholder="Cédula de identidad" required>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres" class="form-control"
                        value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['nombres']) : ''; ?>" required>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Ap. Paterno</label>
                    <input type="text" name="apPaterno" class="form-control"
                        value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['apPaterno']) : ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ap. Materno</label>
                    <input type="text" name="apMaterno" class="form-control"
                        value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['apMaterno']) : ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control"
                        value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['correo']) : ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Celular</label>
                    <input type="text" name="nroCelular" class="form-control"
                        value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['nroCelular']) : ''; ?>" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-save me-1"></i><?php echo !empty($vendedorEditar) ? 'Actualizar' : 'Guardar vendedor'; ?>
                </button>
                <?php if (!empty($vendedorEditar)): ?>
                    <a href="/admin/index.php?page=vendedores" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
<div class="card">
    <div class="card-header" style="background:#f8f9fa;">
        <h6 class="mb-0 fw-bold" style="color:var(--primary)"><i class="bi bi-list-ul me-2"></i>Lista de vendedores</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
                <thead>
                    <tr><th>CI</th><th>Nombre completo</th><th>Correo</th><th>Celular</th><th>Usuario</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    <?php if (!empty($vendedores)): ?>
                        <?php foreach ($vendedores as $v): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($v['ci']); ?></td>
                                <td><?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apPaterno'] . ' ' . $v['apMaterno']); ?></td>
                                <td><small><?php echo htmlspecialchars($v['correo']); ?></small></td>
                                <td><?php echo htmlspecialchars($v['nroCelular']); ?></td>
                                <td><?php echo htmlspecialchars($v['usuarioCuenta']); ?></td>
                                <td>
                                    <a href="/admin/index.php?page=vendedores&editar_ci=<?php echo urlencode($v['ci']); ?>&editar_usuario=<?php echo urlencode($v['usuarioCuenta']); ?>"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('el vendedor <?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apPaterno'], ENT_QUOTES); ?>', function(){
                                            window.location='/admin/index.php?page=vendedores&eliminar_ci=<?php echo urlencode($v['ci']); ?>&eliminar_usuario=<?php echo urlencode($v['usuarioCuenta']); ?>';
                                        })">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">No hay vendedores registrados.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form    = document.querySelector('form[method="POST"]');
    var esEditar = form.querySelector('[name="accion"]').value === 'editar';
    var reglas  = {
        nombres:    [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
        apPaterno:  [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
        apMaterno:  [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
        correo:     [Validacion.reglas.requerido, Validacion.reglas.email],
        nroCelular: [Validacion.reglas.requerido, Validacion.reglas.soloDigitos, Validacion.reglas.minLen(7)],
        ci:         [Validacion.reglas.requerido, Validacion.reglas.minLen(5)],
    };
    if (!esEditar) {
        reglas.usuario  = [Validacion.reglas.requerido, Validacion.reglas.alphanumerico, Validacion.reglas.minLen(3)];
        reglas.password = [Validacion.reglas.requerido, Validacion.reglas.minLen(6)];
    }
    Validacion.iniciar(form, reglas);
});
</script>
<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
