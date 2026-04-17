<?php
/**
 * Vista: Dashboard del Panel de Administración
 * Solo contiene el contenido, sin layout
 */
?>
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
