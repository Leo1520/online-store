<?php
// Incluir la conexión a la base de datos
include 'incluir/conexion.php';
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - Tienda en Línea</title>
    <link rel="stylesheet" href="recursos/css/estilos.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'incluir/encabezado.php'; ?>

    <div class="container mt-5">
        <h1 class="text-center mb-5">Bienvenido a nuestra tienda en línea</h1>
        
        <?php if (isset($_SESSION['mensaje'])) { 
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
            echo $_SESSION['mensaje'];
            echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>';
            echo '</div>';
            unset($_SESSION['mensaje']);
        } ?>
        
        <div class="row">
            <?php
            // Consultar productos de la base de datos
            $consulta = "SELECT * FROM productos ORDER BY fecha_creacion DESC";
            $resultado = $conexion->query($consulta);

            if ($resultado->num_rows > 0) {
                while ($producto = $resultado->fetch_assoc()) {
                    // Concatenar la ruta de la carpeta con el nombre del archivo
                    $ruta_imagen = 'recursos/imagenes/' . htmlspecialchars($producto['imagen']);
                    $disponible = $producto['stock'] > 0 ? 'Disponible' : 'Agotado';
                    $clase_boton = $producto['stock'] > 0 ? 'btn-primary' : 'btn-secondary disabled';

                    echo '
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-img-top" style="height: 200px; overflow: hidden;">
                                <img src="' . $ruta_imagen . '" class="img-fluid" alt="' . htmlspecialchars($producto['nombre']) . '" style="width: 100%; object-fit: cover; height: 100%;">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">' . htmlspecialchars($producto['nombre']) . '</h5>
                                <p class="card-text">' . htmlspecialchars(substr($producto['descripcion'], 0, 80)) . '...</p>
                                <p class="card-text"><strong>Precio: $' . number_format($producto['precio'], 2) . '</strong></p>
                                <p class="card-text">
                                    <small class="text-muted">' . $disponible . ' (Stock: ' . $producto['stock'] . ')</small>
                                </p>
                                <a href="carrito.php?accion=agregar&id=' . $producto['id_producto'] . '" class="btn ' . $clase_boton . ' btn-block">Agregar al carrito</a>
                            </div>
                        </div>
                    </div>
                    ';
                }
            } else {
                echo '<p class="text-center col-12">No hay productos disponibles en este momento.</p>';
            }
            ?>
        </div>
    </div>

    <?php include 'incluir/pie.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>