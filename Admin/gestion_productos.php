<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: inicio_sesion.php");
    exit();
}
include '../incluir/conexion.php';

if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);
    
    // Obtener imagen del producto antes de eliminar
    $stmt = $conexion->prepare("SELECT imagen FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $producto = $resultado->fetch_assoc();
    $stmt->close();
    
    // Eliminar producto
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    
    if ($stmt->execute()) {
        // Eliminar imagen si existe
        if ($producto && $producto['imagen']) {
            $ruta_imagen = '../recursos/imagenes/' . $producto['imagen'];
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }
        $_SESSION['mensaje'] = "Producto eliminado con éxito.";
    }
    $stmt->close();
    header("Location: gestion_productos.php");
    exit();
}
?>
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Gestión de Productos</h2>
        
        <?php if (isset($_SESSION['mensaje'])) { 
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['mensaje'];
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>';
            echo '</div>';
            unset($_SESSION['mensaje']);
        } ?>
        
        <div class="text-right mb-3">
            <a href="agregar_producto.php" class="btn btn-success">Agregar Producto</a>
            <a href="cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $consulta = "SELECT * FROM productos ORDER BY id_producto DESC";
                $resultado = $conexion->query($consulta);
                if ($resultado->num_rows > 0) {
                    while ($producto = $resultado->fetch_assoc()) {
                        echo '
                        <tr>
                            <td>' . htmlspecialchars($producto['id_producto']) . '</td>
                            <td>' . htmlspecialchars($producto['nombre']) . '</td>
                            <td>' . htmlspecialchars(substr($producto['descripcion'], 0, 50)) . '...</td>
                            <td>$' . number_format($producto['precio'], 2) . '</td>
                            <td>' . htmlspecialchars($producto['stock']) . '</td>
                            <td>
                                <a href="editar_producto.php?id=' . $producto['id_producto'] . '" class="btn btn-warning btn-sm">Editar</a>
                                <a href="gestion_productos.php?accion=eliminar&id=' . $producto['id_producto'] . '" class="btn btn-danger btn-sm" onclick="return confirm(\'¿Estás seguro de que quieres eliminar este producto?\')">Eliminar</a>
                            </td>
                        </tr>
                        ';
                    }
                } else {
                    echo '<tr><td colspan="6" class="text-center">No hay productos disponibles.</td></tr>';
                }
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>