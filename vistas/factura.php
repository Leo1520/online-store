<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Factura #<?php echo (int)$nroVenta; ?> — Electrohogar</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 13px; color: #333; background: #fff; }
        .page { max-width: 750px; margin: 30px auto; padding: 30px; border: 1px solid #ddd; }

        /* Encabezado */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; }
        .logo-area h1 { font-size: 24px; color: #1a73e8; letter-spacing: 1px; }
        .logo-area p  { font-size: 11px; color: #666; margin-top: 2px; }
        .factura-info  { text-align: right; }
        .factura-info h2 { font-size: 20px; color: #333; }
        .factura-info .nro { font-size: 16px; color: #1a73e8; font-weight: bold; }
        .factura-info p { font-size: 11px; color: #666; margin-top: 3px; }

        /* Datos */
        .datos { display: flex; justify-content: space-between; margin-bottom: 25px; gap: 20px; }
        .datos-bloque { flex: 1; background: #f9f9f9; padding: 12px 15px; border-radius: 6px; }
        .datos-bloque h4 { font-size: 11px; text-transform: uppercase; color: #888; margin-bottom: 6px; letter-spacing: .5px; }
        .datos-bloque p  { font-size: 12px; line-height: 1.7; }

        /* Tabla */
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #1a73e8; color: #fff; }
        thead th { padding: 9px 12px; text-align: left; font-size: 12px; }
        thead th.right { text-align: right; }
        tbody tr:nth-child(even) { background: #f5f8ff; }
        tbody td { padding: 8px 12px; font-size: 12px; border-bottom: 1px solid #eee; }
        tbody td.right { text-align: right; }

        /* Totales */
        .totales { width: 260px; margin-left: auto; }
        .totales table { margin-bottom: 0; }
        .totales td { padding: 5px 12px; font-size: 12px; border: none; }
        .totales td.label { color: #666; }
        .totales td.valor { text-align: right; font-weight: bold; }
        .totales tr.total-final td { font-size: 15px; color: #1a73e8; border-top: 2px solid #1a73e8; padding-top: 8px; }

        /* QR factura */
        .footer-row { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 35px; border-top: 1px solid #eee; padding-top: 15px; }
        .footer-text { font-size: 11px; color: #999; }
        .footer-text p { margin-bottom: 4px; }
        .qr-factura img { width: 90px; height: 90px; border: 1px solid #ddd; border-radius: 4px; }
        .qr-factura p { font-size: 10px; color: #aaa; text-align: center; margin-top: 4px; }

        /* Botón imprimir */
        .btn-imprimir { display: block; width: 200px; margin: 0 auto 20px; padding: 10px; background: #1a73e8; color: #fff; text-align: center; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        .btn-imprimir:hover { background: #1558b0; }

        @media print {
            .btn-imprimir { display: none; }
            .page { border: none; margin: 0; padding: 20px; }
        }
    </style>
</head>
<body>

<button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir / Guardar PDF</button>

<div class="page">

    <!-- Encabezado -->
    <div class="header">
        <div class="logo-area">
            <h1>⚡ Electrohogar</h1>
            <p>Tu tienda de electrodomésticos de confianza</p>
            <p>electrohogar@gmail.com</p>
        </div>
        <div class="factura-info">
            <h2>FACTURA</h2>
            <div class="nro">#<?php echo str_pad((int)$nroVenta, 6, '0', STR_PAD_LEFT); ?></div>
            <p><strong>Fecha:</strong> <?php echo date('d/m/Y H:i', strtotime($venta['fechaHora'] ?? 'now')); ?></p>
        </div>
    </div>

    <!-- Datos cliente y empresa -->
    <div class="datos">
        <div class="datos-bloque">
            <h4>Datos del cliente</h4>
            <p><strong><?php echo htmlspecialchars($cliente['nombres'] . ' ' . $cliente['apPaterno'] . ' ' . ($cliente['apMaterno'] ?? '')); ?></strong></p>
            <p>CI: <?php echo htmlspecialchars($cliente['ci'] ?? '—'); ?></p>
            <p>Correo: <?php echo htmlspecialchars($cliente['correo'] ?? '—'); ?></p>
            <p>Dirección: <?php echo htmlspecialchars($cliente['direccion'] ?? '—'); ?></p>
            <p>Celular: <?php echo htmlspecialchars($cliente['nroCelular'] ?? '—'); ?></p>
        </div>
        <div class="datos-bloque">
            <h4>Datos de la empresa</h4>
            <p><strong>Electrohogar S.R.L.</strong></p>
            <p>NIT: 1234567890</p>
            <p>Bolivia</p>
            <p>electrohogar@gmail.com</p>
        </div>
    </div>

    <!-- Detalle de productos -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th class="right">Precio unit.</th>
                <th class="right">Cantidad</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php $subtotalGeneral = 0; ?>
            <?php foreach ($detalles as $i => $d): ?>
                <?php $sub = (float)$d['precio'] * (int)$d['cant']; $subtotalGeneral += $sub; ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo htmlspecialchars($d['producto']); ?></td>
                    <td class="right">$<?php echo number_format((float)$d['precio'], 2); ?></td>
                    <td class="right"><?php echo (int)$d['cant']; ?></td>
                    <td class="right">$<?php echo number_format($sub, 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totales">
        <table>
            <tr>
                <td class="label">Subtotal:</td>
                <td class="valor">$<?php echo number_format($subtotalGeneral, 2); ?></td>
            </tr>
            <tr>
                <td class="label">Descuento:</td>
                <td class="valor">$0.00</td>
            </tr>
            <tr class="total-final">
                <td class="label"><strong>TOTAL:</strong></td>
                <td class="valor"><strong>$<?php echo number_format($subtotalGeneral, 2); ?></strong></td>
            </tr>
        </table>
    </div>

    <!-- Pie con QR -->
    <?php
        $protocolo  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $urlFactura = $protocolo . '://' . $_SERVER['HTTP_HOST'] . '/index.php?pagina=factura&nro=' . (int)$nroVenta;
        $qrSrc      = 'https://api.qrserver.com/v1/create-qr-code/?size=90x90&data=' . urlencode($urlFactura);
    ?>
    <div class="footer-row">
        <div class="footer-text">
            <p>Gracias por su compra en <strong>Electrohogar</strong>.</p>
            <p>Este documento es válido como comprobante de pago.</p>
            <p style="margin-top:6px;">Generado el <?php echo date('d/m/Y H:i:s'); ?></p>
        </div>
        <div class="qr-factura">
            <img src="<?php echo $qrSrc; ?>" alt="QR Factura">
            <p>Verifica tu factura</p>
        </div>
    </div>

</div>
</body>
</html>
