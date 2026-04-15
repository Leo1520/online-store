<?php require_once __DIR__ . '/layout/encabezado.php'; ?>
<div class="container mt-5">
    <h1 class="text-center">Carrito de Compras</h1>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['producto']['nombre']); ?></td>
                            <td><?php echo $item['cantidad']; ?></td>
                            <td>$<?php echo number_format($item['producto']['precio'], 2); ?></td>
                            <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                            <td>
                                <a href="index.php?pagina=carrito&accion=eliminar&id=<?php echo $item['producto']['id_producto']; ?>"
                                   class="btn btn-danger btn-sm">Eliminar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="text-center">El carrito está vacío.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <h3 class="text-right">Total: $<?php echo number_format($total, 2); ?></h3>
    <div class="text-right">
        <a href="index.php?pagina=pago" class="btn btn-success">Proceder al Pago</a>
    </div>
</div>
<?php require_once __DIR__ . '/layout/pie.php'; ?>
