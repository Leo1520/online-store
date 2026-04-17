<?php
/**
 * Vista: Listado de Marcas
 * Solo contenido (sin layout)
 */
?>
<h2 class="mb-4">Gestión de Marcas</h2>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <a href="?controlador=admin&accion=crearMarca" class="btn btn-success float-right">
            <i class="bi bi-plus-circle"></i> Crear Marca
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($marcas)): ?>
            <div class="alert alert-info">No hay marcas registradas.</div>
        <?php else: ?>
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($marcas as $marca): ?>
                        <tr>
                            <td><?php echo $marca['cod']; ?></td>
                            <td><?php echo htmlspecialchars($marca['nombre']); ?></td>
                            <td>
                                <a href="?controlador=admin&accion=editarMarca&id=<?php echo $marca['cod']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form method="POST" action="?controlador=admin&accion=eliminarMarca&id=<?php echo $marca['cod']; ?>" style="display: inline;" onsubmit="return confirm('¿Estás seguro?');">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
