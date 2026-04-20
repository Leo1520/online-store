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

\Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

$modelo    = new Producto();
$lineItems = [];

foreach ($_SESSION['carrito'] as $item) {
    $producto = $modelo->obtenerPorId($item['id_producto']);
    if (!$producto) continue;

    $lineItems[] = [
        'price_data' => [
            'currency'     => STRIPE_CURRENCY,
            'unit_amount'  => (int)round((float)$producto['precio'] * 100),
            'product_data' => [
                'name' => $producto['nombre'],
            ],
        ],
        'quantity' => (int)$item['cantidad'],
    ];
}

if (empty($lineItems)) {
    echo json_encode(['error' => 'No hay productos válidos en el carrito']);
    exit();
}

$protocolo = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$base      = $protocolo . '://' . $_SERVER['HTTP_HOST'];

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items'           => $lineItems,
        'mode'                 => 'payment',
        'success_url'          => $base . '/index.php?pagina=pago_exitoso&session_id={CHECKOUT_SESSION_ID}',
        'cancel_url'           => $base . '/index.php?pagina=pago',
        'metadata'             => [
            'usuario' => $_SESSION['usuario'],
        ],
    ]);

    echo json_encode(['url' => $session->url]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
