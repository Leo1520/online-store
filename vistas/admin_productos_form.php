<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

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

                    <!-- Código + Nombre -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label class="form-label small fw-semibold">Código Producto</label>
                            <input type="text" name="codigo" class="form-control font-monospace"
                                   value="<?php echo htmlspecialchars($producto['codigo'] ?? ''); ?>"
                                   placeholder="Ej: 000813" maxlength="20">
                            <div class="form-text">SKU del Excel</div>
                        </div>
                        <div class="col-md-9">
                            <label class="form-label small fw-semibold">Nombre del Producto <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control"
                                   value="<?php echo htmlspecialchars($producto['nombre'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="3"
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

            <!-- Precios -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-tags me-2" style="color:var(--primary)"></i>Precios
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">
                                Precio Propuesto (Bs.)
                                <span class="text-muted fw-normal">— lista/referencia</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Bs.</span>
                                <input type="number" name="precioPropuesto" id="precioPropuesto" class="form-control"
                                       value="<?php echo htmlspecialchars($producto['precioPropuesto'] ?? ''); ?>"
                                       step="0.01" min="0" placeholder="0.00"
                                       oninput="calcDescuento()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">
                                Precio Vigente (Bs.) <span class="text-danger">*</span>
                                <span class="text-muted fw-normal">— precio actual</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white">Bs.</span>
                                <input type="number" name="precioVigente" id="precioVigente" class="form-control border-success"
                                       value="<?php echo htmlspecialchars($producto['precioVigente'] ?? ''); ?>"
                                       step="0.01" min="0.01" required
                                       oninput="calcDescuento()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Descuento</label>
                            <div id="descuentoBox" class="p-2 rounded border text-center" style="min-height:38px;">
                                <span id="descuentoLabel" class="fw-bold">—</span>
                            </div>
                            <div class="form-text">Calculado automáticamente</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estado -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-toggle-on me-2" style="color:var(--primary)"></i>Estado
                </div>
                <div class="card-body">
                    <select name="estado" class="form-select">
                        <option value="activo" <?php echo (($producto['estado'] ?? 'activo') === 'activo') ? 'selected' : ''; ?>>Activo</option>
                        <option value="inactivo" <?php echo (($producto['estado'] ?? '') === 'inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                    </select>
                </div>
            </div>

            <!-- Stock por sucursal -->
            <div class="card">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-boxes me-2" style="color:var(--primary)"></i>Asignar Stock por Sucursal
                    <small class="text-muted fw-normal ms-2">(opcional)</small>
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
                            <input type="number" name="stock_inicial" class="form-control" min="0" placeholder="0">
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

            <!-- Imagen -->
            <div class="card mb-4">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-image me-2" style="color:var(--primary)"></i>Imagen Principal
                </div>
                <div class="card-body">
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

            <!-- Resumen -->
            <div class="card">
                <div class="card-header bg-white fw-semibold py-3 border-bottom">
                    <i class="bi bi-receipt me-2" style="color:var(--primary)"></i>Resumen
                </div>
                <div class="card-body">
                    <?php if ($esEditar): ?>
                        <div class="mb-2 small d-flex justify-content-between">
                            <span class="text-muted">ID Interno:</span>
                            <strong>#<?php echo (int)$producto['id_producto']; ?></strong>
                        </div>
                        <?php if (!empty($producto['codigo'])): ?>
                        <div class="mb-2 small d-flex justify-content-between">
                            <span class="text-muted">Código SKU:</span>
                            <strong class="font-monospace"><?php echo htmlspecialchars($producto['codigo']); ?></strong>
                        </div>
                        <?php endif; ?>
                        <div class="mb-2 small d-flex justify-content-between">
                            <span class="text-muted">P. Propuesto:</span>
                            <span>Bs. <?php echo number_format((float)($producto['precioPropuesto'] ?? 0), 2); ?></span>
                        </div>
                        <div class="mb-2 small d-flex justify-content-between">
                            <span class="text-muted">P. Vigente:</span>
                            <strong class="text-success">Bs. <?php echo number_format((float)($producto['precioVigente'] ?? 0), 2); ?></strong>
                        </div>
                        <?php
                            $pv = (float)($producto['precioVigente'] ?? 0);
                            $pp = (float)($producto['precioPropuesto'] ?? 0);
                            if ($pp > 0 && $pv < $pp):
                                $pct = round((($pp - $pv) / $pp) * 100);
                        ?>
                        <div class="mb-2 small d-flex justify-content-between">
                            <span class="text-muted">Descuento:</span>
                            <span class="badge bg-danger">-<?php echo $pct; ?>%</span>
                        </div>
                        <?php endif; ?>
                        <div class="mb-2 small d-flex justify-content-between">
                            <span class="text-muted">Stock total:</span>
                            <strong><?php echo (int)($producto['stock'] ?? 0); ?> uds.</strong>
                        </div>
                        <hr class="my-2">
                    <?php endif; ?>
                    <div class="d-grid gap-2 mt-2">
                        <button type="submit" class="btn fw-semibold text-white" style="background:var(--primary);">
                            <i class="bi bi-floppy me-2"></i>
                            <?php echo $esEditar ? 'Actualizar Producto' : 'Guardar Producto'; ?>
                        </button>
                        <a href="/admin/index.php?page=productos" class="btn btn-outline-secondary btn-sm">Cancelar</a>
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

function calcDescuento() {
    const pp  = parseFloat(document.getElementById('precioPropuesto').value) || 0;
    const pv  = parseFloat(document.getElementById('precioVigente').value)   || 0;
    const box = document.getElementById('descuentoBox');
    const lbl = document.getElementById('descuentoLabel');

    if (pp > 0 && pv > 0 && pv < pp) {
        const pct = Math.round(((pp - pv) / pp) * 100);
        lbl.textContent  = '-' + pct + '% descuento';
        lbl.className    = 'fw-bold text-danger';
        box.style.borderColor = '#dc3545';
        box.style.background  = '#fff5f5';
    } else if (pp > 0 && pv >= pp) {
        lbl.textContent  = 'Sin descuento';
        lbl.className    = 'fw-bold text-muted';
        box.style.borderColor = '';
        box.style.background  = '';
    } else {
        lbl.textContent  = '—';
        lbl.className    = 'fw-bold text-muted';
        box.style.borderColor = '';
        box.style.background  = '';
    }
}

document.addEventListener('DOMContentLoaded', calcDescuento);
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
