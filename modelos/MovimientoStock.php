<?php
require_once __DIR__ . '/../config/database.php';

class MovimientoStock {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerKardex($codProducto = 0, $codSucursal = 0, $tipo = '', $fechaDesde = null, $fechaHasta = null) {
        $stmt = $this->db->prepare("CALL sp_kardex_filtrado(?, ?, ?, ?, ?)");
        if (!$stmt) return [];
        $stmt->bind_param('iisss', $codProducto, $codSucursal, $tipo, $fechaDesde, $fechaHasta);
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiar();
        return $data;
    }

    public function obtenerStockActual() {
        $stmt = $this->db->prepare("CALL sp_stock_actual_almacen()");
        if (!$stmt) return [];
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiar();
        return $data;
    }

    public function obtenerStockCritico($umbral = 5) {
        $stmt = $this->db->prepare("CALL sp_stock_critico_almacen(?)");
        if (!$stmt) return [];
        $stmt->bind_param('i', $umbral);
        $stmt->execute();
        $res  = $stmt->get_result();
        $data = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiar();
        return $data;
    }

    public function registrarAjuste($codProducto, $codSucursal, $tipo, $cantidad, $observacion, $usuario) {
        $stmt = $this->db->prepare("CALL sp_registrar_ajuste_stock(?, ?, ?, ?, ?, ?)");
        if (!$stmt) return ['ok' => false, 'msg' => 'Error al preparar la consulta'];
        $stmt->bind_param('iisiss', $codProducto, $codSucursal, $tipo, $cantidad, $observacion, $usuario);
        $ok = $stmt->execute();
        $err = $stmt->error;
        $stmt->close();
        $this->limpiar();
        return $ok ? ['ok' => true] : ['ok' => false, 'msg' => $err ?: 'Error al registrar ajuste'];
    }

    private function limpiar() {
        while ($this->db->more_results() && $this->db->next_result()) {
            $r = $this->db->use_result();
            if ($r instanceof mysqli_result) $r->free();
        }
    }
}
