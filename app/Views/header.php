<?php
/**
 * Encabezado Parcial
 * Incluye solo la barra de navegación
 */
?>
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
            <li class="nav-item">
                <a class="nav-link" href="?controlador=carrito&accion=mostrar">
                    <i class="bi bi-cart3"></i> Carrito
                    <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
                        <span class="badge badge-danger"><?php echo count($_SESSION['carrito']); ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <?php if (isset($_SESSION['usuario'])): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['usuario']); ?>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href="?controlador=autenticacion&accion=perfil">Mi Perfil</a>
                        <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], ['admin', 'trabajador'])): ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="?controlador=admin&accion=panel">
                                <i class="bi bi-speedometer2"></i> Panel Admin
                            </a>
                        <?php endif; ?>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?controlador=autenticacion&accion=cerrarSesion">Cerrar Sesión</a>
                    </div>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="?controlador=autenticacion&accion=mostrarLogin">
                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?controlador=autenticacion&accion=mostrarRegistro">
                        <i class="bi bi-person-plus"></i> Registrarse
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
