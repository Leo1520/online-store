<?php
/**
 * Vista: Pago
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../layout.php'; ?>

    <main class="container my-5">
        <div class="row">
            <div class="col-md-8">
                <h2>Información de Pago</h2>
                
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Detalles del Pedido #<?php echo htmlspecialchars($notaVenta['nro']); ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Precio Unitario</th>
                                    <th>Cantidad</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detalle['nombre']); ?></td>
                                        <td>$<?php echo number_format($detalle['precio'], 2); ?></td>
                                        <td><?php echo $detalle['cant']; ?></td>
                                        <td>$<?php echo number_format($detalle['precio'] * $detalle['cant'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Datos de Envío</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Cliente:</strong> <?php echo htmlspecialchars($notaVenta['nombres'] . ' ' . $notaVenta['apPaterno'] . ' ' . $notaVenta['apMaterno']); ?><br>
                            <strong>Email:</strong> <?php echo htmlspecialchars($notaVenta['correo']); ?><br>
                            <strong>Dirección:</strong> <?php echo htmlspecialchars($notaVenta['direccion']); ?>
                        </p>
                    </div>
                </div>

                <form method="POST" action="?controlador=pago&accion=procesar&id=<?php echo $notaVenta['nro']; ?>">
                    <div class="card">
                        <div class="card-header">
                            <h5>Información de Tarjeta de Crédito</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="numeroTarjeta">Número de Tarjeta:</label>
                                <input type="text" class="form-control" id="numeroTarjeta" name="numeroTarjeta" 
                                       placeholder="1234 5678 9012 3456" required>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="nombreTitular">Nombre del Titular:</label>
                                    <input type="text" class="form-control" id="nombreTitular" name="nombreTitular" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="fechaExpiracion">Fecha Expiración (MM/YY):</label>
                                    <input type="text" class="form-control" id="fechaExpiracion" name="fechaExpiracion" 
                                           placeholder="MM/YY" required>
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="cvv">CVV:</label>
                                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="123" required>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <small>
                                    <strong>Nota:</strong> Esta es una demostración. Utiliza números ficticios para probar.
                                </small>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-credit-card"></i> Procesar Pago
                            </button>
                            <a href="?controlador=carrito&accion=mostrar" class="btn btn-secondary">Volver</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5>Resumen de Compra</h5>
                    </div>
                    <div class="card-body">
                        <p>
                            <strong>Subtotal:</strong> $<?php echo number_format($total / 1.13, 2); ?><br>
                            <strong>Impuesto (13%):</strong> $<?php echo number_format($total - ($total / 1.13), 2); ?><br>
                        </p>
                        <hr>
                        <h5 class="text-success">
                            <strong>Total a Pagar:</strong> $<?php echo number_format($total, 2); ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
