<?php
require_once __DIR__ . '/Controlador.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Marca.php';
require_once __DIR__ . '/../Models/Industria.php';
require_once __DIR__ . '/../Models/DetalleProductoSucursal.php';

/**
 * Controlador de Productos
 */
class ProductoControlador extends Controlador {

    /**
     * Lista todos los productos
     */
    public function listar() {
        $modelo = new Producto($this->conexion);
        $productos = $modelo->obtenerTodos();
        
        $this->cargarVista('Productos/listar', [
            'productos' => $productos
        ]);
    }

    /**
     * Muestra el formulario para crear un producto
     */
    public function crear() {
        $this->verificarAdmin();
        
        $modeloMarca = new Marca($this->conexion);
        $modeloCategoria = new Categoria($this->conexion);
        $modeloIndustria = new Industria($this->conexion);
        
        $marcas = $modeloMarca->obtenerTodos();
        $categorias = $modeloCategoria->obtenerTodos();
        $industrias = $modeloIndustria->obtenerTodos();
        
        $this->cargarVista('Productos/crear', [
            'marcas' => $marcas,
            'categorias' => $categorias,
            'industrias' => $industrias
        ]);
    }

    /**
     * Guarda un nuevo producto
     */
    public function guardar() {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?accion=listar');
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $codMarca = intval($_POST['codMarca'] ?? 0);
        $codCategoria = intval($_POST['codCategoria'] ?? 0);
        $codIndustria = intval($_POST['codIndustria'] ?? 0);
        $estado = $_POST['estado'] ?? 'Activo';
        $imagen = $_FILES['imagen'] ?? null;

        $imagen_nombre = 'default.jpg';
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $imagen_nombre = time() . '_' . basename($imagen['name']);
            $ruta_destino = __DIR__ . '/../../Recursos/imagenes/' . $imagen_nombre;
            move_uploaded_file($imagen['tmp_name'], $ruta_destino);
        }

        $modelo = new Producto($this->conexion);
        if ($modelo->crear($nombre, $descripcion, $precio, $imagen_nombre, $estado, $codMarca, $codIndustria, $codCategoria)) {
            $_SESSION['mensaje'] = 'Producto creado exitosamente.';
            $this->redirigir('?accion=listar');
        } else {
            $_SESSION['error'] = 'Error al crear el producto.';
            $this->redirigir('?accion=crear');
        }
    }

    /**
     * Muestra un producto específico
     */
    public function detalle($cod) {
        $modelo = new Producto($this->conexion);
        $producto = $modelo->obtenerPorId($cod);
        
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirigir('?accion=listar');
        }

        $this->cargarVista('Productos/detalle', [
            'producto' => $producto
        ]);
    }

    /**
     * Muestra el formulario para editar un producto
     */
    public function editar($cod) {
        $this->verificarAdmin();
        
        $modelo = new Producto($this->conexion);
        $producto = $modelo->obtenerPorId($cod);
        
        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirigir('?accion=listar');
        }

        $modeloMarca = new Marca($this->conexion);
        $modeloCategoria = new Categoria($this->conexion);
        $modeloIndustria = new Industria($this->conexion);
        
        $marcas = $modeloMarca->obtenerTodos();
        $categorias = $modeloCategoria->obtenerTodos();
        $industrias = $modeloIndustria->obtenerTodos();

        $this->cargarVista('Productos/editar', [
            'producto' => $producto,
            'marcas' => $marcas,
            'categorias' => $categorias,
            'industrias' => $industrias
        ]);
    }

    /**
     * Actualiza un producto
     */
    public function actualizar($cod) {
        $this->verificarAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?accion=editar&id=' . $cod);
        }

        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $codMarca = intval($_POST['codMarca'] ?? 0);
        $codCategoria = intval($_POST['codCategoria'] ?? 0);
        $codIndustria = intval($_POST['codIndustria'] ?? 0);
        $estado = $_POST['estado'] ?? 'Activo';
        $imagen = $_FILES['imagen'] ?? null;

        $modelo = new Producto($this->conexion);
        $productoActual = $modelo->obtenerPorId($cod);
        
        $imagen_nombre = $productoActual['imagen'];
        if ($imagen && $imagen['error'] === UPLOAD_ERR_OK) {
            $imagen_nombre = time() . '_' . basename($imagen['name']);
            $ruta_destino = __DIR__ . '/../../Recursos/imagenes/' . $imagen_nombre;
            move_uploaded_file($imagen['tmp_name'], $ruta_destino);
        }

        if ($modelo->actualizar($cod, $nombre, $descripcion, $precio, $imagen_nombre, $estado, $codMarca, $codIndustria, $codCategoria)) {
            $_SESSION['mensaje'] = 'Producto actualizado exitosamente.';
            $this->redirigir('?accion=detalle&id=' . $cod);
        } else {
            $_SESSION['error'] = 'Error al actualizar el producto.';
            $this->redirigir('?accion=editar&id=' . $cod);
        }
    }

    /**
     * Elimina un producto
     */
    public function eliminar($cod) {
        $this->verificarAdmin();
        
        $modelo = new Producto($this->conexion);
        if ($modelo->eliminar($cod)) {
            $_SESSION['mensaje'] = 'Producto eliminado exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar el producto.';
        }
        
        $this->redirigir('?accion=listar');
    }

    /**
     * Busca productos
     */
    public function buscar() {
        $termino = trim($_GET['q'] ?? '');
        
        if (empty($termino)) {
            $this->redirigir('?accion=listar');
        }

        $modelo = new Producto($this->conexion);
        $productos = $modelo->buscar($termino);

        $this->cargarVista('Productos/listar', [
            'productos' => $productos,
            'termino_busqueda' => $termino
        ]);
    }
}
?>
