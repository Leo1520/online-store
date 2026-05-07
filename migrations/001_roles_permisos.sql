-- ============================================================
-- Migración 001: Roles y Permisos
-- Fecha: 2026-04-26
-- ============================================================

CREATE TABLE IF NOT EXISTS `Rol` (
    `cod`         INT          AUTO_INCREMENT PRIMARY KEY,
    `nombre`      VARCHAR(30)  NOT NULL UNIQUE,
    `descripcion` VARCHAR(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `Permiso` (
    `cod`         INT          AUTO_INCREMENT PRIMARY KEY,
    `nombre`      VARCHAR(60)  NOT NULL UNIQUE,
    `descripcion` VARCHAR(150) DEFAULT NULL,
    `modulo`      VARCHAR(40)  DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `RolPermiso` (
    `codRol`     INT NOT NULL,
    `codPermiso` INT NOT NULL,
    PRIMARY KEY (`codRol`, `codPermiso`),
    FOREIGN KEY (`codRol`)     REFERENCES `Rol`(`cod`)     ON DELETE CASCADE,
    FOREIGN KEY (`codPermiso`) REFERENCES `Permiso`(`cod`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `Rol` (`nombre`, `descripcion`) VALUES
    ('admin',    'Administrador con acceso total al sistema'),
    ('vendedor', 'Acceso a ventas, pedidos y clientes'),
    ('cliente',  'Acceso a la tienda y sus compras');

INSERT IGNORE INTO `Permiso` (`nombre`, `descripcion`, `modulo`) VALUES
    ('ver_dashboard',      'Ver dashboard y estadísticas',           'Dashboard'),
    ('ver_productos',      'Ver listado de productos',               'Catálogo'),
    ('crear_productos',    'Crear nuevos productos',                 'Catálogo'),
    ('editar_productos',   'Editar productos existentes',            'Catálogo'),
    ('eliminar_productos', 'Eliminar productos',                     'Catálogo'),
    ('ver_categorias',     'Ver y gestionar categorías',             'Catálogo'),
    ('ver_marcas',         'Ver y gestionar marcas',                 'Catálogo'),
    ('ver_industrias',     'Ver y gestionar industrias',             'Catálogo'),
    ('ver_sucursales',     'Ver y gestionar sucursales',             'Catálogo'),
    ('ver_pedidos',        'Ver pedidos de clientes',                'Comercial'),
    ('gestionar_ventas',   'Gestionar historial de ventas',          'Comercial'),
    ('ver_clientes',       'Ver y gestionar clientes',               'Comercial'),
    ('ver_vendedores',     'Ver y gestionar vendedores',             'Comercial'),
    ('ver_almacen',        'Ver stock y kardex de almacén',          'Almacén'),
    ('gestionar_almacen',  'Gestionar traspasos y ajustes de stock', 'Almacén'),
    ('gestionar_roles',    'Gestionar roles y permisos del sistema', 'Administración');

-- Stored Procedures

DELIMITER //

DROP PROCEDURE IF EXISTS `sp_listar_roles`//
CREATE PROCEDURE `sp_listar_roles`()
BEGIN
    SELECT r.cod, r.nombre, r.descripcion,
        (SELECT COUNT(*) FROM RolPermiso rp WHERE rp.codRol = r.cod) AS total_permisos,
        (SELECT COUNT(*) FROM Cuenta c WHERE c.rol = r.nombre)       AS total_cuentas
    FROM Rol r ORDER BY r.cod ASC;
END//

DROP PROCEDURE IF EXISTS `sp_crear_rol`//
CREATE PROCEDURE `sp_crear_rol`(IN p_nombre VARCHAR(30), IN p_descripcion VARCHAR(150))
BEGIN
    INSERT INTO Rol (nombre, descripcion) VALUES (p_nombre, p_descripcion);
END//

DROP PROCEDURE IF EXISTS `sp_actualizar_rol`//
CREATE PROCEDURE `sp_actualizar_rol`(IN p_cod INT, IN p_nombre VARCHAR(30), IN p_descripcion VARCHAR(150))
BEGIN
    UPDATE Rol SET nombre = p_nombre, descripcion = p_descripcion WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS `sp_eliminar_rol`//
CREATE PROCEDURE `sp_eliminar_rol`(IN p_cod INT)
BEGIN
    DECLARE cant INT DEFAULT 0;
    SELECT COUNT(*) INTO cant FROM Cuenta WHERE rol = (SELECT nombre FROM Rol WHERE cod = p_cod LIMIT 1);
    IF cant > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se puede eliminar: hay cuentas con este rol';
    ELSE
        DELETE FROM Rol WHERE cod = p_cod;
    END IF;
END//

DROP PROCEDURE IF EXISTS `sp_listar_permisos`//
CREATE PROCEDURE `sp_listar_permisos`()
BEGIN
    SELECT cod, nombre, descripcion, modulo FROM Permiso ORDER BY modulo ASC, nombre ASC;
END//

DROP PROCEDURE IF EXISTS `sp_crear_permiso`//
CREATE PROCEDURE `sp_crear_permiso`(IN p_nombre VARCHAR(60), IN p_descripcion VARCHAR(150), IN p_modulo VARCHAR(40))
BEGIN
    INSERT INTO Permiso (nombre, descripcion, modulo) VALUES (p_nombre, p_descripcion, p_modulo);
END//

DROP PROCEDURE IF EXISTS `sp_actualizar_permiso`//
CREATE PROCEDURE `sp_actualizar_permiso`(IN p_cod INT, IN p_nombre VARCHAR(60), IN p_descripcion VARCHAR(150), IN p_modulo VARCHAR(40))
BEGIN
    UPDATE Permiso SET nombre = p_nombre, descripcion = p_descripcion, modulo = p_modulo WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS `sp_eliminar_permiso`//
CREATE PROCEDURE `sp_eliminar_permiso`(IN p_cod INT)
BEGIN
    DELETE FROM Permiso WHERE cod = p_cod;
END//

DROP PROCEDURE IF EXISTS `sp_permisos_de_rol`//
CREATE PROCEDURE `sp_permisos_de_rol`(IN p_codRol INT)
BEGIN
    SELECT codPermiso FROM RolPermiso WHERE codRol = p_codRol;
END//

DROP PROCEDURE IF EXISTS `sp_limpiar_permisos_rol`//
CREATE PROCEDURE `sp_limpiar_permisos_rol`(IN p_codRol INT)
BEGIN
    DELETE FROM RolPermiso WHERE codRol = p_codRol;
END//

DROP PROCEDURE IF EXISTS `sp_agregar_permiso_a_rol`//
CREATE PROCEDURE `sp_agregar_permiso_a_rol`(IN p_codRol INT, IN p_codPermiso INT)
BEGIN
    INSERT IGNORE INTO RolPermiso (codRol, codPermiso) VALUES (p_codRol, p_codPermiso);
END//

DROP PROCEDURE IF EXISTS `sp_cambiar_rol_cuenta`//
CREATE PROCEDURE `sp_cambiar_rol_cuenta`(IN p_usuario VARCHAR(40), IN p_rol VARCHAR(30))
BEGIN
    UPDATE Cuenta SET rol = p_rol WHERE usuario = p_usuario;
END//

DROP PROCEDURE IF EXISTS `sp_listar_cuentas_con_rol`//
CREATE PROCEDURE `sp_listar_cuentas_con_rol`()
BEGIN
    SELECT usuario, rol FROM Cuenta ORDER BY rol ASC, usuario ASC;
END//

DELIMITER ;
