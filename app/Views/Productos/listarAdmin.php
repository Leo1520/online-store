<?php
/**
 * Vista: Lista de Productos (Admin)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos - Panel Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .table-container {
            overflow-x: auto;
        }
        .btn-group-sm {
            gap: 5px;
        }
        .img-thumbnail {
            max-width: 80px;
            height: auto;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../header.php'; ?>

    <main class="container-fluid my-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Gestión de Productos</h1>
                <p class="text-muted">Administra el catálogo de productos de la tienda</p>
            </div>
            <div class="col-md-4 text-right">
                <a href="?controlador=productos&accion=crear" class="btn btn-success btn-lg">
                    <i class="bi bi-plus-circle"></i> Crear Producto
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">
                <h5>Listado de Productos (<?php echo count($productos); ?>)</h5>
            </div>
            <div class="card-body">
                <?php if (empty($productos)): ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> No hay productos registrados. 
                        <a href="?controlador=productos&accion=crear">Crear uno ahora</a>
                    </div>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-hover table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Marca</th>
                                    <th>Categoría</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($productos as $producto): ?>
                                    <tr>
                                        <td>
                                            <img src="../../Recursos/imagenes/<?php echo htmlspecialchars($producto['imagen'] ?? 'default.jpg'); ?>" 
                                                 class="img-thumbnail" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($producto['nombre']); ?></strong><br>
                                            <small class="text-muted"><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)); ?>...</small>
                                        </td>
                                        <td>
                                            <strong>$<?php echo number_format($producto['precio'], 2); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php 
                                                echo $producto['estado'] === 'Activo' ? 'success' : 
                                                     ($producto['estado'] === 'Inactivo' ? 'warning' : 'danger');
                                            ?>">
                                                <?php echo htmlspecialchars($producto['estado']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($producto['codMarca'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <small><?php echo htmlspecialchars($producto['codCategoria'] ?? 'N/A'); ?></small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="?controlador=productos&accion=detalle&id=<?php echo $producto['cod']; ?>" 
                                                   class="btn btn-info" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="?controlador=productos&accion=editar&id=<?php echo $producto['cod']; ?>" 
                                                   class="btn btn-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form method="POST" action="?controlador=productos&accion=eliminar&id=<?php echo $producto['cod']; ?>" 
                                                      style="display: inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este producto?');">
                                                    <button type="submit" class="btn btn-danger" title="Eliminar">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-4">
            <a href="?controlador=admin&accion=panel" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Panel
            </a>
        </div>
    </main>

    <?php include __DIR__ . '/../footer.php'; ?>
