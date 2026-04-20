<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-4">
    <h1 class="mb-4"><i class="bi bi-person-circle"></i> Mi Cuenta</h1>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?php echo htmlspecialchars($tipoMensaje); ?>">
            <?php echo htmlspecialchars($mensaje); ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Columna izquierda: datos del perfil -->
        <div class="col-md-5">
            <?php if ($cliente): ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-person-fill"></i> Mis datos
                </div>
                <div class="card-body">
                    <p><strong>CI:</strong> <?php echo htmlspecialchars($cliente['ci']); ?></p>
                    <p><strong>Nombre:</strong>
                        <?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apPaterno'] . ' ' . $cliente['apMaterno']); ?>
                    </p>

                    <form id="formPerfil" method="POST" action="index.php?pagina=mi_cuenta">
                        <input type="hidden" name="accion" value="actualizar_perfil">
                        <div class="form-group">
                            <label>Correo</label>
                            <input type="email" name="correo" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['correo']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['direccion']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Celular</label>
                            <input type="text" name="nroCelular" class="form-control"
                                   value="<?php echo htmlspecialchars($cliente['nroCelular']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="bi bi-save"></i> Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
            <?php else: ?>
                <div class="alert alert-warning">No se encontraron datos de cliente para tu cuenta.</div>
            <?php endif; ?>

            <!-- Cambiar contraseña -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <i class="bi bi-lock-fill"></i> Cambiar contraseña
                </div>
                <div class="card-body">
                    <form id="formPassword" method="POST" action="index.php?pagina=mi_cuenta">
                        <input type="hidden" name="accion" value="cambiar_password">
                        <div class="form-group">
                            <label>Contraseña actual</label>
                            <input type="password" name="password_actual" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Nueva contraseña</label>
                            <input type="password" name="password_nuevo" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-secondary btn-block">
                            <i class="bi bi-key"></i> Cambiar contraseña
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Columna derecha: historial de compras -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-bag-check-fill"></i> Mis compras
                </div>
                <div class="card-body p-0">
                    <?php if (empty($historial)): ?>
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-bag-x" style="font-size:2.5rem;"></i>
                            <p class="mt-2">Aún no tienes compras registradas.</p>
                            <a href="index.php?pagina=inicio" class="btn btn-outline-success btn-sm">
                                Ir a la tienda
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="accordion" id="historialAccordion">
                            <?php foreach ($historial as $compra): ?>
                                <div class="card mb-0 border-0 border-bottom">
                                    <div class="card-header p-0" id="head<?php echo (int)$compra['nro']; ?>">
                                        <button class="btn btn-link btn-block text-left d-flex justify-content-between align-items-center px-3 py-2"
                                                type="button"
                                                data-toggle="collapse"
                                                data-target="#col<?php echo (int)$compra['nro']; ?>">
                                            <span>
                                                <strong>Pedido #<?php echo (int)$compra['nro']; ?></strong>
                                                <small class="text-muted ml-2"><?php echo htmlspecialchars($compra['fechaHora']); ?></small>
                                                <?php
                                                    $est = $compra['estado'] ?? 'pendiente';
                                                    $bc  = ['pendiente'=>'badge-secondary','procesando'=>'badge-warning','enviado'=>'badge-info','entregado'=>'badge-success','cancelado'=>'badge-danger'];
                                                ?>
                                                <span class="badge <?php echo $bc[$est] ?? 'badge-secondary'; ?> ml-2">
                                                    <?php echo ucfirst($est); ?>
                                                </span>
                                            </span>
                                            <span class="badge badge-light border">
                                                $<?php echo number_format((float)$compra['totalMonto'], 2); ?>
                                            </span>
                                        </button>
                                    </div>
                                    <div id="col<?php echo (int)$compra['nro']; ?>"
                                         class="collapse"
                                         data-parent="#historialAccordion">
                                        <div class="card-body pt-0">
                                            <?php if (!empty($detalles[$compra['nro']])): ?>
                                                <table class="table table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Producto</th>
                                                            <th>Precio</th>
                                                            <th>Cant</th>
                                                            <th>Subtotal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($detalles[$compra['nro']] as $d): ?>
                                                            <tr>
                                                                <td><?php echo htmlspecialchars($d['producto']); ?></td>
                                                                <td>$<?php echo number_format((float)$d['precio'], 2); ?></td>
                                                                <td><?php echo (int)$d['cant']; ?></td>
                                                                <td>$<?php echo number_format((float)$d['precio'] * (int)$d['cant'], 2); ?></td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    Validacion.iniciar(document.getElementById('formPerfil'), {
        correo:     [Validacion.reglas.requerido, Validacion.reglas.email],
        direccion:  [Validacion.reglas.requerido, Validacion.reglas.minLen(5)],
        nroCelular: [Validacion.reglas.requerido, Validacion.reglas.soloDigitos],
    });
    Validacion.iniciar(document.getElementById('formPassword'), {
        password_actual: [Validacion.reglas.requerido],
        password_nuevo:  [Validacion.reglas.requerido, Validacion.reglas.minLen(4)],
    });
});
</script>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
