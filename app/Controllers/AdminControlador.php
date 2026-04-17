<?php
require_once __DIR__ . '/Controlador.php';
require_once __DIR__ . '/../Models/Marca.php';
require_once __DIR__ . '/../Models/Categoria.php';
require_once __DIR__ . '/../Models/Industria.php';
require_once __DIR__ . '/../Models/Producto.php';
require_once __DIR__ . '/../Models/NotaVenta.php';

/**
 * Controlador para administración de Marcas, Categorías e Industrias
 */
class AdminControlador extends Controlador {

    public function panel() {
        $this->verificarAdmin();
        
        $modelo = new Producto($this->conexion);
        $totalProductos = $modelo->obtenerTodos() ? count($modelo->obtenerTodos()) : 0;
        
        $modeloNota = new NotaVenta($this->conexion);
        $totalVentas = 0; // Implementar lógica real si es necesario
        
        $this->cargarVistaAdmin('Admin/panel', [
            'totalProductos' => $totalProductos,
            'totalVentas' => $totalVentas
        ], 'Dashboard');
    }

    public function listarMarcas() {
        $this->verificarAdmin();
        
        $modelo = new Marca($this->conexion);
        $marcas = $modelo->obtenerTodos();
        
        $this->cargarVistaAdmin('Admin/Marcas/listar', [
            'marcas' => $marcas
        ], 'Marcas');
    }

    public function crearMarca() {
        $this->verificarAdmin();
        $this->cargarVistaAdmin('Admin/Marcas/crear', [], 'Crear Marca');
    }

    public function guardarMarca() {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=listarMarcas');
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es requerido.';
            $this->redirigir('?controlador=admin&accion=crearMarca');
        }

        $modelo = new Marca($this->conexion);
        if ($modelo->crear($nombre)) {
            $_SESSION['mensaje'] = 'Marca creada exitosamente.';
            $this->redirigir('?controlador=admin&accion=listarMarcas');
        } else {
            $_SESSION['error'] = 'Error al crear la marca.';
            $this->redirigir('?controlador=admin&accion=crearMarca');
        }
    }

    public function editarMarca($cod) {
        $this->verificarAdmin();

        $modelo = new Marca($this->conexion);
        $marca = $modelo->obtenerPorId($cod);

        if (!$marca) {
            $_SESSION['error'] = 'Marca no encontrada.';
            $this->redirigir('?controlador=admin&accion=listarMarcas');
        }

        $this->cargarVistaAdmin('Admin/Marcas/editar', [
            'marca' => $marca
        ], 'Editar Marca');
    }

    public function actualizarMarca($cod) {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=editarMarca&id=' . $cod);
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es requerido.';
            $this->redirigir('?controlador=admin&accion=editarMarca&id=' . $cod);
        }

        $modelo = new Marca($this->conexion);
        if ($modelo->actualizar($cod, $nombre)) {
            $_SESSION['mensaje'] = 'Marca actualizada exitosamente.';
            $this->redirigir('?controlador=admin&accion=listarMarcas');
        } else {
            $_SESSION['error'] = 'Error al actualizar la marca.';
            $this->redirigir('?controlador=admin&accion=editarMarca&id=' . $cod);
        }
    }

    public function eliminarMarca($cod) {
        $this->verificarAdmin();

        $modelo = new Marca($this->conexion);
        if ($modelo->eliminar($cod)) {
            $_SESSION['mensaje'] = 'Marca eliminada exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar la marca.';
        }

        $this->redirigir('?controlador=admin&accion=listarMarcas');
    }

    // ===== CATEGORÍAS =====
    public function listarCategorias() {
        $this->verificarAdmin();
        
        $modelo = new Categoria($this->conexion);
        $categorias = $modelo->obtenerTodos();
        
        $this->cargarVistaAdmin('Admin/Categorias/listar', [
            'categorias' => $categorias
        ], 'Categorías');
    }

    public function crearCategoria() {
        $this->verificarAdmin();
        $this->cargarVistaAdmin('Admin/Categorias/crear', [], 'Crear Categoría');
    }

    public function guardarCategoria() {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=listarCategorias');
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es requerido.';
            $this->redirigir('?controlador=admin&accion=crearCategoria');
        }

        $modelo = new Categoria($this->conexion);
        if ($modelo->crear($nombre)) {
            $_SESSION['mensaje'] = 'Categoría creada exitosamente.';
            $this->redirigir('?controlador=admin&accion=listarCategorias');
        } else {
            $_SESSION['error'] = 'Error al crear la categoría.';
            $this->redirigir('?controlador=admin&accion=crearCategoria');
        }
    }

    public function editarCategoria($cod) {
        $this->verificarAdmin();

        $modelo = new Categoria($this->conexion);
        $categoria = $modelo->obtenerPorId($cod);

        if (!$categoria) {
            $_SESSION['error'] = 'Categoría no encontrada.';
            $this->redirigir('?controlador=admin&accion=listarCategorias');
        }

        $this->cargarVistaAdmin('Admin/Categorias/editar', [
            'categoria' => $categoria
        ], 'Editar Categoría');
    }

    public function actualizarCategoria($cod) {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=editarCategoria&id=' . $cod);
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es requerido.';
            $this->redirigir('?controlador=admin&accion=editarCategoria&id=' . $cod);
        }

        $modelo = new Categoria($this->conexion);
        if ($modelo->actualizar($cod, $nombre)) {
            $_SESSION['mensaje'] = 'Categoría actualizada exitosamente.';
            $this->redirigir('?controlador=admin&accion=listarCategorias');
        } else {
            $_SESSION['error'] = 'Error al actualizar la categoría.';
            $this->redirigir('?controlador=admin&accion=editarCategoria&id=' . $cod);
        }
    }

    public function eliminarCategoria($cod) {
        $this->verificarAdmin();

        $modelo = new Categoria($this->conexion);
        if ($modelo->eliminar($cod)) {
            $_SESSION['mensaje'] = 'Categoría eliminada exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar la categoría.';
        }

        $this->redirigir('?controlador=admin&accion=listarCategorias');
    }

    // ===== INDUSTRIAS =====
    public function listarIndustrias() {
        $this->verificarAdmin();
        
        $modelo = new Industria($this->conexion);
        $industrias = $modelo->obtenerTodos();
        
        $this->cargarVistaAdmin('Admin/Industrias/listar', [
            'industrias' => $industrias
        ], 'Industrias');
    }

    public function crearIndustria() {
        $this->verificarAdmin();
        $this->cargarVistaAdmin('Admin/Industrias/crear', [], 'Crear Industria');
    }

    public function guardarIndustria() {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=listarIndustrias');
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es requerido.';
            $this->redirigir('?controlador=admin&accion=crearIndustria');
        }

        $modelo = new Industria($this->conexion);
        if ($modelo->crear($nombre)) {
            $_SESSION['mensaje'] = 'Industria creada exitosamente.';
            $this->redirigir('?controlador=admin&accion=listarIndustrias');
        } else {
            $_SESSION['error'] = 'Error al crear la industria.';
            $this->redirigir('?controlador=admin&accion=crearIndustria');
        }
    }

    public function editarIndustria($cod) {
        $this->verificarAdmin();

        $modelo = new Industria($this->conexion);
        $industria = $modelo->obtenerPorId($cod);

        if (!$industria) {
            $_SESSION['error'] = 'Industria no encontrada.';
            $this->redirigir('?controlador=admin&accion=listarIndustrias');
        }

        $this->cargarVistaAdmin('Admin/Industrias/editar', [
            'industria' => $industria
        ], 'Editar Industria');
    }

    public function actualizarIndustria($cod) {
        $this->verificarAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirigir('?controlador=admin&accion=editarIndustria&id=' . $cod);
        }

        $nombre = trim($_POST['nombre'] ?? '');

        if (empty($nombre)) {
            $_SESSION['error'] = 'El nombre es requerido.';
            $this->redirigir('?controlador=admin&accion=editarIndustria&id=' . $cod);
        }

        $modelo = new Industria($this->conexion);
        if ($modelo->actualizar($cod, $nombre)) {
            $_SESSION['mensaje'] = 'Industria actualizada exitosamente.';
            $this->redirigir('?controlador=admin&accion=listarIndustrias');
        } else {
            $_SESSION['error'] = 'Error al actualizar la industria.';
            $this->redirigir('?controlador=admin&accion=editarIndustria&id=' . $cod);
        }
    }

    public function eliminarIndustria($cod) {
        $this->verificarAdmin();

        $modelo = new Industria($this->conexion);
        if ($modelo->eliminar($cod)) {
            $_SESSION['mensaje'] = 'Industria eliminada exitosamente.';
        } else {
            $_SESSION['error'] = 'Error al eliminar la industria.';
        }

        $this->redirigir('?controlador=admin&accion=listarIndustrias');
    }

}
?>
