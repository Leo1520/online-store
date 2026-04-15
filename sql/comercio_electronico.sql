CREATE DATABASE comercio_electronico;
USE comercio_electronico;

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255),
    stock INT NOT NULL DEFAULT 0,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE ordenes (
    id_orden INT AUTO_INCREMENT PRIMARY KEY,
    total DECIMAL(10, 2) NOT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado VARCHAR(50) DEFAULT 'completada',
    cliente_nombre VARCHAR(100),
    cliente_email VARCHAR(100),
    cliente_telefono VARCHAR(20),
    cliente_direccion TEXT
);

CREATE TABLE detalles_orden (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_orden INT NOT NULL,
    id_producto INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (id_orden) REFERENCES ordenes(id_orden) ON DELETE CASCADE,
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
);

-- Insertar usuario administrador con contraseña hasheada (administrador / 12345)
INSERT INTO usuarios (usuario, password) VALUES ('administrador', '$2y$10$HK4XUXxBH3vVHMz6.qiH.u6p1.pQZPY0.8V.t.m5YUmLQM/AGr1Rm');