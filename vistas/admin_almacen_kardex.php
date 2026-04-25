<?php require_once __DIR__ . '/layout_admin/head.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
.alm-barra { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 8px rgba(27,58,107,.06); margin-bottom:16px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
.alm-barra .form-group { margin-bottom:0; flex:1; min-width:120px; }
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
.badge-tipo { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; white-space:nowrap; }
.tipo-venta            { background:#fdecea; color:#b71c1c; }
.tipo-traspaso_salida  { background:#fff3e0; color:#e65100; }
.tipo-traspaso_entrada { background:#e8f5e9; color:#1b5e20; }
.tipo-devolucion       { background:#e3f2fd; color:#0d47a1; }
.tipo-baja             { background:#f3e5f5; color:#4a148c; }
.tipo-ajuste_entrada   { background:#e0f7fa; color:#006064; }
.tipo-ajuste_salida    { background:#fce4ec; color:#880e4f; }
.alm-loading { text-align:center; padding:40px; color:#aaa; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-journal-text me-2"></i>Kardex
        </h4>
        <small class="text-muted">Historial de movimientos de stock</small>
    </div>
</div>

<div class="alm-barra">
    <div class="form-group">
        <label>Producto</label>
        <select class="form-control form-control-sm" id="kProd">
            <option value="0">Todos</option>
            <?php foreach ($productos as $p): ?>
                <option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" style="max-width:160px;">
        <label>Sucursal</label>
        <select class="form-control form-control-sm" id="kSuc">
            <option value="0">Todas</option>
            <?php foreach ($sucursales as $s): ?>
                <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group" style="max-width:150px;">
        <label>Tipo</label>
        <select class="form-control form-control-sm" id="kTipo">
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
        <input type="date" class="form-control form-control-sm" id="kDesde">
    </div>
    <div class="form-group" style="max-width:140px;">
        <label>Hasta</label>
        <input type="date" class="form-control form-control-sm" id="kHasta">
    </div>
    <button class="btn btn-primary btn-sm" style="height:32px;border-radius:8px;font-weight:600;" onclick="cargarKardex()">
        <i class="bi bi-search"></i> Filtrar
    </button>
    <button class="btn btn-outline-secondary btn-sm" style="height:32px;border-radius:8px;" onclick="limpiarFiltros()">
        <i class="bi bi-x"></i>
    </button>
    <div class="export-btns">
        <button class="btn-exp pdf"   onclick="exportarPDF('tablaKardex','Kardex')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="btn-exp print" onclick="imprimirTabla('tablaKardex','Kardex de Movimientos')"><i class="bi bi-printer"></i> Imprimir</button>
        <button class="btn-exp xls"   onclick="exportarExcel('tablaKardex','Kardex')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
    </div>
</div>

<div class="alm-table-wrap">
    <div id="loadingKardex" class="alm-loading" style="display:none;"><div class="spinner-border" style="color:var(--primary);"></div><p class="mt-2">Cargando...</p></div>
    <table class="alm-table" id="tablaKardex">
        <thead>
            <tr>
                <th>ID</th><th>Fecha/Hora</th>
                <th>Código</th><th>Producto</th>
                <th class="text-end">P. Vigente</th>
                <th>Sucursal</th>
                <th>Tipo</th><th class="text-end">Cantidad</th>
                <th class="text-end">Stock Antes</th><th class="text-end">Stock Después</th>
                <th>Referencia</th><th>Observación</th><th>Usuario</th>
            </tr>
        </thead>
        <tbody id="bodyKardex">
            <tr><td colspan="13" class="text-center text-muted py-4">Aplica filtros y presiona <strong>Filtrar</strong> para ver el kardex.</td></tr>
        </tbody>
    </table>
</div>

<script>
const API = '/api/almacen.php';

function cargarKardex() {
    const prod  = document.getElementById('kProd').value;
    const suc   = document.getElementById('kSuc').value;
    const tipo  = document.getElementById('kTipo').value;
    const desde = document.getElementById('kDesde').value;
    const hasta = document.getElementById('kHasta').value;

    document.getElementById('loadingKardex').style.display = 'block';
    document.getElementById('tablaKardex').style.display   = 'none';

    fetch(`${API}?action=kardex&codProducto=${prod}&codSucursal=${suc}&tipo=${encodeURIComponent(tipo)}&desde=${desde}&hasta=${hasta}`)
        .then(r => r.json())
        .then(res => {
            const data  = res.data || [];
            const tbody = document.getElementById('bodyKardex');
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="13" class="text-center text-muted py-4">Sin movimientos para los filtros aplicados.</td></tr>';
            } else {
                tbody.innerHTML = data.map(r => `
                    <tr>
                        <td>${r.id}</td>
                        <td><small>${r.fechaHora}</small></td>
                        <td><span style="font-family:monospace;font-size:11px;">${esc(r.codigo||'—')}</span></td>
                        <td>${esc(r.producto)}</td>
                        <td class="text-end"><small style="color:#28a745;font-weight:700;">Bs.${parseFloat(r.precioVigente||0).toFixed(2)}</small></td>
                        <td>${esc(r.sucursal)}</td>
                        <td><span class="badge-tipo tipo-${r.tipo}">${labelTipo(r.tipo)}</span></td>
                        <td class="text-end"><strong>${r.cantidad}</strong></td>
                        <td class="text-end">${r.stockAntes}</td>
                        <td class="text-end">${r.stockDespues}</td>
                        <td><small>${esc(r.referencia||'—')}</small></td>
                        <td><small>${esc(r.observacion||'—')}</small></td>
                        <td><small>${esc(r.usuarioCuenta||'—')}</small></td>
                    </tr>`).join('');
            }
            document.getElementById('loadingKardex').style.display = 'none';
            document.getElementById('tablaKardex').style.display   = 'table';
        });
}

function limpiarFiltros() {
    ['kProd','kSuc','kTipo','kDesde','kHasta'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.tagName === 'SELECT' ? (el.selectedIndex = 0) : (el.value = '');
    });
}

function esc(str) { return String(str||'—').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function labelTipo(tipo) {
    const map = { venta:'Venta', traspaso_salida:'Traspaso Salida', traspaso_entrada:'Traspaso Entrada', devolucion:'Devolución', baja:'Baja', ajuste_entrada:'Ajuste +', ajuste_salida:'Ajuste -' };
    return map[tipo] || tipo;
}

function exportarPDF(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'font-family:Arial,sans-serif;padding:20px;background:#fff;';
    wrapper.innerHTML = `<h3 style="color:#1B3A6B;border-bottom:2px solid #1B3A6B;padding-bottom:8px;">${nombre} — Electrohogar</h3>${tabla.outerHTML}`;
    wrapper.querySelectorAll('th').forEach(t => t.style.cssText = 'background:#1B3A6B;color:#fff;padding:6px 8px;font-size:10px;');
    wrapper.querySelectorAll('td').forEach(t => t.style.cssText = 'padding:5px 8px;border-bottom:1px solid #eee;font-size:10px;');
    html2pdf().set({ margin:[10,8,10,8], filename:`${nombre}_${new Date().toISOString().slice(0,10)}.pdf`, html2canvas:{scale:2}, jsPDF:{unit:'mm',format:'a4',orientation:'landscape'} }).from(wrapper).save();
}

function imprimirTabla(tablaId, titulo) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const w = window.open('','_blank','width=1000,height=700');
    w.document.write(`<!DOCTYPE html><html><head><meta charset="UTF-8"><title>${titulo}</title><style>body{font-family:Arial;font-size:11px;margin:15px;}table{width:100%;border-collapse:collapse;}thead tr{background:#1B3A6B;}thead th{color:#fff;padding:6px 8px;font-size:10px;}tbody td{padding:5px 8px;border-bottom:1px solid #eee;}</style></head><body><h3 style="color:#1B3A6B;">${titulo} — Electrohogar</h3>${tabla.outerHTML}</body></html>`);
    w.document.close(); setTimeout(() => w.print(), 500);
}

function exportarExcel(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, XLSX.utils.table_to_sheet(tabla.cloneNode(true)), nombre.slice(0,31));
    XLSX.writeFile(wb, `${nombre}_${new Date().toISOString().slice(0,10)}.xlsx`);
}
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
