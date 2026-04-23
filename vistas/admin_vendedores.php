<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-person-badge me-2"></i>Vendedores
        </h4>
        <small class="text-muted"><?php echo count($vendedores); ?> vendedores registrados</small>
    </div>
    <a href="/admin/index.php?page=vendedores_crear" class="btn fw-semibold text-white"
       style="background:var(--accent);">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Vendedor
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>CI</th>
                        <th>Nombre completo</th>
                        <th>Correo</th>
                        <th>Celular</th>
                        <th>Usuario</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($vendedores)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-person-badge d-block mb-2" style="font-size:2rem;"></i>
                                No hay vendedores. <a href="/admin/index.php?page=vendedores_crear">Registrar el primero</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($vendedores as $v): ?>
                        <tr>
                            <td><span class="font-monospace small text-muted"><?php echo htmlspecialchars($v['ci']); ?></span></td>
                            <td>
                                <div class="fw-semibold small"><?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apPaterno'] . ' ' . $v['apMaterno']); ?></div>
                            </td>
                            <td><small><?php echo htmlspecialchars($v['correo']); ?></small></td>
                            <td><small><?php echo htmlspecialchars($v['nroCelular']); ?></small></td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($v['usuarioCuenta']); ?></span></td>
                            <td class="text-end">
                                <a href="/admin/index.php?page=vendedores_editar&ci=<?php echo urlencode($v['ci']); ?>&usuario=<?php echo urlencode($v['usuarioCuenta']); ?>"
                                   class="btn btn-sm btn-outline-primary py-0 px-2 me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Eliminar"
                                    onclick="confirmDelete('el vendedor <?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apPaterno'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=vendedores&eliminar_ci=<?php echo urlencode($v['ci']); ?>&eliminar_usuario=<?php echo urlencode($v['usuarioCuenta']); ?>';
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
