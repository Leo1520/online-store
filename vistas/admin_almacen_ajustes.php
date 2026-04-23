<?php require_once __DIR__ . '/layout_admin/head.php'; ?>

<style>
.alm-form-card { background:#fff; border-radius:12px; padding:20px 22px; box-shadow:0 2px 8px rgba(27,58,107,.06); margin-bottom:16px; }
.alm-form-card h6 { font-size:14px; font-weight:700; color:var(--primary); margin-bottom:14px; }
.export-btns { display:flex; gap:6px; }
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
.badge-tipo { display:inline-block; padding:3px 9px; border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.4px; }
.tipo-devolucion    { background:#e3f2fd; color:#0d47a1; }
.tipo-baja          { background:#f3e5f5; color:#4a148c; }
.tipo-ajuste_entrada{ background:#e0f7fa; color:#006064; }
.tipo-ajuste_salida { background:#fce4ec; color:#880e4f; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-pencil-square me-2"></i>Ajustes de Inventario
        </h4>
        <small class="text-muted">Entradas, salidas, devoluciones y bajas manuales</small>
    </div>
</div>

<div class="row g-4">
    <!-- Formulario ajuste -->
    <div class="col-lg-4">
        <div class="alm-form-card">
            <h6><i class="bi bi-plus-circle me-1"></i>Registrar Ajuste</h6>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Producto</label>
                <select class="form-select form-select-sm" id="ajProd">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($productos as $p): ?>
                        <option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Sucursal</label>
                <select class="form-select form-select-sm" id="ajSuc">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Tipo de ajuste</label>
                <select class="form-select form-select-sm" id="ajTipo">
                    <option value="ajuste_entrada">Entrada por ajuste</option>
                    <option value="ajuste_salida">Salida por ajuste</option>
                    <option value="devolucion">Entrada por devolución cliente</option>
                    <option value="baja">Salida por baja/merma</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Cantidad</label>
                <input type="number" class="form-control form-control-sm" id="ajCant" min="1" value="1">
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Motivo / Observación</label>
                <input type="text" class="form-control form-control-sm" id="ajObs" placeholder="Descripción del ajuste...">
            </div>
            <button class="btn w-100 fw-semibold text-white" style="background:var(--primary);" onclick="registrarAjuste()">
                <i class="bi bi-check2-circle me-1"></i>Registrar Ajuste
            </button>
            <div id="msgAjuste" class="mt-2" style="font-size:12px;"></div>
        </div>
    </div>

    <!-- Lista de ajustes recientes -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <small class="text-muted">Últimos ajustes registrados</small>
            <div class="export-btns">
                <button class="btn-exp pdf"   onclick="exportarPDF('tablaAjustes','Ajustes_Inventario')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                <button class="btn-exp print" onclick="imprimirTabla('tablaAjustes','Ajustes de Inventario')"><i class="bi bi-printer"></i> Imprimir</button>
                <button class="btn-exp xls"   onclick="exportarExcel('tablaAjustes','Ajustes_Inventario')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
            </div>
        </div>
        <div class="alm-table-wrap">
            <table class="alm-table" id="tablaAjustes">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th><th>Producto</th><th>Sucursal</th>
                        <th>Tipo</th><th class="text-end">Cantidad</th>
                        <th class="text-end">Stock Antes</th><th class="text-end">Stock Después</th>
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

<script>
const API = '/api/almacen.php';
const TIPOS_AJUSTE = ['ajuste_entrada','ajuste_salida','devolucion','baja'];

function registrarAjuste() {
    const prod = document.getElementById('ajProd').value;
    const suc  = document.getElementById('ajSuc').value;
    const tipo = document.getElementById('ajTipo').value;
    const cant = parseInt(document.getElementById('ajCant').value);
    const obs  = document.getElementById('ajObs').value.trim();
    const msg  = document.getElementById('msgAjuste');

    if (!prod || !suc)   { mostrarMsg(msg,'Selecciona producto y sucursal.','danger'); return; }
    if (!cant || cant<1) { mostrarMsg(msg,'La cantidad debe ser mayor a 0.','danger'); return; }

    const fd = new FormData();
    fd.append('action','ajuste');
    fd.append('codProducto', prod);
    fd.append('codSucursal', suc);
    fd.append('tipo', tipo);
    fd.append('cantidad', cant);
    fd.append('observacion', obs);

    fetch(API,{method:'POST',body:fd})
        .then(r => r.json())
        .then(res => {
            if (res.ok) {
                mostrarMsg(msg,'Ajuste registrado correctamente.','success');
                document.getElementById('ajCant').value = 1;
                document.getElementById('ajObs').value  = '';
                cargarAjustes();
            } else {
                mostrarMsg(msg, res.msg||'Error al registrar ajuste.','danger');
            }
        });
}

function cargarAjustes() {
    const prod = document.getElementById('ajProd').value || 0;
    const suc  = document.getElementById('ajSuc').value  || 0;
    fetch(`${API}?action=kardex&codProducto=${prod}&codSucursal=${suc}&tipo=`)
        .then(r => r.json())
        .then(res => {
            const data  = (res.data || []).filter(r => TIPOS_AJUSTE.includes(r.tipo));
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
                        <td class="text-end"><strong>${r.cantidad}</strong></td>
                        <td class="text-end">${r.stockAntes}</td>
                        <td class="text-end">${r.stockDespues}</td>
                        <td><small>${esc(r.observacion||'—')}</small></td>
                        <td><small>${esc(r.usuarioCuenta||'—')}</small></td>
                    </tr>`).join('');
            }
        });
}

function esc(str) { return String(str||'—').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function labelTipo(tipo) {
    const map = { devolucion:'Devolución', baja:'Baja', ajuste_entrada:'Ajuste +', ajuste_salida:'Ajuste -' };
    return map[tipo] || tipo;
}

function mostrarMsg(el, msg, tipo) {
    el.innerHTML = `<div class="alert alert-${tipo} py-1 px-2 mb-0" style="font-size:12px;">${msg}</div>`;
    setTimeout(() => { el.innerHTML = ''; }, 5000);
}

function exportarPDF(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'font-family:Arial,sans-serif;padding:20px;background:#fff;';
    wrapper.innerHTML = `<h3 style="color:#1B3A6B;border-bottom:2px solid #1B3A6B;padding-bottom:8px;">${nombre.replace(/_/g,' ')} — Electrohogar</h3>${tabla.outerHTML}`;
    wrapper.querySelectorAll('th').forEach(t => t.style.cssText='background:#1B3A6B;color:#fff;padding:6px 8px;font-size:10px;');
    wrapper.querySelectorAll('td').forEach(t => t.style.cssText='padding:5px 8px;border-bottom:1px solid #eee;font-size:10px;');
    html2pdf().set({margin:[10,8,10,8],filename:`${nombre}_${new Date().toISOString().slice(0,10)}.pdf`,html2canvas:{scale:2},jsPDF:{unit:'mm',format:'a4',orientation:'landscape'}}).from(wrapper).save();
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

document.addEventListener('DOMContentLoaded', cargarAjustes);
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
