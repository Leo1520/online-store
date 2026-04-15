<?php
// Incluir la conexión a la base de datos
include 'incluir/conexion.php';

// Iniciar sesión para acceder al carrito
session_start();

// Verificar si el carrito tiene productos
if (empty($_SESSION['carrito'])) {
    header("Location: carrito.php");
    exit();
}

// Lógica para procesar el pago
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validar campos requeridos
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    
    if (empty($nombre) || empty($email) || empty($telefono) || empty($direccion)) {
        $error = "Por favor completa todos los campos requeridos.";
    } else {
        // Calcular total y verificar stock de todos los productos
        $total = 0;
        $stock_valido = true;
        $productos_orden = [];
        
        foreach ($_SESSION['carrito'] as $item) {
            $stmt = $conexion->prepare("SELECT id_producto, nombre, precio, stock FROM productos WHERE id_producto = ?");
            $stmt->bind_param("i", $item['id_producto']);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $stmt->close();
            
            if ($resultado->num_rows > 0) {
                $producto = $resultado->fetch_assoc();
                
                // Verificar stock
                if ($item['cantidad'] > $producto['stock']) {
                    $stock_valido = false;
                    $_SESSION['error'] = "Stock insuficiente para el producto: " . htmlspecialchars($producto['nombre']);
                    break;
                }
                
                $total += $producto['precio'] * $item['cantidad'];
                $productos_orden[] = [
                    'id_producto' => $producto['id_producto'],
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $item['cantidad']
                ];
            }
        }
        
        if ($stock_valido) {
            // Iniciar transacción
            $conexion->begin_transaction();
            
            try {
                // Insertar orden
                $stmt = $conexion->prepare("INSERT INTO ordenes (total, cliente_nombre, cliente_email, cliente_telefono, cliente_direccion) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("dssss", $total, $nombre, $email, $telefono, $direccion);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error al crear la orden: " . $stmt->error);
                }
                
                $id_orden = $conexion->insert_id;
                $stmt->close();
                
                // Insertar detalles de la orden y actualizar stock
                foreach ($productos_orden as $prod) {
                    $stmt = $conexion->prepare("INSERT INTO detalles_orden (id_orden, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)");
                    $stmt->bind_param("iiid", $id_orden, $prod['id_producto'], $prod['cantidad'], $prod['precio']);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error al agregar detalle de orden: " . $stmt->error);
                    }
                    $stmt->close();
                    
                    // Actualizar stock
                    $stmt = $conexion->prepare("UPDATE productos SET stock = stock - ? WHERE id_producto = ?");
                    $stmt->bind_param("ii", $prod['cantidad'], $prod['id_producto']);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error al actualizar stock: " . $stmt->error);
                    }
                    $stmt->close();
                }
                
                // Confirmar transacción
                $conexion->commit();
                
                // Limpiar carrito
                $_SESSION['carrito'] = [];
                $_SESSION['id_orden'] = $id_orden;
                
                // Redirigir a página de éxito
                header("Location: pago_exitoso.php");
                exit();
                
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $conexion->rollback();
                $error = $e->getMessage();
            }
        } else {
            // Error de stock ya guardado en $_SESSION['error']
            header("Location: carrito.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago</title>
    <link rel="stylesheet" href="recursos/css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'incluir/encabezado.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center">Proceso de Pago</h1>
        
        <?php if (isset($error)) { 
            echo '<div class="alert alert-danger">' . htmlspecialchars($error) . '</div>';
        } ?>
        
        <!-- Resumen del carrito -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h3>Resumen de tu orden</h3>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach ($_SESSION['carrito'] as $item) {
                            $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
                            $stmt->bind_param("i", $item['id_producto']);
                            $stmt->execute();
                            $resultado = $stmt->get_result();
                            $stmt->close();
                            
                            if ($resultado->num_rows > 0) {
                                $producto = $resultado->fetch_assoc();
                                $subtotal = $producto['precio'] * $item['cantidad'];
                                $total += $subtotal;
                                echo '
                                <tr>
                                    <td>' . htmlspecialchars($producto['nombre']) . '</td>
                                    <td>' . $item['cantidad'] . '</td>
                                    <td>$' . number_format($producto['precio'], 2) . '</td>
                                    <td>$' . number_format($subtotal, 2) . '</td>
                                </tr>
                                ';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <h4 class="text-right">Total a pagar: <strong>$<?php echo number_format($total, 2); ?></strong></h4>
            </div>
        </div>
        
        <!-- Formulario de datos -->
        <div class="row">
            <div class="col-md-8">
                <h3>Datos de Entrega</h3>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="nombre">Nombre Completo *</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico *</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono *</label>
                        <input type="tel" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección de Entrega *</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3" required></textarea>
                    </div>
                    <div class="alert alert-info">
                        <p><strong>Nota:</strong> Este es un proceso de pago simulado. Los datos se guardarán en la base de datos.</p>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg">Completar Compra</button>
                    <a href="carrito.php" class="btn btn-secondary btn-lg">Volver al Carrito</a>
                </form>
            </div>
        </div>
    </div>

    <?php include 'incluir/pie.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>