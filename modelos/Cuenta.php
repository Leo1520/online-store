<?php
require_once __DIR__ . '/../config/database.php';

class Cuenta {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodas() {
        $stmt = $this->db->prepare("CALL sp_listar_cuentas()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function obtenerPorUsuario($usuario) {
        $stmt = $this->db->prepare("CALL sp_obtener_cuenta_por_usuario(?)");
        if (!$stmt) return null;
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = ($resultado && $resultado->num_rows > 0) ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $dato;
    }

    public function verificarCredenciales($usuario, $password) {
        $cuenta = $this->obtenerPorUsuario($usuario);
        if (!$cuenta) {
            return null;
        }

        $passwordGuardado = (string)$cuenta['password'];
        $passwordValido = password_verify($password, $passwordGuardado) || hash_equals($passwordGuardado, $password);

        if (!$passwordValido) {
            return null;
        }

        // Migra passwords legacy en texto plano a hash seguro al primer login exitoso.
        if (!$this->esHashPassword($passwordGuardado)) {
            $this->actualizarPassword($usuario, $password);
        }

        return $cuenta;
    }

    public function crear($usuario, $password) {
        $passwordNormalizado = $this->normalizarPassword($password);
        $stmt = $this->db->prepare("CALL sp_crear_cuenta(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $usuario, $passwordNormalizado);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function actualizarPassword($usuario, $password) {
        $passwordNormalizado = $this->normalizarPassword($password);
        $stmt = $this->db->prepare("CALL sp_actualizar_password_cuenta(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $usuario, $passwordNormalizado);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function tieneClienteAsociado($usuario) {
        $stmt = $this->db->prepare("CALL sp_verificar_cliente_asociado(?)");
        if (!$stmt) return true;
        $stmt->bind_param('s', $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $fila = $resultado ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $fila && (int)$fila['total'] > 0;
    }

    public function eliminar($usuario) {
        $stmt = $this->db->prepare("CALL sp_eliminar_cuenta(?)");
        if (!$stmt) return false;
        $stmt->bind_param('s', $usuario);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    private function esHashPassword($password) {
        $info = password_get_info((string)$password);
        return isset($info['algo']) && $info['algo'] !== 0;
    }

    private function normalizarPassword($password) {
        $password = (string)$password;
        if ($this->esHashPassword($password)) {
            return $password;
        }
        return password_hash($password, PASSWORD_DEFAULT);
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
