<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-people-fill me-2"></i>Usuarios Internos
        </h4>
        <small class="text-muted"><?php echo count($usuarios); ?> usuario<?php echo count($usuarios) !== 1 ? 's' : ''; ?> registrado<?php echo count($usuarios) !== 1 ? 's' : ''; ?></small>
    </div>
    <button class="btn btn-sm fw-semibold text-white" style="background:var(--accent);"
            data-bs-toggle="modal" data-bs-target="#modalCrear">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Usuario
    </button>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger mx-4"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Nombre completo</th>
                        <th>CI</th>
                        <th>Cargo</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($usuarios)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-5">No hay usuarios internos registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>
                                <i class="bi bi-person-circle me-1 text-muted"></i>
                                <strong><?php echo htmlspecialchars($u['usuario']); ?></strong>
                            </td>
                            <td>
                                <?php
                                $colores = [
                                    'admin'      => '#1B3A6B',
                                    'vendedor'   => '#0d6efd',
                                    'almacenero' => '#198754',
                                    'repartidor' => '#fd7e14',
                                    'it'         => '#6f42c1',
                                ];
                                $color = $colores[$u['rol']] ?? '#6c757d';
                                ?>
                                <span class="badge rounded-pill px-3 py-2"
                                      style="background:<?php echo $color; ?>;font-size:.78rem;">
                                    <?php echo htmlspecialchars($u['rol']); ?>
                                </span>
                            </td>
                            <td>
                                <?php
                                $nombre = trim(($u['nombres'] ?? '') . ' ' . ($u['apPaterno'] ?? '') . ' ' . ($u['apMaterno'] ?? ''));
                                echo htmlspecialchars($nombre ?: '—');
                                ?>
                            </td>
                            <td><?php echo htmlspecialchars($u['ci'] ?? '—'); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($u['cargo'] ?? '—'); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($u['correo'] ?? '—'); ?></td>
                            <td class="text-muted"><?php echo htmlspecialchars($u['nroCelular'] ?? '—'); ?></td>
                            <td class="text-end">
                                <?php if (in_array($u['tipo_perfil'], ['empleado', 'vendedor'])): ?>
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2 me-1"
                                        title="Editar"
                                        onclick="abrirEditar(<?php echo htmlspecialchars(json_encode($u), ENT_QUOTES); ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php endif; ?>
                                <?php if ($u['usuario'] !== 'admin'): ?>
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                        title="Eliminar"
                                        onclick="confirmDelete('el usuario <?php echo htmlspecialchars($u['usuario'], ENT_QUOTES); ?>', function(){
                                            window.location='/admin/index.php?page=usuarios_internos&eliminar=<?php echo urlencode($u['usuario']); ?>';
                                        })">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ MODAL CREAR ══ -->
<div class="modal fade" id="modalCrear" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="/admin/index.php?page=usuarios_internos" id="formCrear">
                <input type="hidden" name="accion" value="crear">
                <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:var(--primary);border-radius:14px 14px 0 0;">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-person-plus me-2"></i>Nuevo Usuario Interno</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Usuario <span class="text-danger">*</span></label>
                            <input type="text" name="usuario" class="form-control" maxlength="40" required placeholder="ej: jperez">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" minlength="6" required placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Rol <span class="text-danger">*</span></label>
                            <select name="rol" id="crearRol" class="form-select" required onchange="toggleCargoCrear(this.value)">
                                <option value="">Seleccionar...</option>
                                <option value="admin">admin</option>
                                <option value="vendedor">vendedor</option>
                                <option value="almacenero">almacenero</option>
                                <option value="repartidor">repartidor</option>
                                <option value="it">it</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">CI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" class="form-control" maxlength="20" required placeholder="Cédula de identidad">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="apPaterno" class="form-control" maxlength="20" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Apellido Materno</label>
                            <input type="text" name="apMaterno" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Correo</label>
                            <input type="email" name="correo" class="form-control" maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Celular</label>
                            <input type="text" name="nroCelular" class="form-control" maxlength="30">
                        </div>
                        <div class="col-md-3" id="crearCargoWrap">
                            <label class="form-label small fw-semibold">Cargo</label>
                            <input type="text" name="cargo" class="form-control" maxlength="40" placeholder="ej: Almacenero Senior">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm fw-semibold text-white" style="background:var(--primary);">Crear Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ MODAL EDITAR ══ -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="/admin/index.php?page=usuarios_internos">
                <input type="hidden" name="accion"      value="editar">
                <input type="hidden" name="usuario"     id="editUsuario">
                <input type="hidden" name="tipo_perfil" id="editTipoPerfil">
                <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:var(--primary);border-radius:14px 14px 0 0;">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil me-2"></i>Editar: <span id="editUsuarioLabel"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3">
                    <div class="row g-3">
                        <div class="col-md-6" id="editRolWrap">
                            <label class="form-label small fw-semibold">Rol <span class="text-danger">*</span></label>
                            <select name="rol" id="editRol" class="form-select" required>
                                <option value="admin">admin</option>
                                <option value="almacenero">almacenero</option>
                                <option value="repartidor">repartidor</option>
                                <option value="it">it</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="editRolVendedorWrap" style="display:none;">
                            <label class="form-label small fw-semibold">Rol</label>
                            <input type="text" class="form-control" value="vendedor" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">CI <span class="text-danger">*</span></label>
                            <input type="text" name="ci" id="editCi" class="form-control" maxlength="20" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" name="nombres" id="editNombres" class="form-control" maxlength="50" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Apellido Paterno <span class="text-danger">*</span></label>
                            <input type="text" name="apPaterno" id="editApPaterno" class="form-control" maxlength="20" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Apellido Materno</label>
                            <input type="text" name="apMaterno" id="editApMaterno" class="form-control" maxlength="20">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Correo</label>
                            <input type="email" name="correo" id="editCorreo" class="form-control" maxlength="50">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Celular</label>
                            <input type="text" name="nroCelular" id="editCelular" class="form-control" maxlength="30">
                        </div>
                        <div class="col-md-3" id="editCargoWrap">
                            <label class="form-label small fw-semibold">Cargo</label>
                            <input type="text" name="cargo" id="editCargo" class="form-control" maxlength="40">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-semibold">Nueva contraseña <span class="text-muted fw-normal">(dejar vacío para no cambiar)</span></label>
                            <input type="password" name="password" class="form-control" minlength="6" placeholder="Mínimo 6 caracteres">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm fw-semibold text-white" style="background:var(--primary);">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleCargoCrear(rol) {
    document.getElementById('crearCargoWrap').style.display = (rol === 'vendedor') ? 'none' : '';
}

function abrirEditar(u) {
    const esVendedor = u.tipo_perfil === 'vendedor';

    document.getElementById('editUsuario').value         = u.usuario;
    document.getElementById('editTipoPerfil').value      = u.tipo_perfil;
    document.getElementById('editUsuarioLabel').textContent = u.usuario;
    document.getElementById('editCi').value              = u.ci        || '';
    document.getElementById('editNombres').value         = u.nombres   || '';
    document.getElementById('editApPaterno').value       = u.apPaterno || '';
    document.getElementById('editApMaterno').value       = u.apMaterno || '';
    document.getElementById('editCorreo').value          = u.correo    || '';
    document.getElementById('editCelular').value         = u.nroCelular|| '';
    document.getElementById('editCargo').value           = u.cargo     || '';

    // Rol: vendedores tienen rol fijo, empleados lo pueden cambiar
    document.getElementById('editRolWrap').style.display        = esVendedor ? 'none' : '';
    document.getElementById('editRolVendedorWrap').style.display = esVendedor ? '' : 'none';
    document.getElementById('editRol').required = !esVendedor;
    if (!esVendedor) document.getElementById('editRol').value = u.rol;

    // Cargo: solo empleados
    document.getElementById('editCargoWrap').style.display = esVendedor ? 'none' : '';

    new bootstrap.Modal(document.getElementById('modalEditar')).show();
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
