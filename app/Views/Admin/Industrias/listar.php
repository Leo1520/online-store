<?php
/**
 * Vista: Listado de Industrias
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Industrias - Panel Admin</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include __DIR__ . '/../../header.php'; ?>

    <main class="container my-5">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1>Gestión de Industrias</h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="?controlador=admin&accion=crearIndustria" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Crear Industria
                </a>
            </div>
        </div>

        <?php if (isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_SESSION['mensaje']); unset($_SESSION['mensaje']); ?>
                <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
            </div>
        <?php endif; ?>

        <div class="card">
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

        <a href="?controlador=admin&accion=panel" class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </main>

    <?php include __DIR__ . '/../../footer.php'; ?>
