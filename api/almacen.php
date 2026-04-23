<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
    echo json_encode(['ok' => false, 'msg' => 'No autorizado']);
    exit();
}

require_once __DIR__ . '/../modelos/MovimientoStock.php';
require_once __DIR__ . '/../modelos/Traspaso.php';
require_once __DIR__ . '/../modelos/Sucursal.php';
require_once __DIR__ . '/../modelos/Producto.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$usuario = $_SESSION['usuario'];

switch ($action) {

    case 'stock_actual':
        $m = new MovimientoStock();
        echo json_encode(['ok' => true, 'data' => $m->obtenerStockActual()]);
        break;

    case 'stock_critico':
        $umbral = max(1, (int)($_GET['umbral'] ?? 5));
        $m = new MovimientoStock();
        echo json_encode(['ok' => true, 'data' => $m->obtenerStockCritico($umbral)]);
        break;

    case 'kardex':
        $codProducto = (int)($_GET['codProducto'] ?? 0);
        $codSucursal = (int)($_GET['codSucursal'] ?? 0);
        $tipo        = trim($_GET['tipo'] ?? '');
        $desde       = trim($_GET['desde'] ?? '') ?: null;
        $hasta       = trim($_GET['hasta'] ?? '') ?: null;
        $m = new MovimientoStock();
        echo json_encode(['ok' => true, 'data' => $m->obtenerKardex($codProducto, $codSucursal, $tipo, $desde, $hasta)]);
        break;

    case 'ajuste':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok' => false, 'msg' => 'Método no permitido']); break; }
        $codProducto = (int)($_POST['codProducto'] ?? 0);
        $codSucursal = (int)($_POST['codSucursal'] ?? 0);
        $tipo        = trim($_POST['tipo'] ?? '');
        $cantidad    = (int)($_POST['cantidad'] ?? 0);
        $obs         = trim($_POST['observacion'] ?? '');
        $tiposValidos = ['ajuste_entrada', 'ajuste_salida', 'devolucion', 'baja'];
        if ($codProducto <= 0 || $codSucursal <= 0 || !in_array($tipo, $tiposValidos) || $cantidad <= 0) {
            echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o inválidos']); break;
        }
        $m = new MovimientoStock();
        echo json_encode($m->registrarAjuste($codProducto, $codSucursal, $tipo, $cantidad, $obs, $usuario));
        break;

    case 'traspasos':
        $t = new Traspaso();
        echo json_encode(['ok' => true, 'data' => $t->listarTodos()]);
        break;

    case 'detalle_traspaso':
        $nro = (int)($_GET['nro'] ?? 0);
        $t   = new Traspaso();
        echo json_encode(['ok' => true, 'data' => $t->obtenerDetalle($nro)]);
        break;

    case 'crear_traspaso':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok' => false, 'msg' => 'Método no permitido']); break; }
        $origen      = (int)($_POST['codSucursalOrigen']  ?? 0);
        $destino     = (int)($_POST['codSucursalDestino'] ?? 0);
        $obs         = trim($_POST['observacion'] ?? '');
        $productosJson = trim($_POST['productos'] ?? '[]');
        $productos   = json_decode($productosJson, true) ?: [];

        if ($origen <= 0 || $destino <= 0 || empty($productos)) {
            echo json_encode(['ok' => false, 'msg' => 'Datos de traspaso incompletos']); break;
        }

        $t = new Traspaso();
        $res = $t->crear($origen, $destino, $obs, $usuario);
        if (!$res['ok']) { echo json_encode($res); break; }

        $nroTrp = $res['nro'];
        foreach ($productos as $prod) {
            $cod  = (int)($prod['codProducto'] ?? 0);
            $cant = (int)($prod['cantidad'] ?? 0);
            if ($cod > 0 && $cant > 0) {
                $t->agregarDetalle($nroTrp, $cod, $cant);
            }
        }
        echo json_encode(['ok' => true, 'nro' => $nroTrp]);
        break;

    case 'completar_traspaso':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok' => false, 'msg' => 'Método no permitido']); break; }
        $nro = (int)($_POST['nro'] ?? 0);
        if ($nro <= 0) { echo json_encode(['ok' => false, 'msg' => 'Nro inválido']); break; }
        $t = new Traspaso();
        echo json_encode($t->completar($nro, $usuario));
        break;

    case 'cancelar_traspaso':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo json_encode(['ok' => false, 'msg' => 'Método no permitido']); break; }
        $nro = (int)($_POST['nro'] ?? 0);
        if ($nro <= 0) { echo json_encode(['ok' => false, 'msg' => 'Nro inválido']); break; }
        $t = new Traspaso();
        echo json_encode($t->cancelar($nro));
        break;

    case 'sucursales':
        $s = new Sucursal();
        echo json_encode(['ok' => true, 'data' => $s->obtenerTodas()]);
        break;

    case 'productos':
        $p = new Producto();
        echo json_encode(['ok' => true, 'data' => $p->obtenerTodos()]);
        break;

    default:
        echo json_encode(['ok' => false, 'msg' => 'Acción no reconocida']);
}
