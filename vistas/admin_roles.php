<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-shield-lock me-2"></i>Roles
        </h4>
        <small class="text-muted"><?php echo count($roles); ?> roles registrados</small>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?page=permisos" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-key me-1"></i>Gestionar Permisos
        </a>
        <button class="btn btn-sm fw-semibold text-white" style="background:var(--accent);"
                data-bs-toggle="modal" data-bs-target="#modalCrearRol">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Rol
        </button>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger mx-4"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<!-- ══ TABLA ROLES ══ -->
<div class="card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Rol</th>
                        <th>Descripción</th>
                        <th class="text-center">Permisos</th>
                        <th class="text-center">Cuentas</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-5">No hay roles registrados.</td></tr>
                    <?php else: ?>
                        <?php foreach ($roles as $r): ?>
                        <tr>
                            <td><?php echo (int)$r['cod']; ?></td>
                            <td>
                                <span class="badge rounded-pill px-3 py-2" style="background:var(--primary);font-size:.82rem;">
                                    <?php echo htmlspecialchars($r['nombre']); ?>
                                </span>
                            </td>
                            <td class="text-muted"><?php echo htmlspecialchars($r['descripcion'] ?? '—'); ?></td>
                            <td class="text-center">
                                <span class="fw-semibold"><?php echo (int)$r['total_permisos']; ?></span>
                            </td>
                            <td class="text-center">
                                <span class="fw-semibold"><?php echo (int)$r['total_cuentas']; ?></span>
                            </td>
                            <td class="text-end">
                                <!-- Asignar permisos -->
                                <button class="btn btn-sm btn-outline-primary py-0 px-2 me-1"
                                        title="Asignar permisos"
                                        onclick="abrirPermisos(<?php echo (int)$r['cod']; ?>, '<?php echo htmlspecialchars($r['nombre'], ENT_QUOTES); ?>')">
                                    <i class="bi bi-key"></i>
                                </button>
                                <!-- Editar -->
                                <button class="btn btn-sm btn-outline-secondary py-0 px-2 me-1"
                                        title="Editar"
                                        onclick="abrirEditar(<?php echo (int)$r['cod']; ?>, '<?php echo htmlspecialchars($r['nombre'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($r['descripcion'] ?? '', ENT_QUOTES); ?>')">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <!-- Eliminar -->
                                <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                        title="Eliminar"
                                        onclick="confirmDelete('el rol <?php echo htmlspecialchars($r['nombre'], ENT_QUOTES); ?>', function(){
                                            window.location='/admin/index.php?page=roles&eliminar=<?php echo (int)$r['cod']; ?>';
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

<!-- ══ TABLA CUENTAS ══ -->
<div class="card">
    <div class="card-header bg-white fw-semibold py-3 border-bottom">
        <i class="bi bi-people me-2" style="color:var(--primary)"></i>Cuentas y sus roles
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Usuario</th>
                        <th>Rol actual</th>
                        <th class="text-end">Cambiar rol</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cuentas as $c): ?>
                    <tr>
                        <td><i class="bi bi-person-circle me-2 text-muted"></i><?php echo htmlspecialchars($c['usuario']); ?></td>
                        <td>
                            <span class="badge rounded-pill px-3" style="background:var(--primary);font-size:.8rem;">
                                <?php echo htmlspecialchars($c['rol']); ?>
                            </span>
                        </td>
                        <td class="text-end">
                            <form method="POST" action="index.php?page=roles" class="d-inline-flex align-items-center gap-2">
                                <input type="hidden" name="accion"  value="cambiar_rol_cuenta">
                                <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($c['usuario']); ?>">
                                <select name="rol" class="form-select form-select-sm" style="width:130px;">
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?php echo htmlspecialchars($r['nombre']); ?>"
                                            <?php echo $r['nombre'] === $c['rol'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($r['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-2">
                                    <i class="bi bi-check2"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ══ MODAL CREAR ROL ══ -->
<div class="modal fade" id="modalCrearRol" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="index.php?page=roles">
                <input type="hidden" name="accion" value="crear_rol">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold" style="color:var(--primary)"><i class="bi bi-plus-circle me-2"></i>Nuevo Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control" placeholder="ej: supervisor" required maxlength="30">
                        <div class="form-text">Sin espacios, en minúscula.</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Descripción del rol" maxlength="150">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm fw-semibold text-white" style="background:var(--primary);">Guardar Rol</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ MODAL EDITAR ROL ══ -->
<div class="modal fade" id="modalEditarRol" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="index.php?page=roles">
                <input type="hidden" name="accion" value="editar_rol">
                <input type="hidden" name="cod"    id="editCod">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold" style="color:var(--primary)"><i class="bi bi-pencil me-2"></i>Editar Rol</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="editNombre" class="form-control" required maxlength="30">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" id="editDesc" class="form-control" maxlength="150">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm fw-semibold text-white" style="background:var(--primary);">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ MODAL ASIGNAR PERMISOS ══ -->
<div class="modal fade" id="modalPermisos" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="index.php?page=roles" id="formPermisos">
                <input type="hidden" name="accion"  value="asignar_permisos">
                <input type="hidden" name="codRol"  id="permisosCodRol">
                <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:var(--primary);border-radius:14px 14px 0 0;">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="bi bi-key me-2"></i>Permisos del rol: <span id="permisosNombreRol"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 py-3" id="permisosBody">
                    <div class="text-center py-4"><div class="spinner-border" style="color:var(--primary);"></div></div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm fw-semibold text-white" style="background:var(--primary);">Guardar Permisos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Datos de permisos para JS -->
<script>
const TODOS_PERMISOS = <?php echo json_encode($permisos, JSON_UNESCAPED_UNICODE); ?>;
const PERMISOS_POR_ROL = <?php
    $map = [];
    $rolModel2 = new Rol();
    foreach ($roles as $r) {
        $map[(int)$r['cod']] = $rolModel2->permisosDeRol((int)$r['cod']);
    }
    echo json_encode($map);
?>;

function abrirEditar(cod, nombre, desc) {
    document.getElementById('editCod').value    = cod;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editDesc').value   = desc;
    new bootstrap.Modal(document.getElementById('modalEditarRol')).show();
}

function abrirPermisos(codRol, nombreRol) {
    document.getElementById('permisosCodRol').value      = codRol;
    document.getElementById('permisosNombreRol').textContent = nombreRol;

    const asignados = PERMISOS_POR_ROL[codRol] || [];
    const grupos = {};
    TODOS_PERMISOS.forEach(p => {
        if (!grupos[p.modulo]) grupos[p.modulo] = [];
        grupos[p.modulo].push(p);
    });

    let html = '';
    Object.keys(grupos).sort().forEach(modulo => {
        html += `<div class="mb-3">
            <div class="fw-semibold mb-2" style="color:var(--primary);font-size:.85rem;text-transform:uppercase;letter-spacing:.05em;">
                <i class="bi bi-folder me-1"></i>${modulo}
            </div>
            <div class="row g-2">`;
        grupos[modulo].forEach(p => {
            const checked = asignados.includes(p.cod) ? 'checked' : '';
            html += `<div class="col-md-6">
                <div class="form-check border rounded px-3 py-2" style="background:#f8f9ff;">
                    <input class="form-check-input" type="checkbox" name="permisos[]"
                           value="${p.cod}" id="perm_${p.cod}" ${checked}>
                    <label class="form-check-label w-100" for="perm_${p.cod}" style="cursor:pointer;">
                        <div style="font-size:.84rem;font-weight:600;">${p.nombre}</div>
                        <div style="font-size:.75rem;color:#888;">${p.descripcion || ''}</div>
                    </label>
                </div>
            </div>`;
        });
        html += '</div></div>';
    });

    document.getElementById('permisosBody').innerHTML = html || '<p class="text-muted">No hay permisos registrados.</p>';
    new bootstrap.Modal(document.getElementById('modalPermisos')).show();
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
