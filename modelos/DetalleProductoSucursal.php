<?php
require_once __DIR__ . '/../config/database.php';

class DetalleProductoSucursal {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodos() {
        $stmt = $this->db->prepare("CALL sp_listar_stock_sucursales()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function guardarStock($codProducto, $codSucursal, $stock) {
        $codProducto = (int)$codProducto;
        $codSucursal = (int)$codSucursal;
        $stock       = (int)$stock;

        $stmt = $this->db->prepare("CALL sp_guardar_stock_sucursal(?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('iii', $codProducto, $codSucursal, $stock);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function eliminar($codProducto, $codSucursal) {
        $codProducto = (int)$codProducto;
        $codSucursal = (int)$codSucursal;

        $stmt = $this->db->prepare("CALL sp_eliminar_stock_sucursal(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ii', $codProducto, $codSucursal);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    private function limpiarResultadosPendientes() {
        while ($this->db->more_results() && $this->db->next_result()) {
            $resultado = $this->db->use_result();
            if ($resultado instanceof mysqli_result) {
                $resultado->free();
            }
        }
    }
}
