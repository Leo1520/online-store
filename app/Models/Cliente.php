<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Cliente
 */
class Cliente extends Modelo {
    protected $tabla = 'Cliente';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene un cliente por CI
     */
    public function obtenerPorCi($ci) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE ci = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $ci);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene un cliente por usuario
     */
    public function obtenerPorUsuario($usuarioCuenta) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE usuarioCuenta = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $usuarioCuenta);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Crea un nuevo cliente
     */
    public function crear($ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $usuarioCuenta) {
        $query = "INSERT INTO " . $this->tabla . " (ci, nombres, apPaterno, apMaterno, correo, direccion, nroCelular, usuarioCuenta) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ssssssss", $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $usuarioCuenta);
        return $stmt->execute();
    }

    /**
     * Actualiza un cliente
     */
    public function actualizar($ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular) {
        $query = "UPDATE " . $this->tabla . " SET nombres = ?, apPaterno = ?, apMaterno = ?, correo = ?, direccion = ?, nroCelular = ? WHERE ci = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssssss", $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $ci);
        return $stmt->execute();
    }

    /**
     * Elimina un cliente
     */
    public function eliminar($ci) {
        $query = "DELETE FROM " . $this->tabla . " WHERE ci = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $ci);
        return $stmt->execute();
    }
}
?>
