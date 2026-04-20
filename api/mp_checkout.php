<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../modelos/Producto.php';

if (empty($_SESSION['carrito'])) {
    echo json_encode(['error' => 'Carrito vacío']);
    exit();
}

if (empty($_SESSION['usuario'])) {
    echo json_encode(['error' => 'Debes iniciar sesión', 'login' => true]);
    exit();
}

if (strpos(MP_ACCESS_TOKEN, 'REEMPLAZA') !== false) {
    echo json_encode(['error' => 'MercadoPago no está configurado. Edita config/stripe.php']);
    exit();
}

MercadoPago\SDK::setAccessToken(MP_ACCESS_TOKEN);

$modelo = new Producto();
$mpItems = [];
$total   = 0;

foreach ($_SESSION['carrito'] as $item) {
    $producto = $modelo->obtenerPorId($item['id_producto']);
    if (!$producto) continue;

    $cantidad = (int)$item['cantidad'];
    $precio   = round((float)$producto['precio'], 2);
    $total   += $precio * $cantidad;

    $mpItem              = new MercadoPago\Item();
    $mpItem->title       = $producto['nombre'];
    $mpItem->quantity    = $cantidad;
    $mpItem->unit_price  = $precio;
    $mpItem->currency_id = MP_CURRENCY;
    $mpItems[]           = $mpItem;
}

if (empty($mpItems)) {
    echo json_encode(['error' => 'No hay productos válidos en el carrito']);
    exit();
}

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base      = $protocolo . '://' . $_SERVER['HTTP_HOST'];

$preference = new MercadoPago\Preference();
$preference->items = $mpItems;

$preference->back_urls = [
    'success' => $base . '/index.php?pagina=pago_exitoso&metodo=mp',
    'failure' => $base . '/index.php?pagina=pago&mp_error=1',
    'pending' => $base . '/index.php?pagina=pago&mp_pending=1',
];
$preference->auto_return     = 'approved';
$preference->external_reference = $_SESSION['usuario'] . '_' . time();

$preference->save();

if ($preference->id) {
    // Guardamos en sesión para verificar al regresar
    $_SESSION['mp_preference_id']  = $preference->id;
    $_SESSION['mp_external_ref']   = $preference->external_reference;

    echo json_encode([
        'preference_id' => $preference->id,
        'qr_url'        => $preference->init_point,      // URL que va dentro del QR
        'init_point'    => $preference->init_point,      // Enlace directo (fallback)
    ]);
} else {
    echo json_encode(['error' => 'No se pudo crear la preferencia de pago']);
}
