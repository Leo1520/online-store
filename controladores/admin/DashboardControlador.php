<?php
require_once __DIR__ . '/../../modelos/NotaVenta.php';
require_once __DIR__ . '/../../modelos/MovimientoStock.php';

class DashboardControlador {
    public function dashboard() {
        $db = \Database::conectar();

        $stmt = $db->prepare("CALL sp_resumen_dashboard()");
        $stmt->execute();
        $res  = $stmt->get_result();
        $dash = $res ? $res->fetch_assoc() : [];
        $stmt->close();
        while ($db->more_results() && $db->next_result()) { $r = $db->use_result(); if ($r) $r->free(); }

        $stmt = $db->prepare("CALL sp_productos_mas_vendidos(5)");
        $stmt->execute();
        $res  = $stmt->get_result();
        $topProductos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        while ($db->more_results() && $db->next_result()) { $r = $db->use_result(); if ($r) $r->free(); }

        $msModel      = new MovimientoStock();
        $stockCritico = $msModel->obtenerStockCritico(5);

        $notaModel    = new NotaVenta();
        $ultimasVentas = array_slice($notaModel->obtenerTodasConResumen(), 0, 8);

        $titulo = 'Dashboard';
        require_once __DIR__ . '/../../vistas/admin_dashboard.php';
    }
}
