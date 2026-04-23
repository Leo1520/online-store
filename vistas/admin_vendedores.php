<?php require_once __DIR__ . '/layout_admin/head.php'; ?>
<div class="container mt-4">
    <h1 class="mb-4">Administracion de Vendedores</h1>

    <?php if (!empty($mensaje)): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($mensaje); ?></div>
    <?php endif; ?>

    <form method="POST" action="/admin/index.php?page=vendedores" class="card card-body mb-4">
        <input type="hidden" name="accion" value="<?php echo !empty($vendedorEditar) ? 'editar' : 'crear'; ?>">
        <h5 class="mb-3"><?php echo !empty($vendedorEditar) ? 'Editar vendedor' : 'Nuevo vendedor'; ?></h5>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Usuario</label>
                <?php if (!empty($vendedorEditar)): ?>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($vendedorEditar['usuarioCuenta']); ?>" readonly>
                    <input type="hidden" name="usuarioCuenta" value="<?php echo htmlspecialchars($vendedorEditar['usuarioCuenta']); ?>">
                <?php else: ?>
                    <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
                <?php endif; ?>
            </div>
            <div class="form-group col-md-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control"
                    <?php echo empty($vendedorEditar) ? 'required' : ''; ?>
                    placeholder="<?php echo !empty($vendedorEditar) ? 'Dejar vacio para no cambiar' : ''; ?>">
            </div>
            <div class="form-group col-md-3">
                <label>CI</label>
                <?php if (!empty($vendedorEditar)): ?>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($vendedorEditar['ci']); ?>" readonly>
                    <input type="hidden" name="ci" value="<?php echo htmlspecialchars($vendedorEditar['ci']); ?>">
                <?php else: ?>
                    <input type="text" name="ci" class="form-control" placeholder="Cedula de identidad" required>
                <?php endif; ?>
            </div>
            <div class="form-group col-md-3">
                <label>Nombres</label>
                <input type="text" name="nombres" class="form-control"
                    value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['nombres']) : ''; ?>"
                    placeholder="Nombres" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-3">
                <label>Ap. Paterno</label>
                <input type="text" name="apPaterno" class="form-control"
                    value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['apPaterno']) : ''; ?>"
                    placeholder="Apellido paterno" required>
            </div>
            <div class="form-group col-md-3">
                <label>Ap. Materno</label>
                <input type="text" name="apMaterno" class="form-control"
                    value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['apMaterno']) : ''; ?>"
                    placeholder="Apellido materno" required>
            </div>
            <div class="form-group col-md-3">
                <label>Correo</label>
                <input type="email" name="correo" class="form-control"
                    value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['correo']) : ''; ?>"
                    placeholder="correo@ejemplo.com" required>
            </div>
            <div class="form-group col-md-3">
                <label>Celular</label>
                <input type="text" name="nroCelular" class="form-control"
                    value="<?php echo !empty($vendedorEditar) ? htmlspecialchars($vendedorEditar['nroCelular']) : ''; ?>"
                    placeholder="Numero de celular" required>
            </div>
        </div>

        <div>
            <button class="btn btn-primary" type="submit">
                <?php echo !empty($vendedorEditar) ? 'Actualizar vendedor' : 'Guardar vendedor'; ?>
            </button>
            <?php if (!empty($vendedorEditar)): ?>
                <a href="/admin/index.php?page=vendedores" class="btn btn-secondary ml-2">Cancelar edicion</a>
            <?php endif; ?>
        </div>
    </form>

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

    <h5>Lista de Vendedores</h5>
    <div class="table-responsive">
        <table class="table table-bordered table-sm">
            <thead class="thead-dark">
                <tr>
                    <th>CI</th>
                    <th>Nombre completo</th>
                    <th>Correo</th>
                    <th>Celular</th>
                    <th>Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($vendedores)): ?>
                    <?php foreach ($vendedores as $v): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($v['ci']); ?></td>
                            <td><?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apPaterno'] . ' ' . $v['apMaterno']); ?></td>
                            <td><?php echo htmlspecialchars($v['correo']); ?></td>
                            <td><?php echo htmlspecialchars($v['nroCelular']); ?></td>
                            <td><?php echo htmlspecialchars($v['usuarioCuenta']); ?></td>
                            <td>
                                <a href="/admin/index.php?page=vendedores&editar_ci=<?php echo urlencode($v['ci']); ?>&editar_usuario=<?php echo urlencode($v['usuarioCuenta']); ?>"
                                   class="btn btn-warning btn-sm">Editar</a>
                                <a href="#"
                                   class="btn btn-danger btn-sm btn-delete"
                                   data-toggle="modal"
                                   data-target="#confirmDeleteModal"
                                   data-url="index.php?pagina=admin_vendedores&eliminar_ci=<?php echo urlencode($v['ci']); ?>&eliminar_usuario=<?php echo urlencode($v['usuarioCuenta']); ?>"
                                   data-label="el vendedor <?php echo htmlspecialchars($v['nombres'] . ' ' . $v['apPaterno'], ENT_QUOTES); ?>">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center">No hay vendedores registrados.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar eliminacion</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body" id="confirmDeleteModalBody">
                ¿Estas seguro de eliminar este registro?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <a href="#" class="btn btn-danger" id="confirmDeleteButton">Eliminar</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    $('#confirmDeleteModal').on('show.bs.modal', function (event) {
        var trigger = event.relatedTarget;
        var url     = trigger ? trigger.getAttribute('data-url')   : '#';
        var label   = trigger ? trigger.getAttribute('data-label') : 'este registro';
        document.getElementById('confirmDeleteButton').setAttribute('href', url);
        document.getElementById('confirmDeleteModalBody').textContent = '¿Estas seguro de eliminar ' + label + '?';
    });
    document.getElementById('confirmDeleteModal').addEventListener('hidden.bs.modal', function () {
        document.getElementById('confirmDeleteButton').setAttribute('href', '#');
    });
});
</script>
<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
