<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<style>
    .checkout-wrap { background: var(--gris-bg); min-height: 80vh; padding: 36px 0 60px; }

    /* Pasos */
    .checkout-steps { display: flex; align-items: center; justify-content: center; gap: 0; margin-bottom: 32px; }
    .checkout-step  { display: flex; flex-direction: column; align-items: center; position: relative; flex: 1; max-width: 160px; }
    .checkout-step .ico {
        width: 38px; height: 38px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; font-weight: 700; border: 2px solid #ddd;
        background: #fff; color: #aaa; z-index: 1;
    }
    .checkout-step.done .ico  { background: var(--azul);    border-color: var(--azul);    color: #fff; }
    .checkout-step.active .ico{ background: var(--amarillo); border-color: var(--amarillo); color: #fff; }
    .checkout-step span { font-size: 11px; margin-top: 5px; color: #999; font-weight: 500; }
    .checkout-step.done span, .checkout-step.active span { color: var(--azul); font-weight: 700; }
    .checkout-step::before {
        content: ''; position: absolute; top: 19px; left: calc(-50% + 19px); right: calc(50% + 19px);
        height: 2px; background: #ddd;
    }
    .checkout-step:first-child::before { display: none; }
    .checkout-step.done::before { background: var(--azul); }

    /* Panel izquierdo — resumen */
    .resumen-panel { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .resumen-panel .titulo-panel {
        font-size: 14px; font-weight: 700; color: var(--azul);
        border-bottom: 2px solid var(--amarillo); padding-bottom: 10px; margin-bottom: 16px;
    }
    .resumen-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f4f4f4; }
    .resumen-item img { width: 50px; height: 50px; object-fit: contain; border-radius: 8px; border: 1px solid #eee; background: #fafafa; }
    .resumen-item .nombre { font-size: 13px; font-weight: 600; color: #333; }
    .resumen-item .detalle { font-size: 12px; color: #888; }
    .resumen-item .subtotal { font-size: 13px; font-weight: 700; color: var(--azul); white-space: nowrap; margin-left: auto; }
    .resumen-total { display: flex; justify-content: space-between; align-items: center; padding-top: 14px; }
    .resumen-total .label { font-size: 15px; font-weight: 600; color: #555; }
    .resumen-total .monto { font-size: 22px; font-weight: 900; color: var(--azul); }
    .cliente-info { background: #f8f9ff; border-radius: 10px; padding: 12px 16px; margin-top: 16px; font-size: 13px; }
    .cliente-info i { color: var(--amarillo); margin-right: 6px; }

    /* Panel derecho — métodos de pago */
    .pago-panel { background: #fff; border-radius: 14px; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .pago-panel .titulo-panel {
        font-size: 14px; font-weight: 700; color: var(--azul);
        border-bottom: 2px solid var(--amarillo); padding-bottom: 10px; margin-bottom: 20px;
    }

    /* Tabs de método */
    .metodo-tabs { display: flex; gap: 10px; margin-bottom: 24px; }
    .metodo-tab {
        flex: 1; padding: 12px; border-radius: 10px; border: 2px solid #e0e0e0;
        background: #fafafa; cursor: pointer; text-align: center;
        transition: all .2s; font-size: 13px; font-weight: 600; color: #888;
    }
    .metodo-tab i { display: block; font-size: 22px; margin-bottom: 4px; }
    .metodo-tab.activo { border-color: var(--azul); background: #f0f4ff; color: var(--azul); }
    .metodo-tab:hover:not(.activo) { border-color: #bbb; }

    .metodo-panel { display: none; }
    .metodo-panel.activo { display: block; }

    /* Tarjeta visual */
    .card-preview {
        background: linear-gradient(135deg, var(--azul) 0%, var(--azul-claro) 100%);
        border-radius: 14px; padding: 20px 22px; color: #fff;
        margin-bottom: 20px; position: relative; overflow: hidden;
        box-shadow: 0 6px 20px rgba(27,58,107,.35);
    }
    .card-preview::after {
        content: ''; position: absolute; right: -20px; top: -20px;
        width: 120px; height: 120px; border-radius: 50%;
        background: rgba(255,255,255,.07);
    }
    .card-preview .chip { width: 36px; height: 26px; background: var(--amarillo); border-radius: 5px; margin-bottom: 18px; }
    .card-preview .nro { font-size: 17px; letter-spacing: 3px; font-weight: 600; margin-bottom: 14px; font-family: monospace; }
    .card-preview .datos { display: flex; justify-content: space-between; font-size: 11px; opacity: .8; text-transform: uppercase; letter-spacing: 1px; }

    /* Inputs */
    .pago-panel .form-control { border-radius: 8px; font-size: 14px; border: 1.5px solid #dde; }
    .pago-panel .form-control:focus { border-color: var(--azul); box-shadow: 0 0 0 .18rem rgba(27,58,107,.15); }
    .pago-panel label { font-size: 12px; font-weight: 700; color: var(--azul); margin-bottom: 4px; }

    /* QR */
    .qr-wrap { text-align: center; padding: 10px 0; }
    .qr-wrap img { border-radius: 12px; border: 3px solid var(--azul); max-width: 190px; }
    .qr-monto { font-size: 26px; font-weight: 900; color: var(--azul); margin: 12px 0 4px; }
    .qr-instruccion { font-size: 12px; color: #888; margin-bottom: 20px; }

    /* Botón pagar */
    .btn-pagar {
        background: linear-gradient(90deg, var(--azul) 0%, var(--azul-claro) 100%);
        color: #fff; border: none; border-radius: 10px;
        padding: 13px; font-size: 15px; font-weight: 700;
        width: 100%; cursor: pointer; transition: opacity .2s;
    }
    .btn-pagar:hover { opacity: .9; color: #fff; }
    .btn-pagar:disabled { opacity: .65; cursor: not-allowed; }
    .btn-pagar-qr {
        background: linear-gradient(90deg, #1a6b3a 0%, #28a745 100%);
        color: #fff; border: none; border-radius: 10px;
        padding: 13px; font-size: 15px; font-weight: 700;
        width: 100%; cursor: pointer; transition: opacity .2s;
    }
    .btn-pagar-qr:hover { opacity: .9; }
    .btn-pagar-qr:disabled { opacity: .65; cursor: not-allowed; }

    .seguro-badge { display: flex; align-items: center; justify-content: center; gap: 6px; font-size: 11px; color: #aaa; margin-top: 12px; }
</style>

<div class="checkout-wrap">
<div class="container" style="max-width:960px;">

    <?php
    $sinStock = array_filter($items, function($i) {
        return (int)($i['producto']['stock'] ?? 0) < (int)$i['cantidad'];
    });
    ?>

    <!-- Pasos -->
    <div class="checkout-steps mb-4">
        <div class="checkout-step done">
            <div class="ico"><i class="bi bi-cart-check-fill"></i></div>
            <span>Carrito</span>
        </div>
        <div class="checkout-step active">
            <div class="ico"><i class="bi bi-credit-card-fill"></i></div>
            <span>Pago</span>
        </div>
        <div class="checkout-step">
            <div class="ico"><i class="bi bi-check-lg"></i></div>
            <span>Confirmación</span>
        </div>
    </div>

    <?php if (!empty($sinStock)): ?>
        <div class="alert alert-warning text-center">
            <i class="bi bi-exclamation-triangle-fill mr-2"></i>
            <strong>Algunos productos no tienen stock suficiente.</strong>
            Por favor vuelve al carrito y ajusta las cantidades.
        </div>
        <div class="text-center mt-3">
            <a href="index.php?pagina=inicio" class="btn btn-azul px-4">
                <i class="bi bi-arrow-left mr-1"></i>Volver a la tienda
            </a>
        </div>
    <?php else: ?>

    <div class="row">
        <!-- ══ RESUMEN ══ -->
        <div class="col-md-5 mb-4">
            <div class="resumen-panel">
                <div class="titulo-panel"><i class="bi bi-bag-check mr-2"></i>Resumen del pedido</div>

                <?php foreach ($items as $item): ?>
                <div class="resumen-item">
                    <img src="<?php echo !empty($item['producto']['imagen']) ? 'recursos/imagenes/' . htmlspecialchars($item['producto']['imagen']) : 'recursos/imagenes/ups.png'; ?>"
                         alt="<?php echo htmlspecialchars($item['producto']['nombre']); ?>"
                         onerror="this.onerror=null;this.src='recursos/imagenes/ups.png';">
                    <div style="flex:1;min-width:0;">
                        <div class="nombre"><?php echo htmlspecialchars($item['producto']['nombre']); ?></div>
                        <div class="detalle">
                            <?php echo (int)$item['cantidad']; ?> × Bs. <?php echo number_format((float)$item['producto']['precioVigente'], 2); ?>
                        </div>
                    </div>
                    <div class="subtotal">Bs. <?php echo number_format($item['subtotal'], 2); ?></div>
                </div>
                <?php endforeach; ?>

                <div class="resumen-total mt-2">
                    <span class="label">Total a pagar</span>
                    <span class="monto">Bs. <?php echo number_format($total, 2); ?></span>
                </div>

                <div class="cliente-info">
                    <div><i class="bi bi-person-fill"></i><strong>Cliente:</strong> <?php echo htmlspecialchars($_SESSION['usuario']); ?></div>
                    <div class="mt-1"><i class="bi bi-shield-check"></i>Compra protegida y segura</div>
                </div>
            </div>
        </div>

        <!-- ══ PAGO ══ -->
        <div class="col-md-7 mb-4">
            <div class="pago-panel">
                <div class="titulo-panel"><i class="bi bi-credit-card-2-front mr-2"></i>Método de pago</div>

                <!-- Tabs -->
                <div class="metodo-tabs">
                    <div class="metodo-tab activo" onclick="cambiarMetodo('tarjeta', this)">
                        <i class="bi bi-credit-card-fill"></i>Tarjeta
                    </div>
                    <div class="metodo-tab" onclick="cambiarMetodo('qr', this)">
                        <i class="bi bi-qr-code"></i>Código QR
                    </div>
                </div>

                <!-- ── Tarjeta ── -->
                <div id="panelTarjeta" class="metodo-panel activo">
                    <!-- Preview visual de la tarjeta -->
                    <div class="card-preview" id="cardPreview">
                        <div class="chip"></div>
                        <div class="nro" id="prevNro">•••• •••• •••• ••••</div>
                        <div class="datos">
                            <div>
                                <div style="font-size:9px;opacity:.7;">Titular</div>
                                <div id="prevTitular">NOMBRE APELLIDO</div>
                            </div>
                            <div>
                                <div style="font-size:9px;opacity:.7;">Vence</div>
                                <div id="prevVenc">MM/AA</div>
                            </div>
                            <div style="position:absolute;right:22px;top:20px;font-size:28px;font-weight:900;opacity:.5;">
                                VISA
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info py-2 px-3" style="font-size:12px;border-radius:8px;">
                        <i class="bi bi-info-circle mr-1"></i>Modo demo — usa cualquier dato válido para simular el pago.
                    </div>

                    <form id="formTarjeta">
                        <div class="form-group">
                            <label>Número de tarjeta</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="border-radius:8px 0 0 8px;background:#f8f9ff;">
                                        <i class="bi bi-credit-card" style="color:var(--azul);"></i>
                                    </span>
                                </div>
                                <input type="text" id="nroTarjeta" class="form-control" placeholder="1234 5678 9012 3456"
                                       maxlength="19" style="border-radius:0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-7">
                                <label>Vencimiento</label>
                                <input type="text" id="vencimiento" class="form-control" placeholder="MM/AA" maxlength="5">
                            </div>
                            <div class="form-group col-5">
                                <label>CVV</label>
                                <div class="input-group">
                                    <input type="password" id="cvv" class="form-control" placeholder="•••" maxlength="3" style="border-radius:8px 0 0 8px;">
                                    <div class="input-group-append">
                                        <span class="input-group-text" style="border-radius:0 8px 8px 0;background:#f8f9ff;" title="Código de 3 dígitos al dorso">
                                            <i class="bi bi-question-circle" style="color:#aaa;"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Titular de la tarjeta</label>
                            <input type="text" id="titular" class="form-control" placeholder="Como figura en la tarjeta">
                        </div>
                        <div id="errorTarjeta" class="alert alert-danger py-2" style="display:none;font-size:13px;border-radius:8px;"></div>
                        <button type="submit" class="btn-pagar mt-2">
                            <i class="bi bi-lock-fill mr-2"></i>Pagar Bs. <?php echo number_format($total, 2); ?>
                        </button>
                    </form>
                </div>

                <!-- ── QR ── -->
                <div id="panelQR" class="metodo-panel">
                    <div class="qr-wrap">
                        <p class="text-muted" style="font-size:13px;">
                            <i class="bi bi-phone mr-1"></i>Abre tu app de pagos y escanea el siguiente código
                        </p>
                        <img src="recursos/imagenes/QR_Electrohogar.jpeg" alt="QR Electrohogar">
                        <div class="qr-monto">Bs. <?php echo number_format($total, 2); ?></div>
                        <div class="qr-instruccion">
                            <i class="bi bi-info-circle mr-1"></i>
                            Una vez realizada la transferencia, presiona el botón para confirmar tu pago.
                        </div>
                        <form id="formQR">
                            <button type="submit" class="btn-pagar-qr">
                                <i class="bi bi-check-circle-fill mr-2"></i>Confirmar pago con QR
                            </button>
                        </form>
                    </div>
                </div>

                <div class="seguro-badge">
                    <i class="bi bi-shield-lock-fill" style="color:#28a745;"></i>
                    Pago seguro y encriptado · Electrohogar &copy; <?php echo date('Y'); ?>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <div class="text-center mt-1">
        <a href="index.php?pagina=inicio" class="text-muted" style="font-size:13px;">
            <i class="bi bi-arrow-left mr-1"></i>Seguir comprando
        </a>
    </div>

</div><!-- /container -->
</div><!-- /checkout-wrap -->

<script>
function cambiarMetodo(metodo, el) {
    document.querySelectorAll('.metodo-tab').forEach(function(t) { t.classList.remove('activo'); });
    document.querySelectorAll('.metodo-panel').forEach(function(p) { p.classList.remove('activo'); });
    el.classList.add('activo');
    document.getElementById('panel' + (metodo === 'tarjeta' ? 'Tarjeta' : 'QR')).classList.add('activo');
}

(function () {
    var nroInp  = document.getElementById('nroTarjeta');
    var venInp  = document.getElementById('vencimiento');
    var titInp  = document.getElementById('titular');

    if (nroInp) {
        nroInp.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = v.replace(/(.{4})/g, '$1 ').trim();
            var display = v.padEnd(16, '•');
            document.getElementById('prevNro').textContent =
                display.replace(/(.{4})/g, '$1 ').trim();
        });
    }
    if (venInp) {
        venInp.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 3) v = v.substring(0,2) + '/' + v.substring(2);
            this.value = v;
            document.getElementById('prevVenc').textContent = v || 'MM/AA';
        });
    }
    if (titInp) {
        titInp.addEventListener('input', function () {
            document.getElementById('prevTitular').textContent =
                this.value.toUpperCase() || 'NOMBRE APELLIDO';
        });
    }

    var formTarjeta = document.getElementById('formTarjeta');
    if (formTarjeta) {
        formTarjeta.addEventListener('submit', function (e) {
            e.preventDefault();
            var err     = document.getElementById('errorTarjeta');
            var digitos = nroInp.value.replace(/\s/g, '').length;
            var cvv     = document.getElementById('cvv').value.trim();
            var titular = titInp.value.trim();
            var venc    = venInp.value.trim();

            err.style.display = 'none';
            if (digitos < 16)                        { err.textContent = 'Número de tarjeta inválido.';          err.style.display = ''; return; }
            if (!/^\d{2}\/\d{2}$/.test(venc))        { err.textContent = 'Vencimiento inválido (MM/AA).';        err.style.display = ''; return; }
            if (cvv.length < 3)                      { err.textContent = 'CVV inválido (3 dígitos).';            err.style.display = ''; return; }
            if (titular.length < 3)                  { err.textContent = 'Ingresa el nombre del titular.';       err.style.display = ''; return; }

            var btn = formTarjeta.querySelector('button[type=submit]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Procesando pago...';

            setTimeout(function () {
                var f = document.createElement('form');
                f.method = 'POST';
                f.action = 'index.php?pagina=pago';
                document.body.appendChild(f);
                f.submit();
            }, 1500);
        });
    }

    var formQR = document.getElementById('formQR');
    if (formQR) {
        formQR.addEventListener('submit', function (e) {
            e.preventDefault();
            var btn = formQR.querySelector('button[type=submit]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2"></span>Confirmando...';
            setTimeout(function () {
                var f = document.createElement('form');
                f.method = 'POST';
                f.action = 'index.php?pagina=pago&metodo=qr';
                document.body.appendChild(f);
                f.submit();
            }, 1500);
        });
    }
}());
</script>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
