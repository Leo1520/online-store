<?php require_once __DIR__ . '/layout/encabezado.php'; ?>

<style>
    .exitoso-bg {
        min-height: 80vh;
        background: var(--gris-bg);
        display: flex; align-items: center; justify-content: center;
        padding: 40px 16px;
    }

    /* Animación check */
    @keyframes popIn {
        0%   { transform: scale(0);   opacity: 0; }
        70%  { transform: scale(1.15); opacity: 1; }
        100% { transform: scale(1);   opacity: 1; }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(18px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .ico-anim { animation: popIn .5s ease forwards; }
    .fade-up  { animation: fadeUp .5s ease .3s both; }
    .fade-up2 { animation: fadeUp .5s ease .5s both; }
    .fade-up3 { animation: fadeUp .5s ease .7s both; }

    .exitoso-card {
        background: #fff;
        border-radius: 20px;
        padding: 48px 40px 36px;
        box-shadow: 0 8px 40px rgba(27,58,107,.13);
        max-width: 500px; width: 100%;
        text-align: center;
    }

    .exitoso-card .nro-pedido {
        background: #f0f4ff;
        border: 2px dashed var(--azul);
        border-radius: 12px;
        padding: 14px 24px;
        display: inline-block;
        margin: 20px 0;
    }
    .exitoso-card .nro-pedido .etiqueta { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 1px; }
    .exitoso-card .nro-pedido .numero  { font-size: 28px; font-weight: 900; color: var(--azul); line-height: 1.1; }

    .exitoso-acciones { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; margin-top: 24px; }
    .exitoso-acciones .btn { border-radius: 10px; font-weight: 600; font-size: 14px; padding: 10px 20px; }

    /* Error */
    .error-card {
        background: #fff;
        border-radius: 20px;
        padding: 48px 40px 36px;
        box-shadow: 0 8px 40px rgba(220,53,69,.12);
        max-width: 460px; width: 100%;
        text-align: center;
    }
</style>

<div class="exitoso-bg">

<?php if ($error): ?>

    <div class="error-card">
        <div class="ico-anim">
            <i class="bi bi-x-circle-fill" style="font-size:72px;color:#dc3545;"></i>
        </div>
        <h3 class="fade-up mt-3 font-weight-bold" style="color:#dc3545;">Error en el pago</h3>
        <p class="fade-up2 text-muted" style="font-size:14px;"><?php echo htmlspecialchars($error); ?></p>
        <div class="fade-up3 mt-4">
            <a href="index.php?pagina=pago" class="btn btn-amarillo px-4">
                <i class="bi bi-arrow-repeat mr-1"></i>Reintentar
            </a>
            <a href="index.php?pagina=inicio" class="btn btn-outline-secondary ml-2 px-4">
                <i class="bi bi-house mr-1"></i>Inicio
            </a>
        </div>
    </div>

<?php else: ?>

    <div class="exitoso-card">

        <!-- Ícono animado -->
        <div class="ico-anim">
            <div style="width:90px;height:90px;border-radius:50%;background:linear-gradient(135deg,#1B3A6B,#28a745);
                        display:flex;align-items:center;justify-content:center;margin:0 auto;
                        box-shadow:0 6px 24px rgba(40,167,69,.3);">
                <i class="bi bi-check-lg" style="font-size:46px;color:#fff;"></i>
            </div>
        </div>

        <h2 class="fade-up mt-4 font-weight-bold" style="color:var(--azul);">¡Pago confirmado!</h2>
        <p class="fade-up text-muted" style="font-size:14px;margin-top:4px;">
            Tu pedido fue registrado correctamente. ¡Gracias por comprar en Electrohogar!
        </p>

        <?php if ($nroVenta): ?>
        <div class="fade-up2 nro-pedido">
            <div class="etiqueta">Número de pedido</div>
            <div class="numero">#<?php echo str_pad((int)$nroVenta, 6, '0', STR_PAD_LEFT); ?></div>
        </div>
        <?php endif; ?>

        <!-- Pasos post-compra -->
        <div class="fade-up2" style="background:#f8f9ff;border-radius:12px;padding:16px 20px;text-align:left;margin-top:4px;">
            <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:10px;">
                <i class="bi bi-envelope-check-fill" style="color:var(--amarillo);font-size:18px;flex-shrink:0;margin-top:2px;"></i>
                <span style="font-size:13px;color:#555;">Guarda tu número de pedido para hacer seguimiento.</span>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;margin-bottom:10px;">
                <i class="bi bi-file-earmark-text-fill" style="color:var(--azul);font-size:18px;flex-shrink:0;margin-top:2px;"></i>
                <span style="font-size:13px;color:#555;">Descarga tu factura para tener el comprobante de compra.</span>
            </div>
            <div style="display:flex;gap:12px;align-items:flex-start;">
                <i class="bi bi-person-check-fill" style="color:#28a745;font-size:18px;flex-shrink:0;margin-top:2px;"></i>
                <span style="font-size:13px;color:#555;">Revisa el historial completo en <strong>Mi Cuenta</strong>.</span>
            </div>
        </div>

        <!-- Botones -->
        <div class="fade-up3 exitoso-acciones">
            <a href="index.php?pagina=inicio" class="btn btn-azul">
                <i class="bi bi-shop mr-1"></i>Seguir comprando
            </a>
            <?php if ($nroVenta): ?>
            <a href="index.php?pagina=factura&nro=<?php echo (int)$nroVenta; ?>" target="_blank" class="btn btn-amarillo">
                <i class="bi bi-file-earmark-pdf mr-1"></i>Ver factura
            </a>
            <?php endif; ?>
            <a href="index.php?pagina=mi_cuenta" class="btn btn-outline-secondary">
                <i class="bi bi-person mr-1"></i>Mi cuenta
            </a>
        </div>

    </div>

<?php endif; ?>

</div>

<?php require_once __DIR__ . '/layout/pie.php'; ?>
