<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5">
    <h1 class="text-center">Proceso de Pago</h1>
    <form method="POST" action="index.php?pagina=pago">
        <div class="alert alert-info text-center">
            <p>Este es un proceso de pago simulado. Haz clic en "Completar Compra" para finalizar tu compra.</p>
        </div>
        <div class="text-right">
            <button type="submit" class="btn btn-success">Completar Compra</button>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
