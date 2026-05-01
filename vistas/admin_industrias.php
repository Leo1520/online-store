<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-building me-2"></i>Industrias
        </h4>
        <small class="text-muted"><?php echo count($industrias); ?> industrias registradas</small>
    </div>
    <a href="index.php?page=industrias_crear" class="btn fw-semibold text-white"
       style="background:var(--accent);">
        <i class="bi bi-plus-lg me-1"></i>Nueva Industria
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width:60px;">#</th>
                        <th>Nombre</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($industrias)): ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-5">
                                <i class="bi bi-building d-block mb-2" style="font-size:2rem;"></i>
                                No hay industrias. <a href="index.php?page=industrias_crear">Crear la primera</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($industrias as $item): ?>
                        <tr>
                            <td class="text-muted"><?php echo (int)$item['cod']; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($item['nombre']); ?></td>
                            <td class="text-end">
                                <a href="index.php?page=industrias_editar&id=<?php echo (int)$item['cod']; ?>"
                                   class="btn btn-sm btn-outline-primary py-0 px-2 me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Eliminar"
                                    onclick="confirmDelete('la industria <?php echo htmlspecialchars($item['nombre'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=industrias&eliminar=<?php echo (int)$item['cod']; ?>';
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
