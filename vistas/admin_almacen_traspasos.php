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
.badge-estado { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.est-pendiente  { background:#fff3cd; color:#856404; }
.est-completado { background:#d1e7dd; color:#0a3622; }
.est-cancelado  { background:#f8d7da; color:#842029; }
.prod-row { display:flex; gap:8px; align-items:center; margin-bottom:8px; }
.prod-row select, .prod-row input { flex:1; }
.prod-row .btn-rm { flex-shrink:0; }
.alm-loading { text-align:center; padding:40px; color:#aaa; }
</style>

<div class="page-header d-flex align-items-center justify-content-between">
    <div>
        <h4 class="mb-0 fw-bold" style="color:var(--primary)">
            <i class="bi bi-arrow-left-right me-2"></i>Traspasos
        </h4>
        <small class="text-muted">Transferencia de stock entre sucursales</small>
    </div>
</div>

<div class="row g-4">
    <!-- Formulario nuevo traspaso -->
    <div class="col-lg-4">
        <div class="alm-form-card">
            <h6><i class="bi bi-plus-circle me-1"></i>Nuevo Traspaso</h6>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Sucursal Origen</label>
                <select class="form-select form-select-sm" id="trpOrigen">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small fw-semibold">Sucursal Destino</label>
                <select class="form-select form-select-sm" id="trpDestino">
                    <option value="">Seleccionar...</option>
                    <?php foreach ($sucursales as $s): ?>
                        <option value="<?php echo (int)$s['cod']; ?>"><?php echo htmlspecialchars($s['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold">Observación</label>
                <input type="text" class="form-control form-control-sm" id="trpObs" placeholder="Motivo del traspaso...">
            </div>

            <label class="form-label small fw-semibold" style="color:var(--primary);">Productos a traspasar</label>
            <div id="trpProductos">
                <div class="prod-row">
                    <select class="form-select form-select-sm">
                        <option value="">Seleccionar producto...</option>
                        <?php foreach ($productos as $p): ?>
                            <option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="number" class="form-control form-control-sm" min="1" value="1" style="max-width:70px;">
                    <button class="btn btn-sm btn-outline-danger btn-rm" onclick="quitarFilaProd(this)" title="Quitar"><i class="bi bi-x"></i></button>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-primary mt-1 mb-3" onclick="agregarFilaProd()">
                <i class="bi bi-plus"></i> Añadir producto
            </button>

            <button class="btn w-100 fw-semibold text-white" style="background:var(--primary);" onclick="crearTraspaso()">
                <i class="bi bi-arrow-left-right me-1"></i>Crear Traspaso
            </button>
            <div id="msgTraspaso" class="mt-2" style="font-size:12px;"></div>
        </div>
    </div>

    <!-- Lista de traspasos -->
    <div class="col-lg-8">
        <div class="d-flex justify-content-end mb-2">
            <div class="export-btns">
                <button class="btn-exp pdf"   onclick="exportarPDF('tablaTraspasos','Traspasos')"><i class="bi bi-file-earmark-pdf"></i> PDF</button>
                <button class="btn-exp print" onclick="imprimirTabla('tablaTraspasos','Registro de Traspasos')"><i class="bi bi-printer"></i> Imprimir</button>
                <button class="btn-exp xls"   onclick="exportarExcel('tablaTraspasos','Traspasos')"><i class="bi bi-file-earmark-excel"></i> Excel</button>
            </div>
        </div>
        <div class="alm-table-wrap">
            <div id="loadingTraspasos" class="alm-loading"><div class="spinner-border" style="color:var(--primary);"></div></div>
            <table class="alm-table" id="tablaTraspasos" style="display:none;">
                <thead>
                    <tr>
                        <th>Nro</th><th>Fecha</th><th>Origen</th><th>Destino</th>
                        <th class="text-center">Prods.</th><th class="text-center">Unidades</th>
                        <th>Estado</th><th>Observación</th><th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="bodyTraspasos"></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal detalle traspaso -->
<div class="modal fade" id="modalDetalleTrp" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:var(--primary);color:#fff;">
                <h5 class="modal-title"><i class="bi bi-arrow-left-right me-1"></i>Detalle <span id="modalTrpNro"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalTrpBody">Cargando...</div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
const API = '/api/almacen.php';
const PROD_OPTIONS = `<?php foreach ($productos as $p): ?><option value="<?php echo (int)$p['id_producto']; ?>"><?php echo htmlspecialchars($p['nombre'], ENT_QUOTES); ?></option><?php endforeach; ?>`;

function cargarTraspasos() {
    document.getElementById('loadingTraspasos').style.display = 'block';
    document.getElementById('tablaTraspasos').style.display   = 'none';
    fetch(API + '?action=traspasos')
        .then(r => r.json())
        .then(res => {
            const data  = res.data || [];
            const tbody = document.getElementById('bodyTraspasos');
            if (!data.length) {
                tbody.innerHTML = '<tr><td colspan="9" class="text-center text-muted py-4">No hay traspasos registrados.</td></tr>';
            } else {
                tbody.innerHTML = data.map(t => {
                    const ec = { pendiente:'est-pendiente', completado:'est-completado', cancelado:'est-cancelado' }[t.estado] || '';
                    const acc = t.estado === 'pendiente'
                        ? `<button class="btn btn-sm btn-success me-1" onclick="completarTrp(${t.nro})"><i class="bi bi-check-lg"></i></button>
                           <button class="btn btn-sm btn-danger" onclick="cancelarTrp(${t.nro})"><i class="bi bi-x-lg"></i></button>`
                        : '';
                    return `<tr>
                        <td><strong>TRP-${String(t.nro).padStart(6,'0')}</strong>
                            <button class="btn btn-sm btn-link p-0 ms-1" onclick="verDetalle(${t.nro})"><i class="bi bi-eye"></i></button>
                        </td>
                        <td><small>${t.fechaHora}</small></td>
                        <td>${esc(t.sucursalOrigen)}</td>
                        <td>${esc(t.sucursalDestino)}</td>
                        <td class="text-center">${t.cantProductos||0}</td>
                        <td class="text-center">${t.cantUnidades||0}</td>
                        <td><span class="badge-estado ${ec}">${t.estado}</span></td>
                        <td><small>${esc(t.observacion||'—')}</small></td>
                        <td>${acc}</td>
                    </tr>`;
                }).join('');
            }
            document.getElementById('loadingTraspasos').style.display = 'none';
            document.getElementById('tablaTraspasos').style.display   = 'table';
        });
}

function agregarFilaProd() {
    const div = document.createElement('div');
    div.className = 'prod-row';
    div.innerHTML = `<select class="form-select form-select-sm"><option value="">Seleccionar producto...</option>${PROD_OPTIONS}</select><input type="number" class="form-control form-control-sm" min="1" value="1" style="max-width:70px;"><button class="btn btn-sm btn-outline-danger btn-rm" onclick="quitarFilaProd(this)"><i class="bi bi-x"></i></button>`;
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

    if (!origen || !destino) { mostrarMsg(msg,'Selecciona origen y destino.','danger'); return; }
    if (origen === destino)  { mostrarMsg(msg,'Origen y destino no pueden ser iguales.','danger'); return; }

    const prods = []; let valido = true;
    filas.forEach(f => {
        const cod  = parseInt(f.querySelector('select').value);
        const cant = parseInt(f.querySelector('input[type=number]').value);
        if (!cod || cant < 1) { valido = false; return; }
        prods.push({ codProducto: cod, cantidad: cant });
    });
    if (!valido || !prods.length) { mostrarMsg(msg,'Completa todos los productos.','danger'); return; }

    const fd = new FormData();
    fd.append('action','crear_traspaso');
    fd.append('codSucursalOrigen', origen);
    fd.append('codSucursalDestino', destino);
    fd.append('observacion', obs);
    fd.append('productos', JSON.stringify(prods));

    fetch(API, { method:'POST', body:fd })
        .then(r => r.json())
        .then(res => {
            if (res.ok) { mostrarMsg(msg,`Traspaso TRP-${String(res.nro).padStart(6,'0')} creado.`,'success'); cargarTraspasos(); }
            else         mostrarMsg(msg, res.msg||'Error al crear traspaso.','danger');
        });
}

function completarTrp(nro) {
    if (!confirm(`¿Completar TRP-${String(nro).padStart(6,'0')}? Esto moverá el stock.`)) return;
    const fd = new FormData(); fd.append('action','completar_traspaso'); fd.append('nro',nro);
    fetch(API,{method:'POST',body:fd}).then(r=>r.json()).then(res=>{ if(res.ok) cargarTraspasos(); else alert(res.msg||'Error.'); });
}

function cancelarTrp(nro) {
    if (!confirm(`¿Cancelar TRP-${String(nro).padStart(6,'0')}?`)) return;
    const fd = new FormData(); fd.append('action','cancelar_traspaso'); fd.append('nro',nro);
    fetch(API,{method:'POST',body:fd}).then(r=>r.json()).then(res=>{ if(res.ok) cargarTraspasos(); else alert(res.msg||'Error.'); });
}

function verDetalle(nro) {
    document.getElementById('modalTrpNro').textContent = `TRP-${String(nro).padStart(6,'0')}`;
    document.getElementById('modalTrpBody').innerHTML  = '<div class="text-center py-3"><span class="spinner-border spinner-border-sm"></span> Cargando...</div>';
    new bootstrap.Modal(document.getElementById('modalDetalleTrp')).show();
    fetch(`${API}?action=detalle_traspaso&nro=${nro}`)
        .then(r => r.json())
        .then(res => {
            const data = res.data || [];
            if (!data.length) {
                document.getElementById('modalTrpBody').innerHTML = '<p class="text-muted">Sin detalle.</p>';
            } else {
                let html = '<table class="table table-sm"><thead><tr><th>Producto</th><th>Cantidad</th><th>Stock en Origen</th></tr></thead><tbody>';
                data.forEach(d => { html += `<tr><td>${esc(d.producto)}</td><td>${d.cantidad}</td><td>${d.stockOrigen!==null?d.stockOrigen:'—'}</td></tr>`; });
                document.getElementById('modalTrpBody').innerHTML = html + '</tbody></table>';
            }
        });
}

function esc(str) { return String(str||'—').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

function mostrarMsg(el, msg, tipo) {
    el.innerHTML = `<div class="alert alert-${tipo} py-1 px-2 mb-0" style="font-size:12px;">${msg}</div>`;
    setTimeout(() => { el.innerHTML = ''; }, 5000);
}

function exportarPDF(tablaId, nombre) {
    const tabla = document.getElementById(tablaId); if (!tabla) return;
    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'font-family:Arial,sans-serif;padding:20px;background:#fff;';
    wrapper.innerHTML = `<h3 style="color:#1B3A6B;border-bottom:2px solid #1B3A6B;padding-bottom:8px;">${nombre} — Electrohogar</h3>${tabla.outerHTML}`;
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

document.addEventListener('DOMContentLoaded', cargarTraspasos);
</script>

<?php require_once __DIR__ . '/layout_admin/footer.php'; ?>
