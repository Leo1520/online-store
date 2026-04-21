<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

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

$base = PAYPAL_MODE === 'sandbox'
    ? 'https://api-m.sandbox.paypal.com'
    : 'https://api-m.paypal.com';

// 1. Obtener access token
$ch = curl_init("$base/v1/oauth2/token");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
    CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET,
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
]);
$tokenData   = json_decode(curl_exec($ch), true);
curl_close($ch);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    echo json_encode(['error' => 'No se pudo autenticar con PayPal']);
    exit();
}

// 2. Calcular total
$modelo = new Producto();
$total  = 0;
foreach ($_SESSION['carrito'] as $item) {
    $producto = $modelo->obtenerPorId($item['id_producto']);
    if (!$producto) continue;
    $total += round((float)$producto['precio'], 2) * (int)$item['cantidad'];
}

if ($total <= 0) {
    echo json_encode(['error' => 'Total inválido']);
    exit();
}

// 3. Crear orden PayPal
$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base_url  = $protocolo . '://' . $_SERVER['HTTP_HOST'];

$body = json_encode([
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => [
            'currency_code' => 'USD',
            'value'         => number_format($total, 2, '.', ''),
        ],
    ]],
    'application_context' => [
        'return_url' => $base_url . '/index.php?pagina=pago_exitoso&metodo=paypal',
        'cancel_url' => $base_url . '/index.php?pagina=pago',
    ],
]);

$ch = curl_init("$base/v2/checkout/orders");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $body,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ],
]);
$orden = json_decode(curl_exec($ch), true);
curl_close($ch);

$orderId     = $orden['id'] ?? null;
$approveLink = null;

foreach ($orden['links'] ?? [] as $link) {
    if ($link['rel'] === 'approve') {
        $approveLink = $link['href'];
        break;
    }
}

if (!$orderId || !$approveLink) {
    echo json_encode(['error' => 'No se pudo crear la orden PayPal']);
    exit();
}

$_SESSION['paypal_order_id']  = $orderId;
$_SESSION['paypal_qr_activo'] = true;
unset($_SESSION['paypal_venta_registrada']);

echo json_encode([
    'order_id'    => $orderId,
    'approve_url' => $approveLink,
]);
