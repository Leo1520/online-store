<?php
/**
 * Vista: Detalle de Producto
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($producto['nombre']); ?> - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../layout.php'; ?>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <img src="../../Recursos/imagenes/<?php echo htmlspecialchars($producto['imagen'] ?? 'default.jpg'); ?>" 
                     class="img-fluid" alt="<?php echo htmlspecialchars($producto['nombre']); ?>"
                     style="max-height: 400px; object-fit: contain;">
            </div>
            <div class="col-md-6">
                <h1><?php echo htmlspecialchars($producto['nombre']); ?></h1>
                
                <p class="text-muted mb-3">
                    <strong>Marca:</strong> <?php echo htmlspecialchars($producto['marcaNombre'] ?? 'N/A'); ?> | 
                    <strong>Categoría:</strong> <?php echo htmlspecialchars($producto['categoriaNombre'] ?? 'N/A'); ?> | 
                    <strong>Industria:</strong> <?php echo htmlspecialchars($producto['industriaNombre'] ?? 'N/A'); ?>
                </p>

                <h3 class="text-success mb-3">$<?php echo number_format($producto['precio'], 2); ?></h3>

                <p class="lead"><?php echo htmlspecialchars($producto['descripcion']); ?></p>

                <p class="mb-4">
                    <span class="badge badge-<?php echo $producto['estado'] === 'Activo' ? 'success' : 'danger'; ?>">
                        <?php echo htmlspecialchars($producto['estado']); ?>
                    </span>
                </p>

                <?php if ($producto['estado'] === 'Activo'): ?>
                    <form method="POST" action="?controlador=carrito&accion=agregar&id=<?php echo $producto['cod']; ?>&referrer=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="mb-3">
                        <div class="form-group">
                            <label for="cantidad">Cantidad:</label>
                            <input type="number" class="form-control" id="cantidad" name="cantidad" value="1" min="1" max="10" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-cart-plus"></i> Agregar al Carrito
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Este producto no está disponible en este momento.
                    </div>
                <?php endif; ?>

                <a href="?controlador=productos&accion=listar" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Volver al Catálogo
                </a>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
