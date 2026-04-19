<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../modelos/Producto.php';

if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$accion = $_GET['accion'] ?? $_POST['accion'] ?? 'obtener';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : (isset($_POST['id']) ? (int)$_POST['id'] : 0);

function calcularResumen() {
    $modelo = new Producto();
    $items  = [];
    $total  = 0;

    foreach ($_SESSION['carrito'] as $item) {
        $producto = $modelo->obtenerPorId($item['id_producto']);
        if ($producto) {
            $subtotal = (float)$producto['precio'] * (int)$item['cantidad'];
            $total   += $subtotal;
            $items[]  = [
                'id_producto' => $item['id_producto'],
                'nombre'      => $producto['nombre'],
                'precio'      => (float)$producto['precio'],
                'cantidad'    => (int)$item['cantidad'],
                'subtotal'    => $subtotal,
                'imagen'      => $producto['imagen'],
            ];
        }
    }

    $totalUnidades = array_sum(array_column($items, 'cantidad'));
    return ['items' => $items, 'total' => $total, 'cantidad' => $totalUnidades];
}

switch ($accion) {
    case 'agregar':
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'ID de producto invalido.']);
            exit();
        }

        $modelo   = new Producto();
        $producto = $modelo->obtenerPorId($id);

        if (!$producto || strtolower($producto['estado'] ?? '') !== 'activo') {
            echo json_encode(['ok' => false, 'mensaje' => 'Producto no disponible.']);
            exit();
        }

        $encontrado = false;
        foreach ($_SESSION['carrito'] as &$item) {
            if ($item['id_producto'] == $id) {
                $item['cantidad']++;
                $encontrado = true;
                break;
            }
        }
        unset($item);

        if (!$encontrado) {
            $_SESSION['carrito'][] = ['id_producto' => $id, 'cantidad' => 1];
        }

        $resumen = calcularResumen();
        echo json_encode([
            'ok'       => true,
            'mensaje'  => htmlspecialchars($producto['nombre']) . ' agregado al carrito.',
            'cantidad' => $resumen['cantidad'],
        ]);
        break;

    case 'eliminar':
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'ID invalido.']);
            exit();
        }

        foreach ($_SESSION['carrito'] as $i => $item) {
            if ($item['id_producto'] == $id) {
                unset($_SESSION['carrito'][$i]);
                break;
            }
        }
        $_SESSION['carrito'] = array_values($_SESSION['carrito']);

        $resumen = calcularResumen();
        echo json_encode(array_merge(['ok' => true], $resumen));
        break;

    case 'obtener':
    default:
        echo json_encode(array_merge(['ok' => true], calcularResumen()));
        break;
}
