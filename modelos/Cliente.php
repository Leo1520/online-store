<?php
require_once __DIR__ . '/../config/database.php';

class Cliente {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodos() {
        $stmt = $this->db->prepare("CALL sp_listar_clientes()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function crear($ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $usuarioCuenta) {
        $stmt = $this->db->prepare("CALL sp_crear_cliente(?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ssssssss', $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $usuarioCuenta);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function crearConCuenta($usuario, $passwordHash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular) {
        $stmtSp = $this->db->prepare("CALL sp_crear_cliente_con_cuenta(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmtSp) {
            $stmtSp->bind_param('sssssssss', $usuario, $passwordHash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);
            $ok = $stmtSp->execute();
            $stmtSp->close();
            $this->limpiarResultadosPendientes();
            if ($ok) {
                return true;
            }
        }

        $this->db->begin_transaction();
        try {
            $stmtCuenta = $this->db->prepare("CALL sp_crear_cuenta(?, ?)");
            if (!$stmtCuenta) throw new Exception('No se pudo crear la cuenta.');
            $stmtCuenta->bind_param('ss', $usuario, $passwordHash);
            if (!$stmtCuenta->execute()) throw new Exception('No se pudo crear la cuenta.');
            $stmtCuenta->close();
            $this->limpiarResultadosPendientes();

            $okCliente = $this->crear($ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $usuario);
            if (!$okCliente) throw new Exception('No se pudo crear el cliente.');

            $this->db->commit();
            return true;
        } catch (Throwable $e) {
            $this->db->rollback();
            return false;
        }
    }

    public function obtenerPorClave($ci, $usuarioCuenta) {
        $stmt = $this->db->prepare("CALL sp_obtener_cliente_por_clave(?, ?)");
        if (!$stmt) return null;
        $stmt->bind_param('ss', $ci, $usuarioCuenta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $dato;
    }

    public function actualizar($ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular) {
        $stmt = $this->db->prepare("CALL sp_actualizar_cliente(?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ssssssss', $ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function actualizarConPassword($ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $passwordHash) {
        $passwordHash = (string)$passwordHash;

        $stmtSp = $this->db->prepare("CALL sp_actualizar_cliente_y_password(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmtSp) {
            $stmtSp->bind_param('sssssssss', $ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $passwordHash);
            $ok = $stmtSp->execute();
            $stmtSp->close();
            $this->limpiarResultadosPendientes();
            if ($ok) return true;
        }

        $okCliente = $this->actualizar($ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);
        if (!$okCliente) return false;

        if ($passwordHash !== '') {
            $stmt = $this->db->prepare("CALL sp_actualizar_password_cuenta(?, ?)");
            if (!$stmt) return false;
            $stmt->bind_param('ss', $usuarioCuenta, $passwordHash);
            $ok = $stmt->execute();
            $stmt->close();
            $this->limpiarResultadosPendientes();
            return $ok;
        }

        return true;
    }

    public function eliminar($ci, $usuarioCuenta) {
        $stmt = $this->db->prepare("CALL sp_eliminar_cliente(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $ci, $usuarioCuenta);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function eliminarClienteYCuentaSegura($ci, $usuarioCuenta) {
        $stmtSp = $this->db->prepare("CALL sp_eliminar_cliente_y_cuenta_segura(?, ?)");
        if ($stmtSp) {
            $stmtSp->bind_param('ss', $ci, $usuarioCuenta);
            $ok = $stmtSp->execute();
            $stmtSp->close();
            $this->limpiarResultadosPendientes();
            if ($ok) return true;
        }

        return $this->eliminar($ci, $usuarioCuenta);
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
