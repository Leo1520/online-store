<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-people me-2"></i>Cuentas y Clientes
        </h4>
        <small class="text-muted">Gestión de usuarios y datos de clientes</small>
    </div>
</div>

<!-- Form -->
<div class="card mb-4">
    <div class="card-header" style="background:var(--primary);">
        <h6 class="mb-0 text-white">
            <i class="bi bi-<?php echo !empty($clienteEditar) ? 'pencil' : 'plus-circle'; ?> me-2"></i>
            <?php echo !empty($clienteEditar) ? 'Editar cliente' : 'Nueva cuenta + cliente'; ?>
        </h6>
    </div>
    <div class="card-body">
        <form id="formCliente" method="POST" action="/admin/index.php?page=clientes">
            <input type="hidden" name="accion" value="<?php echo !empty($clienteEditar) ? 'editar' : 'crear'; ?>">
            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label class="form-label">Usuario</label>
                    <?php if (!empty($clienteEditar)): ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($clienteEditar['usuarioCuenta']); ?>" readonly>
                        <input type="hidden" name="usuarioCuenta" value="<?php echo htmlspecialchars($clienteEditar['usuarioCuenta']); ?>">
                    <?php else: ?>
                        <input type="text" name="usuario" class="form-control" required>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Password</label>
                    <input type="text" name="password" class="form-control"
                        <?php echo empty($clienteEditar) ? 'required' : ''; ?>
                        placeholder="<?php echo !empty($clienteEditar) ? 'Dejar vacío para no cambiar' : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">CI</label>
                    <?php if (!empty($clienteEditar)): ?>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($clienteEditar['ci']); ?>" readonly>
                        <input type="hidden" name="ci" value="<?php echo htmlspecialchars($clienteEditar['ci']); ?>">
                    <?php else: ?>
                        <input type="text" name="ci" class="form-control" required>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Nombres</label>
                    <input type="text" name="nombres" class="form-control" value="<?php echo !empty($clienteEditar) ? htmlspecialchars($clienteEditar['nombres']) : ''; ?>" required>
                </div>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label class="form-label">Ap. Paterno</label>
                    <input type="text" name="apPaterno" class="form-control" value="<?php echo !empty($clienteEditar) ? htmlspecialchars($clienteEditar['apPaterno']) : ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ap. Materno</label>
                    <input type="text" name="apMaterno" class="form-control" value="<?php echo !empty($clienteEditar) ? htmlspecialchars($clienteEditar['apMaterno']) : ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control" value="<?php echo !empty($clienteEditar) ? htmlspecialchars($clienteEditar['correo']) : ''; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control" value="<?php echo !empty($clienteEditar) ? htmlspecialchars($clienteEditar['direccion']) : ''; ?>" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Celular</label>
                    <input type="text" name="nroCelular" class="form-control" value="<?php echo !empty($clienteEditar) ? htmlspecialchars($clienteEditar['nroCelular']) : ''; ?>" required>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-save me-1"></i><?php echo !empty($clienteEditar) ? 'Actualizar' : 'Guardar cliente'; ?>
                </button>
                <?php if (!empty($clienteEditar)): ?>
                    <a href="/admin/index.php?page=clientes" class="btn btn-secondary">Cancelar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Cuentas -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header" style="background:var(--accent);color:#333;">
                <h6 class="mb-0"><i class="bi bi-person-lock me-2"></i>Cuentas</h6>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($cuentas as $cuenta): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><?php echo htmlspecialchars($cuenta['usuario']); ?></span>
                        <?php if (in_array($cuenta['usuario'], ['cliente_demo', 'admin'], true)): ?>
                            <span class="badge bg-secondary rounded-pill">Protegido</span>
                        <?php else: ?>
                            <button class="btn btn-outline-danger btn-sm"
                                onclick="confirmDelete('la cuenta <?php echo htmlspecialchars($cuenta['usuario'], ENT_QUOTES); ?>', function(){
                                    window.location='/admin/index.php?page=clientes&eliminar_cuenta=<?php echo urlencode($cuenta['usuario']); ?>';
                                })">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Clientes -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header" style="background:#f8f9fa;">
                <h6 class="mb-0 fw-bold" style="color:var(--primary)"><i class="bi bi-people me-2"></i>Clientes registrados</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm mb-0">
                        <thead>
                            <tr><th>CI</th><th>Nombre completo</th><th>Correo</th><th>Usuario</th><th>Acciones</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clientes as $cliente): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($cliente['ci']); ?></td>
                                    <td><?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apPaterno'] . ' ' . $cliente['apMaterno']); ?></td>
                                    <td><small><?php echo htmlspecialchars($cliente['correo']); ?></small></td>
                                    <td><?php echo htmlspecialchars($cliente['usuarioCuenta']); ?></td>
                                    <td>
                                        <a class="btn btn-sm btn-outline-warning"
                                           href="/admin/index.php?page=clientes&editar_ci=<?php echo urlencode($cliente['ci']); ?>&editar_usuario=<?php echo urlencode($cliente['usuarioCuenta']); ?>">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if (in_array($cliente['usuarioCuenta'], ['cliente_demo', 'admin'], true)): ?>
                                            <span class="badge bg-secondary rounded-pill">Protegido</span>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-outline-danger"
                                                onclick="confirmDelete('el cliente <?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apPaterno'], ENT_QUOTES); ?> y su cuenta', function(){
                                                    window.location='/admin/index.php?page=clientes&eliminar_cliente_ci=<?php echo urlencode($cliente['ci']); ?>&eliminar_cliente_usuario=<?php echo urlencode($cliente['usuarioCuenta']); ?>';
                                                })">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php endif; ?>
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
    var form    = document.getElementById('formCliente');
    var esEditar = form.querySelector('[name="accion"]').value === 'editar';
    var reglas  = {
        nombres:    [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
        apPaterno:  [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
        apMaterno:  [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
        correo:     [Validacion.reglas.requerido, Validacion.reglas.email],
        direccion:  [Validacion.reglas.requerido, Validacion.reglas.minLen(5)],
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
