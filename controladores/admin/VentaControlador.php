<?php
require_once __DIR__ . '/../../modelos/NotaVenta.php';

class VentaControlador {

    public function pedidos() {
        requierePermiso('ver_pedidos');
        $notaModel   = new NotaVenta();
        $mensaje     = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $activos     = ['pendiente', 'procesando', 'enviado'];
        $todas       = $notaModel->obtenerTodasConResumen();
        $ventas      = array_values(array_filter($todas, fn($v) => in_array($v['estado'] ?? 'pendiente', $activos)));
        $titulo      = 'Pedidos Activos';
        require_once __DIR__ . '/../../vistas/admin_pedidos.php';
    }

    public function ventas() {
        requierePermiso('gestionar_ventas');
        $notaModel   = new NotaVenta();
        $mensaje     = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $completados = ['entregado', 'facturado', 'cancelado'];
        $todas       = $notaModel->obtenerTodasConResumen();
        $ventas      = array_values(array_filter($todas, fn($v) => in_array($v['estado'] ?? 'pendiente', $completados)));
        $titulo      = 'Historial de Ventas';
        require_once __DIR__ . '/../../vistas/admin_ventas.php';
    }

    public function ventasDetalle() {
        requierePermiso('gestionar_ventas');
        $notaModel = new NotaVenta();
        $id        = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /admin/index.php?page=ventas'); exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['accion'] ?? '') === 'cambiar_estado') {
            $nro        = (int)($_POST['nro'] ?? 0);
            $estado     = trim($_POST['estado'] ?? '');
            $permitidos = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado', 'facturado'];
            if ($nro > 0 && in_array($estado, $permitidos)) {
                $notaModel->actualizarEstado($nro, $estado);
            }
            $from = trim($_POST['from'] ?? 'ventas');
            header('Location: /admin/index.php?page=ventas_detalle&id=' . $id . '&from=' . urlencode($from) . '&msg=' . urlencode('Estado actualizado.'));
            exit();
        }

        $ventas = $notaModel->obtenerTodasConResumen();
        $venta  = null;
        foreach ($ventas as $v) {
            if ((int)$v['nro'] === $id) { $venta = $v; break; }
        }

        if (!$venta) {
            header('Location: /admin/index.php?page=ventas&msg=' . urlencode('Pedido no encontrado.'));
            exit();
        }

        $detalles = $notaModel->obtenerDetallesPorNota($id);

        $db   = \Database::conectar();
        $ci   = $venta['ciCliente'];
        $stmt = $db->prepare("SELECT correo, direccion, nroCelular FROM Cliente WHERE ci = ?");
        $stmt->bind_param('s', $ci);
        $stmt->execute();
        $res          = $stmt->get_result();
        $clienteExtra = $res ? ($res->fetch_assoc() ?? []) : [];
        $stmt->close();

        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $titulo  = 'Pedido #' . $id;
        require_once __DIR__ . '/../../vistas/admin_ventas_detalle.php';
    }
}
