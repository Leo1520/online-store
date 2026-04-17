<?php
/**
 * Vista Layout Admin
 * Incluye header y sidebar para todas las vistas del admin
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($titulo) ? htmlspecialchars($titulo) . ' - ' : ''; ?>Panel Admin - Tienda en Línea</title>
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
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar.collapsed .sidebar-text {
            display: none;
        }
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        .sidebar.collapsed .section-title {
            display: none;
        }
        .sidebar-toggle {
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            padding: 10px 20px;
            font-size: 1.2rem;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        .sidebar-toggle:hover {
            background-color: #495057;
            border-radius: 5px;
            transform: scale(1.1);
        }
        .sidebar-text {
            transition: all 0.3s ease;
        }
        .sidebar .nav-link {
            color: #ffffff;
            padding: 10px 20px;
            transition: all 0.2s ease;
        }
        .sidebar .nav-link:hover {
            background-color: #495057;
            border-radius: 5px;
            padding-left: 25px;
        }
        .sidebar .nav-link.active {
            background-color: #667eea;
            border-radius: 5px;
        }
        .section-title {
            font-size: 0.85rem;
            font-weight: bold;
            padding: 10px 20px;
            margin-top: 15px;
            margin-bottom: 5px;
            color: #b0bec5;
            cursor: pointer;
            user-select: none;
            transition: all 0.2s ease;
        }
        .section-title:hover {
            color: #ffffff;
            padding-left: 25px;
        }
        .section-title .collapse-icon {
            float: right;
            transition: transform 0.3s ease;
        }
        .section-title[aria-expanded="false"] .collapse-icon {
            transform: rotate(-90deg);
        }
        .submenu-collapse {
            transition: all 0.3s ease;
        }
        .stat-card {
            border-left: 4px solid #667eea;
            padding: 20px;
            margin: 10px 0;
        }
        .main-content {
            margin-left: 20%;
            padding: 20px;
            transition: all 0.3s ease;
        }
        body.sidebar-collapsed .main-content {
            margin-left: 70px;
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
            <nav class="col-md-3 sidebar" id="sidebar">
                <button class="sidebar-toggle" id="sidebarToggle" title="Contraer/Expandir menú">
                    <i class="bi bi-list"></i>
                </button>
                <h5 class="text-white mb-4 sidebar-text">Panel Admin</h5>
                <nav class="nav flex-column">
                    <a class="nav-link" href="?controlador=admin&accion=panel">
                        <i class="bi bi-speedometer2"></i> <span>Dashboard</span>
                    </a>
                    <hr class="bg-secondary">
                    
                    <!-- Sección Productos (Colapsible) -->
                    <h6 class="section-title" data-toggle="collapse" data-target="#productosMenu" aria-expanded="true">
                        <i class="bi bi-box"></i> <span>Productos</span>
                        <i class="bi bi-chevron-right collapse-icon"></i>
                    </h6>
                    <nav class="nav flex-column collapse show submenu-collapse" id="productosMenu">
                        <a class="nav-link pl-4" href="?controlador=productos&accion=listar">
                            <i class="bi bi-eye"></i> <span>Ver Productos</span>
                        </a>
                        <a class="nav-link pl-4" href="?controlador=productos&accion=crear">
                            <i class="bi bi-plus-circle"></i> <span>Crear Producto</span>
                        </a>
                    </nav>
                    
                    <hr class="bg-secondary">
                    
                    <!-- Sección Catálogo (Colapsible) -->
                    <h6 class="section-title" data-toggle="collapse" data-target="#catalogoMenu" aria-expanded="false">
                        <i class="bi bi-collection"></i> <span>Catálogo</span>
                        <i class="bi bi-chevron-right collapse-icon"></i>
                    </h6>
                    <nav class="nav flex-column collapse submenu-collapse" id="catalogoMenu">
                        <a class="nav-link pl-4" href="?controlador=admin&accion=listarMarcas">
                            <i class="bi bi-tag"></i> <span>Marcas</span>
                        </a>
                        <a class="nav-link pl-4" href="?controlador=admin&accion=listarCategorias">
                            <i class="bi bi-collection"></i> <span>Categorías</span>
                        </a>
                        <a class="nav-link pl-4" href="?controlador=admin&accion=listarIndustrias">
                            <i class="bi bi-diagram-3"></i> <span>Industrias</span>
                        </a>
                    </nav>
                    
                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'): ?>
                        <hr class="bg-secondary">
                        
                        <!-- Sección Administración (Colapsible) -->
                        <h6 class="section-title" data-toggle="collapse" data-target="#adminMenu" aria-expanded="false">
                            <i class="bi bi-gear"></i> <span>Administración</span>
                            <i class="bi bi-chevron-right collapse-icon"></i>
                        </h6>
                        <nav class="nav flex-column collapse submenu-collapse" id="adminMenu">
                            <a class="nav-link pl-4" href="?controlador=admin&accion=listarSucursales">
                                <i class="bi bi-shop"></i> <span>Sucursales</span>
                            </a>
                            <a class="nav-link pl-4" href="?controlador=admin&accion=reportes">
                                <i class="bi bi-graph-up"></i> <span>Reportes</span>
                            </a>
                        </nav>
                    <?php endif; ?>
                </nav>
            </nav>

            <!-- Contenido Principal -->
            <main class="col-md-9 main-content">
                <!-- El contenido específico se inserta aquí -->
                <?php include $vistaContenido; ?>
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
    
    <script>
        // Toggle del sidebar (contraer/expandir)
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.querySelector('.main-content');
            const body = document.body;
            
            sidebar.classList.toggle('collapsed');
            body.classList.toggle('sidebar-collapsed');
            
            // Guardar estado en localStorage
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        
        // Restaurar estado del sidebar
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    </script>
</body>
</html>
