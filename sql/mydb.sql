-- =========================================
-- CREACIÓN DE BASE DE DATOS
-- =========================================
DROP DATABASE IF EXISTS mydb;
CREATE DATABASE mydb CHARACTER SET utf8mb4;
USE mydb;

-- =========================================
-- TABLA: Cuenta
-- =========================================
CREATE TABLE Cuenta (
  usuario VARCHAR(40) NOT NULL,
  password VARCHAR(100) NOT NULL,
  rol VARCHAR(20) NOT NULL DEFAULT 'cliente' COMMENT 'admin, trabajador, cliente',
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  estado VARCHAR(20) DEFAULT 'activo',
  PRIMARY KEY (usuario)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: Cliente
-- =========================================
CREATE TABLE Cliente (
  ci VARCHAR(20) NOT NULL,
  nombres VARCHAR(50) NOT NULL,
  apPaterno VARCHAR(20) NOT NULL,
  apMaterno VARCHAR(20) NOT NULL,
  correo VARCHAR(50) NOT NULL,
  direccion VARCHAR(100) NOT NULL,
  nroCelular VARCHAR(20) NOT NULL,
  usuarioCuenta VARCHAR(40) NOT NULL,
  PRIMARY KEY (ci),
  CONSTRAINT fk_cliente_cuenta
    FOREIGN KEY (usuarioCuenta)
    REFERENCES Cuenta(usuario)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: NotaVenta
-- =========================================
CREATE TABLE NotaVenta (
  nro INT AUTO_INCREMENT,
  fechaHora DATETIME NOT NULL,
  ciCliente VARCHAR(20) NOT NULL,
  PRIMARY KEY (nro),
  CONSTRAINT fk_notaventa_cliente
    FOREIGN KEY (ciCliente)
    REFERENCES Cliente(ci)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: Industria
-- =========================================
CREATE TABLE Industria (
  cod INT AUTO_INCREMENT,
  nombre VARCHAR(30) NOT NULL,
  PRIMARY KEY (cod)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: Marca
-- =========================================
CREATE TABLE Marca (
  cod INT AUTO_INCREMENT,
  nombre VARCHAR(30),
  PRIMARY KEY (cod)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: Categoria
-- =========================================
CREATE TABLE Categoria (
  cod INT AUTO_INCREMENT,
  nombre VARCHAR(30),
  PRIMARY KEY (cod)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: Producto
-- =========================================
CREATE TABLE Producto (
  cod INT AUTO_INCREMENT,
  nombre VARCHAR(50) NOT NULL,
  descripcion VARCHAR(200) NOT NULL,
  precio DECIMAL(10,2) NOT NULL,
  imagen VARCHAR(200),
  estado VARCHAR(20) NOT NULL,
  codMarca INT NOT NULL,
  codIndustria INT NOT NULL,
  codCategoria INT NOT NULL,
  PRIMARY KEY (cod),
  CONSTRAINT fk_producto_marca
    FOREIGN KEY (codMarca) REFERENCES Marca(cod),
  CONSTRAINT fk_producto_industria
    FOREIGN KEY (codIndustria) REFERENCES Industria(cod),
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (codCategoria) REFERENCES Categoria(cod)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: DetalleNotaVenta
-- =========================================
CREATE TABLE DetalleNotaVenta (
  item INT AUTO_INCREMENT,
  nroNotaVenta INT NOT NULL,
  codProducto INT NOT NULL,
  cant INT NOT NULL,
  PRIMARY KEY (item),
  CONSTRAINT fk_detalle_notaventa
    FOREIGN KEY (nroNotaVenta) REFERENCES NotaVenta(nro),
  CONSTRAINT fk_detalle_producto
    FOREIGN KEY (codProducto) REFERENCES Producto(cod)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: Sucursal
-- =========================================
CREATE TABLE Sucursal (
  cod INT AUTO_INCREMENT,
  nombre VARCHAR(30) NOT NULL,
  direccion VARCHAR(100) NOT NULL,
  nroTelefono VARCHAR(20),
  PRIMARY KEY (cod)
) ENGINE=InnoDB;

-- =========================================
-- TABLA: DetalleProductoSucursal
-- =========================================
CREATE TABLE DetalleProductoSucursal (
  codProducto INT NOT NULL,
  codSucursal INT NOT NULL,
  stock INT NOT NULL,
  PRIMARY KEY (codProducto, codSucursal),
  CONSTRAINT fk_dps_producto
    FOREIGN KEY (codProducto) REFERENCES Producto(cod),
  CONSTRAINT fk_dps_sucursal
    FOREIGN KEY (codSucursal) REFERENCES Sucursal(cod)
) ENGINE=InnoDB;