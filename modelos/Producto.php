<?php
require_once __DIR__ . '/../config/database.php';

class Producto {
    private $db;

    public function __construct() {
        $this->db = Database::conectar();
    }

    public function obtenerTodos() {
        $sql = "SELECT
                    p.cod AS id_producto,
                    p.codigo,
                    p.nombre,
                    p.descripcion,
                    p.precioVigente,
                    p.precioPropuesto,
                    p.imagen,
                    p.estado,
                    p.codMarca,
                    p.codIndustria,
                    p.codCategoria,
                    COALESCE(SUM(CAST(dps.stock AS UNSIGNED)), 0) AS stock,
                    m.nombre AS marca,
                    c.nombre AS categoria,
                    i.nombre AS industria
                FROM `Producto` p
                LEFT JOIN `DetalleProductoSucursal` dps ON dps.codProducto = p.cod
                LEFT JOIN `Marca` m ON m.cod = p.codMarca
                LEFT JOIN `Categoria` c ON c.cod = p.codCategoria
                LEFT JOIN `Industria` i ON i.cod = p.codIndustria
                GROUP BY p.cod, p.codigo, p.nombre, p.descripcion, p.precioVigente, p.precioPropuesto, p.imagen, p.estado, p.codMarca, p.codIndustria, p.codCategoria, m.nombre, c.nombre, i.nombre
                ORDER BY p.cod DESC";

        $resultado = $this->db->query($sql);
        if (!$resultado) return [];

        $productos = [];
        while ($fila = $resultado->fetch_assoc()) {
            $productos[] = $fila;
        }
        return $productos;
    }

    public function obtenerPorId($id) {
        $id = (int)$id;

        $sql = "SELECT
                    p.cod AS id_producto,
                    p.codigo,
                    p.nombre,
                    p.descripcion,
                    p.precioVigente,
                    p.precioPropuesto,
                    p.imagen,
                    p.estado,
                    p.codMarca,
                    p.codIndustria,
                    p.codCategoria,
                    COALESCE(SUM(CAST(dps.stock AS UNSIGNED)), 0) AS stock,
                    m.nombre AS marca,
                    c.nombre AS categoria,
                    i.nombre AS industria
                FROM `Producto` p
                LEFT JOIN `DetalleProductoSucursal` dps ON dps.codProducto = p.cod
                LEFT JOIN `Marca` m ON m.cod = p.codMarca
                LEFT JOIN `Categoria` c ON c.cod = p.codCategoria
                LEFT JOIN `Industria` i ON i.cod = p.codIndustria
                WHERE p.cod = ?
                GROUP BY p.cod, p.codigo, p.nombre, p.descripcion, p.precioVigente, p.precioPropuesto, p.imagen, p.estado, p.codMarca, p.codIndustria, p.codCategoria, m.nombre, c.nombre, i.nombre";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if (!$resultado || $resultado->num_rows === 0) return null;

        return $resultado->fetch_assoc();
    }

    public function buscar($nombre = '', $codCategoria = 0, $precioMin = 0, $precioMax = 0, $orden = '') {
        $nombre       = trim($nombre);
        $codCategoria = (int)$codCategoria;
        $precioMin    = (float)$precioMin;
        $precioMax    = (float)$precioMax;

        $where = ['p.estado = "activo"'];
        $params = []; $types = '';

        if ($nombre !== '') {
            $where[] = 'p.nombre LIKE ?';
            $params[] = '%' . $nombre . '%';
            $types .= 's';
        }
        if ($codCategoria > 0) {
            $where[] = 'p.codCategoria = ?';
            $params[] = $codCategoria;
            $types .= 'i';
        }
        if ($precioMin > 0) {
            $where[] = 'p.precioVigente >= ?';
            $params[] = $precioMin;
            $types .= 'd';
        }
        if ($precioMax > 0) {
            $where[] = 'p.precioVigente <= ?';
            $params[] = $precioMax;
            $types .= 'd';
        }

        $sql = "SELECT
                    p.cod AS id_producto,
                    p.codigo,
                    p.nombre,
                    p.descripcion,
                    p.precioVigente,
                    p.precioPropuesto,
                    p.imagen,
                    p.estado,
                    p.codMarca,
                    p.codIndustria,
                    p.codCategoria,
                    COALESCE(SUM(CAST(dps.stock AS UNSIGNED)), 0) AS stock,
                    m.nombre AS marca,
                    c.nombre AS categoria,
                    i.nombre AS industria
                FROM `Producto` p
                LEFT JOIN `DetalleProductoSucursal` dps ON dps.codProducto = p.cod
                LEFT JOIN `Marca` m ON m.cod = p.codMarca
                LEFT JOIN `Categoria` c ON c.cod = p.codCategoria
                LEFT JOIN `Industria` i ON i.cod = p.codIndustria
                WHERE " . implode(' AND ', $where) . "
                GROUP BY p.cod, p.codigo, p.nombre, p.descripcion, p.precioVigente, p.precioPropuesto, p.imagen, p.estado, p.codMarca, p.codIndustria, p.codCategoria, m.nombre, c.nombre, i.nombre
                ORDER BY p.cod DESC";

        if (!empty($params)) {
            $stmt = $this->db->prepare($sql);
            if (!$stmt) return [];
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
            $stmt->close();
        } else {
            $resultado = $this->db->query($sql);
            $datos = $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
        }

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

        $sql = "INSERT INTO `Producto` (codigo, nombre, descripcion, precioVigente, precioPropuesto, imagen, estado, codMarca, codIndustria, codCategoria)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("sssddssiii", $codigo, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $estado, $codMarca, $codIndustria, $codCategoria);
        return $stmt->execute();
    }

    public function actualizar($id, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $codMarca, $codIndustria, $codCategoria, $estado = 'activo', $codigo = null) {
        $id              = (int)$id;
        $precioVigente   = (float)$precioVigente;
        $precioPropuesto = (float)$precioPropuesto;
        $codMarca        = (int)$codMarca;
        $codIndustria    = (int)$codIndustria;
        $codCategoria    = (int)$codCategoria;

        $sql = "UPDATE `Producto`
                SET codigo = ?, nombre = ?, descripcion = ?, precioVigente = ?, precioPropuesto = ?, imagen = ?, estado = ?, codMarca = ?, codIndustria = ?, codCategoria = ?
                WHERE cod = ?";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param("sssddssiiii", $codigo, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $estado, $codMarca, $codIndustria, $codCategoria, $id);
        return $stmt->execute();
    }

    public function obtenerUltimoId(): ?int {
        $res = $this->db->query("SELECT MAX(cod) AS ultimo FROM `Producto`");
        if ($res) {
            $fila = $res->fetch_assoc();
            return $fila ? (int)$fila['ultimo'] : null;
        }
        return null;
    }

    public function eliminar($id) {
        $id = (int)$id;
        $sql = "DELETE FROM `Producto` WHERE cod = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return false;
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
