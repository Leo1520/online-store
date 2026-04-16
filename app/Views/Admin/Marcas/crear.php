<?php
/**
 * Vista: Crear Marca
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Marca - Panel Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../../header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="mb-4">Crear Nueva Marca</h1>

                <form method="POST" action="?controlador=admin&accion=guardarMarca">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombre">Nombre de la Marca:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Crear Marca
                            </button>
                            <a href="?controlador=admin&accion=listarMarcas" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../../footer.php'; ?>
