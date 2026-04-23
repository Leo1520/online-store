<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
/* ── Layout general ── */
.alm-container { max-width: 1400px; margin: 0 auto; padding: 24px 16px; }
.alm-titulo { font-size: 22px; font-weight: 800; color: var(--azul); margin-bottom: 4px; }
.alm-subtitulo { font-size: 13px; color: #888; margin-bottom: 24px; }

/* ── Tarjetas métricas ── */
.alm-cards { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }
@media(max-width:768px){ .alm-cards{ grid-template-columns: repeat(2,1fr); } }
.alm-card {
    background: #fff; border-radius: 14px; padding: 18px 20px;
    box-shadow: 0 2px 12px rgba(27,58,107,.08); display: flex; align-items: center; gap: 14px;
}
.alm-card-ico {
    width: 48px; height: 48px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0;
}
.alm-card-ico.azul   { background: #e8f0fe; color: var(--azul); }
.alm-card-ico.verde  { background: #e6f9ed; color: #28a745; }
.alm-card-ico.amari  { background: #fff7e0; color: var(--amarillo); }
.alm-card-ico.rojo   { background: #fdecea; color: #dc3545; }
.alm-card-num { font-size: 26px; font-weight: 900; color: var(--azul); line-height: 1; }
.alm-card-lbl { font-size: 12px; color: #888; margin-top: 2px; }

/* ── Tabs ── */
.alm-tabs { display: flex; gap: 4px; border-bottom: 2px solid #e8ecf8; margin-bottom: 20px; flex-wrap: wrap; }
.alm-tab {
    padding: 10px 18px; font-size: 13px; font-weight: 600; color: #888;
    cursor: pointer; border-radius: 8px 8px 0 0; border: none; background: none;
    transition: all .15s;
}
.alm-tab:hover { color: var(--azul); background: #f0f4ff; }
.alm-tab.activo { color: var(--azul); background: #fff; border: 2px solid #e8ecf8; border-bottom: 2px solid #fff; margin-bottom: -2px; }
.alm-panel { display: none; }
.alm-panel.activo { display: block; }

/* ── Barra de acciones (filtros + export) ── */
.alm-barra {
    background: #fff; border-radius: 12px; padding: 14px 18px;
    box-shadow: 0 2px 8px rgba(27,58,107,.06); margin-bottom: 16px;
    display: flex; align-items: flex-end; gap: 10px; flex-wrap: wrap;
}
.alm-barra .form-group { margin-bottom: 0; flex: 1; min-width: 130px; }
.alm-barra label { font-size: 11px; font-weight: 600; color: #666; margin-bottom: 3px; text-transform: uppercase; letter-spacing: .4px; }
.alm-barra .form-control { font-size: 13px; height: 36px; border-radius: 8px; border: 1.5px solid #d8e0f0; }
.alm-barra .form-control:focus { border-color: var(--azul); box-shadow: none; }

/* ── Botones export ── */
.export-btns { display: flex; gap: 6px; flex-shrink: 0; }
.btn-exp {
    display: flex; align-items: center; gap: 5px;
    padding: 7px 13px; border: none; border-radius: 8px;
    font-size: 12px; font-weight: 700; cursor: pointer; transition: opacity .2s;
}
.btn-exp:hover { opacity: .85; }
.btn-exp.pdf  { background: #1B3A6B; color: #fff; }
.btn-exp.print{ background: #6c757d; color: #fff; }
.btn-exp.xls  { background: #1d7a35; color: #fff; }

/* ── Tabla ── */
.alm-table-wrap { background: #fff; border-radius: 12px; box-shadow: 0 2px 8px rgba(27,58,107,.06); overflow: hidden; }
.alm-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.alm-table thead tr { background: var(--azul); color: #fff; }
.alm-table thead th { padding: 10px 12px; font-weight: 600; font-size: 12px; white-space: nowrap; }
.alm-table tbody tr:nth-child(even) { background: #f8f9ff; }
.alm-table tbody tr:hover { background: #eef2ff; }
.alm-table tbody td { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; vertical-align: middle; }
.alm-table tfoot td { padding: 8px 12px; font-weight: 700; background: #f0f4ff; }

/* ── Badges tipo movimiento ── */
.badge-tipo {
    display: inline-block; padding: 3px 9px; border-radius: 20px;
    font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; white-space: nowrap;
}
.tipo-venta           { background: #fdecea; color: #b71c1c; }
.tipo-traspaso_salida { background: #fff3e0; color: #e65100; }
.tipo-traspaso_entrada{ background: #e8f5e9; color: #1b5e20; }
.tipo-devolucion      { background: #e3f2fd; color: #0d47a1; }
.tipo-baja            { background: #f3e5f5; color: #4a148c; }
.tipo-ajuste_entrada  { background: #e0f7fa; color: #006064; }
.tipo-ajuste_salida   { background: #fce4ec; color: #880e4f; }

/* ── Badge estado traspaso ── */
.badge-estado { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.est-pendiente  { background: #fff3cd; color: #856404; }
.est-completado { background: #d1e7dd; color: #0a3622; }
.est-cancelado  { background: #f8d7da; color: #842029; }

/* ── Badge stock crítico ── */
.badge-agotado { background: #dc3545; color: #fff; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.badge-bajo    { background: #ffc107; color: #333; padding: 3px 9px; border-radius: 20px; font-size: 11px; font-weight: 700; }

/* ── Formularios internos ── */
.alm-form-card {
    background: #fff; border-radius: 12px; padding: 20px 22px;
    box-shadow: 0 2px 8px rgba(27,58,107,.06); margin-bottom: 16px;
}
.alm-form-card h6 { font-size: 14px; font-weight: 700; color: var(--azul); margin-bottom: 14px; }
.alm-form-card .form-control, .alm-form-card .form-control-sm { border-radius: 8px; border: 1.5px solid #d8e0f0; font-size: 13px; }
.alm-form-card .form-control:focus { border-color: var(--azul); box-shadow: none; }
.alm-form-card .btn { border-radius: 8px; font-weight: 600; font-size: 13px; }

/* ── Fila dinámica de productos en traspaso ── */
.prod-row { display: flex; gap: 8px; align-items: center; margin-bottom: 8px; }
.prod-row select, .prod-row input { flex: 1; }
.prod-row .btn-rm { flex-shrink: 0; }

/* ── Loading spinner ── */
.alm-loading { text-align: center; padding: 40px; color: #aaa; }
.alm-loading .spinner-border { width: 2rem; height: 2rem; color: var(--azul); }

/* ── Detalle expandible traspaso ── */
.trp-detail { display: none; background: #f8f9ff; }
.trp-detail td { padding: 6px 16px !important; }
.trp-detail-table { width: 100%; font-size: 12px; }
.trp-detail-table th { padding: 4px 8px; background: #e8ecf8; }
.trp-detail-table td { padding: 4px 8px; border-bottom: 1px solid #eee; }
</style>

<div class="alm-container">

    <!-- Título -->
    <div class="alm-titulo"><i class="bi bi-archive-fill mr-2"></i>Módulo de Almacén</div>
    <div class="alm-subtitulo">Kardex, traspasos, ajustes y stock crítico — Electrohogar</div>

    <!-- Tarjetas métricas -->
    <div class="alm-cards">
        <div class="alm-card">
            <div class="alm-card-ico azul"><i class="bi bi-box-seam"></i></div>
            <div>
                <div class="alm-card-num"><?php echo $totalProductos; ?></div>
                <div class="alm-card-lbl">Productos con stock</div>
            </div>
        </div>
        <div class="alm-card">
            <div class="alm-card-ico verde"><i class="bi bi-layers"></i></div>
            <div>
                <div class="alm-card-num"><?php echo number_format($stockTotal); ?></div>
                <div class="alm-card-lbl">Unidades en almacén</div>
            </div>
        </div>
        <div class="alm-card">
            <div class="alm-card-ico amari"><i class="bi bi-cart-check"></i></div>
            <div>
                <div class="alm-card-num"><?php echo number_format($stockComp); ?></div>
                <div class="alm-card-lbl">Stock comprometido</div>
            </div>
        </div>
        <div class="alm-card">
            <div class="alm-card-ico rojo"><i class="bi bi-exclamation-triangle"></i></div>
            <div>
                <div class="alm-card-num"><?php echo $totalCriticos; ?></div>
                <div class="alm-card-lbl">Productos críticos (≤5)</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="alm-tabs">
        <button class="alm-tab activo" onclick="cambiarTab('stock')"><i class="bi bi-table mr-1"></i>Stock Actual</button>
        <button class="alm-tab" onclick="cambiarTab('kardex')"><i class="bi bi-journal-text mr-1"></i>Kardex</button>
        <button class="alm-tab" onclick="cambiarTab('traspasos')"><i class="bi bi-arrow-left-right mr-1"></i>Traspasos</button>
        <button class="alm-tab" onclick="cambiarTab('ajustes')"><i class="bi bi-pencil-square mr-1"></i>Ajustes</button>
        <button class="alm-tab" onclick="cambiarTab('critico')">
            <i class="bi bi-exclamation-diamond mr-1"></i>Stock Crítico
            <?php if ($totalCriticos > 0): ?>
                <span class="badge badge-danger ml-1" style="font-size:10px;"><?php echo $totalCriticos; ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- ═══════════════════════════════════ TAB: STOCK ACTUAL ══════════════════════════════════ -->
    <div class="alm-panel activo" id="panel-stock">
        <div class="alm-barra">
            <div class="form-group">
                <label>Buscar producto</label>
                <input type="text" class="form-control" id="filtroStockProd" placeholder="Nombre del producto..." oninput="filtrarTablaStock()">
            </div>
            <div class="form-group" style="max-width:180px;">
                <label>Sucursal</label>
                <select class="form-control" id="filtroStockSuc" onchange="filtrarTablaStock()">
                    <option value="">Todas</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?php echo htmlspecialchars($s['nombre']); ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="export-btns">
                <button class="btn-exp pdf"   onclick="exportarPDF('tablaStock', 'Stock_Actual')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                <button class="btn-exp print" onclick="imprimirTabla('tablaStock', 'Stock Actual por Producto/Sucursal')"><i class="bi bi-printer"></i> Imprimir</button>
                <button class="btn-exp xls"   onclick="exportarExcel('tablaStock', 'Stock_Actual')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
            </div>
        </div>

        <div class="alm-table-wrap">
            <div id="loadingStock" class="alm-loading"><div class="spinner-border"></div><p class="mt-2">Cargando stock...</p></div>
            <table class="alm-table" id="tablaStock" style="display:none;">
                <thead>
                    <tr>
                        <th>#</th><th>Producto</th><th>Categoría</th><th>Sucursal</th>
                        <th class="text-right">Stock Actual</th>
                        <th class="text-right">Comprometido</th>
                        <th class="text-right">Disponible</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="bodyStock"></tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">TOTALES</td>
                        <td class="text-right" id="totStock">—</td>
                        <td class="text-right" id="totComp">—</td>
                        <td class="text-right" id="totDisp">—</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- ═══════════════════════════════════ TAB: KARDEX ══════════════════════════════════ -->
    <div class="alm-panel" id="panel-kardex">
        <div class="alm-barra">
            <div class="form-group">
                <label>Producto</label>
                <select class="form-control" id="kProd">
                    <option value="0">Todos</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="max-width:160px;">
                <label>Sucursal</label>
                <select class="form-control" id="kSuc">
                    <option value="0">Todas</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group" style="max-width:160px;">
                <label>Tipo</label>
                <select class="form-control" id="kTipo">
                    <option value="">Todos</option>
                    <option value="venta">Venta</option>
                    <option value="traspaso_salida">Traspaso Salida</option>
                    <option value="traspaso_entrada">Traspaso Entrada</option>
                    <option value="devolucion">Devolución</option>
                    <option value="baja">Baja</option>
                    <option value="ajuste_entrada">Ajuste Entrada</option>
                    <option value="ajuste_salida">Ajuste Salida</option>
                </select>
            </div>
            <div class="form-group" style="max-width:140px;">
                <label>Desde</label>
                <input type="date" class="form-control" id="kDesde">
            </div>
            <div class="form-group" style="max-width:140px;">
                <label>Hasta</label>
                <input type="date" class="form-control" id="kHasta">
            </div>
            <button class="btn btn-primary btn-sm" style="height:36px;border-radius:8px;font-weight:600;" onclick="cargarKardex()">
                <i class="bi bi-search"></i> Filtrar
            </button>
            <button class="btn btn-outline-secondary btn-sm" style="height:36px;border-radius:8px;" onclick="limpiarFiltrosKardex()">
                <i class="bi bi-x"></i>
            </button>
            <div class="export-btns">
                <button class="btn-exp pdf"   onclick="exportarPDF('tablaKardex', 'Kardex')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                <button class="btn-exp print" onclick="imprimirTabla('tablaKardex', 'Kardex de Movimientos')"><i class="bi bi-printer"></i> Imprimir</button>
                <button class="btn-exp xls"   onclick="exportarExcel('tablaKardex', 'Kardex')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
            </div>
        </div>

        <div class="alm-table-wrap">
            <div id="loadingKardex" class="alm-loading" style="display:none;"><div class="spinner-border"></div><p class="mt-2">Cargando...</p></div>
            <table class="alm-table" id="tablaKardex">
                <thead>
                    <tr>
                        <th>ID</th><th>Fecha/Hora</th><th>Producto</th><th>Sucursal</th>
                        <th>Tipo</th><th class="text-right">Cantidad</th>
                        <th class="text-right">Stock Antes</th><th class="text-right">Stock Después</th>
                        <th>Referencia</th><th>Observación</th><th>Usuario</th>
                    </tr>
                </thead>
                <tbody id="bodyKardex">
                    <tr><td colspan="11" class="text-center text-muted py-4">Aplica filtros y presiona <strong>Filtrar</strong> para ver el kardex.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ═══════════════════════════════════ TAB: TRASPASOS ══════════════════════════════════ -->
    <div class="alm-panel" id="panel-traspasos">
        <div class="row">
            <!-- Formulario nuevo traspaso -->
            <div class="col-lg-4">
                <div class="alm-form-card">
                    <h6><i class="bi bi-plus-circle mr-1"></i>Nuevo Traspaso</h6>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Sucursal Origen</label>
                        <select class="form-control form-control-sm" id="trpOrigen">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Sucursal Destino</label>
                        <select class="form-control form-control-sm" id="trpDestino">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Observación</label>
                        <input type="text" class="form-control form-control-sm" id="trpObs" placeholder="Motivo del traspaso...">
                    </div>

                    <label style="font-size:12px;font-weight:600;color:var(--azul);">Productos a traspasar</label>
                    <div id="trpProductos">
                        <div class="prod-row">
                            <select class="form-control form-control-sm">
                                <option value="">Seleccionar producto...</option>
                                <?php foreach ($productos as $p): ?>
                                    <option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" class="form-control form-control-sm" min="1" value="1" style="max-width:70px;">
                            <button class="btn btn-sm btn-outline-danger btn-rm" onclick="quitarFilaProd(this)" title="Quitar"><i class="bi bi-x"></i></button>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-primary mt-1 mb-3" onclick="agregarFilaProd()"><i class="bi bi-plus"></i> Añadir producto</button>

                    <button class="btn btn-primary btn-block" onclick="crearTraspaso()">
                        <i class="bi bi-arrow-left-right mr-1"></i>Crear Traspaso
                    </button>
                    <div id="msgTraspaso" class="mt-2" style="font-size:12px;"></div>
                </div>
            </div>

            <!-- Lista de traspasos -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="export-btns">
                        <button class="btn-exp pdf"   onclick="exportarPDF('tablaTraspasos', 'Traspasos')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                        <button class="btn-exp print" onclick="imprimirTabla('tablaTraspasos', 'Registro de Traspasos')"><i class="bi bi-printer"></i> Imprimir</button>
                        <button class="btn-exp xls"   onclick="exportarExcel('tablaTraspasos', 'Traspasos')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                    </div>
                </div>
                <div class="alm-table-wrap">
                    <div id="loadingTraspasos" class="alm-loading"><div class="spinner-border"></div></div>
                    <table class="alm-table" id="tablaTraspasos" style="display:none;">
                        <thead>
                            <tr>
                                <th>Nro</th><th>Fecha</th><th>Origen</th><th>Destino</th>
                                <th class="text-center">Productos</th><th class="text-center">Unidades</th>
                                <th>Estado</th><th>Observación</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="bodyTraspasos"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════ TAB: AJUSTES ══════════════════════════════════ -->
    <div class="alm-panel" id="panel-ajustes">
        <div class="row">
            <div class="col-lg-4">
                <div class="alm-form-card">
                    <h6><i class="bi bi-pencil-square mr-1"></i>Registrar Ajuste de Inventario</h6>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Producto</label>
                        <select class="form-control form-control-sm" id="ajProd">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($productos as $p): ?>
                                <option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Sucursal</label>
                        <select class="form-control form-control-sm" id="ajSuc">
                            <option value="">Seleccionar...</option>
                            <?php foreach ($sucursales as $s): ?>
                                <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Tipo de ajuste</label>
                        <select class="form-control form-control-sm" id="ajTipo">
                            <option value="ajuste_entrada">Entrada por ajuste</option>
                            <option value="ajuste_salida">Salida por ajuste</option>
                            <option value="devolucion">Entrada por devolución cliente</option>
                            <option value="baja">Salida por baja/merma</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Cantidad</label>
                        <input type="number" class="form-control form-control-sm" id="ajCant" min="1" value="1">
                    </div>
                    <div class="form-group">
                        <label style="font-size:12px;font-weight:600;">Motivo / Observación</label>
                        <input type="text" class="form-control form-control-sm" id="ajObs" placeholder="Descripción del ajuste...">
                    </div>
                    <button class="btn btn-primary btn-block" onclick="registrarAjuste()">
                        <i class="bi bi-check2-circle mr-1"></i>Registrar Ajuste
                    </button>
                    <div id="msgAjuste" class="mt-2" style="font-size:12px;"></div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">Últimos ajustes registrados</small>
                    <div class="export-btns">
                        <button class="btn-exp pdf"   onclick="exportarPDF('tablaAjustes', 'Ajustes_Inventario')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                        <button class="btn-exp print" onclick="imprimirTabla('tablaAjustes', 'Ajustes de Inventario')"><i class="bi bi-printer"></i> Imprimir</button>
                        <button class="btn-exp xls"   onclick="exportarExcel('tablaAjustes', 'Ajustes_Inventario')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
                    </div>
                </div>
                <div class="alm-table-wrap">
                    <table class="alm-table" id="tablaAjustes">
                        <thead>
                            <tr>
                                <th>Fecha/Hora</th><th>Producto</th><th>Sucursal</th>
                                <th>Tipo</th><th class="text-right">Cantidad</th>
                                <th class="text-right">Stock Antes</th><th class="text-right">Stock Después</th>
                                <th>Observación</th><th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody id="bodyAjustes">
                            <tr><td colspan="9" class="text-center text-muted py-4">Registra un ajuste para verlo aquí.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════ TAB: STOCK CRÍTICO ══════════════════════════════════ -->
    <div class="alm-panel" id="panel-critico">
        <div class="alm-barra">
            <div class="form-group" style="max-width:180px;">
                <label>Umbral crítico (unidades)</label>
                <input type="number" class="form-control" id="umbralCritico" min="1" value="5">
            </div>
            <button class="btn btn-primary btn-sm" style="height:36px;border-radius:8px;font-weight:600;" onclick="cargarCritico()">
                <i class="bi bi-search"></i> Consultar
            </button>
            <div class="export-btns ml-auto">
                <button class="btn-exp pdf"   onclick="exportarPDF('tablaCritico', 'Stock_Critico')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                <button class="btn-exp print" onclick="imprimirTabla('tablaCritico', 'Stock Crítico')"><i class="bi bi-printer"></i> Imprimir</button>
                <button class="btn-exp xls"   onclick="exportarExcel('tablaCritico', 'Stock_Critico')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
            </div>
        </div>

        <div class="alm-table-wrap">
            <div id="loadingCritico" class="alm-loading" style="display:none;"><div class="spinner-border"></div></div>
            <table class="alm-table" id="tablaCritico">
                <thead>
                    <tr>
                        <th>#</th><th>Producto</th><th>Categoría</th>
                        <th class="text-right">Stock Total</th><th class="text-center">Alerta</th>
                    </tr>
                </thead>
                <tbody id="bodyCritico">
                    <?php if (empty($stockCritico)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">No hay productos en stock crítico (≤5).</td></tr>
                    <?php else: ?>
                        <?php foreach ($stockCritico as $i => $c): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><strong><?php echo htmlspecialchars($c['producto']); ?></strong></td>
                            <td><?php echo htmlspecialchars($c['categoria'] ?? '—'); ?></td>
                            <td class="text-right"><strong><?php echo (int)$c['stockTotal']; ?></strong></td>
                            <td class="text-center">
                                <span class="badge-<?php echo $c['alerta'] === 'agotado' ? 'agotado' : 'bajo'; ?>">
                                    <?php echo $c['alerta'] === 'agotado' ? 'AGOTADO' : 'STOCK BAJO'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div><!-- /alm-container -->

<!-- Modal detalle traspaso -->
<div class="modal fade" id="modalDetalleTrp" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--azul);color:#fff;">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right mr-1"></i>Detalle del Traspaso <span id="modalTrpNro"></span></h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="modalTrpBody">Cargando...</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
// ══════════════════════════════════════════════════════════════
//  Utilidades
// ══════════════════════════════════════════════════════════════
const API = 'api/almacen.php';

function cambiarTab(id) {
    document.querySelectorAll('.alm-tab').forEach(t => t.classList.remove('activo'));
    document.querySelectorAll('.alm-panel').forEach(p => p.classList.remove('activo'));
    event.currentTarget.classList.add('activo');
    document.getElementById('panel-' + id).classList.add('activo');
    if (id === 'stock'     && !stockCargado)    cargarStock();
    if (id === 'traspasos' && !traspasosCargado) cargarTraspasos();
}

// ══════════════════════════════════════════════════════════════
//  TAB: STOCK ACTUAL
// ══════════════════════════════════════════════════════════════
let stockData = [];
let stockCargado = false;

function cargarStock() {
    document.getElementById('loadingStock').style.display = 'block';
    document.getElementById('tablaStock').style.display  = 'none';
    fetch(API + '?action=stock_actual')
        .then(r => r.json())
        .then(res => {
            stockData = res.data || [];
            renderStock(stockData);
            stockCargado = true;
        });
}

function renderStock(data) {
    const tbody = document.getElementById('bodyStock');
    let html = '', ts = 0, tc = 0, td2 = 0;

    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Sin datos de stock.</td></tr>';
    } else {
        data.forEach((r, i) => {
            const sa = parseInt(r.stockActual), sc = parseInt(r.stockComprometido), sd = parseInt(r.stockDisponible);
            ts += sa; tc += sc; td2 += sd;
            const color = sd === 0 ? 'color:#dc3545;font-weight:700;' : (sd <= 5 ? 'color:#e65100;font-weight:700;' : '');
            html += `<tr>
                <td>${i+1}</td>
                <td><strong>${esc(r.producto)}</strong></td>
                <td>${esc(r.categoria || '—')}</td>
                <td>${esc(r.sucursal)}</td>
                <td class="text-right">${sa}</td>
                <td class="text-right">${sc > 0 ? `<span style="color:#856404;">${sc}</span>` : '0'}</td>
                <td class="text-right"><span style="${color}">${sd}</span></td>
                <td>${sd === 0 ? '<span class="badge-agotado">AGOTADO</span>' : (sd <= 5 ? '<span class="badge-bajo">BAJO</span>' : '<span style="color:#1b5e20;font-weight:600;font-size:11px;">OK</span>')}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    document.getElementById('totStock').textContent = ts;
    document.getElementById('totComp').textContent  = tc;
    document.getElementById('totDisp').textContent  = td2;
    document.getElementById('loadingStock').style.display = 'none';
    document.getElementById('tablaStock').style.display   = 'table';
}

function filtrarTablaStock() {
    const busq = document.getElementById('filtroStockProd').value.toLowerCase();
    const suc  = document.getElementById('filtroStockSuc').value.toLowerCase();
    const filt = stockData.filter(r =>
        (!busq || r.producto.toLowerCase().includes(busq)) &&
        (!suc  || r.sucursal.toLowerCase().includes(suc))
    );
    renderStock(filt);
}

// ══════════════════════════════════════════════════════════════
//  TAB: KARDEX
// ══════════════════════════════════════════════════════════════
function cargarKardex() {
    const prod  = document.getElementById('kProd').value;
    const suc   = document.getElementById('kSuc').value;
    const tipo  = document.getElementById('kTipo').value;
    const desde = document.getElementById('kDesde').value;
    const hasta = document.getElementById('kHasta').value;

    document.getElementById('loadingKardex').style.display = 'block';
    document.getElementById('tablaKardex').style.display   = 'none';

    const url = `${API}?action=kardex&codProducto=${prod}&codSucursal=${suc}&tipo=${encodeURIComponent(tipo)}&desde=${desde}&hasta=${hasta}`;
    fetch(url)
        .then(r => r.json())
        .then(res => {
            const data = res.data || [];
            const tbody = document.getElementById('bodyKardex');
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="11" class="text-center text-muted py-4">Sin movimientos para los filtros aplicados.</td></tr>';
            } else {
                tbody.innerHTML = data.map((r, i) => `
                    <tr>
                        <td>${r.id}</td>
                        <td><small>${r.fechaHora}</small></td>
                        <td>${esc(r.producto)}</td>
                        <td>${esc(r.sucursal)}</td>
                        <td><span class="badge-tipo tipo-${r.tipo}">${labelTipo(r.tipo)}</span></td>
                        <td class="text-right"><strong>${r.cantidad}</strong></td>
                        <td class="text-right">${r.stockAntes}</td>
                        <td class="text-right">${r.stockDespues}</td>
                        <td><small>${esc(r.referencia || '—')}</small></td>
                        <td><small>${esc(r.observacion || '—')}</small></td>
                        <td><small>${esc(r.usuarioCuenta || '—')}</small></td>
                    </tr>
                `).join('');
            }
            document.getElementById('loadingKardex').style.display = 'none';
            document.getElementById('tablaKardex').style.display   = 'table';
        });
}

function limpiarFiltrosKardex() {
    ['kProd','kSuc','kTipo','kDesde','kHasta'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.tagName === 'SELECT' ? (el.selectedIndex = 0) : (el.value = '');
    });
}

// ══════════════════════════════════════════════════════════════
//  TAB: TRASPASOS
// ══════════════════════════════════════════════════════════════
let traspasosCargado = false;
const PROD_OPTIONS = `<?php foreach ($productos as $p): ?><option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?></option><?php endforeach; ?>`;

function cargarTraspasos() {
    document.getElementById('loadingTraspasos').style.display = 'block';
    document.getElementById('tablaTraspasos').style.display   = 'none';
    fetch(API + '?action=traspasos')
        .then(r => r.json())
        .then(res => {
            const data = res.data || [];
            const tbody = document.getElementById('bodyTraspasos');
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay traspasos registrados.</td></tr>';
            } else {
                tbody.innerHTML = data.map(t => {
                    const estadoClass = { pendiente:'est-pendiente', completado:'est-completado', cancelado:'est-cancelado' }[t.estado] || '';
                    const acciones = t.estado === 'pendiente'
                        ? `<button class="btn btn-sm btn-success mr-1" onclick="completarTrp(${t.nro})" title="Completar"><i class="bi bi-check-lg"></i></button>
                           <button class="btn btn-sm btn-danger" onclick="cancelarTrp(${t.nro})" title="Cancelar"><i class="bi bi-x-lg"></i></button>`
                        : '';
                    return `<tr>
                        <td><strong>TRP-${String(t.nro).padStart(6,'0')}</strong>
                            <button class="btn btn-sm btn-link p-0 ml-1" onclick="verDetalleTrp(${t.nro})" title="Ver detalle"><i class="bi bi-eye"></i></button>
                        </td>
                        <td><small>${t.fechaHora}</small></td>
                        <td>${esc(t.sucursalOrigen)}</td>
                        <td>${esc(t.sucursalDestino)}</td>
                        <td class="text-center">${t.cantProductos || 0}</td>
                        <td class="text-center">${t.cantUnidades || 0}</td>
                        <td><span class="badge-estado ${estadoClass}">${t.estado}</span></td>
                        <td><small>${esc(t.observacion || '—')}</small></td>
                        <td>${acciones}</td>
                    </tr>`;
                }).join('');
            }
            document.getElementById('loadingTraspasos').style.display = 'none';
            document.getElementById('tablaTraspasos').style.display   = 'table';
            traspasosCargado = true;
        });
}

function agregarFilaProd() {
    const div = document.createElement('div');
    div.className = 'prod-row';
    div.innerHTML = `
        <select class="form-control form-control-sm"><option value="">Seleccionar producto...</option>${PROD_OPTIONS}</select>
        <input type="number" class="form-control form-control-sm" min="1" value="1" style="max-width:70px;">
        <button class="btn btn-sm btn-outline-danger btn-rm" onclick="quitarFilaProd(this)" title="Quitar"><i class="bi bi-x"></i></button>`;
    document.getElementById('trpProductos').appendChild(div);
}

function quitarFilaProd(btn) {
    const rows = document.querySelectorAll('#trpProductos .prod-row');
    if (rows.length > 1) btn.closest('.prod-row').remove();
}

function crearTraspaso() {
    const origen  = document.getElementById('trpOrigen').value;
    const destino = document.getElementById('trpDestino').value;
    const obs     = document.getElementById('trpObs').value.trim();
    const filas   = document.querySelectorAll('#trpProductos .prod-row');
    const msg     = document.getElementById('msgTraspaso');

    if (!origen || !destino) { mostrarMsg(msg, 'Selecciona origen y destino.', 'danger'); return; }
    if (origen === destino)  { mostrarMsg(msg, 'Origen y destino no pueden ser iguales.', 'danger'); return; }

    const productos = [];
    let valido = true;
    filas.forEach(f => {
        const sel  = f.querySelector('select');
        const inp  = f.querySelector('input[type=number]');
        const cod  = parseInt(sel ? sel.value : 0);
        const cant = parseInt(inp ? inp.value : 0);
        if (!cod || cant < 1) { valido = false; return; }
        productos.push({ codProducto: cod, cantidad: cant });
    });
    if (!valido || !productos.length) { mostrarMsg(msg, 'Completa todos los productos.', 'danger'); return; }

    const fd = new FormData();
    fd.append('action', 'crear_traspaso');
    fd.append('codSucursalOrigen',  origen);
    fd.append('codSucursalDestino', destino);
    fd.append('observacion', obs);
    fd.append('productos', JSON.stringify(productos));

    fetch(API, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                mostrarMsg(msg, `Traspaso TRP-${String(res.nro).padStart(6,'0')} creado correctamente.`, 'success');
                traspasosCargado = false;
                cargarTraspasos();
            } else {
                mostrarMsg(msg, res.msg || 'Error al crear traspaso.', 'danger');
            }
        });
}

function completarTrp(nro) {
    if (!confirm(`¿Completar el traspaso TRP-${String(nro).padStart(6,'0')}? Esto moverá el stock entre sucursales.`)) return;
    const fd = new FormData();
    fd.append('action', 'completar_traspaso');
    fd.append('nro', nro);
    fetch(API, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.ok) { traspasosCargado = false; cargarTraspasos(); stockCargado = false; }
            else alert(res.msg || 'Error al completar.');
        });
}

function cancelarTrp(nro) {
    if (!confirm(`¿Cancelar el traspaso TRP-${String(nro).padStart(6,'0')}?`)) return;
    const fd = new FormData();
    fd.append('action', 'cancelar_traspaso');
    fd.append('nro', nro);
    fetch(API, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.ok) { traspasosCargado = false; cargarTraspasos(); }
            else alert(res.msg || 'Error al cancelar.');
        });
}

function verDetalleTrp(nro) {
    document.getElementById('modalTrpNro').textContent = `TRP-${String(nro).padStart(6,'0')}`;
    document.getElementById('modalTrpBody').innerHTML  = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm"></span> Cargando...</div>';
    $('#modalDetalleTrp').modal('show');
    fetch(`${API}?action=detalle_traspaso&nro=${nro}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data || [];
            if (!data.length) {
                document.getElementById('modalTrpBody').innerHTML = '<p class="text-muted">Sin detalle.</p>';
            } else {
                let html = '<table class="trp-detail-table"><thead><tr><th>Producto</th><th>Cantidad</th><th>Stock en Origen</th></tr></thead><tbody>';
                data.forEach(d => {
                    html += `<tr><td>${esc(d.producto)}</td><td>${d.cantidad}</td><td>${d.stockOrigen !== null ? d.stockOrigen : '—'}</td></tr>`;
                });
                html += '</tbody></table>';
                document.getElementById('modalTrpBody').innerHTML = html;
            }
        });
}

// ══════════════════════════════════════════════════════════════
//  TAB: AJUSTES
// ══════════════════════════════════════════════════════════════
function registrarAjuste() {
    const prod = document.getElementById('ajProd').value;
    const suc  = document.getElementById('ajSuc').value;
    const tipo = document.getElementById('ajTipo').value;
    const cant = parseInt(document.getElementById('ajCant').value);
    const obs  = document.getElementById('ajObs').value.trim();
    const msg  = document.getElementById('msgAjuste');

    if (!prod || !suc)   { mostrarMsg(msg, 'Selecciona producto y sucursal.', 'danger'); return; }
    if (!cant || cant<1) { mostrarMsg(msg, 'La cantidad debe ser mayor a 0.', 'danger'); return; }

    const fd = new FormData();
    fd.append('action', 'ajuste');
    fd.append('codProducto', prod);
    fd.append('codSucursal', suc);
    fd.append('tipo', tipo);
    fd.append('cantidad', cant);
    fd.append('observacion', obs);

    fetch(API, { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                mostrarMsg(msg, 'Ajuste registrado correctamente.', 'success');
                document.getElementById('ajCant').value = 1;
                document.getElementById('ajObs').value  = '';
                stockCargado = false;
                actualizarTablaAjustes();
            } else {
                mostrarMsg(msg, res.msg || 'Error al registrar ajuste.', 'danger');
            }
        });
}

function actualizarTablaAjustes() {
    const prod  = document.getElementById('ajProd').value;
    const suc   = document.getElementById('ajSuc').value;
    const tipos = ['ajuste_entrada','ajuste_salida','devolucion','baja'];
    fetch(`${API}?action=kardex&codProducto=${prod}&codSucursal=${suc}&tipo=`)
        .then(r => r.json())
        .then(res => {
            const data = (res.data || []).filter(r => tipos.includes(r.tipo));
            const tbody = document.getElementById('bodyAjustes');
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-3">Sin ajustes recientes.</td></tr>';
            } else {
                tbody.innerHTML = data.slice(0,50).map(r => `
                    <tr>
                        <td><small>${r.fechaHora}</small></td>
                        <td>${esc(r.producto)}</td>
                        <td>${esc(r.sucursal)}</td>
                        <td><span class="badge-tipo tipo-${r.tipo}">${labelTipo(r.tipo)}</span></td>
                        <td class="text-right"><strong>${r.cantidad}</strong></td>
                        <td class="text-right">${r.stockAntes}</td>
                        <td class="text-right">${r.stockDespues}</td>
                        <td><small>${esc(r.observacion||'—')}</small></td>
                        <td><small>${esc(r.usuarioCuenta||'—')}</small></td>
                    </tr>
                `).join('');
            }
        });
}

// ══════════════════════════════════════════════════════════════
//  TAB: STOCK CRÍTICO
// ══════════════════════════════════════════════════════════════
function cargarCritico() {
    const umbral = parseInt(document.getElementById('umbralCritico').value) || 5;
    document.getElementById('loadingCritico').style.display = 'block';
    document.getElementById('tablaCritico').style.display   = 'none';
    fetch(`${API}?action=stock_critico&umbral=${umbral}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data || [];
            const tbody = document.getElementById('bodyCritico');
            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay productos con stock ≤ ${umbral}.</td></tr>`;
            } else {
                tbody.innerHTML = data.map((r, i) => `
                    <tr>
                        <td>${i+1}</td>
                        <td><strong>${esc(r.producto)}</strong></td>
                        <td>${esc(r.categoria||'—')}</td>
                        <td class="text-right"><strong>${r.stockTotal}</strong></td>
                        <td class="text-center">
                            <span class="badge-${r.alerta === 'agotado' ? 'agotado' : 'bajo'}">
                                ${r.alerta === 'agotado' ? 'AGOTADO' : 'STOCK BAJO'}
                            </span>
                        </td>
                    </tr>
                `).join('');
            }
            document.getElementById('loadingCritico').style.display = 'none';
            document.getElementById('tablaCritico').style.display   = 'table';
        });
}

// ══════════════════════════════════════════════════════════════
//  EXPORTAR PDF
// ══════════════════════════════════════════════════════════════
function exportarPDF(tablaId, nombre) {
    const tabla = document.getElementById(tablaId);
    if (!tabla) return;

    const fecha = new Date().toLocaleDateString('es-BO');
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'font-family:Arial,sans-serif;padding:20px;background:#fff;';
    wrapper.innerHTML = `
        <div style="display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #1B3A6B;padding-bottom:10px;margin-bottom:16px;">
            <div>
                <h2 style="margin:0;color:#1B3A6B;font-size:18px;">⚡ Electrohogar</h2>
                <p style="margin:2px 0;color:#888;font-size:11px;">Sistema de Almacén</p>
            </div>
            <div style="text-align:right;">
                <p style="margin:0;font-size:14px;font-weight:700;color:#333;">${nombre.replace(/_/g,' ')}</p>
                <p style="margin:0;font-size:11px;color:#888;">Generado: ${fecha}</p>
            </div>
        </div>
        ${tabla.outerHTML}
    `;
    // Ajustar estilos de tabla para PDF
    wrapper.querySelectorAll('table').forEach(t => {
        t.style.cssText = 'width:100%;border-collapse:collapse;font-size:11px;';
    });
    wrapper.querySelectorAll('th').forEach(t => {
        t.style.cssText = 'background:#1B3A6B;color:#fff;padding:6px 8px;text-align:left;font-size:10px;';
    });
    wrapper.querySelectorAll('td').forEach(t => {
        t.style.cssText = 'padding:5px 8px;border-bottom:1px solid #eee;font-size:10px;';
    });
    wrapper.querySelectorAll('tr:nth-child(even)').forEach(t => {
        t.style.background = '#f8f9ff';
    });

    const opts = {
        margin: [10, 8, 10, 8],
        filename: `${nombre}_${new Date().toISOString().slice(0,10)}.pdf`,
        image:    { type: 'jpeg', quality: 0.97 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF:    { unit: 'mm', format: 'a4', orientation: 'landscape' },
        pagebreak: { mode: 'avoid-all' }
    };
    html2pdf().set(opts).from(wrapper).save();
}

// ══════════════════════════════════════════════════════════════
//  IMPRIMIR
// ══════════════════════════════════════════════════════════════
function imprimirTabla(tablaId, titulo) {
    const tabla = document.getElementById(tablaId);
    if (!tabla) return;
    const fecha = new Date().toLocaleString('es-BO');
    const html = `<!DOCTYPE html><html><head><meta charset="UTF-8">
        <title>${titulo} — Electrohogar</title>
        <style>
            body{font-family:Arial,sans-serif;font-size:11px;margin:15px;color:#333;}
            .header{display:flex;justify-content:space-between;border-bottom:2px solid #1B3A6B;padding-bottom:8px;margin-bottom:12px;}
            .header h2{margin:0;color:#1B3A6B;font-size:16px;}
            .header small{color:#888;font-size:10px;}
            table{width:100%;border-collapse:collapse;}
            thead tr{background:#1B3A6B;}
            thead th{color:#fff;padding:6px 8px;text-align:left;font-size:10px;}
            tbody tr:nth-child(even){background:#f5f8ff;}
            tbody td{padding:5px 8px;border-bottom:1px solid #eee;}
            tfoot td{font-weight:bold;background:#e8ecf8;padding:5px 8px;}
            @media print{body{margin:5mm;}}
        </style></head><body>
        <div class="header">
            <div><h2>⚡ Electrohogar</h2><small>Sistema de Almacén</small></div>
            <div style="text-align:right;"><strong>${titulo}</strong><br><small>${fecha}</small></div>
        </div>
        ${tabla.outerHTML}
        </body></html>`;
    const w = window.open('', '_blank', 'width=1000,height=700');
    w.document.write(html);
    w.document.close();
    w.focus();
    setTimeout(() => w.print(), 600);
}

// ══════════════════════════════════════════════════════════════
//  EXPORTAR EXCEL (SheetJS)
// ══════════════════════════════════════════════════════════════
function exportarExcel(tablaId, nombre) {
    const tabla = document.getElementById(tablaId);
    if (!tabla) return;

    // Clonar tabla limpiando botones y badges
    const clon = tabla.cloneNode(true);
    clon.querySelectorAll('button, .btn').forEach(b => b.remove());
    clon.querySelectorAll('[class*="badge"]').forEach(b => {
        b.replaceWith(document.createTextNode(b.textContent.trim()));
    });

    const wb  = XLSX.utils.book_new();
    const ws  = XLSX.utils.table_to_sheet(clon);
    XLSX.utils.book_append_sheet(wb, ws, nombre.slice(0,31));
    XLSX.writeFile(wb, `${nombre}_${new Date().toISOString().slice(0,10)}.xlsx`);
}

// ══════════════════════════════════════════════════════════════
//  Helpers
// ══════════════════════════════════════════════════════════════
function esc(str) {
    if (!str) return '—';
    return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function labelTipo(tipo) {
    const map = {
        venta: 'Venta', traspaso_salida: 'Traspaso Salida', traspaso_entrada: 'Traspaso Entrada',
        devolucion: 'Devolución', baja: 'Baja', ajuste_entrada: 'Ajuste +', ajuste_salida: 'Ajuste -'
    };
    return map[tipo] || tipo;
}

function mostrarMsg(el, msg, tipo) {
    el.innerHTML = `<div class="alert alert-${tipo} alert-sm py-1 px-2 mb-0" style="font-size:12px;">${msg}</div>`;
    setTimeout(() => { if (el) el.innerHTML = ''; }, 5000);
}

// ══════════════════════════════════════════════════════════════
//  Init: carga stock al abrir la página
// ══════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    cargarStock();
    actualizarTablaAjustes();
});
</script>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
