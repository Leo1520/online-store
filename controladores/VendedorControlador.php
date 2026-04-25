<?php
require_once __DIR__ . '/../config/database.php';

class VendedorControlador {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function panel() {
        if (!isset($_SESSION['usuario'])) {
            header('Location: index.php?pagina=login');
            exit();
        }

        $rol = $_SESSION['rol'] ?? 'cliente';
        if ($rol !== 'vendedor' && $rol !== 'admin') {
            header('Location: index.php?pagina=inicio');
            exit();
        }

        $resumen       = $this->obtenerResumen();
        $topProductos  = $this->obtenerTopProductos(5);
        $ultimasVentas = $this->obtenerUltimasVentas(10);

        $titulo = 'Panel Vendedor';
        require_once __DIR__ . '/../vistas/vendedor_panel.php';
    }

    private function obtenerResumen() {
        $stmt = $this->db->prepare("CALL sp_resumen_dashboard()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = $resultado ? $resultado->fetch_assoc() : [];
        $stmt->close();
        $this->limpiar();
        return $dato ?: [];
    }

    private function obtenerTopProductos($limite) {
        $stmt = $this->db->prepare("CALL sp_productos_mas_vendidos(?)");
        if (!$stmt) return [];
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiar();
        return $datos;
    }

    private function obtenerUltimasVentas($limite) {
        $sql = "SELECT nv.`nro`, nv.`fechaHora`, nv.`ciCliente`, nv.`estado`,
                       CONCAT(cl.`nombres`, ' ', cl.`apPaterno`) AS cliente,
                       COALESCE(SUM(dnv.`cant` * p.`precioVigente`), 0)  AS totalMonto,
                       COALESCE(SUM(dnv.`cant`), 0)               AS totalItems
                FROM `NotaVenta` nv
                INNER JOIN `Cliente` cl ON cl.`ci` = nv.`ciCliente`
                LEFT  JOIN `DetalleNotaVenta` dnv ON dnv.`nroNotaVenta` = nv.`nro`
                LEFT  JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
                GROUP BY nv.`nro`, nv.`fechaHora`, nv.`ciCliente`, nv.`estado`, cl.`nombres`, cl.`apPaterno`
                ORDER BY nv.`nro` DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param('i', $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        return $datos;
    }

    private function limpiar() {
        while ($this->db->more_results() && $this->db->next_result()) {
            $r = $this->db->use_result();
            if ($r instanceof mysqli_result) $r->free();
        }
    }
}
