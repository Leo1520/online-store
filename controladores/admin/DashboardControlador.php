<?php
require_once __DIR__ . '/../../modelos/NotaVenta.php';
require_once __DIR__ . '/../../modelos/MovimientoStock.php';

class DashboardControlador {

    public function inicio() {
        $db       = \Database::conectar();
        $usuario  = $_SESSION['usuario'] ?? '';

        // Nombre completo — busca primero en Vendedor, luego en Cliente
        $nombreCompleto = '';
        $rolLabel       = 'Administrador';

        $stmt = $db->prepare("SELECT nombres, apPaterno, apMaterno FROM Vendedor WHERE usuarioCuenta = ?");
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row) {
            $nombreCompleto = trim($row['nombres'] . ' ' . $row['apPaterno'] . ' ' . $row['apMaterno']);
            $rolLabel = 'Vendedor / Administrador';
        } else {
            // Buscar en Cliente
            $stmt = $db->prepare("SELECT nombres, apPaterno, apMaterno FROM Cliente WHERE usuarioCuenta = ?");
            $stmt->bind_param('s', $usuario);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $stmt->close();
            if ($row) {
                $nombreCompleto = trim($row['nombres'] . ' ' . $row['apPaterno'] . ' ' . $row['apMaterno']);
                $rolLabel = 'Administrador';
            }
        }

        // Métricas básicas para el resumen de hoy
        $dash = [];
        try {
            $stmt = $db->prepare("CALL sp_resumen_dashboard()");
            $stmt->execute();
            $res  = $stmt->get_result();
            $dash = $res ? ($res->fetch_assoc() ?? []) : [];
            $stmt->close();
            while ($db->more_results() && $db->next_result()) { $r = $db->use_result(); if ($r) $r->free(); }
        } catch (\Exception $e) { $dash = []; }

        // Stock crítico (número para badge)
        $msModel       = new MovimientoStock();
        $stockCritico  = $msModel->obtenerStockCritico(5);
        $totalCriticos = count($stockCritico);

        $titulo = 'Inicio';
        require_once __DIR__ . '/../../vistas/admin_inicio.php';
    }

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

        $msModel       = new MovimientoStock();
        $stockCritico  = $msModel->obtenerStockCritico(5);
        $notaModel     = new NotaVenta();
        $ultimasVentas = array_slice($notaModel->obtenerTodasConResumen(), 0, 8);

        $titulo = 'Dashboard';
        require_once __DIR__ . '/../../vistas/admin_dashboard.php';
    }
}
