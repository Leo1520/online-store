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

    public function catalogos() {
        $this->validarAutenticacion();

        $marcaModel = new Marca();
        $categoriaModel = new Categoria();
        $industriaModel = new Industria();
        $mensaje = null;
        $edicion = [
            'tipo' => '',
            'cod' => 0,
            'nombre' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? 'crear';
            $tipo = $_POST['tipo'] ?? '';
            $cod = (int)($_POST['cod'] ?? 0);
            $nombre = trim($_POST['nombre'] ?? '');

            if ($nombre !== '') {
                if ($tipo === 'marca') {
                    if ($accion === 'editar' && $cod > 0) {
                        $marcaModel->actualizar($cod, $nombre);
                        $mensaje = 'Marca actualizada correctamente.';
                    } else {
                        $marcaModel->crear($nombre);
                        $mensaje = 'Marca creada correctamente.';
                    }
                } elseif ($tipo === 'categoria') {
                    if ($accion === 'editar' && $cod > 0) {
                        $categoriaModel->actualizar($cod, $nombre);
                        $mensaje = 'Categoria actualizada correctamente.';
                    } else {
                        $categoriaModel->crear($nombre);
                        $mensaje = 'Categoria creada correctamente.';
                    }
                } elseif ($tipo === 'industria') {
                    if ($accion === 'editar' && $cod > 0) {
                        $industriaModel->actualizar($cod, $nombre);
                        $mensaje = 'Industria actualizada correctamente.';
                    } else {
                        $industriaModel->crear($nombre);
                        $mensaje = 'Industria creada correctamente.';
                    }
                }
            }
        }

        if (isset($_GET['eliminar_tipo'], $_GET['cod'])) {
            $tipo = $_GET['eliminar_tipo'];
            $cod = (int)$_GET['cod'];

            if ($tipo === 'marca') {
                $marcaModel->eliminar($cod);
            } elseif ($tipo === 'categoria') {
                $categoriaModel->eliminar($cod);
            } elseif ($tipo === 'industria') {
                $industriaModel->eliminar($cod);
            }

            header('Location: index.php?pagina=admin_catalogos');
            exit();
        }

        $marcas = $marcaModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $industrias = $industriaModel->obtenerTodos();

        if (isset($_GET['editar_tipo'], $_GET['cod'])) {
            $tipo = $_GET['editar_tipo'];
            $cod = (int)$_GET['cod'];

            if ($tipo === 'marca') {
                foreach ($marcas as $fila) {
                    if ((int)$fila['cod'] === $cod) {
                        $edicion = ['tipo' => 'marca', 'cod' => $cod, 'nombre' => $fila['nombre']];
                        break;
                    }
                }
            }

            if ($tipo === 'categoria') {
                foreach ($categorias as $fila) {
                    if ((int)$fila['cod'] === $cod) {
                        $edicion = ['tipo' => 'categoria', 'cod' => $cod, 'nombre' => $fila['nombre']];
                        break;
                    }
                }
            }

            if ($tipo === 'industria') {
                foreach ($industrias as $fila) {
                    if ((int)$fila['cod'] === $cod) {
                        $edicion = ['tipo' => 'industria', 'cod' => $cod, 'nombre' => $fila['nombre']];
                        break;
                    }
                }
            }
        }

        $titulo = 'Administracion - Catalogos';
        require_once __DIR__ . '/../vistas/admin_catalogos.php';
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

    public function productos() {
        $this->validarAutenticacion();

        $productoModel = new Producto();
        $marcaModel = new Marca();
        $categoriaModel = new Categoria();
        $industriaModel = new Industria();
        $sucursalModel = new Sucursal();
        $stockModel = new DetalleProductoSucursal();
        $mensaje = null;
        $productoEditar = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            if ($accion === 'crear_producto' || $accion === 'editar_producto') {
                $nombre       = trim($_POST['nombre']       ?? '');
                $descripcion  = trim($_POST['descripcion']  ?? '');
                $precio       = (float)($_POST['precio']    ?? 0);
                $imagen       = trim($_POST['imagen']       ?? 'sudadera.png');
                $estado       = trim($_POST['estado']       ?? 'activo');
                $codMarca     = (int)($_POST['codMarca']    ?? 0);
                $codIndustria = (int)($_POST['codIndustria'] ?? 0);
                $codCategoria = (int)($_POST['codCategoria'] ?? 0);

                // Subida de imagen si se proporcionó archivo
                if (!empty($_FILES['imagen_file']['name'])) {
                    $ext      = strtolower(pathinfo($_FILES['imagen_file']['name'], PATHINFO_EXTENSION));
                    $permitidos = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $maxBytes = 5 * 1024 * 1024;

                    if (in_array($ext, $permitidos) && $_FILES['imagen_file']['size'] <= $maxBytes && $_FILES['imagen_file']['error'] === 0) {
                        $nombreArchivo = uniqid('img_', true) . '.' . $ext;
                        $destino       = __DIR__ . '/../recursos/imagenes/' . $nombreArchivo;
                        if (move_uploaded_file($_FILES['imagen_file']['tmp_name'], $destino)) {
                            $imagen = $nombreArchivo;
                        }
                    } else {
                        $mensaje = 'Imagen invalida. Use jpg, png, gif o webp de maximo 5 MB.';
                    }
                }

                if (!$mensaje && $nombre !== '' && $descripcion !== '' && $precio > 0 && $codMarca > 0 && $codIndustria > 0 && $codCategoria > 0) {
                    if ($accion === 'crear_producto') {
                        $productoModel->agregar($nombre, $descripcion, $precio, $imagen, $codMarca, $codIndustria, $codCategoria, $estado);
                        $mensaje = 'Producto creado correctamente.';
                    } else {
                        $idProducto = (int)($_POST['id_producto'] ?? 0);
                        if ($idProducto > 0) {
                            $productoModel->actualizar($idProducto, $nombre, $descripcion, $precio, $imagen, $codMarca, $codIndustria, $codCategoria, $estado);
                            $mensaje = 'Producto actualizado correctamente.';
                        }
                    }
                }
            }

            if ($accion === 'guardar_stock') {
                $codProducto = (int)($_POST['codProducto'] ?? 0);
                $codSucursal = (int)($_POST['codSucursal'] ?? 0);
                $stock = trim($_POST['stock'] ?? '0');

                if ($codProducto > 0 && $codSucursal > 0) {
                    $stockModel->guardarStock($codProducto, $codSucursal, $stock);
                    $mensaje = 'Stock guardado correctamente.';
                }
            }
        }

        if (isset($_GET['eliminar_producto'])) {
            $productoModel->eliminar((int)$_GET['eliminar_producto']);
            header('Location: index.php?pagina=admin_productos');
            exit();
        }

        if (isset($_GET['eliminar_stock_producto'], $_GET['eliminar_stock_sucursal'])) {
            $stockModel->eliminar((int)$_GET['eliminar_stock_producto'], (int)$_GET['eliminar_stock_sucursal']);
            header('Location: index.php?pagina=admin_productos');
            exit();
        }

        if (isset($_GET['editar_producto'])) {
            $productoEditar = $productoModel->obtenerPorId((int)$_GET['editar_producto']);
        }

        $productos = $productoModel->obtenerTodos();
        $marcas = $marcaModel->obtenerTodos();
        $categorias = $categoriaModel->obtenerTodos();
        $industrias = $industriaModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();
        $stocks = $stockModel->obtenerTodos();

        $titulo = 'Administracion - Productos';
        require_once __DIR__ . '/../vistas/admin_productos.php';
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
        $vendedorEditar = null;

        if (isset($_GET['eliminar_ci'], $_GET['eliminar_usuario'])) {
            $ci      = trim($_GET['eliminar_ci']);
            $usuario = trim($_GET['eliminar_usuario']);

            if ($usuario === 'admin') {
                header('Location: index.php?pagina=admin_vendedores&msg=' . urlencode('No se puede eliminar la cuenta admin.'));
                exit();
            }

            $ok = $vendedorModel->eliminarVendedorYCuenta($ci, $usuario);
            $msg = $ok ? 'Vendedor eliminado correctamente.' : 'No se pudo eliminar el vendedor.';
            header('Location: index.php?pagina=admin_vendedores&msg=' . urlencode($msg));
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion       = $_POST['accion'] ?? 'crear';
            $ci           = trim($_POST['ci'] ?? '');
            $nombres      = trim($_POST['nombres'] ?? '');
            $apPaterno    = trim($_POST['apPaterno'] ?? '');
            $apMaterno    = trim($_POST['apMaterno'] ?? '');
            $correo       = trim($_POST['correo'] ?? '');
            $nroCelular   = trim($_POST['nroCelular'] ?? '');
            $password     = trim($_POST['password'] ?? '');

            if ($accion === 'crear') {
                $usuario = trim($_POST['usuario'] ?? '');
                if ($usuario !== '' && $password !== '' && $ci !== '' && $nombres !== '' && $apPaterno !== '' && $apMaterno !== '' && $correo !== '' && $nroCelular !== '') {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $ok   = $vendedorModel->crearConCuenta($usuario, $hash, $ci, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular);
                    $mensaje = $ok ? 'Vendedor creado correctamente.' : 'No se pudo crear el vendedor (usuario o CI ya existe).';
                }
            }

            if ($accion === 'editar') {
                $usuarioCuenta = trim($_POST['usuarioCuenta'] ?? '');
                if ($usuarioCuenta !== '' && $ci !== '' && $nombres !== '' && $apPaterno !== '' && $apMaterno !== '' && $correo !== '' && $nroCelular !== '') {
                    $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : '';
                    $ok   = $vendedorModel->actualizarConPassword($ci, $usuarioCuenta, $nombres, $apPaterno, $apMaterno, $correo, $nroCelular, $hash);
                    $mensaje = $ok ? 'Vendedor actualizado correctamente.' : 'No se pudo actualizar el vendedor.';
                }
            }
        }

        if (isset($_GET['editar_ci'], $_GET['editar_usuario'])) {
            $vendedorEditar = $vendedorModel->obtenerPorClave($_GET['editar_ci'], $_GET['editar_usuario']);
        }

        $vendedores = $vendedorModel->obtenerTodos();
        $titulo = 'Administracion - Vendedores';
        require_once __DIR__ . '/../vistas/admin_vendedores.php';
    }
}
