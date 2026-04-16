<?php
session_start();

// Verificar si el administrador ha iniciado sesión
if (!isset($_SESSION['admin'])) {
    header("Location: inicio_sesion.php");
    exit();
}

// Incluir la conexión a la base de datos
include '../incluir/conexion.php';

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);

    // Validaciones
    if (empty($nombre) || empty($descripcion) || $precio <= 0 || $stock < 0) {
        $error = "Por favor completa todos los campos correctamente.";
    } else {
        // Manejo de la imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
            $nombre_imagen = basename($_FILES['imagen']['name']);
            $extension = strtolower(pathinfo($nombre_imagen, PATHINFO_EXTENSION));
            $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (!in_array($extension, $extensiones_permitidas)) {
                $error = "Tipo de imagen no permitido. Usa: jpg, jpeg, png, gif, webp";
            } else {
                // Renombrar imagen con timestamp para evitar duplicados
                $nombre_imagen = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $nombre_imagen);
                $ruta_destino = '../recursos/imagenes/' . $nombre_imagen;

                // Mover la imagen al directorio de destino
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
                    // Usar prepared statements para evitar inyección SQL
                    $stmt = $conexion->prepare("INSERT INTO productos (nombre, descripcion, precio, imagen, stock) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $nombre_imagen, $stock);
                    
                    if ($stmt->execute()) {
                        $_SESSION['mensaje'] = "Producto agregado con éxito.";
                        header("Location: gestion_productos.php");
                        exit();
                    } else {
                        $error = "Error al guardar el producto: " . $conexion->error;
                        // Eliminar imagen si hay error
                        if (file_exists($ruta_destino)) {
                            unlink($ruta_destino);
                        }
                    }
                    $stmt->close();
                } else {
                    $error = "Error al subir la imagen.";
                }
            }
        } else {
            $error = "No se ha seleccionado una imagen o hubo un error al subirla.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Agregar Nuevo Producto</h2>
        <?php if (isset($error)) { echo '<div class="alert alert-danger">' . $error . '</div>'; } ?>
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nombre">Nombre del producto:</label>
                <input type="text" class="form-control" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" step="0.01" class="form-control" id="precio" name="precio" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock:</label>
                <input type="number" class="form-control" id="stock" name="stock" required>
            </div>
            <div class="form-group">
                <label for="imagen">Imagen:</label>
                <input type="file" class="form-control-file" id="imagen" name="imagen" accept="image/*" required>
            </div>
            <button type="submit" class="btn btn-success">Agregar Producto</button>
            <a href="gestion_productos.php" class="btn btn-secondary">Volver</a>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>