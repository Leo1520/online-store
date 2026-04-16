<?php
/**
 * Vista: Registro
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-top: 50px;
            padding-bottom: 50px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7">
                <div class="card">
                    <div class="card-body p-5">
                        <h2 class="card-title text-center mb-4">Crear Cuenta</h2>

                        <?php if (isset($_SESSION['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?php echo htmlspecialchars($_SESSION['error']); ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php unset($_SESSION['error']); ?>
                        <?php endif; ?>

                        <form method="POST" action="?controlador=autenticacion&accion=registrar">
                            <h5 class="mb-3">Datos de Usuario</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="usuario">Usuario:</label>
                                    <input type="text" class="form-control" id="usuario" name="usuario" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="ci">CI/Cédula:</label>
                                    <input type="text" class="form-control" id="ci" name="ci" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="password">Contraseña:</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="confirmar_password">Confirmar Contraseña:</label>
                                    <input type="password" class="form-control" id="confirmar_password" name="confirmar_password" required>
                                </div>
                            </div>

                            <h5 class="mb-3 mt-4">Datos Personales</h5>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nombres">Nombres:</label>
                                    <input type="text" class="form-control" id="nombres" name="nombres" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="correo">Email:</label>
                                    <input type="email" class="form-control" id="correo" name="correo" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="apPaterno">Apellido Paterno:</label>
                                    <input type="text" class="form-control" id="apPaterno" name="apPaterno" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="apMaterno">Apellido Materno:</label>
                                    <input type="text" class="form-control" id="apMaterno" name="apMaterno" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion">Dirección:</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>

                            <div class="form-group">
                                <label for="nroCelular">Teléfono/Celular:</label>
                                <input type="tel" class="form-control" id="nroCelular" name="nroCelular" required>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                        </form>

                        <hr class="my-4">

                        <p class="text-center">
                            ¿Ya tienes cuenta? 
                            <a href="?controlador=autenticacion&accion=mostrarLogin">Inicia sesión aquí</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
