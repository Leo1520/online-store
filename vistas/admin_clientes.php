<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-people me-2"></i>Clientes
        </h4>
        <small class="text-muted"><?php echo count($clientes); ?> clientes registrados</small>
    </div>
    <a href="index.php?page=clientes_crear" class="btn fw-semibold text-white"
       style="background:var(--accent);">
        <i class="bi bi-plus-lg me-1"></i>Nuevo Cliente
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
                    <?php if (empty($clientes)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="bi bi-people d-block mb-2" style="font-size:2rem;"></i>
                                No hay clientes. <a href="index.php?page=clientes_crear">Registrar el primero</a>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($clientes as $c): ?>
                        <tr>
                            <td><span class="font-monospace small text-muted"><?php echo htmlspecialchars($c['ci']); ?></span></td>
                            <td>
                                <div class="fw-semibold small"><?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apPaterno'] . ' ' . $c['apMaterno']); ?></div>
                            </td>
                            <td><small><?php echo htmlspecialchars($c['correo']); ?></small></td>
                            <td><small><?php echo htmlspecialchars($c['nroCelular'] ?? '—'); ?></small></td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($c['usuarioCuenta']); ?></span></td>
                            <td class="text-end">
                                <a href="index.php?page=clientes_editar&ci=<?php echo urlencode($c['ci']); ?>&usuario=<?php echo urlencode($c['usuarioCuenta']); ?>"
                                   class="btn btn-sm btn-outline-primary py-0 px-2 me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if (!in_array($c['usuarioCuenta'], ['cliente_demo', 'admin'])): ?>
                                <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Eliminar"
                                    onclick="confirmDelete('el cliente <?php echo htmlspecialchars($c['nombres'] . ' ' . $c['apPaterno'], ENT_QUOTES); ?>', function(){
                                        window.location='/admin/index.php?page=clientes&eliminar_ci=<?php echo urlencode($c['ci']); ?>&eliminar_usuario=<?php echo urlencode($c['usuarioCuenta']); ?>';
                                    })">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php else: ?>
                                <span class="badge bg-secondary rounded-pill ms-1">Protegido</span>
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

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
