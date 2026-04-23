<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-person-plus me-2"></i>
            <?php echo $esEditar ? 'Editar Cliente' : 'Nuevo Cliente'; ?>
        </h4>
    </div>
    <a href="/admin/index.php?page=clientes" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- ══ COLUMNA PRINCIPAL (8) ══ -->
    <div class="col-lg-8">

        <!-- Datos personales -->
        <div class="card mb-4">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-person me-2" style="color:var(--primary)"></i>Datos Personales
            </div>
            <div class="card-body">
                <form id="formCliente" method="POST"
                      action="/admin/index.php?page=<?php echo $esEditar
                          ? 'clientes_editar&ci=' . urlencode($cliente['ci'] ?? '') . '&usuario=' . urlencode($cliente['usuarioCuenta'] ?? '')
                          : 'clientes_crear'; ?>">
                    <input type="hidden" name="accion" value="<?php echo $esEditar ? 'editar' : 'crear'; ?>">
                    <?php if ($esEditar): ?>
                        <input type="hidden" name="usuarioCuenta" value="<?php echo htmlspecialchars($cliente['usuarioCuenta'] ?? ''); ?>">
                        <input type="hidden" name="ci" value="<?php echo htmlspecialchars($cliente['ci'] ?? ''); ?>">
                    <?php endif; ?>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['nombres'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Ap. Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="apPaterno" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['apPaterno'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Ap. Materno <span class="text-danger">*</span></label>
                            <input type="text" name="apMaterno" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['apMaterno'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">CI <span class="text-danger">*</span></label>
                            <?php if ($esEditar): ?>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['ci'] ?? ''); ?>" readonly>
                            <?php else: ?>
                                <input type="text" name="ci" class="form-control" placeholder="Cédula de identidad" required>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Correo <span class="text-danger">*</span></label>
                            <input type="email" name="correo" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Celular <span class="text-danger">*</span></label>
                            <input type="text" name="nroCelular" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['nroCelular'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="direccion" class="form-control"
                               value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>" required>
                    </div>
            </div>
        </div>

        <!-- Credenciales -->
        <div class="card">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-shield-lock me-2" style="color:var(--primary)"></i>Credenciales de Acceso
            </div>
            <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Usuario <span class="text-danger">*</span></label>
                            <?php if ($esEditar): ?>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($cliente['usuarioCuenta'] ?? ''); ?>" readonly>
                            <?php else: ?>
                                <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">
                                Password <?php echo $esEditar ? '' : '<span class="text-danger">*</span>'; ?>
                            </label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="<?php echo $esEditar ? 'Dejar vacío para no cambiar' : 'Contraseña'; ?>"
                                   <?php echo $esEditar ? '' : 'required'; ?>>
                            <?php if ($esEditar): ?>
                                <div class="form-text">Solo completar si desea cambiar la contraseña.</div>
                            <?php endif; ?>
                        </div>
                    </div>
            </div>
        </div>

    </div>

    <!-- ══ COLUMNA LATERAL (4) ══ -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white fw-semibold py-3 border-bottom">
                <i class="bi bi-info-circle me-2" style="color:var(--primary)"></i>Resumen
            </div>
            <div class="card-body">
                <?php if ($esEditar): ?>
                    <div class="mb-2 small"><span class="text-muted">CI:</span>
                        <strong class="ms-1"><?php echo htmlspecialchars($cliente['ci'] ?? ''); ?></strong></div>
                    <div class="mb-2 small"><span class="text-muted">Usuario:</span>
                        <strong class="ms-1"><?php echo htmlspecialchars($cliente['usuarioCuenta'] ?? ''); ?></strong></div>
                    <hr>
                <?php endif; ?>
                <div class="d-grid gap-2">
                    <button type="submit" form="formCliente" class="btn fw-semibold text-white"
                            style="background:var(--primary);">
                        <i class="bi bi-floppy me-2"></i>
                        <?php echo $esEditar ? 'Actualizar Cliente' : 'Guardar Cliente'; ?>
                    </button>
                    <a href="/admin/index.php?page=clientes" class="btn btn-outline-secondary btn-sm">Cancelar</a>
                </div>
            </div>
        </div>
    </div>

</div>
</form>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
