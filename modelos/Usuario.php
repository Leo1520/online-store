<?php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function verificarCredenciales($usuario, $password) {
        $stmt = $this->db->prepare("CALL sp_verificar_credenciales_usuario(?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('ss', $usuario, $password);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $encontrado = $resultado && $resultado->num_rows === 1;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $encontrado;
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
