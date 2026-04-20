<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../modelos/Producto.php';
require_once __DIR__ . '/../modelos/Categoria.php';

$nombre       = trim($_GET['nombre']   ?? '');
$codCategoria = (int)($_GET['categoria'] ?? 0);
$precioMin    = (float)($_GET['precioMin'] ?? 0);
$precioMax    = (float)($_GET['precioMax'] ?? 0);
$orden        = trim($_GET['orden']    ?? '');

$ordenesPermitidos = ['precio_asc', 'precio_desc', 'nombre_asc', 'nombre_desc', ''];
if (!in_array($orden, $ordenesPermitidos)) $orden = '';

$hayFiltros = $nombre !== '' || $codCategoria > 0 || $precioMin > 0 || $precioMax > 0 || $orden !== '';

$productoModel = new Producto();
$productos = $hayFiltros
    ? $productoModel->buscar($nombre, $codCategoria, $precioMin, $precioMax, $orden)
    : $productoModel->obtenerTodos();

$categoriaModel = new Categoria();
$categorias = $categoriaModel->obtenerTodos();

echo json_encode([
    'ok'         => true,
    'productos'  => $productos,
    'categorias' => $categorias,
    'total'      => count($productos),
]);
