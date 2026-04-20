<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';
require_once __DIR__ . '/../modelos/Venta.php';

if (empty($_SESSION['mp_preference_id']) || empty($_SESSION['usuario'])) {
    echo json_encode(['status' => 'sin_sesion']);
    exit();
}

if (strpos(MP_ACCESS_TOKEN, 'REEMPLAZA') !== false) {
    echo json_encode(['status' => 'no_configurado']);
    exit();
}

MercadoPago\SDK::setAccessToken(MP_ACCESS_TOKEN);

$externalRef = $_SESSION['mp_external_ref'] ?? '';

// Buscar pagos aprobados con esta referencia externa
$pagos = MercadoPago\Payment::search([
    'external_reference' => $externalRef,
    'status'             => 'approved',
]);

if (!empty($pagos->results)) {
    // Evitar doble registro
    if (!empty($_SESSION['mp_venta_registrada'])) {
        echo json_encode([
            'status'    => 'pagado',
            'nroVenta'  => $_SESSION['ultimo_nro_venta'] ?? null,
        ]);
        exit();
    }

    $venta    = new Venta();
    $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);

    if ($nroVenta !== false) {
        $_SESSION['mp_venta_registrada'] = true;
        $_SESSION['ultimo_nro_venta']    = $nroVenta;
        $_SESSION['carrito']             = [];
        unset($_SESSION['mp_preference_id'], $_SESSION['mp_external_ref']);

        echo json_encode(['status' => 'pagado', 'nroVenta' => $nroVenta]);
    } else {
        echo json_encode(['status' => 'error_venta']);
    }
    exit();
}

// Verificar si hay pago pendiente
$pagosPendientes = MercadoPago\Payment::search([
    'external_reference' => $externalRef,
    'status'             => 'pending',
]);

if (!empty($pagosPendientes->results)) {
    echo json_encode(['status' => 'pendiente']);
    exit();
}

echo json_encode(['status' => 'esperando']);
