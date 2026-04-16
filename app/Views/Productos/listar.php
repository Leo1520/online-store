<?php
/**
 * Vista: Listado de Productos (Tienda)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../layout.php'; ?>

    <main class="container my-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Nuestros Productos</h1>
            </div>
            <div class="col-md-4">
                <form method="GET" class="form-inline">
                    <input type="hidden" name="controlador" value="productos">
                    <input type="hidden" name="accion" value="buscar">
                    <input class="form-control mr-2 flex-grow-1" type="search" placeholder="Buscar productos..." name="q" required>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </form>
            </div>
        </div>

        <?php if (isset($termino_busqueda)): ?>
            <p class="text-muted">Resultados para: <strong><?php echo htmlspecialchars($termino_busqueda); ?></strong></p>
        <?php endif; ?>

        <div class="row">
            <?php if (empty($productos)): ?>
                <div class="col-12">
                    <div class="alert alert-info" role="alert">
                        No hay productos disponibles en este momento.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $producto): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="../../Recursos/imagenes/<?php echo htmlspecialchars($producto['imagen'] ?? 'default.jpg'); ?>" 
                                 class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" 
                                 style="height: 250px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                <p class="card-text text-muted">
                                    <small><?php echo htmlspecialchars($producto['descripcion']); ?></small>
                                </p>
                                <p class="card-text">
                                    <strong class="text-success">$<?php echo number_format($producto['precio'], 2); ?></strong>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($producto['marcaNombre'] ?? 'N/A'); ?> | 
                                        <?php echo htmlspecialchars($producto['categoriaNombre'] ?? 'N/A'); ?>
                                    </small>
                                </p>
                            </div>
                            <div class="card-footer">
                                <a href="?controlador=productos&accion=detalle&id=<?php echo $producto['cod']; ?>" 
                                   class="btn btn-sm btn-primary w-100">
                                    <i class="bi bi-eye"></i> Ver Detalles
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
