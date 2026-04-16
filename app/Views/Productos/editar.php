<?php
/**
 * Vista: Editar Producto
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto - Panel Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Editar Producto</h1>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?controlador=productos&accion=actualizar&id=<?php echo $producto['cod']; ?>" enctype="multipart/form-data">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombre">Nombre del Producto:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="precio">Precio:</label>
                                    <input type="number" class="form-control" id="precio" name="precio" step="0.01" min="0" value="<?php echo $producto['precio']; ?>" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="estado">Estado:</label>
                                    <select class="form-control" id="estado" name="estado" required>
                                        <option value="Activo" <?php echo $producto['estado'] === 'Activo' ? 'selected' : ''; ?>>Activo</option>
                                        <option value="Inactivo" <?php echo $producto['estado'] === 'Inactivo' ? 'selected' : ''; ?>>Inactivo</option>
                                        <option value="Descontinuado" <?php echo $producto['estado'] === 'Descontinuado' ? 'selected' : ''; ?>>Descontinuado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="codMarca">Marca:</label>
                                    <select class="form-control" id="codMarca" name="codMarca" required>
                                        <?php foreach ($marcas as $marca): ?>
                                            <option value="<?php echo $marca['cod']; ?>" <?php echo $producto['codMarca'] == $marca['cod'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($marca['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="codCategoria">Categoría:</label>
                                    <select class="form-control" id="codCategoria" name="codCategoria" required>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo $categoria['cod']; ?>" <?php echo $producto['codCategoria'] == $categoria['cod'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categoria['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="codIndustria">Industria:</label>
                                    <select class="form-control" id="codIndustria" name="codIndustria" required>
                                        <?php foreach ($industrias as $industria): ?>
                                            <option value="<?php echo $industria['cod']; ?>" <?php echo $producto['codIndustria'] == $industria['cod'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($industria['nombre']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Imagen Actual:</label><br>
                                <img src="../../Recursos/imagenes/<?php echo htmlspecialchars($producto['imagen'] ?? 'default.jpg'); ?>" 
                                     style="max-width: 200px; height: auto; margin-bottom: 10px;">
                            </div>

                            <div class="form-group">
                                <label for="imagen">Cambiar Imagen:</label>
                                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                                <small class="form-text text-muted">Deja en blanco para mantener la actual</small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Actualizar Producto
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
