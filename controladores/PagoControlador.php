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

        $stripeConfigurado = (
            defined('STRIPE_PUBLISHABLE_KEY') &&
            strpos(STRIPE_PUBLISHABLE_KEY, 'REEMPLAZA') === false
        );

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

        // ── Retorno desde MercadoPago ──
        if ($metodo === 'mp') {
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
            $nroVenta = isset($_GET['nro']) ? (int)$_GET['nro'] : null;
        }

        $titulo = 'Compra Exitosa';
        require_once __DIR__ . '/../vistas/pago_exitoso.php';
    }
}
