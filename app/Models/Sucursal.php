<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Sucursal
 */
class Sucursal extends Modelo {
    protected $tabla = 'Sucursal';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene una sucursal por ID
     */
    public function obtenerPorId($cod) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Crea una nueva sucursal
     */
    public function crear($nombre, $direccion, $nroTelefono) {
        $query = "INSERT INTO " . $this->tabla . " (nombre, direccion, nroTelefono) VALUES (?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sss", $nombre, $direccion, $nroTelefono);
        return $stmt->execute();
    }

    /**
     * Actualiza una sucursal
     */
    public function actualizar($cod, $nombre, $direccion, $nroTelefono) {
        $query = "UPDATE " . $this->tabla . " SET nombre = ?, direccion = ?, nroTelefono = ? WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("sssi", $nombre, $direccion, $nroTelefono, $cod);
        return $stmt->execute();
    }

    /**
     * Elimina una sucursal
     */
    public function eliminar($cod) {
        $query = "DELETE FROM " . $this->tabla . " WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        return $stmt->execute();
    }
}
?>
