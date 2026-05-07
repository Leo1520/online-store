-- ============================================================
-- Migración 002: Empleados y Usuarios Internos
-- Fecha: 2026-04-26
-- Propósito: Soporte para roles internos adicionales
--            (almacenero, repartidor, it, etc.)
-- ============================================================

-- Nuevos roles internos
INSERT IGNORE INTO `Rol` (`nombre`, `descripcion`) VALUES
    ('almacenero', 'Gestión de stock, traspasos y ajustes de inventario'),
    ('repartidor', 'Entrega de pedidos a domicilio'),
    ('it',         'Soporte técnico y administración de sistemas');

-- Permisos adicionales para nuevos roles
INSERT IGNORE INTO `Permiso` (`nombre`, `descripcion`, `modulo`) VALUES
    ('gestionar_usuarios', 'Crear y administrar usuarios internos', 'Administración'),
    ('ver_usuarios',       'Ver listado de usuarios internos',      'Administración');

-- Tabla de empleados internos (para roles distintos a vendedor/cliente)
CREATE TABLE IF NOT EXISTS `Empleado` (
    `ci`            VARCHAR(20) NOT NULL,
    `nombres`       VARCHAR(50) NOT NULL,
    `apPaterno`     VARCHAR(20) NOT NULL,
    `apMaterno`     VARCHAR(20) DEFAULT NULL,
    `correo`        VARCHAR(50) DEFAULT NULL,
    `nroCelular`    VARCHAR(30) DEFAULT NULL,
    `cargo`         VARCHAR(40) DEFAULT NULL,
    `usuarioCuenta` VARCHAR(40) NOT NULL,
    PRIMARY KEY (`ci`),
    UNIQUE KEY `uq_empleado_cuenta` (`usuarioCuenta`),
    FOREIGN KEY (`usuarioCuenta`) REFERENCES `Cuenta`(`usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Stored Procedures para Empleado

DELIMITER //

DROP PROCEDURE IF EXISTS `sp_listar_empleados`//
CREATE PROCEDURE `sp_listar_empleados`()
BEGIN
    SELECT e.ci, e.nombres, e.apPaterno, e.apMaterno,
           e.correo, e.nroCelular, e.cargo, e.usuarioCuenta,
           c.rol
    FROM Empleado e
    INNER JOIN Cuenta c ON c.usuario = e.usuarioCuenta
    ORDER BY e.apPaterno ASC, e.nombres ASC;
END//

DROP PROCEDURE IF EXISTS `sp_crear_empleado_con_cuenta`//
CREATE PROCEDURE `sp_crear_empleado_con_cuenta`(
    IN p_usuario    VARCHAR(40),
    IN p_password   VARCHAR(255),
    IN p_rol        VARCHAR(30),
    IN p_ci         VARCHAR(20),
    IN p_nombres    VARCHAR(50),
    IN p_apPaterno  VARCHAR(20),
    IN p_apMaterno  VARCHAR(20),
    IN p_correo     VARCHAR(50),
    IN p_nroCelular VARCHAR(30),
    IN p_cargo      VARCHAR(40)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    START TRANSACTION;
    INSERT INTO `Cuenta` (`usuario`, `password`, `rol`)
        VALUES (p_usuario, p_password, p_rol);
    INSERT INTO `Empleado` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `nroCelular`, `cargo`, `usuarioCuenta`)
        VALUES (p_ci, p_nombres, p_apPaterno, p_apMaterno, p_correo, p_nroCelular, p_cargo, p_usuario);
    COMMIT;
END//

DROP PROCEDURE IF EXISTS `sp_obtener_empleado_por_usuario`//
CREATE PROCEDURE `sp_obtener_empleado_por_usuario`(IN p_usuario VARCHAR(40))
BEGIN
    SELECT e.ci, e.nombres, e.apPaterno, e.apMaterno,
           e.correo, e.nroCelular, e.cargo, e.usuarioCuenta,
           c.rol
    FROM Empleado e
    INNER JOIN Cuenta c ON c.usuario = e.usuarioCuenta
    WHERE e.usuarioCuenta = p_usuario
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS `sp_actualizar_empleado`//
CREATE PROCEDURE `sp_actualizar_empleado`(
    IN p_usuario    VARCHAR(40),
    IN p_ci         VARCHAR(20),
    IN p_nombres    VARCHAR(50),
    IN p_apPaterno  VARCHAR(20),
    IN p_apMaterno  VARCHAR(20),
    IN p_correo     VARCHAR(50),
    IN p_nroCelular VARCHAR(30),
    IN p_cargo      VARCHAR(40),
    IN p_rol        VARCHAR(30)
)
BEGIN
    UPDATE `Empleado`
       SET ci = p_ci, nombres = p_nombres, apPaterno = p_apPaterno,
           apMaterno = p_apMaterno, correo = p_correo,
           nroCelular = p_nroCelular, cargo = p_cargo
     WHERE usuarioCuenta = p_usuario;
    UPDATE `Cuenta` SET rol = p_rol WHERE usuario = p_usuario;
END//

DROP PROCEDURE IF EXISTS `sp_actualizar_password_empleado`//
CREATE PROCEDURE `sp_actualizar_password_empleado`(IN p_usuario VARCHAR(40), IN p_password VARCHAR(255))
BEGIN
    UPDATE `Cuenta` SET password = p_password WHERE usuario = p_usuario;
END//

DROP PROCEDURE IF EXISTS `sp_eliminar_empleado_y_cuenta`//
CREATE PROCEDURE `sp_eliminar_empleado_y_cuenta`(IN p_usuario VARCHAR(40))
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;
    IF p_usuario = 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cuenta protegida: no se puede eliminar al admin';
    END IF;
    START TRANSACTION;
    DELETE FROM `Empleado` WHERE usuarioCuenta = p_usuario;
    DELETE FROM `Cuenta`   WHERE usuario = p_usuario;
    COMMIT;
END//

-- Vista unificada de todos los usuarios internos (no clientes)
DROP PROCEDURE IF EXISTS `sp_listar_usuarios_internos`//
CREATE PROCEDURE `sp_listar_usuarios_internos`()
BEGIN
    SELECT
        c.usuario,
        c.rol,
        COALESCE(v.nombres, e.nombres) AS nombres,
        COALESCE(v.apPaterno, e.apPaterno) AS apPaterno,
        COALESCE(v.apMaterno, e.apMaterno) AS apMaterno,
        COALESCE(v.correo, e.correo) AS correo,
        COALESCE(v.nroCelular, e.nroCelular) AS nroCelular,
        COALESCE(v.ci, e.ci) AS ci,
        CASE
            WHEN v.usuarioCuenta IS NOT NULL THEN 'vendedor'
            WHEN e.usuarioCuenta IS NOT NULL THEN 'empleado'
            ELSE 'solo_cuenta'
        END AS tipo_perfil
    FROM Cuenta c
    LEFT JOIN Vendedor e2 ON e2.usuarioCuenta = c.usuario
    LEFT JOIN Empleado e  ON e.usuarioCuenta  = c.usuario
    LEFT JOIN Vendedor v  ON v.usuarioCuenta  = c.usuario
    WHERE c.rol != 'cliente'
    ORDER BY c.rol ASC, c.usuario ASC;
END//

DELIMITER ;
