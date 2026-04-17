<?php
/**
 * Vista: Editar Categoría
 */
?>
<h2 class="mb-4">Editar Categoría</h2>

<div class="row justify-content-center">
    <div class="col-md-8">
        <form method="POST" action="?controlador=admin&accion=actualizarCategoria&id=<?php echo $categoria['cod']; ?>">
                    <div class="card">
                        <div class="card-body">
                            <div class="form-group">
                                <label for="nombre">Nombre de la Categoría:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($categoria['nombre']); ?>" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Actualizar
                            </button>
                            <a href="?controlador=admin&accion=listarCategorias" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Volver
                            </a>
                        </div>
                    </div>
        </form>
    </div>
</div>
