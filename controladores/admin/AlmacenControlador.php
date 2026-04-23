<?php
require_once __DIR__ . '/../../modelos/MovimientoStock.php';
require_once __DIR__ . '/../../modelos/Traspaso.php';
require_once __DIR__ . '/../../modelos/Sucursal.php';
require_once __DIR__ . '/../../modelos/Producto.php';

class AlmacenControlador {

    public function almacen() {
        $msModel       = new MovimientoStock();
        $traspasoModel = new Traspaso();
        $sucursalModel = new Sucursal();
        $productoModel = new Producto();

        $stockCritico = $msModel->obtenerStockCritico(5);
        $sucursales   = $sucursalModel->obtenerTodas();
        $productos    = $productoModel->obtenerTodos();
        $traspasos    = $traspasoModel->listarTodos();

        $stockActual    = $msModel->obtenerStockActual();
        $totalProductos = count(array_unique(array_column($stockActual, 'codProducto')));
        $stockTotal     = array_sum(array_column($stockActual, 'stockActual'));
        $stockComp      = array_sum(array_column($stockActual, 'stockComprometido'));
        $totalCriticos  = count($stockCritico);

        $titulo = 'Almacén';
        require_once __DIR__ . '/../../vistas/admin_almacen.php';
    }
}
