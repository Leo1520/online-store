<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-key me-2"></i>Permisos
        </h4>
        <small class="text-muted"><?php echo count($permisos); ?> permisos registrados</small>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?page=roles" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Volver a Roles
        </a>
        <button class="btn btn-sm fw-semibold text-white" style="background:var(--accent);"
                data-bs-toggle="modal" data-bs-target="#modalCrearPermiso">
            <i class="bi bi-plus-lg me-1"></i>Nuevo Permiso
        </button>
    </div>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger mx-4"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php
// Agrupar por módulo
$grupos = [];
foreach ($permisos as $p) {
    $grupos[$p['modulo'] ?? 'Sin módulo'][] = $p;
}
ksort($grupos);
?>

<?php foreach ($grupos as $modulo => $items): ?>
<div class="card mb-3">
    <div class="card-header bg-white py-2 px-3 border-bottom d-flex align-items-center">
        <i class="bi bi-folder me-2" style="color:var(--primary)"></i>
        <span class="fw-semibold"><?php echo htmlspecialchars($modulo); ?></span>
        <span class="badge ms-2" style="background:var(--primary);"><?php echo count($items); ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $p): ?>
                    <tr>
                        <td><?php echo (int)$p['cod']; ?></td>
                        <td>
                            <code style="font-size:.82rem;color:var(--primary);">
                                <?php echo htmlspecialchars($p['nombre']); ?>
                            </code>
                        </td>
                        <td class="text-muted"><?php echo htmlspecialchars($p['descripcion'] ?? '—'); ?></td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2 me-1"
                                    title="Editar"
                                    onclick="abrirEditar(
                                        <?php echo (int)$p['cod']; ?>,
                                        '<?php echo htmlspecialchars($p['nombre'],      ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES); ?>',
                                        '<?php echo htmlspecialchars($p['modulo']      ?? '', ENT_QUOTES); ?>'
                                    )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger py-0 px-2"
                                    title="Eliminar"
                                    onclick="confirmDelete('el permiso <?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=permisos&eliminar=<?php echo (int)$p['cod']; ?>';
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
<?php endforeach; ?>

<?php if (empty($permisos)): ?>
    <div class="card"><div class="card-body text-center text-muted py-5">No hay permisos registrados.</div></div>
<?php endif; ?>

<!-- ══ MODAL CREAR ══ -->
<div class="modal fade" id="modalCrearPermiso" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="index.php?page=permisos">
                <input type="hidden" name="accion" value="crear_permiso">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold" style="color:var(--primary)"><i class="bi bi-plus-circle me-2"></i>Nuevo Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control font-monospace" placeholder="ej: ver_reportes" required maxlength="60">
                        <div class="form-text">Snake_case, sin espacios.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Módulo <span class="text-danger">*</span></label>
                        <input type="text" name="modulo" class="form-control" placeholder="ej: Reportes" required maxlength="40">
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <input type="text" name="descripcion" class="form-control" placeholder="Descripción del permiso" maxlength="150">
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-sm fw-semibold text-white" style="background:var(--primary);">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ MODAL EDITAR ══ -->
<div class="modal fade" id="modalEditarPermiso" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius:14px;">
            <form method="POST" action="index.php?page=permisos">
                <input type="hidden" name="accion" value="editar_permiso">
                <input type="hidden" name="cod"    id="editCod">
                <div class="modal-header border-0 px-4 pt-4 pb-2">
                    <h5 class="modal-title fw-bold" style="color:var(--primary)"><i class="bi bi-pencil me-2"></i>Editar Permiso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" id="editNombre" class="form-control font-monospace" required maxlength="60">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Módulo <span class="text-danger">*</span></label>
                        <input type="text" name="modulo" id="editModulo" class="form-control" required maxlength="40">
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

<script>
function abrirEditar(cod, nombre, desc, modulo) {
    document.getElementById('editCod').value    = cod;
    document.getElementById('editNombre').value = nombre;
    document.getElementById('editDesc').value   = desc;
    document.getElementById('editModulo').value = modulo;
    new bootstrap.Modal(document.getElementById('modalEditarPermiso')).show();
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
