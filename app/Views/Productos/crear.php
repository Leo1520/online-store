<?php
/**
 * Vista: Crear Producto
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto - Panel Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Crear Nuevo Producto</h1>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?controlador=productos&accion=guardar" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>

                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="precio">Precio:</label>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="estado">Estado:</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="Activo">Activo</option>
                                        <option value="Inactivo">Inactivo</option>
                                        <option value="Descontinuado">Descontinuado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="codMarca">Marca:</label>
                                    <select class="form-control" id="codMarca" name="codMarca" required>
                                        <option value="">Selecciona una marca</option>
                                        <?php foreach ($marcas as $marca): ?>
                                            <option value="<?php echo $marca['cod']; ?>">
                                                <?php echo htmlspecialchars($marca['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="codCategoria">Categoría:</label>
                                    <select class="form-control" id="codCategoria" name="codCategoria" required>
                                        <option value="">Selecciona una categoría</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['cod']; ?>">
                                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="codIndustria">Industria:</label>
                                    <select class="form-control" id="codIndustria" name="codIndustria" required>
                                        <option value="">Selecciona una industria</option>
                                        <?php foreach ($industrias as $industria): ?>
                                            <option value="<?php echo $industria['cod']; ?>">
                                                <?php echo htmlspecialchars($industria['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="imagen">Imagen del Producto:</label>
                                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                                <small class="form-text text-muted">Formatos: JPG, PNG, GIF, WebP (Máx: 5MB)</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-plus-circle"></i> Crear Producto
                            </button>
                            <a href="?controlador=productos&accion=listar" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../footer.php'; ?>
