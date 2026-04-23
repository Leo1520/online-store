<?php require_once __DIR__ . '/layout_admin/head.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
.alm-cards { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:1.5rem; }
@media(max-width:768px){ .alm-cards{ grid-template-columns:repeat(2,1fr); } }
.alm-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 2px 12px rgba(27,58,107,.08); display:flex; align-items:center; gap:14px; }
.alm-card-ico { width:48px; height:48px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:22px; flex-shrink:0; }
.alm-card-ico.azul  { background:#e8f0fe; color:var(--primary); }
.alm-card-ico.verde { background:#e6f9ed; color:#28a745; }
.alm-card-ico.amari { background:#fff7e0; color:var(--accent); }
.alm-card-ico.rojo  { background:#fdecea; color:#dc3545; }
.alm-card-num { font-size:26px; font-weight:900; color:var(--primary); line-height:1; }
.alm-card-lbl { font-size:12px; color:#888; margin-top:2px; }

.alm-barra { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 8px rgba(27,58,107,.06); margin-bottom:16px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
.alm-barra .form-group { margin-bottom:0; flex:1; min-width:130px; }
.alm-barra label { font-size:11px; font-weight:600; color:#666; margin-bottom:3px; text-transform:uppercase; letter-spacing:.4px; display:block; }
.export-btns { display:flex; gap:6px; flex-shrink:0; }
.btn-exp { display:flex; align-items:center; gap:5px; padding:7px 13px; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; transition:opacity .2s; }
.btn-exp:hover { opacity:.85; }
.btn-exp.pdf   { background:#1B3A6B; color:#fff; }
.btn-exp.print { background:#6c757d; color:#fff; }
.btn-exp.xls   { background:#1d7a35; color:#fff; }

.alm-table-wrap { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(27,58,107,.06); overflow:hidden; }
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

<!-- Métricas -->
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

<!-- Filtros + exportar -->
<div class="alm-barra">
    <div class="form-group">
        <label>Buscar producto</label>
        <input type="text" class="form-control form-control-sm" id="filtroStockProd" placeholder="Nombre del producto..." oninput="filtrarTablaStock()">
    </div>
    <div class="form-group" style="max-width:180px;">
        <label>Sucursal</label>
        <select class="form-control form-control-sm" id="filtroStockSuc" onchange="filtrarTablaStock()">
            <option value="">Todas</option>
            <?php foreach ($sucursales as $s): ?>
                <option value="<?php echo htmlspecialchars($s['nombre']); ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="export-btns">
        <button class="btn-exp pdf"   onclick="exportarPDF('tablaStock','Stock_Actual')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="btn-exp print" onclick="imprimirTabla('tablaStock','Stock Actual')"><i class="bi bi-printer"></i> Imprimir</button>
        <button class="btn-exp xls"   onclick="exportarExcel('tablaStock','Stock_Actual')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
    </div>
</div>

<div class="alm-table-wrap">
    <div id="loadingStock" class="alm-loading"><div class="spinner-border" style="color:var(--primary);"></div><p class="mt-2">Cargando stock...</p></div>
    <table class="alm-table" id="tablaStock" style="display:none;">
        <thead>
            <tr>
                <th>#</th><th>Producto</th><th>Categoría</th><th>Sucursal</th>
                <th class="text-end">Stock Actual</th>
                <th class="text-end">Comprometido</th>
                <th class="text-end">Disponible</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody id="bodyStock"></tbody>
        <tfoot>
            <tr>
                <td colspan="4">TOTALES</td>
                <td class="text-end" id="totStock">—</td>
                <td class="text-end" id="totComp">—</td>
                <td class="text-end" id="totDisp">—</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</div>

<script>
const API = '/api/almacen.php';
let stockData = [];

function cargarStock() {
    document.getElementById('loadingStock').style.display = 'block';
    document.getElementById('tablaStock').style.display   = 'none';
    fetch(API + '?action=stock_actual')
        .then(r => r.json())
        .then(res => { stockData = res.data || []; renderStock(stockData); });
}

function renderStock(data) {
    const tbody = document.getElementById('bodyStock');
    let html = '', ts = 0, tc = 0, td = 0;
    if (!data.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Sin datos de stock.</td></tr>';
    } else {
        data.forEach((r, i) => {
            const sa = parseInt(r.stockActual), sc = parseInt(r.stockComprometido), sd = parseInt(r.stockDisponible);
            ts += sa; tc += sc; td += sd;
            const color = sd === 0 ? 'color:#dc3545;font-weight:700;' : (sd <= 5 ? 'color:#e65100;font-weight:700;' : '');
            html += `<tr>
                <td>${i+1}</td>
                <td><strong>${esc(r.producto)}</strong></td>
                <td>${esc(r.categoria||'—')}</td>
                <td>${esc(r.sucursal)}</td>
                <td class="text-end">${sa}</td>
                <td class="text-end">${sc > 0 ? `<span style="color:#856404;">${sc}</span>` : '0'}</td>
                <td class="text-end"><span style="${color}">${sd}</span></td>
                <td>${sd===0?'<span class="badge-agotado">AGOTADO</span>':(sd<=5?'<span class="badge-bajo">BAJO</span>':'<span style="color:#1b5e20;font-weight:600;font-size:11px;">OK</span>')}</td>
            </tr>`;
        });
        tbody.innerHTML = html;
    }
    document.getElementById('totStock').textContent = ts;
    document.getElementById('totComp').textContent  = tc;
    document.getElementById('totDisp').textContent  = td;
    document.getElementById('loadingStock').style.display = 'none';
    document.getElementById('tablaStock').style.display   = 'table';
}

function filtrarTablaStock() {
    const busq = document.getElementById('filtroStockProd').value.toLowerCase();
    const suc  = document.getElementById('filtroStockSuc').value.toLowerCase();
    renderStock(stockData.filter(r =>
        (!busq || r.producto.toLowerCase().includes(busq)) &&
        (!suc  || r.sucursal.toLowerCase().includes(suc))
    ));
}

function esc(str) {
    return String(str||'—').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function exportarPDF(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const fecha = new Date().toLocaleDateString('es-BO');
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'font-family:Arial,sans-serif;padding:20px;background:#fff;';
    wrapper.innerHTML = `<div style="display:flex;justify-content:space-between;align-items:center;border-bottom:2px solid #1B3A6B;padding-bottom:10px;margin-bottom:16px;"><div><h2 style="margin:0;color:#1B3A6B;font-size:18px;">⚡ Electrohogar</h2><p style="margin:0;color:#888;font-size:11px;">Almacén</p></div><div style="text-align:right;"><p style="margin:0;font-size:14px;font-weight:700;">${nombre.replace(/_/g,' ')}</p><p style="margin:0;font-size:11px;color:#888;">Generado: ${fecha}</p></div></div>${tabla.outerHTML}`;
    wrapper.querySelectorAll('th').forEach(t => t.style.cssText = 'background:#1B3A6B;color:#fff;padding:6px 8px;font-size:10px;');
    wrapper.querySelectorAll('td').forEach(t => t.style.cssText = 'padding:5px 8px;border-bottom:1px solid #eee;font-size:10px;');
    html2pdf().set({ margin:[10,8,10,8], filename:`${nombre}_${new Date().toISOString().slice(0,10)}.pdf`, html2canvas:{scale:2,useCORS:true}, jsPDF:{unit:'mm',format:'a4',orientation:'landscape'} }).from(wrapper).save();
}

function imprimirTabla(tablaId, titulo) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const w = window.open('','_blank','width=1000,height=700');
    w.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${titulo}</title><style>body{font-family:Arial;font-size:11px;margin:15px;}table{width:100%;border-collapse:collapse;}thead tr{background:#1B3A6B;}thead th{color:#fff;padding:6px 8px;font-size:10px;}tbody td{padding:5px 8px;border-bottom:1px solid #eee;}tfoot td{font-weight:bold;background:#e8ecf8;padding:5px 8px;}</style></head><body><h3 style="color:#1B3A6B;">${titulo} — Electrohogar</h3>${tabla.outerHTML}</body></html>`);
    w.document.close(); setTimeout(() => w.print(), 500);
}

function exportarExcel(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const clon = tabla.cloneNode(true);
    clon.querySelectorAll('button,.btn').forEach(b => b.remove());
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(clon), nombre.slice(0,31));
    XLSX.writeFile(wb, `${nombre}_${new Date().toISOString().slice(0,10)}.xlsx`);
}

document.addEventListener('DOMContentLoaded', cargarStock);
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
