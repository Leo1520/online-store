<?php
/**
 * Vista: Carrito de Compras
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../layout.php'; ?>

    <main class="container my-5">
        <h1 class="mb-4">Carrito de Compras</h1>

        <?php if (empty($productos)): ?>
            <div class="alert alert-info" role="alert">
                Tu carrito está vacío. <a href="?controlador=productos&accion=listar">Continúa comprando</a>
            </div>
        <?php else: ?>
            <div class="table-responsive mb-4">
                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td>
                                    <a href="?controlador=productos&accion=detalle&id=<?php echo $producto['cod']; ?>">
                                        <?php echo htmlspecialchars($producto['nombre']); ?>
                                    </a>
                                </td>
                                <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                                <td>
                                    <form method="POST" action="?controlador=carrito&accion=actualizar&id=<?php echo $producto['cod']; ?>" class="form-inline">
                                        <input type="number" name="cantidad" value="<?php echo $producto['cantidad']; ?>" 
                                               min="1" class="form-control" style="width: 80px;">
                                        <button type="submit" class="btn btn-sm btn-info ml-2">Actualizar</button>
                                    </form>
                                </td>
                                <td>$<?php echo number_format($producto['subtotal'], 2); ?></td>
                                <td>
                                    <a href="?controlador=carrito&accion=eliminar&id=<?php echo $producto['cod']; ?>" 
                                       class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="row mb-4">
                <div class="col-md-8">
                    <a href="?controlador=productos&accion=listar" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Continuar Comprando
                    </a>
                    <a href="?controlador=carrito&accion=vaciar" class="btn btn-danger" 
                       onclick="return confirm('¿Vaciar todo el carrito?')">
                        <i class="bi bi-trash"></i> Vaciar Carrito
                    </a>
                </div>
                <div class="col-md-4 text-right">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Resumen</h5>
                            <p class="card-text">
                                <strong>Total: </strong>$<?php echo number_format($total, 2); ?>
                            </p>
                            <?php if (isset($_SESSION['usuario'])): ?>
                                <a href="?controlador=carrito&accion=checkout" class="btn btn-success btn-block">
                                    <i class="bi bi-credit-card"></i> Proceder al Pago
                                </a>
                            <?php else: ?>
                                <a href="?controlador=autenticacion&accion=mostrarLogin" class="btn btn-success btn-block">
                                    <i class="bi bi-box-arrow-in-right"></i> Inicia Sesión para Pagar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
