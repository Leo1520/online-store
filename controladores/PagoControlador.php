<?php
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Venta.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/stripe.php';

class PagoControlador {

    public function index() {
        if (empty($_SESSION['carrito'])) {
            header('Location: index.php?pagina=carrito');
            exit();
        }

        // POST = pago simulado (tarjeta demo o QR demo)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_SESSION['usuario'])) {
                header('Location: index.php?pagina=login');
                exit();
            }
            $metodo = trim($_GET['metodo'] ?? 'tarjeta');
            $venta    = new Venta();
            $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);
            if ($nroVenta !== false) {
                $_SESSION['carrito'] = [];
                header('Location: index.php?pagina=pago_exitoso&nro=' . (int)$nroVenta . '&metodo=' . $metodo);
            } else {
                header('Location: index.php?pagina=pago_exitoso&error=1');
            }
            exit();
        }

        $modelo = new Producto();
        $items  = [];
        $total  = 0;

        foreach ($_SESSION['carrito'] as $itemCarrito) {
            $producto = $modelo->obtenerPorId($itemCarrito['id_producto']);
            if (!$producto) continue;

            $cantidad = (int)$itemCarrito['cantidad'];
            $subtotal = (float)$producto['precio'] * $cantidad;
            $total   += $subtotal;

            $items[] = [
                'producto' => $producto,
                'cantidad' => $cantidad,
                'subtotal' => $subtotal,
            ];
        }

        $titulo = 'Proceso de Pago';
        require_once __DIR__ . '/../vistas/pago.php';
    }

    public function exitoso() {
        $sessionId = trim($_GET['session_id'] ?? '');
        $metodo    = trim($_GET['metodo']     ?? '');
        $nroVenta  = null;
        $error     = null;

        if (empty($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit();
        }

        // ── Retorno desde PayPal (navegador) ──
        if ($metodo === 'paypal') {
            $orderId  = trim($_GET['token'] ?? '');
            $payerId  = trim($_GET['PayerID'] ?? '');

            if (!$orderId || !$payerId) {
                $error = 'Pago cancelado o no completado.';
            } elseif (!empty($_SESSION['paypal_venta_registrada'])) {
                $nroVenta = $_SESSION['ultimo_nro_venta'] ?? null;
            } else {
                $ppBase = PAYPAL_MODE === 'sandbox'
                    ? 'https://api-m.sandbox.paypal.com'
                    : 'https://api-m.paypal.com';

                // Token
                $ch = curl_init("$ppBase/v1/oauth2/token");
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
                    $error = 'No se pudo autenticar con PayPal.';
                } else {
                    // Capturar orden
                    $ch = curl_init("$ppBase/v2/checkout/orders/$orderId/capture");
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

                    if (($capture['status'] ?? '') === 'COMPLETED') {
                        $venta    = new Venta();
                        $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);
                        if ($nroVenta !== false) {
                            $_SESSION['paypal_venta_registrada'] = true;
                            $_SESSION['ultimo_nro_venta']        = $nroVenta;
                            $_SESSION['carrito']                 = [];
                            unset($_SESSION['paypal_order_id']);
                        } else {
                            $error = 'Pago aprobado pero no se pudo registrar la venta.';
                        }
                    } else {
                        $error = 'El pago no fue completado por PayPal.';
                    }
                }
            }

        // ── Retorno desde MercadoPago ──
        } elseif ($metodo === 'mp') {
            $paymentId = (int)($_GET['payment_id'] ?? 0);
            $status    = trim($_GET['status'] ?? '');

            if ($status === 'approved' && $paymentId > 0) {
                if (!empty($_SESSION['mp_venta_registrada'])) {
                    $nroVenta = $_SESSION['ultimo_nro_venta'] ?? null;
                } elseif (!empty($_SESSION['carrito'])) {
                    require_once __DIR__ . '/../vendor/autoload.php';
                    require_once __DIR__ . '/../config/stripe.php';
                    MercadoPago\SDK::setAccessToken(MP_ACCESS_TOKEN);

                    $pago = MercadoPago\Payment::find_by_id($paymentId);
                    if ($pago && $pago->status === 'approved') {
                        $venta    = new Venta();
                        $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);
                        if ($nroVenta !== false) {
                            $_SESSION['mp_venta_registrada'] = true;
                            $_SESSION['ultimo_nro_venta']    = $nroVenta;
                            $_SESSION['carrito']             = [];
                        } else {
                            $error = 'El pago fue aprobado pero no se pudo registrar la venta. Contacta al soporte.';
                        }
                    } else {
                        $error = 'No se pudo verificar el pago con MercadoPago.';
                    }
                } else {
                    $nroVenta = $_SESSION['ultimo_nro_venta'] ?? null;
                }
            } elseif ($status === 'pending') {
                $error = 'Tu pago está pendiente de acreditación. Te notificaremos cuando sea confirmado.';
            } else {
                $error = 'El pago no fue completado o fue rechazado.';
            }

        // ── Retorno desde Stripe ──
        } elseif ($sessionId !== '') {
            if (isset($_SESSION['stripe_session_registrada']) &&
                $_SESSION['stripe_session_registrada'] === $sessionId) {
                $nroVenta = $_SESSION['ultimo_nro_venta'] ?? null;
            } else {
                try {
                    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
                    $caBundle   = __DIR__ . '/../vendor/stripe/stripe-php/data/ca-certificates.crt';
                    $curlClient = new \Stripe\HttpClient\CurlClient([CURLOPT_CAINFO => $caBundle]);
                    \Stripe\ApiRequestor::setHttpClient($curlClient);
                    $session = \Stripe\Checkout\Session::retrieve($sessionId);

                    if ($session->payment_status === 'paid') {
                        if (!empty($_SESSION['carrito'])) {
                            $venta    = new Venta();
                            $nroVenta = $venta->registrarVenta($_SESSION['carrito'], $_SESSION['usuario']);
                            if ($nroVenta !== false) {
                                $_SESSION['stripe_session_registrada'] = $sessionId;
                                $_SESSION['ultimo_nro_venta']          = $nroVenta;
                                $_SESSION['carrito']                   = [];
                            } else {
                                $error = 'El pago fue exitoso pero no se pudo registrar la venta.';
                            }
                        } else {
                            $nroVenta = $_SESSION['ultimo_nro_venta'] ?? null;
                        }
                    } else {
                        $error = 'El pago no fue completado. Estado: ' . $session->payment_status;
                    }
                } catch (\Stripe\Exception\ApiErrorException $e) {
                    $error = 'Error al verificar el pago: ' . $e->getMessage();
                }
            }

        // ── Flujo simulado ──
        } else {
            if (!empty($_GET['error'])) {
                $error = 'No se pudo registrar la venta. Intenta nuevamente.';
            } else {
                $nroVenta = isset($_GET['nro']) ? (int)$_GET['nro'] : null;
            }
        }

        $titulo = 'Compra Exitosa';
        require_once __DIR__ . '/../vistas/pago_exitoso.php';
    }
}
