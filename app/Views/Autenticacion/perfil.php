<?php
/**
 * Vista: Perfil de Usuario
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Mi Perfil</h1>

                <?php if (!isset($cliente)): ?>
                    <div class="alert alert-warning">
                        No se encontraron datos de cliente. Por favor, completa tu registro.
                    </div>
                <?php else: ?>
                    <form method="POST" action="?controlador=autenticacion&accion=actualizarPerfil">
                        <div class="card">
                            <div class="card-header">
                                <h5>Información Personal</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="ci">CI/Cédula:</label>
                                        <input type="text" class="form-control" id="ci" name="ci" 
                                               value="<?php echo htmlspecialchars($cliente['ci'] ?? ''); ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nombres">Nombres:</label>
                                        <input type="text" class="form-control" id="nombres" name="nombres" 
                                               value="<?php echo htmlspecialchars($cliente['nombres'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="apPaterno">Apellido Paterno:</label>
                                        <input type="text" class="form-control" id="apPaterno" name="apPaterno" 
                                               value="<?php echo htmlspecialchars($cliente['apPaterno'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="apMaterno">Apellido Materno:</label>
                                        <input type="text" class="form-control" id="apMaterno" name="apMaterno" 
                                               value="<?php echo htmlspecialchars($cliente['apMaterno'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="correo">Email:</label>
                                        <input type="email" class="form-control" id="correo" name="correo" 
                                               value="<?php echo htmlspecialchars($cliente['correo'] ?? ''); ?>" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="nroCelular">Teléfono/Celular:</label>
                                        <input type="tel" class="form-control" id="nroCelular" name="nroCelular" 
                                               value="<?php echo htmlspecialchars($cliente['nroCelular'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="direccion">Dirección:</label>
                                    <input type="text" class="form-control" id="direccion" name="direccion" 
                                           value="<?php echo htmlspecialchars($cliente['direccion'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Guardar Cambios
                                </button>
                                <a href="?controlador=productos&accion=listar" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../footer.php'; ?>
