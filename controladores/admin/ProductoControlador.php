<?php
require_once __DIR__ . '/../../modelos/Producto.php';
require_once __DIR__ . '/../../modelos/Marca.php';
require_once __DIR__ . '/../../modelos/Categoria.php';
require_once __DIR__ . '/../../modelos/Industria.php';
require_once __DIR__ . '/../../modelos/Sucursal.php';
require_once __DIR__ . '/../../modelos/DetalleProductoSucursal.php';

class ProductoControlador {

    private function procesarImagen(): string {
        $imagen = trim($_POST['imagen'] ?? 'producto.png');
        if (!empty($_FILES['imagen_file']['name'])) {
            $ext        = strtolower(pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $permitidos)
                && $_FILES['imagen_file']['size'] <= 5 * 1024 * 1024
                && $_FILES['imagen_file']['error'] === 0) {
                $nombreArchivo = uniqid('img_', true) . '.' . $ext;
                $destino = __DIR__ . '/../../recursos/imagenes/' . $nombreArchivo;
                if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $destino)) {
                    $imagen = $nombreArchivo;
                }
            }
        }
        return $imagen;
    }

    public function productos() {
        requierePermiso('ver_productos');
        $productoModel  = new Producto();
        $stockModel     = new DetalleProductoSucursal();
        $categoriaModel = new Categoria();

        if (isset($_GET['eliminar_producto'])) {
            requierePermiso('eliminar_productos');
            $productoModel->eliminar((int)$_GET['eliminar_producto']);
            header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto eliminado.'));
            exit();
        }

        if (isset($_GET['eliminar_stock_producto'], $_GET['eliminar_stock_sucursal'])) {
            $stockModel->eliminar((int)$_GET['eliminar_stock_producto'], (int)$_GET['eliminar_stock_sucursal']);
            header('Location: /admin/index.php?page=productos&msg=' . urlencode('Stock eliminado.'));
            exit();
        }

        $productos  = $productoModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $mensaje    = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $titulo     = 'Productos';
        require_once __DIR__ . '/../../vistas/admin_productos.php';
    }

    public function productosCrear() {
        requierePermiso('crear_productos');
        $productoModel  = new Producto();
        $marcaModel     = new Marca();
        $categoriaModel = new Categoria();
        $industriaModel = new Industria();
        $sucursalModel  = new Sucursal();
        $stockModel     = new DetalleProductoSucursal();
        $error    = null;
        $esEditar = false;
        $producto = [];
        $stocks   = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo          = trim($_POST['codigo']          ?? '') ?: null;
            $nombre          = trim($_POST['nombre']          ?? '');
            $descripcion     = trim($_POST['descripcion']     ?? '');
            $precioVigente   = (float)($_POST['precioVigente']   ?? 0);
            $precioPropuesto = (float)($_POST['precioPropuesto'] ?? 0);
            $estado          = trim($_POST['estado']          ?? 'activo');
            $codMarca        = (int)($_POST['codMarca']       ?? 0);
            $codIndustria    = (int)($_POST['codIndustria']   ?? 0);
            $codCategoria    = (int)($_POST['codCategoria']   ?? 0);
            $imagen          = $this->procesarImagen();
            if ($precioPropuesto <= 0) $precioPropuesto = $precioVigente;

            if ($nombre === '' || $descripcion === '' || $precioVigente <= 0 || $codMarca === 0 || $codIndustria === 0 || $codCategoria === 0) {
                $error = 'Completa todos los campos obligatorios.';
            } else {
                $productoModel->agregar($nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $codMarca, $codIndustria, $codCategoria, $estado, $codigo);

                $codSucursal  = (int)($_POST['codSucursal']   ?? 0);
                $stockInicial = (int)($_POST['stock_inicial'] ?? 0);
                if ($codSucursal > 0 && $stockInicial > 0) {
                    $nuevoId = $productoModel->obtenerUltimoId();
                    if ($nuevoId) { $stockModel->guardarStock($nuevoId, $codSucursal, $stockInicial); }
                }

                header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto creado correctamente.'));
                exit();
            }

            $producto = compact('codigo', 'nombre', 'descripcion', 'precioVigente', 'precioPropuesto', 'estado', 'codMarca', 'codIndustria', 'codCategoria', 'imagen');
        }

        $marcas     = $marcaModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $industrias = $industriaModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();
        $titulo     = 'Nuevo Producto';
        require_once __DIR__ . '/../../vistas/admin_productos_form.php';
    }

    public function productosEditar() {
        requierePermiso('editar_productos');
        $productoModel  = new Producto();
        $marcaModel     = new Marca();
        $categoriaModel = new Categoria();
        $industriaModel = new Industria();
        $sucursalModel  = new Sucursal();
        $stockModel     = new DetalleProductoSucursal();
        $error    = null;
        $esEditar = true;
        $id       = (int)($_GET['id'] ?? 0);

        if ($id <= 0) { header('Location: /admin/index.php?page=productos'); exit(); }

        $producto = $productoModel->obtenerPorId($id);
        if (!$producto) {
            header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto no encontrado.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codigo          = trim($_POST['codigo']          ?? '') ?: null;
            $nombre          = trim($_POST['nombre']          ?? '');
            $descripcion     = trim($_POST['descripcion']     ?? '');
            $precioVigente   = (float)($_POST['precioVigente']   ?? 0);
            $precioPropuesto = (float)($_POST['precioPropuesto'] ?? 0);
            $estado          = trim($_POST['estado']          ?? 'activo');
            $codMarca        = (int)($_POST['codMarca']       ?? 0);
            $codIndustria    = (int)($_POST['codIndustria']   ?? 0);
            $codCategoria    = (int)($_POST['codCategoria']   ?? 0);
            $imagen          = $this->procesarImagen();
            if ($precioPropuesto <= 0) $precioPropuesto = $precioVigente;

            if ($nombre === '' || $descripcion === '' || $precioVigente <= 0 || $codMarca === 0 || $codIndustria === 0 || $codCategoria === 0) {
                $error = 'Completa todos los campos obligatorios.';
            } else {
                $productoModel->actualizar($id, $nombre, $descripcion, $precioVigente, $precioPropuesto, $imagen, $codMarca, $codIndustria, $codCategoria, $estado, $codigo);

                $codSucursal  = (int)($_POST['codSucursal']   ?? 0);
                $stockInicial = (int)($_POST['stock_inicial'] ?? 0);
                if ($codSucursal > 0 && $stockInicial > 0) {
                    $stockModel->guardarStock($id, $codSucursal, $stockInicial);
                }

                header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto actualizado correctamente.'));
                exit();
            }

            $producto = array_merge($producto, compact('codigo', 'nombre', 'descripcion', 'precioVigente', 'precioPropuesto', 'estado', 'codMarca', 'codIndustria', 'codCategoria', 'imagen'));
        }

        $marcas     = $marcaModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $industrias = $industriaModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();
        $stocks     = $stockModel->obtenerTodos();
        $titulo     = 'Editar Producto';
        require_once __DIR__ . '/../../vistas/admin_productos_form.php';
    }
}
