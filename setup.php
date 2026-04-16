<?php
/**
 * Crear datos de prueba para la tienda
 * Ejecutar una sola vez: http://localhost/online-store/setup.php
 */

session_start();
require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conexion = $db->conectar();

// Verificar si ya existen datos
$resultado = $conexion->query("SELECT COUNT(*) as total FROM Marca");
$fila = $resultado->fetch_assoc();

if ($fila['total'] > 0) {
    die("
    <div style='font-family: Arial; padding: 50px; text-align: center;'>
        <h2>⚠️ La base de datos ya tiene datos</h2>
        <p>Los datos de prueba ya han sido cargados previamente.</p>
        <a href='public/index.php' style='background: #667eea; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;'>
            Ir a la Tienda
        </a>
    </div>
    ");
}

// Crear datos de prueba
try {
    // 1. Crear Marcas
    $marcas = ['Samsung', 'LG', 'Sony', 'Apple', 'Dell', 'HP', 'Lenovo', 'ASUS'];
    foreach ($marcas as $marca) {
        $conexion->query("INSERT INTO Marca (nombre) VALUES ('$marca')");
    }

    // 2. Crear Categorías
    $categorias = ['Electrónica', 'Computadoras', 'Smartphones', 'Accesorios', 'Audio', 'Monitores', 'Impresoras'];
    foreach ($categorias as $categoria) {
        $conexion->query("INSERT INTO Categoria (nombre) VALUES ('$categoria')");
    }

    // 3. Crear Industrias
    $industrias = ['Tecnología', 'Electrónica de Consumo', 'Informática', 'Telecomunicaciones', 'Electrónica Industrial'];
    foreach ($industrias as $industria) {
        $conexion->query("INSERT INTO Industria (nombre) VALUES ('$industria')");
    }

    // 4. Crear Sucursales
    $conexion->query("INSERT INTO Sucursal (nombre, direccion, nroTelefono) VALUES ('Sucursal Central', 'Calle Principal 123', '555-0001')");
    $conexion->query("INSERT INTO Sucursal (nombre, direccion, nroTelefono) VALUES ('Sucursal Centro', 'Av. Comercio 456', '555-0002')");
    $conexion->query("INSERT INTO Sucursal (nombre, direccion, nroTelefono) VALUES ('Sucursal Este', 'Calle Este 789', '555-0003')");

    // 5. Crear Productos
    $productos = [
        ['Televisor Samsung 55"', 'Televisor LED Full HD Samsung 55 pulgadas', 899.99, 'Activo', 1, 1, 1],
        ['Laptop Dell XPS 13', 'Laptop ultradelgada con procesador Intel i7', 1299.99, 'Activo', 6, 2, 2],
        ['iPhone 14 Pro', 'Último modelo de Apple con A16 Bionic', 999.99, 'Activo', 4, 2, 3],
        ['Monitor LG 27"', 'Monitor 4K IPS LG de 27 pulgadas', 399.99, 'Activo', 2, 1, 6],
        ['Parlante Bluetooth Sony', 'Parlante portátil con sonido envolvente', 149.99, 'Activo', 3, 1, 5],
        ['Mouse Inalámbrico', 'Mouse óptico inalámbrico USB', 29.99, 'Activo', 8, 1, 4],
        ['Teclado Mecánico', 'Teclado gaming RGB mecánico', 79.99, 'Activo', 8, 1, 4],
        ['Webcam HD 1080p', 'Cámara web para videoconferencias', 59.99, 'Activo', 8, 1, 4],
        ['Cable HDMI 2M', 'Cable HDMI de alta velocidad 2 metros', 9.99, 'Activo', 8, 1, 4],
        ['Hub USB 3.0', 'Concentrador USB con 7 puertos', 34.99, 'Activo', 8, 1, 4],
    ];

    foreach ($productos as $p) {
        $conexion->query("INSERT INTO Producto (nombre, descripcion, precio, estado, codMarca, codIndustria, codCategoria) 
                         VALUES ('{$p[0]}', '{$p[1]}', {$p[2]}, '{$p[3]}', {$p[4]}, {$p[5]}, {$p[6]})");
    }

    // 6. Crear Stock para todos los productos en todas las sucursales
    for ($cod = 1; $cod <= 10; $cod++) {
        for ($sucursal = 1; $sucursal <= 3; $sucursal++) {
            $stock = rand(5, 20);
            $conexion->query("INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock) VALUES ($cod, $sucursal, $stock)");
        }
    }

    // 7. Crear cuenta de administrador
    $admin_password = password_hash('admin123', PASSWORD_BCRYPT);
    $conexion->query("INSERT INTO Cuenta (usuario, password) VALUES ('admin', '$admin_password')");

    // 8. Crear cuenta de cliente de prueba
    $cliente_password = password_hash('cliente123', PASSWORD_BCRYPT);
    $conexion->query("INSERT INTO Cuenta (usuario, password) VALUES ('cliente1', '$cliente_password')");
    $conexion->query("INSERT INTO Cliente (ci, nombres, apPaterno, apMaterno, correo, direccion, nroCelular, usuarioCuenta) 
                     VALUES ('1234567', 'Juan', 'Pérez', 'García', 'juan@example.com', 'Calle 123 Apt 4', '555-1234', 'cliente1')");

    echo "
    <div style='font-family: Arial; padding: 50px; text-align: center;'>
        <h2 style='color: #4CAF50;'>✅ ¡Datos de prueba cargados correctamente!</h2>
        <p style='font-size: 16px; margin: 20px 0;'>
            Los datos de la tienda han sido inicializados. Puedes acceder con:
        </p>
        <div style='background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0;'>
            <p><strong>Usuario Administrador:</strong><br>
               Usuario: <code>admin</code><br>
               Contraseña: <code>admin123</code>
            </p>
            <hr>
            <p><strong>Usuario Cliente:</strong><br>
               Usuario: <code>cliente1</code><br>
               Contraseña: <code>cliente123</code>
            </p>
        </div>
        <a href='public/index.php' style='display: inline-block; background: #667eea; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-size: 16px; margin-top: 20px;'>
            Ir a la Tienda
        </a>
        <p style='margin-top: 30px; color: #999;'>
            Los datos de prueba se cargan automáticamente solo si la BD está vacía.
        </p>
    </div>
    ";

    $db->cerrar();

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
