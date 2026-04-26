<?php
require_once __DIR__ . '/../../modelos/Marca.php';
require_once __DIR__ . '/../../modelos/Categoria.php';
require_once __DIR__ . '/../../modelos/Industria.php';

class CatalogoControlador {

    // ── MARCAS ────────────────────────────────────────────────────
    public function marcas() {
        requierePermiso('ver_marcas');
        $marcaModel = new Marca();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if (isset($_GET['eliminar'])) {
            $marcaModel->eliminar((int)$_GET['eliminar']);
            header('Location: /admin/index.php?page=marcas&msg=' . urlencode('Marca eliminada.'));
            exit();
        }

        $marcas = $marcaModel->obtenerTodos();
        $titulo = 'Marcas';
        require_once __DIR__ . '/../../vistas/admin_marcas.php';
    }

    public function marcasCrear() {
        requierePermiso('ver_marcas');
        $marcaModel = new Marca();
        $error  = null;
        $esEditar = false;
        $marca  = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $error = 'El nombre de la marca es obligatorio.';
            } else {
                $marcaModel->crear($nombre);
                header('Location: /admin/index.php?page=marcas&msg=' . urlencode('Marca creada correctamente.'));
                exit();
            }
        }

        $titulo = 'Nueva Marca';
        require_once __DIR__ . '/../../vistas/admin_marcas_form.php';
    }

    public function marcasEditar() {
        requierePermiso('ver_marcas');
        $marcaModel = new Marca();
        $error  = null;
        $esEditar = true;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) { header('Location: /admin/index.php?page=marcas'); exit(); }

        $marca = $marcaModel->obtenerPorId($id);
        if (!$marca) {
            header('Location: /admin/index.php?page=marcas&msg=' . urlencode('Marca no encontrada.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $error = 'El nombre de la marca es obligatorio.';
            } else {
                $marcaModel->actualizar($id, $nombre);
                header('Location: /admin/index.php?page=marcas&msg=' . urlencode('Marca actualizada correctamente.'));
                exit();
            }
            $marca = array_merge($marca, ['nombre' => $nombre]);
        }

        $titulo = 'Editar Marca';
        require_once __DIR__ . '/../../vistas/admin_marcas_form.php';
    }

    // ── CATEGORÍAS ────────────────────────────────────────────────
    public function categorias() {
        requierePermiso('ver_categorias');
        $categoriaModel = new Categoria();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if (isset($_GET['eliminar'])) {
            $categoriaModel->eliminar((int)$_GET['eliminar']);
            header('Location: /admin/index.php?page=categorias&msg=' . urlencode('Categoría eliminada.'));
            exit();
        }

        $categorias = $categoriaModel->obtenerTodos();
        $titulo = 'Categorías';
        require_once __DIR__ . '/../../vistas/admin_categorias.php';
    }

    public function categoriasCrear() {
        requierePermiso('ver_categorias');
        $categoriaModel = new Categoria();
        $error  = null;
        $esEditar = false;
        $categoria = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $error = 'El nombre de la categoría es obligatorio.';
            } else {
                $categoriaModel->crear($nombre);
                header('Location: /admin/index.php?page=categorias&msg=' . urlencode('Categoría creada correctamente.'));
                exit();
            }
        }

        $titulo = 'Nueva Categoría';
        require_once __DIR__ . '/../../vistas/admin_categorias_form.php';
    }

    public function categoriasEditar() {
        requierePermiso('ver_categorias');
        $categoriaModel = new Categoria();
        $error  = null;
        $esEditar = true;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) { header('Location: /admin/index.php?page=categorias'); exit(); }

        $categoria = $categoriaModel->obtenerPorId($id);
        if (!$categoria) {
            header('Location: /admin/index.php?page=categorias&msg=' . urlencode('Categoría no encontrada.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $error = 'El nombre de la categoría es obligatorio.';
            } else {
                $categoriaModel->actualizar($id, $nombre);
                header('Location: /admin/index.php?page=categorias&msg=' . urlencode('Categoría actualizada correctamente.'));
                exit();
            }
            $categoria = array_merge($categoria, ['nombre' => $nombre]);
        }

        $titulo = 'Editar Categoría';
        require_once __DIR__ . '/../../vistas/admin_categorias_form.php';
    }

    // ── INDUSTRIAS ────────────────────────────────────────────────
    public function industrias() {
        requierePermiso('ver_industrias');
        $industriaModel = new Industria();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if (isset($_GET['eliminar'])) {
            $industriaModel->eliminar((int)$_GET['eliminar']);
            header('Location: /admin/index.php?page=industrias&msg=' . urlencode('Industria eliminada.'));
            exit();
        }

        $industrias = $industriaModel->obtenerTodos();
        $titulo = 'Industrias';
        require_once __DIR__ . '/../../vistas/admin_industrias.php';
    }

    public function industriasCrear() {
        requierePermiso('ver_industrias');
        $industriaModel = new Industria();
        $error  = null;
        $esEditar = false;
        $industria = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $error = 'El nombre de la industria es obligatorio.';
            } else {
                $industriaModel->crear($nombre);
                header('Location: /admin/index.php?page=industrias&msg=' . urlencode('Industria creada correctamente.'));
                exit();
            }
        }

        $titulo = 'Nueva Industria';
        require_once __DIR__ . '/../../vistas/admin_industrias_form.php';
    }

    public function industriasEditar() {
        requierePermiso('ver_industrias');
        $industriaModel = new Industria();
        $error  = null;
        $esEditar = true;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) { header('Location: /admin/index.php?page=industrias'); exit(); }

        $industria = $industriaModel->obtenerPorId($id);
        if (!$industria) {
            header('Location: /admin/index.php?page=industrias&msg=' . urlencode('Industria no encontrada.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre = trim($_POST['nombre'] ?? '');
            if ($nombre === '') {
                $error = 'El nombre de la industria es obligatorio.';
            } else {
                $industriaModel->actualizar($id, $nombre);
                header('Location: /admin/index.php?page=industrias&msg=' . urlencode('Industria actualizada correctamente.'));
                exit();
            }
            $industria = array_merge($industria, ['nombre' => $nombre]);
        }

        $titulo = 'Editar Industria';
        require_once __DIR__ . '/../../vistas/admin_industrias_form.php';
    }
}
