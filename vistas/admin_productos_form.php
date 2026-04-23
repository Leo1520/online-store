<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<!-- Cabecera -->
<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-<?php echo $esEditar ? 'pencil' : 'plus-circle'; ?> me-2"></i>
            <?php echo $esEditar ? 'Editar Producto' : 'Nuevo Producto'; ?>
        </h4>
    </div>
    <a href="/admin/index.php?page=productos" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Volver
    </a>
</div>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-2"></i><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<form id="formProducto" method="POST"
      action="/admin/index.php?page=<?php echo $esEditar ? 'productos_editar&id=' . (int)$producto['id_producto'] : 'productos_crear'; ?>"
      enctype="multipart/form-data">
    <input type="hidden" name="accion" value="<?php echo $esEditar ? 'editar_producto' : 'crear_producto'; ?>">
    <?php if ($esEditar): ?>
        <input type="hidden" name="id_producto" value="<?php echo (int)$producto['id_producto']; ?>">
    <?php endif; ?>

    <div class="row g-4">

        <!-- ══ COLUMNA PRINCIPAL (8) ══ -->
        <div class="col-lg-8">

            <!-- Información General -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-info-circle me-2" style="color:var(--primary)"></i>Información General
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                        <input type="text" name="nombre" class="form-control"
                               value="<?php echo htmlspecialchars($producto['nombre'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4"
                                  placeholder="Descripción detallada del producto..."><?php echo htmlspecialchars($producto['descripcion'] ?? ''); ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Categoría <span class="text-danger">*</span></label>
                            <select name="codCategoria" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo (int)$cat['cod']; ?>"
                                        <?php echo (isset($producto['codCategoria']) && (int)$producto['codCategoria'] === (int)$cat['cod']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Marca <span class="text-danger">*</span></label>
                            <select name="codMarca" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($marcas as $marca): ?>
                                    <option value="<?php echo (int)$marca['cod']; ?>"
                                        <?php echo (isset($producto['codMarca']) && (int)$producto['codMarca'] === (int)$marca['cod']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($marca['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Industria <span class="text-danger">*</span></label>
                            <select name="codIndustria" class="form-select" required>
                                <option value="">Seleccionar...</option>
                                <?php foreach ($industrias as $ind): ?>
                                    <option value="<?php echo (int)$ind['cod']; ?>"
                                        <?php echo (isset($producto['codIndustria']) && (int)$producto['codIndustria'] === (int)$ind['cod']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ind['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Precio -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-tag me-2" style="color:var(--primary)"></i>Precio
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Precio de Venta (Bs.) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Bs.</span>
                                <input type="number" name="precio" class="form-control"
                                       value="<?php echo htmlspecialchars($producto['precio'] ?? ''); ?>"
                                       step="0.01" min="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Estado <span class="text-danger">*</span></label>
                            <select name="estado" class="form-select">
                                <option value="activo" <?php echo (($producto['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                                <option value="inactivo" <?php echo (($producto['estado'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock por sucursal -->
            <div class="card">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-boxes me-2" style="color:var(--primary)"></i>Asignar Stock por Sucursal
                    <small class="text-muted fw-normal ms-2">(opcional, se puede hacer después)</small>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label small fw-semibold">Sucursal</label>
                            <select name="codSucursal" class="form-select">
                                <option value="">Seleccionar...</option>
                                <?php foreach ($sucursales as $suc): ?>
                                    <option value="<?php echo (int)$suc['cod']; ?>">
                                        <?php echo htmlspecialchars($suc['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Cantidad</label>
                            <input type="number" name="stock_inicial" class="form-control"
                                   min="0" placeholder="0">
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Se guardará al crear/actualizar el producto.</small>
                        </div>
                    </div>

                    <?php if ($esEditar && !empty($stocks)): ?>
                        <hr class="my-3">
                        <p class="small fw-semibold mb-2">Stock actual por sucursal:</p>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead><tr><th>Sucursal</th><th>Stock</th><th></th></tr></thead>
                                <tbody>
                                    <?php foreach ($stocks as $st):
                                        if ((int)$st['codProducto'] !== (int)$producto['id_producto']) continue; ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($st['sucursal']); ?></td>
                                            <td><strong><?php echo (int)$st['stock']; ?></strong></td>
                                            <td>
                                                <button type="button" class="btn btn-xs btn-outline-danger py-0 px-1"
                                                    onclick="confirmDelete('el stock de <?php echo htmlspecialchars($st['sucursal'], ENT_QUOTES); ?>', function(){
                                                        window.location='/admin/index.php?page=productos&eliminar_stock_producto=<?php echo (int)$st['codProducto']; ?>&eliminar_stock_sucursal=<?php echo (int)$st['codSucursal']; ?>';
                                                    })">
                                                    <i class="bi bi-trash" style="font-size:.7rem;"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ══ COLUMNA LATERAL (4) ══ -->
        <div class="col-lg-4">

            <!-- Imagen Principal -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-image me-2" style="color:var(--primary)"></i>Imagen Principal
                </div>
                <div class="card-body">
                    <!-- Preview -->
                    <div id="imgPreviewBox" class="text-center mb-3 <?php echo ($esEditar && !empty($producto['imagen'])) ? '' : 'd-none'; ?>">
                        <img id="imgPreview"
                             src="<?php echo $esEditar ? '/recursos/imagenes/' . htmlspecialchars($producto['imagen'] ?? '') : ''; ?>"
                             class="img-fluid rounded" style="max-height:180px;object-fit:contain;">
                    </div>
                    <input type="file" name="imagen_file" id="imagenFile" class="form-control form-control-sm mb-2"
                           accept="image/*" onchange="previsualizarImagen(this)">
                    <div class="form-text mb-2">JPG, PNG, WebP. Máx 5MB.</div>
                    <label class="form-label small">O nombre de archivo existente:</label>
                    <input type="text" name="imagen" class="form-control form-control-sm"
                           value="<?php echo htmlspecialchars($producto['imagen'] ?? 'producto.png'); ?>"
                           placeholder="nombre-archivo.png">
                </div>
            </div>

            <!-- Opciones -->
            <div class="card">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-sliders me-2" style="color:var(--primary)"></i>Resumen
                </div>
                <div class="card-body">
                    <?php if ($esEditar): ?>
                        <div class="mb-2 small">
                            <span class="text-muted">ID:</span>
                            <strong class="ms-1">#<?php echo (int)$producto['id_producto']; ?></strong>
                        </div>
                        <div class="mb-2 small">
                            <span class="text-muted">Stock total:</span>
                            <strong class="ms-1"><?php echo (int)($producto['stock'] ?? 0); ?> uds.</strong>
                        </div>
                    <?php endif; ?>
                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn fw-semibold text-white"
                                style="background:var(--primary);">
                            <i class="bi bi-floppy me-2"></i>
                            <?php echo $esEditar ? 'Actualizar Producto' : 'Guardar Producto'; ?>
                        </button>
                        <a href="/admin/index.php?page=productos" class="btn btn-outline-secondary btn-sm">
                            Cancelar
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>

<script>
function previsualizarImagen(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('imgPreview').src = e.target.result;
            document.getElementById('imgPreviewBox').classList.remove('d-none');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
