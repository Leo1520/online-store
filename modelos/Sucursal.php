<?php
require_once __DIR__ . '/../config/database.php';

class Sucursal {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodas() {
        $stmt = $this->db->prepare("CALL sp_listar_sucursales()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function obtenerPorCod($cod) {
        $stmt = $this->db->prepare("CALL sp_obtener_sucursal_por_cod(?)");
        if (!$stmt) return null;
        $stmt->bind_param('i', $cod);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $dato;
    }

    public function crear($nombre, $direccion, $nroTelefono) {
        $stmt = $this->db->prepare("CALL sp_crear_sucursal(?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('sss', $nombre, $direccion, $nroTelefono);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function actualizar($cod, $nombre, $direccion, $nroTelefono) {
        $stmt = $this->db->prepare("CALL sp_actualizar_sucursal(?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('isss', $cod, $nombre, $direccion, $nroTelefono);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function eliminar($cod) {
        $stmt = $this->db->prepare("CALL sp_eliminar_sucursal(?)");
        if (!$stmt) return false;
        $stmt->bind_param('i', $cod);
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
