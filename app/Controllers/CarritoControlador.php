<?php
require_once __DIR__ . '/Controlador.php';
require_once __DIR__ . '/../Models/NotaVenta.php';
require_once __DIR__ . '/../Models/DetalleNotaVenta.php';
require_once __DIR__ . '/../Models/Producto.php';

/**
 * Controlador de Carrito de Compras
 */
class CarritoControlador extends Controlador {

    /**
     * Muestra el carrito
     */
    public function mostrar() {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $modeloProducto = new Producto($this->conexion);
        $productos = [];

        foreach ($_SESSION['carrito'] as $codProducto => $cantidad) {
            $producto = $modeloProducto->obtenerPorId($codProducto);
            if ($producto) {
                $producto['cantidad'] = $cantidad;
                $producto['subtotal'] = $producto['precio'] * $cantidad;
                $productos[] = $producto;
            }
        }

        $total = array_sum(array_map(function($p) { return $p['subtotal']; }, $productos));

        $this->cargarVista('Carrito/mostrar', [
            'productos' => $productos,
            'total' => $total
        ]);
    }

    /**
     * Añade un producto al carrito
     */
    public function agregar($codProducto) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $modeloProducto = new Producto($this->conexion);
        $producto = $modeloProducto->obtenerPorId($codProducto);

        if (!$producto) {
            $_SESSION['error'] = 'Producto no encontrado.';
            $this->redirigir('index.php');
        }

        $cantidad = intval($_POST['cantidad'] ?? 1);

        if (isset($_SESSION['carrito'][$codProducto])) {
            $_SESSION['carrito'][$codProducto] += $cantidad;
        } else {
            $_SESSION['carrito'][$codProducto] = $cantidad;
        }

        $_SESSION['mensaje'] = 'Producto añadido al carrito.';
        $this->redirigir($_GET['referrer'] ?? 'index.php');
    }

    /**
     * Actualiza la cantidad de un producto en el carrito
     */
    public function actualizar($codProducto) {
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $cantidad = intval($_POST['cantidad'] ?? 0);

        if ($cantidad <= 0) {
            unset($_SESSION['carrito'][$codProducto]);
        } else {
            $_SESSION['carrito'][$codProducto] = $cantidad;
        }

        $_SESSION['mensaje'] = 'Carrito actualizado.';
        $this->redirigir('?accion=mostrar');
    }

    /**
     * Elimina un producto del carrito
     */
    public function eliminar($codProducto) {
        if (isset($_SESSION['carrito'][$codProducto])) {
            unset($_SESSION['carrito'][$codProducto]);
        }

        $_SESSION['mensaje'] = 'Producto eliminado del carrito.';
        $this->redirigir('?accion=mostrar');
    }

    /**
     * Vacía el carrito
     */
    public function vaciar() {
        $_SESSION['carrito'] = [];
        $_SESSION['mensaje'] = 'Carrito vaciado.';
        $this->redirigir('?accion=mostrar');
    }

    /**
     * Procesa el checkout
     */
    public function checkout() {
        if (!isset($_SESSION['usuario'])) {
            $_SESSION['error'] = 'Debes iniciar sesión para comprar.';
            $this->redirigir('?controlador=autenticacion&accion=login');
        }

        if (empty($_SESSION['carrito'])) {
            $_SESSION['error'] = 'Tu carrito está vacío.';
            $this->redirigir('?accion=mostrar');
        }

        $usuario = $_SESSION['usuario'];
        $modeloCliente = new \Cliente($this->conexion);
        $cliente = $modeloCliente->obtenerPorUsuario($usuario);

        if (!$cliente) {
            $_SESSION['error'] = 'Datos de cliente no encontrados.';
            $this->redirigir('?accion=mostrar');
        }

        // Crear la nota de venta
        $modeloNotaVenta = new NotaVenta($this->conexion);
        $modeloNotaVenta->crear($cliente['ci']);
        $nroNotaVenta = $modeloNotaVenta->ultimoId();

        // Añadir los detalles
        $modeloDetalleNotaVenta = new DetalleNotaVenta($this->conexion);
        $modeloProducto = new Producto($this->conexion);

        foreach ($_SESSION['carrito'] as $codProducto => $cantidad) {
            $producto = $modeloProducto->obtenerPorId($codProducto);
            if ($producto) {
                $modeloDetalleNotaVenta->crear($nroNotaVenta, $codProducto, $cantidad);
            }
        }

        // Vaciar carrito y redirigir a pago
        $_SESSION['carrito'] = [];
        $_SESSION['nroNotaVenta'] = $nroNotaVenta;
        $_SESSION['mensaje'] = 'Orden creada. Por favor completa el pago.';
        $this->redirigir('?controlador=pago&accion=mostrar&nro=' . $nroNotaVenta);
    }
}
?>
