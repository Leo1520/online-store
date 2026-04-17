<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Rol
 */
class Rol extends Modelo {
    protected $tabla = 'Rol';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene un rol por su nombre
     */
    public function obtenerPorNombre($nombre) {
        $query = "SELECT id, nombre FROM " . $this->tabla . " WHERE nombre = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $nombre);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene un rol por su ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT id, nombre FROM " . $this->tabla . " WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }
}
?>
