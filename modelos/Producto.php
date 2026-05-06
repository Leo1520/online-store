<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodos() {
        $stmt = $this->db->prepare("CALL sp_listar_productos_con_stock_total()");
        if (!$stmt) return [];
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $datos;
    }

    public function obtenerPorId($id) {
        $id = (int)$id;
        $stmt = $this->db->prepare("CALL sp_obtener_producto_por_id(?)");
        if (!$stmt) return null;
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dato = ($resultado && $resultado->num_rows > 0) ? $resultado->fetch_assoc() : null;
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $dato;
    }

    public function buscar($nombre = '', $codCategoria = 0, $precioMin = 0, $precioMax = 0, $orden = '') {
        $nombre       = trim($nombre);
        $codCategoria = (int)$codCategoria;
        $precioMin    = (float)$precioMin;
        $precioMax    = (float)$precioMax;

        $stmt = $this->db->prepare("CALL sp_buscar_productos(?, ?, ?, ?)");
        if (!$stmt) return [];
        $stmt->bind_param('sidd', $nombre, $codCategoria, $precioMin, $precioMax);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $this->aplicarOrden($datos, $orden);
    }

    private function aplicarOrden(array $datos, string $orden): array {
        if (empty($orden) || empty($datos)) return $datos;
        usort($datos, function ($a, $b) use ($orden) {
            switch ($orden) {
                case 'precio_asc':  return $a['precioVigente'] <=> $b['precioVigente'];
                case 'precio_desc': return $b['precioVigente'] <=> $a['precioVigente'];
                case 'nombre_asc':  return strcmp($a['nombre'], $b['nombre']);
                case 'nombre_desc': return strcmp($b['nombre'], $a['nombre']);
                default: return 0;
            }
        });
        return $datos;
    }

    public function agregar($nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $codMarca, $codIndustria, $codCategoria, $estado = 'activo', $codigo = null) {
        $precioVigente   = (float)$precioVigente;
        $precioPropuesto = (float)$precioPropuesto;
        $codMarca        = (int)$codMarca;
        $codIndustria    = (int)$codIndustria;
        $codCategoria    = (int)$codCategoria;

        $stmt = $this->db->prepare("CALL sp_crear_producto(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('sssddssiii', $codigo, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $estado, $codMarca, $codIndustria, $codCategoria);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function actualizar($id, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $codMarca, $codIndustria, $codCategoria, $estado = 'activo', $codigo = null) {
        $id              = (int)$id;
        $precioVigente   = (float)$precioVigente;
        $precioPropuesto = (float)$precioPropuesto;
        $codMarca        = (int)$codMarca;
        $codIndustria    = (int)$codIndustria;
        $codCategoria    = (int)$codCategoria;

        $stmt = $this->db->prepare("CALL sp_actualizar_producto(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) return false;
        $stmt->bind_param('isssddssiiii', $id, $codigo, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $estado, $codMarca, $codIndustria, $codCategoria);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
    }

    public function obtenerUltimoId(): ?int {
        $stmt = $this->db->prepare("CALL sp_listar_productos_con_stock_total()");
        if (!$stmt) return null;
        $stmt->execute();
        $resultado = $stmt->get_result();
        $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        $this->limpiarResultadosPendientes();
        if (empty($datos)) return null;
        return (int)max(array_column($datos, 'id_producto'));
    }

    public function eliminar($id) {
        $id = (int)$id;
        $stmt = $this->db->prepare("CALL sp_eliminar_producto(?)");
        if (!$stmt) return false;
        $stmt->bind_param('i', $id);
        $ok = $stmt->execute();
        $stmt->close();
        $this->limpiarResultadosPendientes();
        return $ok;
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
