<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../modelos/Venta.php';

if (empty($_SESSION['paypal_order_id']) || empty($_SESSION['usuario'])) {
    echo json_encode(['status' => 'sin_sesion']);
    exit();
}

// Evitar doble registro
if (!empty($_SESSION['paypal_venta_registrada'])) {
    echo json_encode([
        'status'   => 'pagado',
        'nroVenta' => $_SESSION['ultimo_nro_venta'] ?? null,
    ]);
    exit();
}

$orderId = $_SESSION['paypal_order_id'];
$base    = PAYPAL_MODE === 'sandbox'
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
    echo json_encode(['status' => 'error_auth']);
    exit();
}

// 2. Consultar estado de la orden
$ch = curl_init("$base/v2/checkout/orders/$orderId");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => [
        'Authorization: Bearer ' . $accessToken,
    ],
]);
$orden  = json_decode(curl_exec($ch), true);
curl_close($ch);

$status = $orden['status'] ?? '';

if ($status === 'APPROVED' || $status === 'COMPLETED') {
    // Si aún no fue capturado, capturarlo ahora
    if ($status === 'APPROVED') {
        $ch = curl_init("$base/v2/checkout/orders/$orderId/capture");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => '{}',
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ],
        ]);
        $capture = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (($capture['status'] ?? '') !== 'COMPLETED') {
            echo json_encode(['status' => 'esperando']);
            exit();
        }
    }

    // Registrar venta
    $venta    = new Venta();
    $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);

    if ($nroVenta !== false) {
        $_SESSION['paypal_venta_registrada'] = true;
        $_SESSION['ultimo_nro_venta']        = $nroVenta;
        $_SESSION['carrito']                 = [];
        unset($_SESSION['paypal_order_id'], $_SESSION['paypal_qr_activo']);

        echo json_encode(['status' => 'pagado', 'nroVenta' => $nroVenta]);
    } else {
        echo json_encode(['status' => 'error_venta']);
    }
    exit();
}

echo json_encode(['status' => 'esperando']);
