<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Cuenta (Usuario)
 */
class Cuenta extends Modelo {
    protected $tabla = 'Cuenta';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene una cuenta por usuario
     */
    public function obtenerPorUsuario($usuario) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Crea una nueva cuenta
     */
    public function crear($usuario, $password) {
        $query = "INSERT INTO " . $this->tabla . " (usuario, password) VALUES (?, ?)";
        $passwordHasheada = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $usuario, $passwordHasheada);
        return $stmt->execute();
    }

    /**
     * Actualiza la contraseña
     */
    public function actualizarPassword($usuario, $password) {
        $query = "UPDATE " . $this->tabla . " SET password = ? WHERE usuario = ?";
        $passwordHasheada = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $passwordHasheada, $usuario);
        return $stmt->execute();
    }

    /**
     * Elimina una cuenta
     */
    public function eliminar($usuario) {
        $query = "DELETE FROM " . $this->tabla . " WHERE usuario = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $usuario);
        return $stmt->execute();
    }
}
?>
