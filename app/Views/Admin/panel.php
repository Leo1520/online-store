<?php
/**
 * Vista: Panel de Administración
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Tienda en Línea</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1;
        }
        footer {
            background-color: #343a40;
            color: white;
            margin-top: auto;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            width: 20%;
            left: 0;
            top: 60px;
        }
        .sidebar .nav-link {
            color: #ffffff;
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            border-radius: 5px;
        }
        .sidebar .nav-link.active {
            background-color: #667eea;
            border-radius: 5px;
        }
        .stat-card {
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 10px 0;
        }
        .main-content {
            margin-left: 20%;
            padding: 20px;
        }
    </style>
</head>
<body>
    <!-- Navegación Superior -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="?controlador=productos&accion=listar">🛒 Tienda Online</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="?controlador=productos&accion=listar">Productos</a>
                </li>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="?controlador=autenticacion&accion=cerrarSesion">Cerrar Sesión</a>
                        </div>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 sidebar">
                <h5 class="text-white mb-4">Panel Admin</h5>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="?controlador=admin&accion=panel">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <hr class="bg-secondary">
                    <h6 class="text-white px-3">Productos</h6>
                    <a class="nav-link" href="?controlador=productos&accion=listar">
                        <i class="bi bi-box"></i> Ver Productos
                    </a>
                    <a class="nav-link" href="?controlador=productos&accion=crear">
                        <i class="bi bi-plus-circle"></i> Crear Producto
                    </a>
                    <hr class="bg-secondary">
                    <h6 class="text-white px-3">Catálogo</h6>
                    <a class="nav-link" href="?controlador=admin&accion=listarMarcas">
                        <i class="bi bi-tag"></i> Marcas
                    </a>
                    <a class="nav-link" href="?controlador=admin&accion=listarCategorias">
                        <i class="bi bi-collection"></i> Categorías
                    </a>
                    <a class="nav-link" href="?controlador=admin&accion=listarIndustrias">
                        <i class="bi bi-diagram-3"></i> Industrias
                    </a>
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                        <hr class="bg-secondary">
                        <h6 class="text-white px-3">Administración</h6>
                        <a class="nav-link" href="?controlador=admin&accion=listarSucursales">
                            <i class="bi bi-shop"></i> Sucursales
                        </a>
                        <a class="nav-link" href="?controlador=admin&accion=reportes">
                            <i class="bi bi-graph-up"></i> Reportes
                        </a>
                    <?php endif; ?>
                </nav>
            </nav>

            <!-- Contenido Principal -->
            <main class="col-md-9 main-content">
                <h2 class="mb-4">Dashboard</h2>

                <div class="row">
                    <div class="col-md-6">
                        <div class="stat-card bg-light">
                            <h5>Total de Productos</h5>
                            <h2 class="text-primary"><?php echo $totalProductos; ?></h2>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="stat-card bg-light">
                            <h5>Total de Ventas</h5>
                            <h2 class="text-success"><?php echo $totalVentas; ?></h2>
                        </div>
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5>Opciones Rápidas</h5>
                    </div>
                    <div class="card-body">
                        <a href="?controlador=productos&accion=crear" class="btn btn-success btn-lg">
                            <i class="bi bi-plus-circle"></i> Crear Nuevo Producto
                        </a>
                        <a href="?controlador=productos&accion=listar" class="btn btn-info btn-lg">
                            <i class="bi bi-list"></i> Gestionar Productos
                        </a>
                        <a href="?controlador=productos&accion=listar" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-left"></i> Ir a la Tienda
                        </a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> Tienda en Línea. Todos los derechos reservados.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
