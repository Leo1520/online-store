<?php
require_once __DIR__ . '/../../modelos/MovimientoStock.php';
require_once __DIR__ . '/../../modelos/Traspaso.php';
require_once __DIR__ . '/../../modelos/Sucursal.php';
require_once __DIR__ . '/../../modelos/Producto.php';

class AlmacenControlador {

    public function stockActual() {
        requierePermiso('ver_almacen');
        $msModel       = new MovimientoStock();
        $sucursalModel = new Sucursal();

        $stockActual    = $msModel->obtenerStockActual();
        $totalProductos = count(array_unique(array_column($stockActual, 'codProducto')));
        $stockTotal     = array_sum(array_column($stockActual, 'stockActual'));
        $stockComp      = array_sum(array_column($stockActual, 'stockComprometido'));
        $stockCritico   = $msModel->obtenerStockCritico(5);
        $totalCriticos  = count($stockCritico);
        $sucursales     = $sucursalModel->obtenerTodas();

        $titulo = 'Stock Actual';
        require_once __DIR__ . '/../../vistas/admin_almacen.php';
    }

    public function kardex() {
        requierePermiso('ver_almacen');
        $productoModel = new Producto();
        $sucursalModel = new Sucursal();

        $productos  = $productoModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();

        $titulo = 'Kardex';
        require_once __DIR__ . '/../../vistas/admin_almacen_kardex.php';
    }

    public function traspasos() {
        requierePermiso('gestionar_almacen');
        $sucursalModel = new Sucursal();
        $productoModel = new Producto();

        $sucursales = $sucursalModel->obtenerTodas();
        $productos  = $productoModel->obtenerTodos();

        $titulo = 'Traspasos';
        require_once __DIR__ . '/../../vistas/admin_almacen_traspasos.php';
    }

    public function ajustes() {
        requierePermiso('gestionar_almacen');
        $productoModel = new Producto();
        $sucursalModel = new Sucursal();

        $productos  = $productoModel->obtenerTodos();
        $sucursales = $sucursalModel->obtenerTodas();

        $titulo = 'Ajustes de Inventario';
        require_once __DIR__ . '/../../vistas/admin_almacen_ajustes.php';
    }

    public function stockCritico() {
        requierePermiso('ver_almacen');
        $msModel      = new MovimientoStock();
        $stockCritico = $msModel->obtenerStockCritico(5);
        $totalCriticos = count($stockCritico);

        $titulo = 'Stock Crítico';
        require_once __DIR__ . '/../../vistas/admin_almacen_critico.php';
    }
}
