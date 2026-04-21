<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5" style="max-width:700px;">
    <h1 class="mb-4"><i class="bi bi-credit-card"></i> Proceso de Pago</h1>

    <?php
    $sinStock = array_filter($items, function($i) {
        return (int)($i['producto']['stock'] ?? 0) < (int)$i['cantidad'];
    });
    ?>

    <!-- Resumen -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light font-weight-bold"><i class="bi bi-bag"></i> Resumen del pedido</div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead class="thead-light">
                    <tr><th>Producto</th><th class="text-center">Cant.</th><th class="text-right">Subtotal</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr <?php echo (int)($item['producto']['stock'] ?? 0) < (int)$item['cantidad'] ? 'class="table-danger"' : ''; ?>>
                            <td><?php echo htmlspecialchars($item['producto']['nombre']); ?></td>
                            <td class="text-center"><?php echo (int)$item['cantidad']; ?></td>
                            <td class="text-right">$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="font-weight-bold">
                        <td colspan="2" class="text-right">Total:</td>
                        <td class="text-right text-success">$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <?php if (!empty($sinStock)): ?>
        <div class="alert alert-warning">
            <strong><i class="bi bi-exclamation-triangle"></i> Sin stock suficiente.</strong>
        </div>
        <a href="index.php?pagina=carrito" class="btn btn-warning btn-block">Editar carrito</a>
    <?php else: ?>

    <div class="row">
        <!-- Tarjeta simulada -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white"><i class="bi bi-credit-card-fill"></i> Pagar con tarjeta</div>
                <div class="card-body">
                    <p class="text-muted small"><i class="bi bi-info-circle"></i> Modo demo — usa cualquier dato</p>
                    <form id="formTarjeta">
                        <div class="form-group">
                            <label class="small font-weight-bold">Número de tarjeta</label>
                            <input type="text" id="nroTarjeta" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-7">
                                <label class="small font-weight-bold">Vencimiento</label>
                                <input type="text" id="vencimiento" class="form-control" placeholder="MM/AA" maxlength="5">
                            </div>
                            <div class="form-group col-5">
                                <label class="small font-weight-bold">CVV</label>
                                <input type="text" id="cvv" class="form-control" placeholder="123" maxlength="3">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="small font-weight-bold">Titular</label>
                            <input type="text" id="titular" class="form-control" placeholder="Nombre en la tarjeta">
                        </div>
                        <div id="errorTarjeta" class="alert alert-danger" style="display:none;"></div>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="bi bi-lock-fill"></i> Pagar $<?php echo number_format($total, 2); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- QR Demo -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success text-white"><i class="bi bi-qr-code"></i> Pagar con QR</div>
                <div class="card-body d-flex flex-column align-items-center justify-content-center text-center">
                    <p class="text-muted small mb-2">Escanea el QR con tu app de pagos</p>
                    <img src="recursos/imagenes/QR_Electrohogar.jpeg" alt="QR de pago" class="img-fluid border rounded" style="max-width:200px;">
                    <div class="mt-3 w-100">
                        <p class="mb-1 small text-muted">Monto a pagar:</p>
                        <h4 id="montoQR" class="text-success font-weight-bold">$<?php echo number_format($total, 2); ?></h4>
                    </div>
                    <form id="formQR" class="w-100 mt-2">
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="bi bi-check-circle-fill"></i> Pagar con QR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <a href="index.php?pagina=carrito" class="btn btn-link btn-block mt-2 text-muted">
        <i class="bi bi-arrow-left"></i> Volver al carrito
    </a>
</div>

<script>
(function () {
    // Formato tarjeta
    var nro = document.getElementById('nroTarjeta');
    var ven = document.getElementById('vencimiento');
    if (nro) {
        nro.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').substring(0, 16);
            this.value = v.replace(/(.{4})/g, '$1 ').trim();
        });
    }
    if (ven) {
        ven.addEventListener('input', function () {
            var v = this.value.replace(/\D/g, '').substring(0, 4);
            if (v.length >= 2) v = v.substring(0,2) + '/' + v.substring(2);
            this.value = v;
        });
    }

    // Submit tarjeta simulada
    var form = document.getElementById('formTarjeta');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            var err = document.getElementById('errorTarjeta');
            var digitos = (document.getElementById('nroTarjeta').value.replace(/\s/g,'')).length;
            var cvv     = document.getElementById('cvv').value.trim();
            var titular = document.getElementById('titular').value.trim();
            var venc    = document.getElementById('vencimiento').value.trim();

            if (digitos < 16)        { err.textContent = 'Número de tarjeta inválido.';  err.style.display=''; return; }
            if (!/^\d{2}\/\d{2}$/.test(venc)) { err.textContent = 'Vencimiento inválido (MM/AA).'; err.style.display=''; return; }
            if (cvv.length < 3)      { err.textContent = 'CVV inválido.';                err.style.display=''; return; }
            if (titular.length < 3)  { err.textContent = 'Ingresa el nombre del titular.'; err.style.display=''; return; }

            err.style.display = 'none';
            var btn = form.querySelector('button[type=submit]');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

            // Simular 1.5s de "procesamiento"
            setTimeout(function () {
                var f = document.createElement('form');
                f.method = 'POST';
                f.action = 'index.php?pagina=pago';
                document.body.appendChild(f);
                f.submit();
            }, 1500);
        });
    }

}());
</script>

<?php if (empty($sinStock)): ?>
<script>
(function () {
    var formQR = document.getElementById('formQR');
    if (!formQR) return;
    formQR.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = formQR.querySelector('button[type=submit]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';
        document.getElementById('montoQR').textContent = '$0.00';
        setTimeout(function () {
            var f = document.createElement('form');
            f.method = 'POST';
            f.action = 'index.php?pagina=pago&metodo=qr';
            document.body.appendChild(f);
            f.submit();
        }, 1500);
    });
}());
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
