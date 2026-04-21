<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../modelos/Venta.php';

if (empty($_SESSION['carrito']) || empty($_SESSION['usuario'])) {
    echo json_encode(['error' => 'Sesión inválida']);
    exit();
}

$data    = json_decode(file_get_contents('php://input'), true);
$orderId = $data['orderID'] ?? '';

if (!$orderId) {
    echo json_encode(['error' => 'Order ID inválido']);
    exit();
}

$base = PAYPAL_MODE === 'sandbox'
    ? 'https://api-m.sandbox.paypal.com'
    : 'https://api-m.paypal.com';

// 1. Obtener token de acceso
$ch = curl_init("$base/v1/oauth2/token");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
    CURLOPT_USERPWD        => PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET,
    CURLOPT_HTTPHEADER     => ['Accept: application/json'],
]);
$resp       = curl_exec($ch);
$tokenData  = json_decode($resp, true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    echo json_encode(['error' => 'No se pudo autenticar con PayPal']);
    exit();
}

// 2. Capturar el pedido
$ch = curl_init("$base/v2/checkout/orders/$orderId/capture");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $accessToken,
    ],
    CURLOPT_POSTFIELDS     => '{}',
]);
$resp    = curl_exec($ch);
$capture = json_decode($resp, true);
curl_close($ch);

$status = $capture['status'] ?? '';

if ($status !== 'COMPLETED') {
    echo json_encode(['error' => 'Pago no completado. Estado: ' . $status]);
    exit();
}

// 3. Registrar venta (evitar doble registro)
if (!empty($_SESSION['paypal_venta_registrada'])) {
    echo json_encode([
        'ok'       => true,
        'nroVenta' => $_SESSION['ultimo_nro_venta'] ?? null,
    ]);
    exit();
}

$venta    = new Venta();
$nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);

if ($nroVenta !== false) {
    $_SESSION['paypal_venta_registrada'] = true;
    $_SESSION['ultimo_nro_venta']        = $nroVenta;
    $_SESSION['carrito']                 = [];
    echo json_encode(['ok' => true, 'nroVenta' => $nroVenta]);
} else {
    echo json_encode(['error' => 'Pago aprobado pero no se pudo registrar la venta.']);
}
