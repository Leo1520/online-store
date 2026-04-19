<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5">
    <h1 class="text-center">Proceso de Pago</h1>
    <?php if (!empty($errorPago)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($errorPago); ?></div>
    <?php endif; ?>

    <?php if (!empty($items)): ?>
        <div class="table-responsive mb-3">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['producto']['nombre']); ?></td>
                            <td><?php echo (int)$item['cantidad']; ?></td>
                            <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h4 class="text-right">Total a pagar: $<?php echo number_format($total, 2); ?></h4>
    <?php endif; ?>

    <?php
    $sinStock = array_filter($items, function($i) {
        return (int)($i['producto']['stock'] ?? 0) < (int)$i['cantidad'];
    });
    ?>

    <?php if (!empty($sinStock)): ?>
        <div class="alert alert-warning">
            <strong>Atencion:</strong> Los siguientes productos no tienen stock suficiente:
            <ul class="mb-0 mt-1">
                <?php foreach ($sinStock as $i): ?>
                    <li>
                        <?php echo htmlspecialchars($i['producto']['nombre']); ?>
                        — pedido: <?php echo (int)$i['cantidad']; ?>,
                        disponible: <?php echo (int)($i['producto']['stock'] ?? 0); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?pagina=pago">
        <div class="alert alert-info text-center">
            <p>Este es un proceso de pago simulado. Haz clic en "Completar Compra" para finalizar tu compra.</p>
        </div>
        <div class="text-right">
            <button type="submit" class="btn btn-success" <?php echo !empty($sinStock) ? 'disabled' : ''; ?>>
                Completar Compra
            </button>
            <?php if (!empty($sinStock)): ?>
                <a href="index.php?pagina=carrito" class="btn btn-warning ml-2">Editar carrito</a>
            <?php endif; ?>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
