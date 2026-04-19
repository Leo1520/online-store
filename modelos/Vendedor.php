<?php
require_once __DIR__ . '/../config/database.php';

class Vendedor {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodos() {
        $stmt = $this->db->prepare("CALL sp_listar_vendedores()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function obtenerPorClave($ci, $usuarioCuenta) {
        $stmt = $this->db->prepare("CALL sp_obtener_vendedor_por_clave(?, ?)");
        if (!$stmt) return null;
        $stmt->bind_param('ss', $ci, $usuarioCuenta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $dato;
    }

    public function crearConCuenta($usuario, $passwordHash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular) {
        $stmt = $this->db->prepare("CALL sp_crear_vendedor_con_cuenta(?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ssssssss', $usuario, $passwordHash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function actualizarConPassword($ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $passwordHash) {
        $stmt = $this->db->prepare("CALL sp_actualizar_vendedor_y_password(?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ssssssss', $ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $passwordHash);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function eliminarVendedorYCuenta($ci, $usuarioCuenta) {
        $stmt = $this->db->prepare("CALL sp_eliminar_vendedor_y_cuenta(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $ci, $usuarioCuenta);
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
