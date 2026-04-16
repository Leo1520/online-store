<?php
/**
 * Clase Base para Modelos
 */
class Modelo {
    protected $conexion;
    protected $tabla;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene todos los registros
     */
    public function obtenerTodos() {
        $query = "SELECT * FROM " . $this->tabla;
        $resultado = $this->conexion->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene un registro por ID
     */
    public function obtenerPorId($id) {
        $query = "SELECT * FROM " . $this->tabla . " WHERE id = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Ejecuta una consulta preparada
     */
    protected function ejecutar($query, $tipos = "", $parametros = []) {
        $stmt = $this->conexion->prepare($query);
        if (!empty($tipos) && !empty($parametros)) {
            $stmt->bind_param($tipos, ...$parametros);
        }
        return $stmt->execute();
    }

    /**
     * Obtiene el último ID insertado
     */
    public function ultimoId() {
        return $this->conexion->insert_id;
    }

    /**
     * Obtiene el número de filas afectadas
     */
    public function filasAfectadas() {
        return $this->conexion->affected_rows;
    }
}
?>
