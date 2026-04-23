<?php require_once __DIR__ . '/layout_admin/head.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<style>
.alm-barra { background:#fff; border-radius:12px; padding:14px 18px; box-shadow:0 2px 8px rgba(27,58,107,.06); margin-bottom:16px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; }
.alm-barra .form-group { margin-bottom:0; }
.alm-barra label { font-size:11px; font-weight:600; color:#666; margin-bottom:3px; text-transform:uppercase; letter-spacing:.4px; display:block; }
.export-btns { display:flex; gap:6px; margin-left:auto; }
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
.badge-agotado { background:#dc3545; color:#fff; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.badge-bajo    { background:#ffc107; color:#333; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; }
.alm-loading { text-align:center; padding:40px; color:#aaa; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-exclamation-diamond me-2"></i>Stock Crítico
            <?php if ($totalCriticos > 0): ?>
                <span class="badge bg-danger ms-2" style="font-size:13px;"><?php echo $totalCriticos; ?></span>
            <?php endif; ?>
        </h4>
        <small class="text-muted">Productos con stock bajo o agotado</small>
    </div>
</div>

<div class="alm-barra">
    <div class="form-group">
        <label>Umbral crítico (unidades)</label>
        <input type="number" class="form-control form-control-sm" id="umbralCritico" min="1" value="5" style="max-width:140px;">
    </div>
    <button class="btn btn-primary btn-sm" style="height:32px;border-radius:8px;font-weight:600;" onclick="cargarCritico()">
        <i class="bi bi-search"></i> Consultar
    </button>
    <div class="export-btns">
        <button class="btn-exp pdf"   onclick="exportarPDF('tablaCritico','Stock_Critico')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
        <button class="btn-exp print" onclick="imprimirTabla('tablaCritico','Stock Crítico')"><i class="bi bi-printer"></i> Imprimir</button>
        <button class="btn-exp xls"   onclick="exportarExcel('tablaCritico','Stock_Critico')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
    </div>
</div>

<div class="alm-table-wrap">
    <div id="loadingCritico" class="alm-loading" style="display:none;"><div class="spinner-border" style="color:var(--primary);"></div></div>
    <table class="alm-table" id="tablaCritico">
        <thead>
            <tr>
                <th>#</th><th>Producto</th><th>Categoría</th>
                <th class="text-end">Stock Total</th><th class="text-center">Alerta</th>
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
                    <td class="text-end"><strong><?php echo (int)$c['stockTotal']; ?></strong></td>
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

<script>
const API = '/api/almacen.php';

function cargarCritico() {
    const umbral = parseInt(document.getElementById('umbralCritico').value) || 5;
    document.getElementById('loadingCritico').style.display = 'block';
    document.getElementById('tablaCritico').style.display   = 'none';
    fetch(`${API}?action=stock_critico&umbral=${umbral}`)
        .then(r => r.json())
        .then(res => {
            const data  = res.data || [];
            const tbody = document.getElementById('bodyCritico');
            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">No hay productos con stock ≤ ${umbral}.</td></tr>`;
            } else {
                tbody.innerHTML = data.map((r, i) => `
                    <tr>
                        <td>${i+1}</td>
                        <td><strong>${esc(r.producto)}</strong></td>
                        <td>${esc(r.categoria||'—')}</td>
                        <td class="text-end"><strong>${r.stockTotal}</strong></td>
                        <td class="text-center">
                            <span class="badge-${r.alerta==='agotado'?'agotado':'bajo'}">
                                ${r.alerta==='agotado'?'AGOTADO':'STOCK BAJO'}
                            </span>
                        </td>
                    </tr>`).join('');
            }
            document.getElementById('loadingCritico').style.display = 'none';
            document.getElementById('tablaCritico').style.display   = 'table';
        });
}

function esc(str) { return String(str||'—').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function exportarPDF(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'font-family:Arial,sans-serif;padding:20px;background:#fff;';
    wrapper.innerHTML = `<h3 style="color:#1B3A6B;border-bottom:2px solid #1B3A6B;padding-bottom:8px;">${nombre.replace(/_/g,' ')} — Electrohogar</h3>${tabla.outerHTML}`;
    wrapper.querySelectorAll('th').forEach(t => t.style.cssText='background:#1B3A6B;color:#fff;padding:6px 8px;font-size:10px;');
    wrapper.querySelectorAll('td').forEach(t => t.style.cssText='padding:5px 8px;border-bottom:1px solid #eee;font-size:10px;');
    html2pdf().set({margin:[10,8,10,8],filename:`${nombre}_${new Date().toISOString().slice(0,10)}.pdf`,html2canvas:{scale:2},jsPDF:{unit:'mm',format:'a4'}}).from(wrapper).save();
}

function imprimirTabla(tablaId, titulo) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const w = window.open('','_blank','width=800,height:600');
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
