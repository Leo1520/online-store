<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<style>

.alm-barra { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 8px rgba(27,58,107,.06); margin-bottom:16px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
.alm-barra .form-group { margin-bottom:0; flex:1; min-width:130px; }
.alm-barra label { font-size:11px; font-weight:600; color:#666; margin-bottom:3px; text-transform:uppercase; letter-spacing:.4px; display:block; }
.export-btns { display:flex; gap:6px; flex-shrink:0; }
.btn-exp { display:flex; align-items:center; gap:5px; padding:7px 13px; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; transition:opacity .2s; }
.btn-exp:hover { opacity:.85; }
.btn-exp.pdf   { background:#1B3A6B; color:#fff; }
.btn-exp.print { background:#6c757d; color:#fff; }
.btn-exp.xls   { background:#1d7a35; color:#fff; }

.alm-table-wrap { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(27,58,107,.06); overflow-x:auto; }
.alm-paginacion { display:flex; align-items:center; justify-content:space-between; padding:10px 16px; background:#fafbff; border-top:1px solid #f0f0f0; font-size:13px; }
.alm-paginacion .pag-info { color:#666; }
.alm-paginacion .pag-btns { display:flex; gap:4px; }
.alm-paginacion .pag-btns button { border:1px solid #dde2f0; background:#fff; border-radius:6px; padding:3px 10px; font-size:12px; cursor:pointer; transition:all .15s; }
.alm-paginacion .pag-btns button:hover:not(:disabled) { background:var(--primary); color:#fff; border-color:var(--primary); }
.alm-paginacion .pag-btns button.activo { background:var(--primary); color:#fff; border-color:var(--primary); }
.alm-paginacion .pag-btns button:disabled { opacity:.4; cursor:default; }
.alm-table { width:100%; border-collapse:collapse; font-size:13px; }
.alm-table thead tr { background:var(--primary); color:#fff; }
.alm-table thead th { padding:10px 12px; font-weight:600; font-size:12px; white-space:nowrap; }
.alm-table tbody tr:nth-child(even) { background:#f8f9ff; }
.alm-table tbody tr:hover { background:#eef2ff; }
.alm-table tbody td { padding:8px 12px; border-bottom:1px solid #f0f0f0; vertical-align:middle; }
.alm-table tfoot td { padding:8px 12px; font-weight:700; background:#f0f4ff; }
.badge-agotado { background:#dc3545; color:#fff; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-bajo    { background:#ffc107; color:#333; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.alm-loading { text-align:center; padding:40px; color:#aaa; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-table me-2"></i>Stock Actual
        </h4>
        <small class="text-muted">Inventario por producto y sucursal</small>
    </div>
</div>


<!-- Filtros + exportar -->
<div class="alm-barra">
    <div class="form-group">
        <label>Buscar producto</label>
        <input type="text" class="form-control form-control-sm" id="filtroStockProd" placeholder="Nombre del producto..." oninput="filtrarTablaStock()">
    </div>
    <div class="export-btns">
        <button class="btn-exp pdf"   onclick="exportarPDF('tablaStock','Stock_Actual')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="btn-exp print" onclick="imprimirTabla('tablaStock','Stock Actual')"><i class="bi bi-printer"></i> Imprimir</button>
        <button class="btn-exp xls"   onclick="exportarExcel('tablaStock','Stock_Actual')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
    </div>
</div>

<!-- Modal desglose por sucursal -->
<div class="modal fade" id="modalSucursales" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0" style="border-radius:14px;overflow:hidden;box-shadow:0 8px 32px rgba(27,58,107,.18);">

            <!-- Header -->
            <div class="modal-header border-0 px-4 py-3" style="background:var(--primary);">
                <div>
                    <h5 class="modal-title text-white mb-0 fw-bold">
                        <i class="bi bi-buildings me-2"></i>Desglose por Sucursal
                    </h5>
                    <small class="text-white-50">Distribución de stock por punto de venta</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Info del producto -->
            <div class="px-4 py-3" style="background:#f8f9ff;border-bottom:1px solid #e8ecf8;">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:42px;height:42px;background:#e8f0fe;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-box-seam" style="color:var(--primary);font-size:18px;"></i>
                    </div>
                    <div>
                        <div class="fw-bold" id="modalProdTexto" style="font-size:15px;color:#1a1a2e;"></div>
                        <div style="font-family:monospace;font-size:12px;color:#888;" id="modalProdCodigo"></div>
                    </div>
                </div>
            </div>

            <!-- Tabla -->
            <div class="modal-body p-0">
                <table class="table mb-0" style="font-size:13px;">
                    <thead>
                        <tr style="background:#f0f4ff;">
                            <th class="ps-4 py-3 fw-600 text-uppercase" style="font-size:11px;letter-spacing:.4px;color:#555;font-weight:700;">Sucursal</th>
                            <th class="text-end py-3 fw-600 text-uppercase" style="font-size:11px;letter-spacing:.4px;color:#555;font-weight:700;">Stock Actual</th>
                            <th class="text-end py-3 fw-600 text-uppercase" style="font-size:11px;letter-spacing:.4px;color:#555;font-weight:700;">Comprometido</th>
                            <th class="text-end pe-4 py-3 fw-600 text-uppercase" style="font-size:11px;letter-spacing:.4px;color:#555;font-weight:700;">Disponible</th>
                        </tr>
                    </thead>
                    <tbody id="modalBodySuc"></tbody>
                    <tfoot>
                        <tr style="background:#f0f4ff;border-top:2px solid #d0d9f0;">
                            <td class="ps-4 py-3 fw-bold" style="font-size:13px;">TOTAL</td>
                            <td class="text-end py-3 fw-bold" id="modalTotStock"></td>
                            <td class="text-end py-3 fw-bold" id="modalTotComp"></td>
                            <td class="text-end pe-4 py-3 fw-bold" id="modalTotDisp"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 px-4 py-3" style="background:#fafbff;">
                <button type="button" class="btn btn-secondary btn-sm px-4" data-bs-dismiss="modal">Cerrar</button>
            </div>

        </div>
    </div>
</div>

<div class="alm-table-wrap">
    <div id="loadingStock" class="alm-loading"><div class="spinner-border" style="color:var(--primary);"></div><p class="mt-2">Cargando stock...</p></div>
    <table class="alm-table" id="tablaStock" style="display:none;">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th>Marca</th>
                <th>Industria</th>
                <th class="text-end">P. Propuesto</th>
                <th class="text-end">P. Vigente</th>
                <th class="text-end">Stock Total</th>
                <th class="text-end">Comprometido</th>
                <th class="text-end">Disponible</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody id="bodyStock"></tbody>
        <tfoot>
            <tr>
                <td colspan="8">TOTALES</td>
                <td class="text-end" id="totStock">—</td>
                <td class="text-end" id="totComp">—</td>
                <td class="text-end" id="totDisp">—</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <div class="alm-paginacion" id="paginacion" style="display:none;">
        <span class="pag-info" id="pagInfo"></span>
        <div class="pag-btns" id="pagBtns"></div>
    </div>
</div>

<script>
const API = '../api/almacen.php';
let stockData    = [];
let consolidado  = [];
let filtrados    = [];
let paginaActual = 1;
const POR_PAGINA = 10;

function cargarStock() {
    document.getElementById('loadingStock').style.display = 'block';
    document.getElementById('tablaStock').style.display   = 'none';
    fetch(API + '?action=stock_actual')
        .then(r => r.json())
        .then(res => {
            stockData = res.data || [];
            consolidado = consolidar(stockData);
            renderStock(consolidado);
        });
}

// Agrupa filas por codProducto y suma stocks
function consolidar(data) {
    const map = {};
    data.forEach(r => {
        const k = r.codProducto;
        if (!map[k]) {
            map[k] = {
                codProducto:     r.codProducto,
                codigo:          r.codigo,
                producto:        r.producto,
                categoria:       r.categoria,
                marca:           r.marca,
                industria:       r.industria,
                precioVigente:   r.precioVigente,
                precioPropuesto: r.precioPropuesto,
                stockActual:     0,
                stockComprometido: 0,
                stockDisponible: 0,
                sucursales: []
            };
        }
        const sa = parseInt(r.stockActual)||0;
        const sc = parseInt(r.stockComprometido)||0;
        const sd = parseInt(r.stockDisponible)||0;
        map[k].stockActual      += sa;
        map[k].stockComprometido += sc;
        map[k].stockDisponible  += sd;
        map[k].sucursales.push({ sucursal: r.sucursal, stockActual: sa, stockComprometido: sc, stockDisponible: sd });
    });
    return Object.values(map);
}

function renderStock(data) {
    filtrados    = data;
    paginaActual = 1;
    renderPagina();
}

function renderPagina() {
    const total    = filtrados.length;
    const totalPag = Math.max(1, Math.ceil(total / POR_PAGINA));
    if (paginaActual > totalPag) paginaActual = totalPag;

    const desde = (paginaActual - 1) * POR_PAGINA;
    const slice  = filtrados.slice(desde, desde + POR_PAGINA);

    // Totales sobre todos los datos filtrados (no solo la página)
    let ts = 0, tc = 0, td = 0;
    filtrados.forEach(r => { ts += r.stockActual; tc += r.stockComprometido; td += r.stockDisponible; });

    const tbody = document.getElementById('bodyStock');
    let html = '';
    if (!slice.length) {
        tbody.innerHTML = '<tr><td colspan="12" class="text-center text-muted py-4">Sin datos de stock.</td></tr>';
    } else {
        slice.forEach((r, i) => {
            const sa = r.stockActual, sc = r.stockComprometido, sd = r.stockDisponible;
            const pv = parseFloat(r.precioVigente)||0, pp = parseFloat(r.precioPropuesto)||0;
            html += `<tr>
                <td>${desde + i + 1}</td>
                <td>${esc(r.codigo||'—')}</td>
                <td>${esc(r.producto)}</td>
                <td>${esc(r.categoria||'—')}</td>
                <td>${esc(r.marca||'—')}</td>
                <td>${esc(r.industria||'—')}</td>
                <td class="text-end">${pp > 0 ? `Bs.${pp.toFixed(2)}` : '—'}</td>
                <td class="text-end">Bs.${pv.toFixed(2)}</td>
                <td class="text-end">
                    <span onclick="abrirModalSucursal('${r.codProducto}')" title="Ver por sucursal"
                        style="cursor:pointer;text-decoration:underline dotted;">
                        ${sa}
                    </span>
                </td>
                <td class="text-end">${sc}</td>
                <td class="text-end">${sd}</td>
                <td>${sd===0?'Agotado':(sd<=5?'Últimas unidades':'Disponible')}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }

    document.getElementById('totStock').textContent = ts;
    document.getElementById('totComp').textContent  = tc;
    document.getElementById('totDisp').textContent  = td;
    document.getElementById('loadingStock').style.display = 'none';
    document.getElementById('tablaStock').style.display   = 'table';

    renderPaginacion(total, totalPag);
}

function renderPaginacion(total, totalPag) {
    const pagEl  = document.getElementById('paginacion');
    const infoEl = document.getElementById('pagInfo');
    const btnsEl = document.getElementById('pagBtns');

    if (total === 0) { pagEl.style.display = 'none'; return; }
    pagEl.style.display = 'flex';

    const desde = (paginaActual - 1) * POR_PAGINA + 1;
    const hasta  = Math.min(paginaActual * POR_PAGINA, total);
    infoEl.textContent = `Mostrando ${desde}–${hasta} de ${total} productos`;

    const rango = paginaRange(paginaActual, totalPag);
    let btns = '';

    btns += `<button onclick="irPagina(${paginaActual - 1})" ${paginaActual === 1 ? 'disabled' : ''}>‹ Anterior</button>`;

    if (rango[0] > 1) btns += `<button onclick="irPagina(1)">1</button>`;
    if (rango[0] > 2) btns += `<span style="padding:0 6px;color:#999;align-self:center;">…</span>`;

    rango.forEach(p => {
        btns += `<button class="${p === paginaActual ? 'activo' : ''}" onclick="irPagina(${p})">${p}</button>`;
    });

    if (rango[rango.length - 1] < totalPag - 1) btns += `<span style="padding:0 6px;color:#999;align-self:center;">…</span>`;
    if (rango[rango.length - 1] < totalPag)     btns += `<button onclick="irPagina(${totalPag})">${totalPag}</button>`;

    btns += `<button onclick="irPagina(${paginaActual + 1})" ${paginaActual === totalPag ? 'disabled' : ''}>Siguiente ›</button>`;

    btnsEl.innerHTML = btns;
}

function paginaRange(actual, total) {
    const delta = 2;
    const ini   = Math.max(1, actual - delta);
    const fin   = Math.min(total, actual + delta);
    const arr   = [];
    for (let i = ini; i <= fin; i++) arr.push(i);
    return arr;
}

function irPagina(p) {
    const totalPag = Math.max(1, Math.ceil(filtrados.length / POR_PAGINA));
    if (p < 1 || p > totalPag) return;
    paginaActual = p;
    renderPagina();
    document.querySelector('.alm-table-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function abrirModalSucursal(codProducto) {
    const r = consolidado.find(x => String(x.codProducto) === String(codProducto));
    if (!r) return;
    document.getElementById('modalProdTexto').textContent  = r.producto;
    document.getElementById('modalProdCodigo').textContent = r.codigo ? '(' + r.codigo + ')' : '';

    let ts = 0, tc = 0, td = 0;
    const rows = r.sucursales.map(s => {
        ts += s.stockActual; tc += s.stockComprometido; td += s.stockDisponible;
        return `<tr style="border-bottom:1px solid #f0f0f0;">
            <td class="ps-4 py-3">
                <i class="bi bi-geo-alt me-2" style="color:#aaa;"></i>${esc(s.sucursal)}
            </td>
            <td class="text-end py-3 fw-bold">${s.stockActual}</td>
            <td class="text-end py-3">${s.stockComprometido}</td>
            <td class="text-end pe-4 py-3">${s.stockDisponible}</td>
        </tr>`;
    }).join('');

    document.getElementById('modalBodySuc').innerHTML   = rows;
    document.getElementById('modalTotStock').textContent = ts;
    document.getElementById('modalTotComp').textContent  = tc;
    document.getElementById('modalTotDisp').textContent  = td;

    new bootstrap.Modal(document.getElementById('modalSucursales')).show();
}

function filtrarTablaStock() {
    const busq = document.getElementById('filtroStockProd').value.toLowerCase().trim();
    renderStock(!busq ? consolidado : consolidado.filter(r =>
        r.producto.toLowerCase().includes(busq) || (r.codigo||'').toLowerCase().includes(busq)
    ));
}

function esc(str) {
    return String(str||'—').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function cargarScript(src) {
    return new Promise((resolve, reject) => {
        if (document.querySelector(`script[src="${src}"]`)) { resolve(); return; }
        const s = document.createElement('script');
        s.src = src; s.onload = resolve; s.onerror = reject;
        document.head.appendChild(s);
    });
}

async function exportarPDF(tablaId, nombre) {
    const btn = document.querySelector('.btn-exp.pdf');
    btn.disabled = true; btn.textContent = 'Generando...';
    try {
        await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
        await cargarScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js');
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({ orientation:'landscape', unit:'mm', format:'a4' });
        const fecha = new Date().toLocaleDateString('es-BO');
        doc.setFontSize(16); doc.setTextColor(27,58,107);
        doc.text('Electrohogar — ' + nombre.replace(/_/g,' '), 14, 14);
        doc.setFontSize(9); doc.setTextColor(150,150,150);
        doc.text('Generado: ' + fecha, 14, 20);
        doc.autoTable({
            head: [['#','Código','Producto','Categoría','Marca','Industria','P. Propuesto','P. Vigente','Stock Total','Comprometido','Disponible','Estado']],
            body: consolidado.map((r,i) => [
                i+1,
                r.codigo||'—',
                r.producto,
                r.categoria||'—',
                r.marca||'—',
                r.industria||'—',
                parseFloat(r.precioPropuesto)>0 ? 'Bs.'+parseFloat(r.precioPropuesto).toFixed(2) : '—',
                'Bs.'+parseFloat(r.precioVigente||0).toFixed(2),
                r.stockActual,
                r.stockComprometido,
                r.stockDisponible,
                r.stockDisponible===0?'AGOTADO':(r.stockDisponible<=5?'BAJO':'OK')
            ]),
            startY: 25,
            styles: { fontSize:8, cellPadding:2 },
            headStyles: { fillColor:[27,58,107], textColor:255, fontStyle:'bold' },
            alternateRowStyles: { fillColor:[248,249,255] },
            margin: { top:25, right:8, bottom:10, left:8 }
        });
        doc.save(`${nombre}_${new Date().toISOString().slice(0,10)}.pdf`);
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-file-earmark-pdf"></i> PDF';
    }
}

function imprimirTabla(tablaId, titulo) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const clon = tabla.cloneNode(true);
    clon.querySelectorAll('button').forEach(b => { b.outerHTML = b.textContent; });
    const w = window.open('','_blank','width=1000,height=700');
    w.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${titulo}</title><style>body{font-family:Arial;font-size:11px;margin:15px;}table{width:100%;border-collapse:collapse;}thead tr{background:#1B3A6B;}thead th{color:#fff;padding:6px 8px;font-size:10px;}tbody td{padding:5px 8px;border-bottom:1px solid #eee;}tfoot td{font-weight:bold;background:#e8ecf8;padding:5px 8px;}</style></head><body><h3 style="color:#1B3A6B;">${titulo} — Electrohogar</h3>${clon.outerHTML}</body></html>`);
    w.document.close(); setTimeout(() => w.print(), 500);
}

async function exportarExcel(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const btn = document.querySelector('.btn-exp.xls');
    btn.disabled = true; btn.textContent = 'Exportando...';
    try {
        await cargarScript('https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js');
        const clon = tabla.cloneNode(true);
        clon.querySelectorAll('button,.btn').forEach(b => { b.replaceWith(document.createTextNode(b.textContent)); });
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(clon), nombre.slice(0,31));
        XLSX.writeFile(wb, `${nombre}_${new Date().toISOString().slice(0,10)}.xlsx`);
    } finally {
        btn.disabled = false; btn.innerHTML = '<i class="bi bi-file-earmark-excel"></i> Excel';
    }
}

document.addEventListener('DOMContentLoaded', cargarStock);
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
