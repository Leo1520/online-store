<?php
require_once __DIR__ . '/../modelos/Marca.php';
require_once __DIR__ . '/../modelos/Categoria.php';
require_once __DIR__ . '/../modelos/Industria.php';
require_once __DIR__ . '/../modelos/Sucursal.php';
require_once __DIR__ . '/../modelos/Cuenta.php';
require_once __DIR__ . '/../modelos/Cliente.php';
require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/DetalleProductoSucursal.php';
require_once __DIR__ . '/../modelos/NotaVenta.php';
require_once __DIR__ . '/../modelos/Vendedor.php';
require_once __DIR__ . '/../modelos/MovimientoStock.php';
require_once __DIR__ . '/../modelos/Traspaso.php';

class AdminControlador {
    private function validarAutenticacion() {
        if (!isset($_SESSION['usuario']) || !isset($_SESSION['es_admin']) || !$_SESSION['es_admin']) {
            header('Location: index.php?pagina=login');
            exit();
        }
    }

    public function marcas() {
        $this->validarAutenticacion();

        $marcaModel = new Marca();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        // Eliminar marca
        if (isset($_GET['eliminar'])) {
            $marcaModel->eliminar((int)$_GET['eliminar']);
            header('Location: /admin/index.php?page=marcas&msg=' . urlencode('Marca eliminada.'));
            exit();
        }

        $marcas = $marcaModel->obtenerTodos();
        $titulo = 'Marcas';
        require_once __DIR__ . '/../vistas/admin_marcas.php';
    }

    public function marcasCrear() {
        $this->validarAutenticacion();

        $marcaModel = new Marca();
        $error = null;
        $esEditar = false;
        $marca = [];

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
        require_once __DIR__ . '/../vistas/admin_marcas_form.php';
    }

    public function marcasEditar() {
        $this->validarAutenticacion();

        $marcaModel = new Marca();
        $error = null;
        $esEditar = true;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /admin/index.php?page=marcas');
            exit();
        }

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

            // Si hay error, recargar datos
            $marca = array_merge($marca, ['nombre' => $nombre]);
        }

        $titulo = 'Editar Marca';
        require_once __DIR__ . '/../vistas/admin_marcas_form.php';
    }

    public function categorias() {
        $this->validarAutenticacion();

        $categoriaModel = new Categoria();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        // Eliminar categoria
        if (isset($_GET['eliminar'])) {
            $categoriaModel->eliminar((int)$_GET['eliminar']);
            header('Location: /admin/index.php?page=categorias&msg=' . urlencode('Categoría eliminada.'));
            exit();
        }

        $categorias = $categoriaModel->obtenerTodos();
        $titulo = 'Categorías';
        require_once __DIR__ . '/../vistas/admin_categorias.php';
    }

    public function categoriasCrear() {
        $this->validarAutenticacion();

        $categoriaModel = new Categoria();
        $error = null;
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
        require_once __DIR__ . '/../vistas/admin_categorias_form.php';
    }

    public function categoriasEditar() {
        $this->validarAutenticacion();

        $categoriaModel = new Categoria();
        $error = null;
        $esEditar = true;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /admin/index.php?page=categorias');
            exit();
        }

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

            // Si hay error, recargar datos
            $categoria = array_merge($categoria, ['nombre' => $nombre]);
        }

        $titulo = 'Editar Categoría';
        require_once __DIR__ . '/../vistas/admin_categorias_form.php';
    }

    public function industrias() {
        $this->validarAutenticacion();

        $industriaModel = new Industria();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        // Eliminar industria
        if (isset($_GET['eliminar'])) {
            $industriaModel->eliminar((int)$_GET['eliminar']);
            header('Location: /admin/index.php?page=industrias&msg=' . urlencode('Industria eliminada.'));
            exit();
        }

        $industrias = $industriaModel->obtenerTodos();
        $titulo = 'Industrias';
        require_once __DIR__ . '/../vistas/admin_industrias.php';
    }

    public function industriasCrear() {
        $this->validarAutenticacion();

        $industriaModel = new Industria();
        $error = null;
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
        require_once __DIR__ . '/../vistas/admin_industrias_form.php';
    }

    public function industriasEditar() {
        $this->validarAutenticacion();

        $industriaModel = new Industria();
        $error = null;
        $esEditar = true;
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /admin/index.php?page=industrias');
            exit();
        }

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

            // Si hay error, recargar datos
            $industria = array_merge($industria, ['nombre' => $nombre]);
        }

        $titulo = 'Editar Industria';
        require_once __DIR__ . '/../vistas/admin_industrias_form.php';
    }

    public function sucursales() {
        $this->validarAutenticacion();

        $sucursalModel = new Sucursal();
        $mensaje = null;
        $sucursalEditar = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? 'crear';
            $cod = (int)($_POST['cod'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $nroTelefono = trim($_POST['nroTelefono'] ?? '');

            if ($nombre !== '' && $direccion !== '' && $nroTelefono !== '') {
                if ($accion === 'editar' && $cod > 0) {
                    $sucursalModel->actualizar($cod, $nombre, $direccion, $nroTelefono);
                    $mensaje = 'Sucursal actualizada correctamente.';
                } else {
                    $sucursalModel->crear($nombre, $direccion, $nroTelefono);
                    $mensaje = 'Sucursal creada correctamente.';
                }
            }
        }

        if (isset($_GET['eliminar'])) {
            $sucursalModel->eliminar((int)$_GET['eliminar']);
            header('Location: index.php?pagina=admin_sucursales');
            exit();
        }

        if (isset($_GET['editar'])) {
            $sucursalEditar = $sucursalModel->obtenerPorCod((int)$_GET['editar']);
        }

        $sucursales = $sucursalModel->obtenerTodas();
        $titulo = 'Administracion - Sucursales';
        require_once __DIR__ . '/../vistas/admin_sucursales.php';
    }

    public function clientes() {
        $this->validarAutenticacion();

        $cuentaModel = new Cuenta();
        $clienteModel = new Cliente();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $clienteEditar = null;
        $usuariosProtegidos = ['cliente_demo', 'admin'];

        if (isset($_GET['eliminar_cliente_ci'], $_GET['eliminar_cliente_usuario'])) {
            $ci = trim($_GET['eliminar_cliente_ci']);
            $usuario = trim($_GET['eliminar_cliente_usuario']);

            if (in_array($usuario, $usuariosProtegidos, true)) {
                header('Location: index.php?pagina=admin_clientes&msg=' . urlencode('No se puede eliminar la cuenta ' . $usuario . ' porque es una cuenta protegida.'));
                exit();
            }

            $okCliente = $clienteModel->eliminarClienteYCuentaSegura($ci, $usuario);
            if ($okCliente) {
                header('Location: index.php?pagina=admin_clientes&msg=' . urlencode('Cliente eliminado correctamente.'));
                exit();
            }

            header('Location: index.php?pagina=admin_clientes&msg=' . urlencode('No se pudo eliminar el cliente.'));
            exit();
        }

        if (isset($_GET['eliminar_cuenta'])) {
            $usuario = trim($_GET['eliminar_cuenta']);

            if (in_array($usuario, $usuariosProtegidos, true)) {
                header('Location: index.php?pagina=admin_clientes&msg=' . urlencode('No se puede eliminar la cuenta ' . $usuario . ' porque es una cuenta protegida.'));
                exit();
            }

            if ($cuentaModel->tieneClienteAsociado($usuario)) {
                header('Location: index.php?pagina=admin_clientes&msg=' . urlencode('No se puede eliminar la cuenta: tiene cliente asociado.'));
                exit();
            }

            $okCuenta = $cuentaModel->eliminar($usuario);
            $msg = $okCuenta ? 'Cuenta eliminada correctamente.' : 'No se pudo eliminar la cuenta.';
            header('Location: index.php?pagina=admin_clientes&msg=' . urlencode($msg));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? 'crear';

            if ($accion === 'crear') {
                $usuario = trim($_POST['usuario'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $ci = trim($_POST['ci'] ?? '');
                $nombres = trim($_POST['nombres'] ?? '');
                $apPaterno = trim($_POST['apPaterno'] ?? '');
                $apMaterno = trim($_POST['apMaterno'] ?? '');
                $correo = trim($_POST['correo'] ?? '');
                $direccion = trim($_POST['direccion'] ?? '');
                $nroCelular = trim($_POST['nroCelular'] ?? '');

                if ($usuario !== '' && $password !== '' && $ci !== '' && $nombres !== '' && $apPaterno !== '' && $apMaterno !== '' && $correo !== '' && $direccion !== '' && $nroCelular !== '') {
                    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                    $okCreacion = $clienteModel->crearConCuenta($usuario, $passwordHash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular);
                    if ($okCreacion) {
                        $mensaje = 'Cliente y cuenta creados correctamente.';
                    } else {
                        $mensaje = 'No se pudo crear la cuenta (puede existir ya el usuario).';
                    }
                }
            }

            if ($accion === 'editar') {
                $usuarioCuenta = trim($_POST['usuarioCuenta'] ?? '');
                $ci = trim($_POST['ci'] ?? '');
                $password = trim($_POST['password'] ?? '');
                $nombres = trim($_POST['nombres'] ?? '');
                $apPaterno = trim($_POST['apPaterno'] ?? '');
                $apMaterno = trim($_POST['apMaterno'] ?? '');
                $correo = trim($_POST['correo'] ?? '');
                $direccion = trim($_POST['direccion'] ?? '');
                $nroCelular = trim($_POST['nroCelular'] ?? '');

                if ($usuarioCuenta !== '' && $ci !== '' && $nombres !== '' && $apPaterno !== '' && $apMaterno !== '' && $correo !== '' && $direccion !== '' && $nroCelular !== '') {
                    $passwordHash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '';
                    $okActualizacion = $clienteModel->actualizarConPassword($ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $direccion, $nroCelular, $passwordHash);
                    $mensaje = $okActualizacion ? 'Cliente actualizado correctamente.' : 'No se pudo actualizar el cliente.';
                }
            }
        }

        if (isset($_GET['editar_ci'], $_GET['editar_usuario'])) {
            $clienteEditar = $clienteModel->obtenerPorClave($_GET['editar_ci'], $_GET['editar_usuario']);
            if ($clienteEditar) {
                $clienteEditar['password'] = '';
            }
        }

        $cuentas = $cuentaModel->obtenerTodas();
        $clientes = $clienteModel->obtenerTodos();

        $titulo = 'Administracion - Clientes';
        require_once __DIR__ . '/../vistas/admin_clientes.php';
    }

    private function procesarImagen(): string {
        $imagen = trim($_POST['imagen'] ?? 'producto.png');
        if (!empty($_FILES['imagen_file']['name'])) {
            $ext      = strtolower(pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION));
            $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($ext, $permitidos)
                && $_FILES['imagen_file']['size'] <= 5 * 1024 * 1024
                && $_FILES['imagen_file']['error'] === 0) {
                $nombreArchivo = uniqid('img_', true) . '.' . $ext;
                $destino = __DIR__ . '/../recursos/imagenes/' . $nombreArchivo;
                if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $destino)) {
                    $imagen = $nombreArchivo;
                }
            }
        }
        return $imagen;
    }

    public function productos() {
        $this->validarAutenticacion();

        $productoModel = new Producto();
        $stockModel    = new DetalleProductoSucursal();
        $categoriaModel = new Categoria();

        // Eliminar producto
        if (isset($_GET['eliminar_producto'])) {
            $productoModel->eliminar((int)$_GET['eliminar_producto']);
            header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto eliminado.'));
            exit();
        }

        // Eliminar stock por sucursal
        if (isset($_GET['eliminar_stock_producto'], $_GET['eliminar_stock_sucursal'])) {
            $stockModel->eliminar((int)$_GET['eliminar_stock_producto'], (int)$_GET['eliminar_stock_sucursal']);
            header('Location: /admin/index.php?page=productos&msg=' . urlencode('Stock eliminado.'));
            exit();
        }

        $productos  = $productoModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $mensaje    = isset($_GET['msg']) ? trim($_GET['msg']) : null;
        $titulo     = 'Productos';
        require_once __DIR__ . '/../vistas/admin_productos.php';
    }

    public function productosCrear() {
        $this->validarAutenticacion();

        $productoModel = new Producto();
        $marcaModel    = new Marca();
        $categoriaModel = new Categoria();
        $industriaModel = new Industria();
        $sucursalModel  = new Sucursal();
        $stockModel     = new DetalleProductoSucursal();
        $error          = null;
        $esEditar       = false;
        $producto       = [];
        $stocks         = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre       = trim($_POST['nombre']       ?? '');
            $descripcion  = trim($_POST['descripcion']  ?? '');
            $precio       = (float)($_POST['precio']    ?? 0);
            $estado       = trim($_POST['estado']       ?? 'activo');
            $codMarca     = (int)($_POST['codMarca']    ?? 0);
            $codIndustria = (int)($_POST['codIndustria'] ?? 0);
            $codCategoria = (int)($_POST['codCategoria'] ?? 0);
            $imagen       = $this->procesarImagen();

            if ($nombre === '' || $descripcion === '' || $precio <= 0 || $codMarca === 0 || $codIndustria === 0 || $codCategoria === 0) {
                $error = 'Completa todos los campos obligatorios.';
            } else {
                $productoModel->agregar($nombre, $descripcion, $precio, $imagen, $codMarca, $codIndustria, $codCategoria, $estado);

                // Asignar stock inicial si se indicó sucursal
                $codSucursal   = (int)($_POST['codSucursal']   ?? 0);
                $stockInicial  = (int)($_POST['stock_inicial'] ?? 0);
                if ($codSucursal > 0 && $stockInicial > 0) {
                    $nuevoId = $productoModel->obtenerUltimoId();
                    if ($nuevoId) {
                        $stockModel->guardarStock($nuevoId, $codSucursal, $stockInicial);
                    }
                }

                header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto creado correctamente.'));
                exit();
            }
        }

        $marcas     = $marcaModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $industrias = $industriaModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();
        $titulo     = 'Nuevo Producto';
        require_once __DIR__ . '/../vistas/admin_productos_form.php';
    }

    public function productosEditar() {
        $this->validarAutenticacion();

        $productoModel  = new Producto();
        $marcaModel     = new Marca();
        $categoriaModel = new Categoria();
        $industriaModel = new Industria();
        $sucursalModel  = new Sucursal();
        $stockModel     = new DetalleProductoSucursal();
        $error          = null;
        $esEditar       = true;
        $id             = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /admin/index.php?page=productos');
            exit();
        }

        $producto = $productoModel->obtenerPorId($id);
        if (!$producto) {
            header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto no encontrado.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nombre       = trim($_POST['nombre']       ?? '');
            $descripcion  = trim($_POST['descripcion']  ?? '');
            $precio       = (float)($_POST['precio']    ?? 0);
            $estado       = trim($_POST['estado']       ?? 'activo');
            $codMarca     = (int)($_POST['codMarca']    ?? 0);
            $codIndustria = (int)($_POST['codIndustria'] ?? 0);
            $codCategoria = (int)($_POST['codCategoria'] ?? 0);
            $imagen       = $this->procesarImagen();

            if ($nombre === '' || $descripcion === '' || $precio <= 0 || $codMarca === 0 || $codIndustria === 0 || $codCategoria === 0) {
                $error = 'Completa todos los campos obligatorios.';
            } else {
                $productoModel->actualizar($id, $nombre, $descripcion, $precio, $imagen, $codMarca, $codIndustria, $codCategoria, $estado);

                // Asignar stock adicional si se indicó sucursal
                $codSucursal  = (int)($_POST['codSucursal']   ?? 0);
                $stockInicial = (int)($_POST['stock_inicial'] ?? 0);
                if ($codSucursal > 0 && $stockInicial > 0) {
                    $stockModel->guardarStock($id, $codSucursal, $stockInicial);
                }

                header('Location: /admin/index.php?page=productos&msg=' . urlencode('Producto actualizado correctamente.'));
                exit();
            }

            // Si hay error, recargar datos
            $producto = array_merge($producto, [
                'nombre' => $nombre, 'descripcion' => $descripcion,
                'precio' => $precio, 'estado' => $estado,
                'codMarca' => $codMarca, 'codIndustria' => $codIndustria,
                'codCategoria' => $codCategoria, 'imagen' => $imagen,
            ]);
        }

        $marcas     = $marcaModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $industrias = $industriaModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();
        $stocks     = $stockModel->obtenerTodos();
        $titulo     = 'Editar Producto';
        require_once __DIR__ . '/../vistas/admin_productos_form.php';
    }

    public function ventas() {
        $this->validarAutenticacion();

        $notaModel = new NotaVenta();
        $mensaje   = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] === 'cambiar_estado') {
            $nro    = (int)($_POST['nro'] ?? 0);
            $estado = trim($_POST['estado'] ?? '');
            $permitidos = ['pendiente', 'procesando', 'enviado', 'entregado', 'cancelado', 'facturado'];
            if ($nro > 0 && in_array($estado, $permitidos)) {
                $notaModel->actualizarEstado($nro, $estado);
            }
            header('Location: index.php?pagina=admin_ventas');
            exit();
        }

        $ventas = $notaModel->obtenerTodasConResumen();

        $detalles = [];
        $clientesExtra = [];
        $db = \Database::conectar();
        foreach ($ventas as $venta) {
            $nro = (int)$venta['nro'];
            $detalles[$nro] = $notaModel->obtenerDetallesPorNota($nro);
            $ci = $venta['ciCliente'];
            if (!isset($clientesExtra[$ci])) {
                $stmt = $db->prepare("SELECT correo, direccion, nroCelular FROM Cliente WHERE ci = ?");
                $stmt->bind_param('s', $ci);
                $stmt->execute();
                $res = $stmt->get_result();
                $clientesExtra[$ci] = $res ? $res->fetch_assoc() : [];
                $stmt->close();
            }
        }

        $titulo = 'Administracion - Ventas';
        require_once __DIR__ . '/../vistas/admin_ventas.php';
    }

    public function dashboard() {
        $this->validarAutenticacion();
        $db = \Database::conectar();

        // Métricas generales
        $stmt = $db->prepare("CALL sp_resumen_dashboard()");
        $stmt->execute();
        $res  = $stmt->get_result();
        $dash = $res ? $res->fetch_assoc() : [];
        $stmt->close();
        while ($db->more_results() && $db->next_result()) { $r = $db->use_result(); if ($r) $r->free(); }

        // Productos más vendidos
        $stmt = $db->prepare("CALL sp_productos_mas_vendidos(5)");
        $stmt->execute();
        $res  = $stmt->get_result();
        $topProductos = $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
        $stmt->close();
        while ($db->more_results() && $db->next_result()) { $r = $db->use_result(); if ($r) $r->free(); }

        // Stock crítico rápido
        $msModel     = new MovimientoStock();
        $stockCritico = $msModel->obtenerStockCritico(5);

        // Últimas ventas
        $notaModel = new NotaVenta();
        $ultimasVentas = array_slice($notaModel->obtenerTodasConResumen(), 0, 8);

        $titulo = 'Dashboard';
        require_once __DIR__ . '/../vistas/admin_dashboard.php';
    }

    public function almacen() {
        $this->validarAutenticacion();

        $msModel       = new MovimientoStock();
        $traspasoModel = new Traspaso();
        $sucursalModel = new Sucursal();
        $productoModel = new Producto();

        $stockCritico  = $msModel->obtenerStockCritico(5);
        $sucursales    = $sucursalModel->obtenerTodas();
        $productos     = $productoModel->obtenerTodos();
        $traspasos     = $traspasoModel->listarTodos();

        // Métricas para tarjetas del dashboard
        $stockActual   = $msModel->obtenerStockActual();
        $totalProductos = count(array_unique(array_column($stockActual, 'codProducto')));
        $stockTotal     = array_sum(array_column($stockActual, 'stockActual'));
        $stockComp      = array_sum(array_column($stockActual, 'stockComprometido'));
        $totalCriticos  = count($stockCritico);

        $titulo = 'Administración - Almacén';
        require_once __DIR__ . '/../vistas/admin_almacen.php';
    }

    public function vendedores() {
        $this->validarAutenticacion();

        $vendedorModel = new Vendedor();
        $mensaje = isset($_GET['msg']) ? trim($_GET['msg']) : null;

        if (isset($_GET['eliminar_ci'], $_GET['eliminar_usuario'])) {
            $ci      = trim($_GET['eliminar_ci']);
            $usuario = trim($_GET['eliminar_usuario']);

            if ($usuario === 'admin') {
                header('Location: /admin/index.php?page=vendedores&msg=' . urlencode('No se puede eliminar la cuenta admin.'));
                exit();
            }

            $ok  = $vendedorModel->eliminarVendedorYCuenta($ci, $usuario);
            $msg = $ok ? 'Vendedor eliminado correctamente.' : 'No se pudo eliminar el vendedor.';
            header('Location: /admin/index.php?page=vendedores&msg=' . urlencode($msg));
            exit();
        }

        $vendedores = $vendedorModel->obtenerTodos();
        $titulo     = 'Vendedores';
        require_once __DIR__ . '/../vistas/admin_vendedores.php';
    }

    public function vendedoresCrear() {
        $this->validarAutenticacion();

        $vendedorModel = new Vendedor();
        $esEditar      = false;
        $error         = null;
        $vendedor      = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario    = trim($_POST['usuario']    ?? '');
            $password   = trim($_POST['password']   ?? '');
            $ci         = trim($_POST['ci']         ?? '');
            $nombres    = trim($_POST['nombres']    ?? '');
            $apPaterno  = trim($_POST['apPaterno']  ?? '');
            $apMaterno  = trim($_POST['apMaterno']  ?? '');
            $correo     = trim($_POST['correo']     ?? '');
            $nroCelular = trim($_POST['nroCelular'] ?? '');

            if ($usuario === '' || $password === '' || $ci === '' || $nombres === '' || $apPaterno === '' || $apMaterno === '' || $correo === '' || $nroCelular === '') {
                $error    = 'Completa todos los campos obligatorios.';
                $vendedor = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular');
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ok   = $vendedorModel->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular);
                if ($ok) {
                    header('Location: /admin/index.php?page=vendedores&msg=' . urlencode('Vendedor creado correctamente.'));
                    exit();
                }
                $error    = 'No se pudo crear el vendedor. El usuario o CI ya existe.';
                $vendedor = compact('usuario', 'ci', 'nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular');
            }
        }

        $titulo = 'Nuevo Vendedor';
        require_once __DIR__ . '/../vistas/admin_vendedores_form.php';
    }

    public function vendedoresEditar() {
        $this->validarAutenticacion();

        $vendedorModel = new Vendedor();
        $esEditar      = true;
        $error         = null;
        $ci            = trim($_GET['ci']      ?? '');
        $usuario       = trim($_GET['usuario'] ?? '');

        if ($ci === '' || $usuario === '') {
            header('Location: /admin/index.php?page=vendedores');
            exit();
        }

        $vendedor = $vendedorModel->obtenerPorClave($ci, $usuario);
        if (!$vendedor) {
            header('Location: /admin/index.php?page=vendedores&msg=' . urlencode('Vendedor no encontrado.'));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuarioCuenta = trim($_POST['usuarioCuenta'] ?? '');
            $ciPost        = trim($_POST['ci']            ?? '');
            $nombres       = trim($_POST['nombres']       ?? '');
            $apPaterno     = trim($_POST['apPaterno']     ?? '');
            $apMaterno     = trim($_POST['apMaterno']     ?? '');
            $correo        = trim($_POST['correo']        ?? '');
            $nroCelular    = trim($_POST['nroCelular']    ?? '');
            $password      = trim($_POST['password']      ?? '');

            if ($nombres === '' || $apPaterno === '' || $apMaterno === '' || $correo === '' || $nroCelular === '') {
                $error    = 'Completa todos los campos obligatorios.';
                $vendedor = array_merge($vendedor, compact('nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular'));
            } else {
                $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '';
                $ok   = $vendedorModel->actualizarConPassword($ciPost, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $hash);
                if ($ok) {
                    header('Location: /admin/index.php?page=vendedores&msg=' . urlencode('Vendedor actualizado correctamente.'));
                    exit();
                }
                $error    = 'No se pudo actualizar el vendedor.';
                $vendedor = array_merge($vendedor, compact('nombres', 'apPaterno', 'apMaterno', 'correo', 'nroCelular'));
            }
        }

        $titulo = 'Editar Vendedor';
        require_once __DIR__ . '/../vistas/admin_vendedores_form.php';
    }
}
