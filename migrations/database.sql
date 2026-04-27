CREATE DATABASE IF NOT EXISTS comercio_electronico;
USE comercio_electronico;

CREATE TABLE productos (
    id_producto INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255),
    stock INT NOT NULL DEFAULT 0
);

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

-- Usuario administrador de ejemplo
INSERT INTO usuarios (usuario, password) VALUES ('administrador', '12345');

-- Producto de ejemplo
INSERT INTO productos (nombre, descripcion, precio, imagen, stock) VALUES ('Sudadera', 'Sudadera de algodón unisex', 250.00, 'sudadera.png', 10);
