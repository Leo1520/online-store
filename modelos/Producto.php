<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodos() {
        return [
            [
                'id_producto' => 1,
                'nombre' => 'Sudadera',
                'descripcion' => 'Sudadera de algodón unisex',
                'precio' => 250.00,
                'imagen' => 'sudadera.png',
                'stock' => 10
            ],
            
        ];
    }

    public function obtenerPorId($id) {
        $id = (int)$id;
        $resultado = $this->db->query("SELECT * FROM productos WHERE id_producto = $id");
        if ($resultado->num_rows == 0) return null;
        return $resultado->fetch_assoc();
    }

    public function agregar($nombre, $descripcion, $precio, $imagen, $stock) {
        $nombre      = $this->db->real_escape_string($nombre);
        $descripcion = $this->db->real_escape_string($descripcion);
        $precio      = (float)$precio;
        $imagen      = $this->db->real_escape_string($imagen);
        $stock       = (int)$stock;
        return $this->db->query(
            "INSERT INTO productos (nombre, descripcion, precio, imagen, stock)
             VALUES ('$nombre', '$descripcion', $precio, '$imagen', $stock)"
        );
    }

    public function actualizar($id, $nombre, $descripcion, $precio, $imagen, $stock) {
        $id          = (int)$id;
        $nombre      = $this->db->real_escape_string($nombre);
        $descripcion = $this->db->real_escape_string($descripcion);
        $precio      = (float)$precio;
        $imagen      = $this->db->real_escape_string($imagen);
        $stock       = (int)$stock;
        return $this->db->query(
            "UPDATE productos
             SET nombre='$nombre', descripcion='$descripcion', precio=$precio, imagen='$imagen', stock=$stock
             WHERE id_producto=$id"
        );
    }

    public function eliminar($id) {
        $id = (int)$id;
        return $this->db->query("DELETE FROM productos WHERE id_producto = $id");
    }
}
