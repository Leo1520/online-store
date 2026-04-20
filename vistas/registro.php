<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="card-title text-center mb-4">Crear cuenta</h1>

                    <?php if ($mensaje): ?>
                        <div class="alert alert-<?php echo htmlspecialchars($tipoMensaje); ?>">
                            <?php echo htmlspecialchars($mensaje); ?>
                        </div>
                    <?php endif; ?>

                    <form id="formRegistro" method="POST" action="index.php?pagina=registro">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Usuario</label>
                                <input type="text" name="usuario" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['usuario'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Contraseña</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>CI</label>
                                <input type="text" name="ci" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['ci'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-8">
                                <label>Nombres</label>
                                <input type="text" name="nombres" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['nombres'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Apellido Paterno</label>
                                <input type="text" name="apPaterno" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['apPaterno'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Apellido Materno</label>
                                <input type="text" name="apMaterno" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['apMaterno'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Correo electrónico</label>
                                <input type="email" name="correo" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['correo'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Celular</label>
                                <input type="text" name="nroCelular" class="form-control"
                                       value="<?php echo htmlspecialchars($datos['nroCelular'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Dirección</label>
                            <input type="text" name="direccion" class="form-control"
                                   value="<?php echo htmlspecialchars($datos['direccion'] ?? ''); ?>" required>
                        </div>

                        <button type="submit" class="btn btn-success btn-block">
                            <i class="bi bi-person-check"></i> Crear cuenta
                        </button>
                    </form>

                    <hr>
                    <p class="text-center mb-0">
                        ¿Ya tienes cuenta?
                        <a href="index.php?pagina=login">Inicia sesión</a>
                    </p>

                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        Validacion.iniciar(document.getElementById('formRegistro'), {
                            usuario:    [Validacion.reglas.requerido, Validacion.reglas.minLen(3), Validacion.reglas.maxLen(40)],
                            password:   [Validacion.reglas.requerido, Validacion.reglas.minLen(4)],
                            ci:         [Validacion.reglas.requerido, Validacion.reglas.soloDigitos],
                            nombres:    [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios, Validacion.reglas.minLen(2)],
                            apPaterno:  [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios],
                            apMaterno:  [Validacion.reglas.requerido, Validacion.reglas.soloLetrasEspacios],
                            correo:     [Validacion.reglas.requerido, Validacion.reglas.email],
                            nroCelular: [Validacion.reglas.requerido, Validacion.reglas.soloDigitos],
                            direccion:  [Validacion.reglas.requerido, Validacion.reglas.minLen(5)],
                        });
                    });
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
