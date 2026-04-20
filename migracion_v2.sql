-- =============================================
-- MIGRACIÓN v2: rol en Cuenta + nuevos SPs
-- =============================================

USE `mydb`;

-- 1. Agregar columna rol a Cuenta (seguro si ya existe)
ALTER TABLE `Cuenta` ADD COLUMN IF NOT EXISTS `rol` VARCHAR(20) NOT NULL DEFAULT 'cliente';

-- 2. Marcar admin como admin
UPDATE `Cuenta` SET `rol` = 'admin' WHERE `usuario` = 'admin' AND `rol` = 'cliente';

-- 3. Corregir vendedores creados antes de la migración (sin rol)
UPDATE `Cuenta` c
INNER JOIN `Vendedor` v ON v.`usuarioCuenta` = c.`usuario`
SET c.`rol` = 'vendedor'
WHERE c.`rol` = 'cliente';

DELIMITER //

-- 3. sp_crear_vendedor_con_cuenta actualizado (inserta rol='vendedor')
DROP PROCEDURE IF EXISTS sp_crear_vendedor_con_cuenta//
CREATE PROCEDURE sp_crear_vendedor_con_cuenta(
    IN p_usuario    VARCHAR(40),
    IN p_password   VARCHAR(255),
    IN p_ci         VARCHAR(20),
    IN p_nombres    VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo     VARCHAR(50),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO `Cuenta` (`usuario`, `password`, `rol`)
    VALUES (p_usuario, p_password, 'vendedor');

    INSERT INTO `Vendedor` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `nroCelular`, `usuarioCuenta`)
    VALUES (p_ci, p_nombres, p_ap_paterno, p_ap_materno, p_correo, p_nro_celular, p_usuario);

    COMMIT;
END//

-- 4. Registro público de cliente
DROP PROCEDURE IF EXISTS sp_registrar_cliente_con_cuenta//
CREATE PROCEDURE sp_registrar_cliente_con_cuenta(
    IN p_usuario     VARCHAR(40),
    IN p_password    VARCHAR(255),
    IN p_ci          VARCHAR(20),
    IN p_nombres     VARCHAR(50),
    IN p_ap_paterno  VARCHAR(20),
    IN p_ap_materno  VARCHAR(20),
    IN p_correo      VARCHAR(50),
    IN p_direccion   VARCHAR(45),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO `Cuenta` (`usuario`, `password`, `rol`)
    VALUES (p_usuario, p_password, 'cliente');

    INSERT INTO `Cliente` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`)
    VALUES (p_ci, p_nombres, p_ap_paterno, p_ap_materno, p_correo, p_direccion, p_nro_celular, p_usuario);

    COMMIT;
END//

-- 5. Obtener datos del cliente por usuario
DROP PROCEDURE IF EXISTS sp_obtener_cliente_por_usuario//
CREATE PROCEDURE sp_obtener_cliente_por_usuario(IN p_usuario VARCHAR(40))
BEGIN
    SELECT cl.`ci`, cl.`nombres`, cl.`apPaterno`, cl.`apMaterno`,
           cl.`correo`, cl.`direccion`, cl.`nroCelular`, cl.`usuarioCuenta`
    FROM `Cliente` cl
    WHERE cl.`usuarioCuenta` = p_usuario
    LIMIT 1;
END//

-- 6. Historial de compras del cliente
DROP PROCEDURE IF EXISTS sp_historial_compras_cliente//
CREATE PROCEDURE sp_historial_compras_cliente(IN p_usuario VARCHAR(40))
BEGIN
    SELECT nv.`nro`, nv.`fechaHora`,
           COALESCE(SUM(dnv.`cant`), 0)               AS totalItems,
           COALESCE(SUM(dnv.`cant` * p.`precio`), 0)  AS totalMonto
    FROM `NotaVenta` nv
    INNER JOIN `Cliente` cl ON cl.`ci` = nv.`ciCliente` AND cl.`usuarioCuenta` = p_usuario
    LEFT  JOIN `DetalleNotaVenta` dnv ON dnv.`nroNotaVenta` = nv.`nro`
    LEFT  JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    GROUP BY nv.`nro`, nv.`fechaHora`
    ORDER BY nv.`nro` DESC;
END//

-- 7. Actualizar perfil del cliente
DROP PROCEDURE IF EXISTS sp_actualizar_perfil_cliente//
CREATE PROCEDURE sp_actualizar_perfil_cliente(
    IN p_ci          VARCHAR(20),
    IN p_usuario     VARCHAR(40),
    IN p_correo      VARCHAR(50),
    IN p_direccion   VARCHAR(45),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    UPDATE `Cliente`
    SET `correo`     = p_correo,
        `direccion`  = p_direccion,
        `nroCelular` = p_nro_celular
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;
END//

-- 8. sp_resumen_ventas con totalMonto
DROP PROCEDURE IF EXISTS sp_resumen_ventas//
CREATE PROCEDURE sp_resumen_ventas()
BEGIN
    SELECT nv.`nro`, nv.`fechaHora`, nv.`ciCliente`,
           CONCAT(cl.`nombres`, ' ', cl.`apPaterno`, ' ', cl.`apMaterno`) AS cliente,
           COALESCE(SUM(dnv.`cant`), 0)               AS totalItems,
           COALESCE(SUM(dnv.`cant` * p.`precio`), 0)  AS totalMonto
    FROM `NotaVenta` nv
    INNER JOIN `Cliente` cl ON cl.`ci` = nv.`ciCliente`
    LEFT  JOIN `DetalleNotaVenta` dnv ON dnv.`nroNotaVenta` = nv.`nro`
    LEFT  JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    GROUP BY nv.`nro`, nv.`fechaHora`, nv.`ciCliente`, cl.`nombres`, cl.`apPaterno`, cl.`apMaterno`
    ORDER BY nv.`nro` DESC;
END//

-- 9. Panel vendedor: resumen del dashboard
DROP PROCEDURE IF EXISTS sp_resumen_dashboard//
CREATE PROCEDURE sp_resumen_dashboard()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM `NotaVenta`
         WHERE DATE(`fechaHora`) = CURDATE())                                                 AS ventasHoy,
        (SELECT COUNT(*) FROM `NotaVenta`
         WHERE `fechaHora` >= DATE_SUB(NOW(), INTERVAL 7 DAY))                               AS ventasSemana,
        (SELECT COUNT(*) FROM `NotaVenta`
         WHERE MONTH(`fechaHora`) = MONTH(NOW()) AND YEAR(`fechaHora`) = YEAR(NOW()))        AS ventasMes,
        (SELECT COALESCE(SUM(dnv.`cant` * p.`precio`), 0)
         FROM `DetalleNotaVenta` dnv
         INNER JOIN `Producto`  p  ON p.`cod`  = dnv.`codProducto`
         INNER JOIN `NotaVenta` nv ON nv.`nro` = dnv.`nroNotaVenta`
         WHERE MONTH(nv.`fechaHora`) = MONTH(NOW()) AND YEAR(nv.`fechaHora`) = YEAR(NOW()))  AS ingresosMes,
        (SELECT COALESCE(SUM(dnv.`cant` * p.`precio`), 0)
         FROM `DetalleNotaVenta` dnv
         INNER JOIN `Producto` p ON p.`cod` = dnv.`codProducto`)                            AS ingresosTotal;
END//

-- 10. Panel vendedor: top N productos más vendidos
DROP PROCEDURE IF EXISTS sp_productos_mas_vendidos//
CREATE PROCEDURE sp_productos_mas_vendidos(IN p_limite INT)
BEGIN
    SELECT p.`nombre`,
           SUM(dnv.`cant`)               AS totalVendido,
           SUM(dnv.`cant` * p.`precio`)  AS totalIngresos
    FROM `DetalleNotaVenta` dnv
    INNER JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    GROUP BY p.`cod`, p.`nombre`
    ORDER BY totalVendido DESC
    LIMIT p_limite;
END//

DELIMITER ;

-- Verificación final
SELECT COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'mydb' AND TABLE_NAME = 'Cuenta' AND COLUMN_NAME = 'rol';

SELECT NAME FROM mysql.proc WHERE db = 'mydb'
  AND NAME IN ('sp_registrar_cliente_con_cuenta','sp_obtener_cliente_por_usuario',
               'sp_historial_compras_cliente','sp_actualizar_perfil_cliente',
               'sp_resumen_ventas','sp_resumen_dashboard','sp_productos_mas_vendidos')
ORDER BY NAME;
