<?php
/**
 * Vista: Listado de Industrias
 * Solo contenido (sin layout)
 */
?>
<h2 class="mb-4">Gestión de Industrias</h2>

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
        <a href="?controlador=admin&accion=crearIndustria" class="btn btn-success float-right">
            <i class="bi bi-plus-circle"></i> Crear Industria
        </a>
    </div>
    <div class="card-body">
        <?php if (empty($industrias)): ?>
            <div class="alert alert-info">No hay industrias registradas.</div>
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
                    <?php foreach ($industrias as $industria): ?>
                        <tr>
                            <td><?php echo $industria['cod']; ?></td>
                            <td><?php echo htmlspecialchars($industria['nombre']); ?></td>
                            <td>
                                <a href="?controlador=admin&accion=editarIndustria&id=<?php echo $industria['cod']; ?>" class="btn btn-sm btn-primary">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form method="POST" action="?controlador=admin&accion=eliminarIndustria&id=<?php echo $industria['cod']; ?>" style="display: inline;" onsubmit="return confirm('¿Estás seguro?');">
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
