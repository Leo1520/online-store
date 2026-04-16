<?php
require_once __DIR__ . '/Modelo.php';

/**
 * Modelo de Producto
 */
class Producto extends Modelo {
    protected $tabla = 'Producto';

    public function __construct($conexion) {
        parent::__construct($conexion);
    }

    /**
     * Obtiene un producto por ID
     */
    public function obtenerPorId($cod) {
        $query = "SELECT p.*, m.nombre as marcaNombre, i.nombre as industriaNombre, c.nombre as categoriaNombre 
                  FROM " . $this->tabla . " p
                  LEFT JOIN Marca m ON p.codMarca = m.cod
                  LEFT JOIN Industria i ON p.codIndustria = i.cod
                  LEFT JOIN Categoria c ON p.codCategoria = c.cod
                  WHERE p.cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->num_rows > 0 ? $resultado->fetch_assoc() : null;
    }

    /**
     * Obtiene todos los productos
     */
    public function obtenerTodos() {
        $query = "SELECT p.*, m.nombre as marcaNombre, i.nombre as industriaNombre, c.nombre as categoriaNombre 
                  FROM " . $this->tabla . " p
                  LEFT JOIN Marca m ON p.codMarca = m.cod
                  LEFT JOIN Industria i ON p.codIndustria = i.cod
                  LEFT JOIN Categoria c ON p.codCategoria = c.cod
                  ORDER BY p.cod DESC";
        $resultado = $this->conexion->query($query);
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene productos por estado
     */
    public function obtenerPorEstado($estado) {
        $query = "SELECT p.*, m.nombre as marcaNombre, i.nombre as industriaNombre, c.nombre as categoriaNombre 
                  FROM " . $this->tabla . " p
                  LEFT JOIN Marca m ON p.codMarca = m.cod
                  LEFT JOIN Industria i ON p.codIndustria = i.cod
                  LEFT JOIN Categoria c ON p.codCategoria = c.cod
                  WHERE p.estado = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("s", $estado);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Obtiene productos por categoría
     */
    public function obtenerPorCategoria($codCategoria) {
        $query = "SELECT p.*, m.nombre as marcaNombre, i.nombre as industriaNombre, c.nombre as categoriaNombre 
                  FROM " . $this->tabla . " p
                  LEFT JOIN Marca m ON p.codMarca = m.cod
                  LEFT JOIN Industria i ON p.codIndustria = i.cod
                  LEFT JOIN Categoria c ON p.codCategoria = c.cod
                  WHERE p.codCategoria = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $codCategoria);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Busca productos por nombre
     */
    public function buscar($termino) {
        $termino = "%" . $termino . "%";
        $query = "SELECT p.*, m.nombre as marcaNombre, i.nombre as industriaNombre, c.nombre as categoriaNombre 
                  FROM " . $this->tabla . " p
                  LEFT JOIN Marca m ON p.codMarca = m.cod
                  LEFT JOIN Industria i ON p.codIndustria = i.cod
                  LEFT JOIN Categoria c ON p.codCategoria = c.cod
                  WHERE p.nombre LIKE ? OR p.descripcion LIKE ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ss", $termino, $termino);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Crea un nuevo producto
     */
    public function crear($nombre, $descripcion, $precio, $imagen, $estado, $codMarca, $codIndustria, $codCategoria) {
        $query = "INSERT INTO " . $this->tabla . " (nombre, descripcion, precio, imagen, estado, codMarca, codIndustria, codCategoria) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ssdssiis", $nombre, $descripcion, $precio, $imagen, $estado, $codMarca, $codIndustria, $codCategoria);
        return $stmt->execute();
    }

    /**
     * Actualiza un producto
     */
    public function actualizar($cod, $nombre, $descripcion, $precio, $imagen, $estado, $codMarca, $codIndustria, $codCategoria) {
        $query = "UPDATE " . $this->tabla . " SET nombre = ?, descripcion = ?, precio = ?, imagen = ?, estado = ?, codMarca = ?, codIndustria = ?, codCategoria = ? WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("ssdssiiii", $nombre, $descripcion, $precio, $imagen, $estado, $codMarca, $codIndustria, $codCategoria, $cod);
        return $stmt->execute();
    }

    /**
     * Elimina un producto
     */
    public function eliminar($cod) {
        $query = "DELETE FROM " . $this->tabla . " WHERE cod = ?";
        $stmt = $this->conexion->prepare($query);
        $stmt->bind_param("i", $cod);
        return $stmt->execute();
    }
}
?>
