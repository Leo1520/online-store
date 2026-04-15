<?php
session_start();

// Verificar si el administrador ha iniciado sesión
if (!isset($_SESSION['admin'])) {
    header("Location: inicio_sesion.php");
    exit();
}

// Incluir la conexión a la base de datos
include '../incluir/conexion.php';

// Obtener el producto a editar
if (isset($_GET['id'])) {
    $id_producto = intval($_GET['id']);
    $stmt = $conexion->prepare("SELECT * FROM productos WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 1) {
        $producto = $resultado->fetch_assoc();
    } else {
        header("Location: gestion_productos.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: gestion_productos.php");
    exit();
}

// Manejar el envío del formulario para actualizar el producto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $nombre_imagen = $producto['imagen']; // Imagen actual por defecto

    // Validaciones
    if (empty($nombre) || empty($descripcion) || $precio <= 0 || $stock < 0) {
        $error = "Por favor completa todos los campos correctamente.";
    } else {
        // Verificar si se ha subido una nueva imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($extension, $extensiones_permitidas)) {
                $error = "Tipo de imagen no permitido. Usa: jpg, jpeg, png, gif, webp";
            } else {
                $nombre_imagen = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['imagen']['name']));
                $ruta_destino = '../recursos/imagenes/' . $nombre_imagen;

                // Mover la nueva imagen al directorio de destino
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    // Eliminar imagen anterior si existe
                    $ruta_antigua = '../recursos/imagenes/' . $producto['imagen'];
                    if (file_exists($ruta_antigua) && $producto['imagen']) {
                        unlink($ruta_antigua);
                    }
                } else {
                    $error = "Error al subir la nueva imagen.";
                }
            }
        }

        // Si no hay error, actualizar los datos en la base de datos
        if (!isset($error)) {
            $stmt = $conexion->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, imagen=?, stock=? WHERE id_producto=?");
            $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio, $nombre_imagen, $stock, $id_producto);
            
            if ($stmt->execute()) {
                $_SESSION['mensaje'] = "Producto actualizado con éxito.";
                header("Location: gestion_productos.php");
                exit();
            } else {
                $error = "Error al actualizar el producto: " . $conexion->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Editar Producto</h2>
        <?php if (isset($error)) { echo '<div class="alert alert-danger">' . $error . '</div>'; } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del producto:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $producto['nombre']; ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required><?php echo $producto['descripcion']; ?></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" value="<?php echo $producto['precio']; ?>" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" class="form-control" id="stock" name="stock" value="<?php echo $producto['stock']; ?>" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen (deja en blanco si no deseas cambiarla):</label>
                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*">
                <small class="form-text text-muted">Imagen actual: <?php echo $producto['imagen']; ?></small>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Producto</button>
            <a href="gestion_productos.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>