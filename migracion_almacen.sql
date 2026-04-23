-- ══════════════════════════════════════════════════════════════
--  MIGRACIÓN: Módulo de Almacén / Kardex
--  Ejecutar en phpMyAdmin, HeidiSQL o MySQL CLI
--  Base de datos: mydb
-- ══════════════════════════════════════════════════════════════

USE mydb;

-- ──────────────────────────────────────────────────────────────
-- 1. TABLA: MovimientoStock (Kardex)
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `MovimientoStock` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `fechaHora`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `codProducto`   INT          NOT NULL,
  `codSucursal`   INT          NOT NULL,
  `tipo`          VARCHAR(30)  NOT NULL COMMENT 'venta|traspaso_salida|traspaso_entrada|baja|devolucion|ajuste_entrada|ajuste_salida',
  `cantidad`      INT          NOT NULL,
  `stockAntes`    INT          NOT NULL,
  `stockDespues`  INT          NOT NULL,
  `referencia`    VARCHAR(50)  NULL     COMMENT 'NV-000012, TRP-003, etc.',
  `observacion`   VARCHAR(200) NULL,
  `usuarioCuenta` VARCHAR(40)  NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_ms_producto` (`codProducto`),
  INDEX `idx_ms_sucursal` (`codSucursal`),
  INDEX `idx_ms_fecha`    (`fechaHora`),
  INDEX `idx_ms_tipo`     (`tipo`),
  CONSTRAINT `fk_ms_producto`
    FOREIGN KEY (`codProducto`) REFERENCES `Producto` (`cod`) ON DELETE CASCADE,
  CONSTRAINT `fk_ms_sucursal`
    FOREIGN KEY (`codSucursal`) REFERENCES `Sucursal` (`cod`) ON DELETE CASCADE
) ENGINE = InnoDB;

-- ──────────────────────────────────────────────────────────────
-- 2. TABLA: Traspaso
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `Traspaso` (
  `nro`                 INT          NOT NULL AUTO_INCREMENT,
  `fechaHora`           DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `codSucursalOrigen`   INT          NOT NULL,
  `codSucursalDestino`  INT          NOT NULL,
  `estado`              VARCHAR(20)  NOT NULL DEFAULT 'pendiente' COMMENT 'pendiente|completado|cancelado',
  `observacion`         VARCHAR(200) NULL,
  `usuarioCuenta`       VARCHAR(40)  NULL,
  PRIMARY KEY (`nro`),
  CONSTRAINT `fk_trp_origen`  FOREIGN KEY (`codSucursalOrigen`)  REFERENCES `Sucursal` (`cod`),
  CONSTRAINT `fk_trp_destino` FOREIGN KEY (`codSucursalDestino`) REFERENCES `Sucursal` (`cod`)
) ENGINE = InnoDB;

-- ──────────────────────────────────────────────────────────────
-- 3. TABLA: DetalleTraspaso
-- ──────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `DetalleTraspaso` (
  `nroTraspaso`  INT NOT NULL,
  `codProducto`  INT NOT NULL,
  `cantidad`     INT NOT NULL,
  PRIMARY KEY (`nroTraspaso`, `codProducto`),
  CONSTRAINT `fk_dt_traspaso` FOREIGN KEY (`nroTraspaso`) REFERENCES `Traspaso`  (`nro`) ON DELETE CASCADE,
  CONSTRAINT `fk_dt_producto` FOREIGN KEY (`codProducto`) REFERENCES `Producto`  (`cod`)
) ENGINE = InnoDB;


-- ══════════════════════════════════════════════════════════════
--  STORED PROCEDURES
-- ══════════════════════════════════════════════════════════════

DELIMITER //

-- ──────────────────────────────────────────────────────────────
-- SP MODIFICADO: sp_descontar_stock_producto
-- Ahora recibe nroVenta y usuario para registrar en Kardex
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_descontar_stock_producto//
CREATE PROCEDURE sp_descontar_stock_producto(
    IN p_cod_producto INT,
    IN p_cantidad     INT,
    IN p_nro_venta    INT,
    IN p_usuario      VARCHAR(40)
)
BEGIN
    DECLARE v_stock_total  INT DEFAULT 0;
    DECLARE v_restante     INT DEFAULT p_cantidad;
    DECLARE v_cod_sucursal INT;
    DECLARE v_stock_actual INT;
    DECLARE v_descontado   INT;
    DECLARE done           INT DEFAULT FALSE;

    DECLARE cur CURSOR FOR
        SELECT `codSucursal`, CAST(`stock` AS UNSIGNED)
        FROM `DetalleProductoSucursal`
        WHERE `codProducto` = p_cod_producto
          AND CAST(`stock` AS UNSIGNED) > 0
        ORDER BY `codSucursal` ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SELECT COALESCE(SUM(CAST(`stock` AS UNSIGNED)), 0)
    INTO v_stock_total
    FROM `DetalleProductoSucursal`
    WHERE `codProducto` = p_cod_producto;

    IF v_stock_total < p_cantidad THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stock insuficiente para completar la compra';
    END IF;

    OPEN cur;
    descuento_loop: LOOP
        FETCH cur INTO v_cod_sucursal, v_stock_actual;
        IF done OR v_restante <= 0 THEN
            LEAVE descuento_loop;
        END IF;

        IF v_stock_actual >= v_restante THEN
            SET v_descontado = v_restante;
            UPDATE `DetalleProductoSucursal`
            SET `stock` = CAST(CAST(`stock` AS UNSIGNED) - v_restante AS CHAR)
            WHERE `codProducto` = p_cod_producto AND `codSucursal` = v_cod_sucursal;
            SET v_restante = 0;
        ELSE
            SET v_descontado = v_stock_actual;
            UPDATE `DetalleProductoSucursal`
            SET `stock` = '0'
            WHERE `codProducto` = p_cod_producto AND `codSucursal` = v_cod_sucursal;
            SET v_restante = v_restante - v_stock_actual;
        END IF;

        INSERT INTO `MovimientoStock`
            (fechaHora, codProducto, codSucursal, tipo, cantidad, stockAntes, stockDespues, referencia, observacion, usuarioCuenta)
        VALUES
            (NOW(), p_cod_producto, v_cod_sucursal, 'venta', v_descontado,
             v_stock_actual, v_stock_actual - v_descontado,
             CONCAT('NV-', LPAD(p_nro_venta, 6, '0')),
             'Salida por venta', p_usuario);

    END LOOP;
    CLOSE cur;
END//

-- ──────────────────────────────────────────────────────────────
-- Kardex filtrado
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_kardex_filtrado//
CREATE PROCEDURE sp_kardex_filtrado(
    IN p_cod_producto INT,
    IN p_cod_sucursal INT,
    IN p_tipo         VARCHAR(30),
    IN p_fecha_desde  DATE,
    IN p_fecha_hasta  DATE
)
BEGIN
    SELECT
        ms.id,
        ms.fechaHora,
        p.nombre        AS producto,
        s.nombre        AS sucursal,
        ms.tipo,
        ms.cantidad,
        ms.stockAntes,
        ms.stockDespues,
        ms.referencia,
        ms.observacion,
        ms.usuarioCuenta
    FROM `MovimientoStock` ms
    INNER JOIN `Producto` p ON p.cod = ms.codProducto
    INNER JOIN `Sucursal` s ON s.cod = ms.codSucursal
    WHERE (p_cod_producto = 0 OR ms.codProducto = p_cod_producto)
      AND (p_cod_sucursal = 0 OR ms.codSucursal  = p_cod_sucursal)
      AND (p_tipo = ''        OR ms.tipo          = p_tipo)
      AND (p_fecha_desde IS NULL OR DATE(ms.fechaHora) >= p_fecha_desde)
      AND (p_fecha_hasta IS NULL OR DATE(ms.fechaHora) <= p_fecha_hasta)
    ORDER BY ms.fechaHora DESC
    LIMIT 1000;
END//

-- ──────────────────────────────────────────────────────────────
-- Stock actual por producto/sucursal + stock comprometido
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_stock_actual_almacen//
CREATE PROCEDURE sp_stock_actual_almacen()
BEGIN
    SELECT
        p.cod        AS codProducto,
        p.nombre     AS producto,
        c.nombre     AS categoria,
        s.cod        AS codSucursal,
        s.nombre     AS sucursal,
        CAST(dps.stock AS UNSIGNED) AS stockActual,
        COALESCE((
            SELECT SUM(dnv.cant)
            FROM `DetalleNotaVenta` dnv
            INNER JOIN `NotaVenta` nv ON nv.nro = dnv.nroNotaVenta
            WHERE dnv.codProducto = p.cod
              AND nv.estado IN ('pendiente','procesando')
        ), 0) AS stockComprometido,
        GREATEST(0, CAST(dps.stock AS UNSIGNED) - COALESCE((
            SELECT SUM(dnv.cant)
            FROM `DetalleNotaVenta` dnv
            INNER JOIN `NotaVenta` nv ON nv.nro = dnv.nroNotaVenta
            WHERE dnv.codProducto = p.cod
              AND nv.estado IN ('pendiente','procesando')
        ), 0)) AS stockDisponible
    FROM `DetalleProductoSucursal` dps
    INNER JOIN `Producto`  p ON p.cod = dps.codProducto
    INNER JOIN `Sucursal`  s ON s.cod = dps.codSucursal
    LEFT  JOIN `Categoria` c ON c.cod = p.codCategoria
    ORDER BY p.nombre ASC, s.nombre ASC;
END//

-- ──────────────────────────────────────────────────────────────
-- Stock crítico (productos con stock total <= umbral)
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_stock_critico_almacen//
CREATE PROCEDURE sp_stock_critico_almacen(IN p_umbral INT)
BEGIN
    SELECT
        p.cod    AS codProducto,
        p.nombre AS producto,
        c.nombre AS categoria,
        SUM(CAST(dps.stock AS UNSIGNED)) AS stockTotal,
        CASE
            WHEN SUM(CAST(dps.stock AS UNSIGNED)) = 0 THEN 'agotado'
            ELSE 'bajo'
        END AS alerta
    FROM `Producto` p
    INNER JOIN `DetalleProductoSucursal` dps ON dps.codProducto = p.cod
    LEFT  JOIN `Categoria` c ON c.cod = p.codCategoria
    WHERE p.estado = 'activo'
    GROUP BY p.cod, p.nombre, c.nombre
    HAVING stockTotal <= p_umbral
    ORDER BY stockTotal ASC;
END//

-- ──────────────────────────────────────────────────────────────
-- Registrar ajuste manual de inventario
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_registrar_ajuste_stock//
CREATE PROCEDURE sp_registrar_ajuste_stock(
    IN p_cod_producto INT,
    IN p_cod_sucursal INT,
    IN p_tipo         VARCHAR(30),
    IN p_cantidad     INT,
    IN p_observacion  VARCHAR(200),
    IN p_usuario      VARCHAR(40)
)
BEGIN
    DECLARE v_stock_antes INT DEFAULT 0;
    DECLARE v_stock_despues INT;

    SELECT COALESCE(CAST(stock AS UNSIGNED), 0)
    INTO v_stock_antes
    FROM `DetalleProductoSucursal`
    WHERE codProducto = p_cod_producto AND codSucursal = p_cod_sucursal;

    IF p_tipo = 'ajuste_salida' THEN
        IF v_stock_antes < p_cantidad THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Stock insuficiente para el ajuste de salida';
        END IF;
        SET v_stock_despues = v_stock_antes - p_cantidad;
    ELSE
        SET v_stock_despues = v_stock_antes + p_cantidad;
    END IF;

    INSERT INTO `DetalleProductoSucursal` (codProducto, codSucursal, stock)
    VALUES (p_cod_producto, p_cod_sucursal, CAST(v_stock_despues AS CHAR))
    ON DUPLICATE KEY UPDATE stock = CAST(v_stock_despues AS CHAR);

    INSERT INTO `MovimientoStock`
        (fechaHora, codProducto, codSucursal, tipo, cantidad, stockAntes, stockDespues, referencia, observacion, usuarioCuenta)
    VALUES
        (NOW(), p_cod_producto, p_cod_sucursal, p_tipo, p_cantidad,
         v_stock_antes, v_stock_despues, 'AJU', p_observacion, p_usuario);
END//

-- ──────────────────────────────────────────────────────────────
-- Crear traspaso (cabecera)
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_crear_traspaso//
CREATE PROCEDURE sp_crear_traspaso(
    IN  p_cod_sucursal_origen  INT,
    IN  p_cod_sucursal_destino INT,
    IN  p_observacion          VARCHAR(200),
    IN  p_usuario              VARCHAR(40),
    OUT p_nro                  INT
)
BEGIN
    IF p_cod_sucursal_origen = p_cod_sucursal_destino THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Origen y destino no pueden ser la misma sucursal';
    END IF;

    INSERT INTO `Traspaso` (fechaHora, codSucursalOrigen, codSucursalDestino, estado, observacion, usuarioCuenta)
    VALUES (NOW(), p_cod_sucursal_origen, p_cod_sucursal_destino, 'pendiente', p_observacion, p_usuario);

    SET p_nro = LAST_INSERT_ID();
END//

-- ──────────────────────────────────────────────────────────────
-- Agregar producto a traspaso
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_agregar_detalle_traspaso//
CREATE PROCEDURE sp_agregar_detalle_traspaso(
    IN p_nro_traspaso INT,
    IN p_cod_producto INT,
    IN p_cantidad     INT
)
BEGIN
    DECLARE v_estado VARCHAR(20);

    SELECT estado INTO v_estado FROM `Traspaso` WHERE nro = p_nro_traspaso;

    IF v_estado != 'pendiente' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Solo se pueden agregar productos a traspasos pendientes';
    END IF;

    INSERT INTO `DetalleTraspaso` (nroTraspaso, codProducto, cantidad)
    VALUES (p_nro_traspaso, p_cod_producto, p_cantidad)
    ON DUPLICATE KEY UPDATE cantidad = p_cantidad;
END//

-- ──────────────────────────────────────────────────────────────
-- Completar traspaso: mueve stock + registra en Kardex
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_completar_traspaso//
CREATE PROCEDURE sp_completar_traspaso(
    IN p_nro     INT,
    IN p_usuario VARCHAR(40)
)
BEGIN
    DECLARE v_origen     INT;
    DECLARE v_destino    INT;
    DECLARE v_estado     VARCHAR(20);
    DECLARE v_codProd    INT;
    DECLARE v_cant       INT;
    DECLARE v_stock_ori  INT;
    DECLARE v_stock_dest INT;
    DECLARE done         INT DEFAULT FALSE;

    DECLARE cur CURSOR FOR
        SELECT codProducto, cantidad FROM `DetalleTraspaso` WHERE nroTraspaso = p_nro;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    SELECT estado, codSucursalOrigen, codSucursalDestino
    INTO v_estado, v_origen, v_destino
    FROM `Traspaso` WHERE nro = p_nro;

    IF v_estado != 'pendiente' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El traspaso no está en estado pendiente';
    END IF;

    START TRANSACTION;

    OPEN cur;
    traspaso_loop: LOOP
        FETCH cur INTO v_codProd, v_cant;
        IF done THEN LEAVE traspaso_loop; END IF;

        -- Stock en origen
        SELECT COALESCE(CAST(stock AS UNSIGNED), 0)
        INTO v_stock_ori
        FROM `DetalleProductoSucursal`
        WHERE codProducto = v_codProd AND codSucursal = v_origen;

        IF v_stock_ori < v_cant THEN
            SIGNAL SQLSTATE '45000'
                SET MESSAGE_TEXT = 'Stock insuficiente en sucursal origen para completar el traspaso';
        END IF;

        -- Stock en destino
        SELECT COALESCE(CAST(stock AS UNSIGNED), 0)
        INTO v_stock_dest
        FROM `DetalleProductoSucursal`
        WHERE codProducto = v_codProd AND codSucursal = v_destino;

        -- Restar en origen
        UPDATE `DetalleProductoSucursal`
        SET stock = CAST(CAST(stock AS UNSIGNED) - v_cant AS CHAR)
        WHERE codProducto = v_codProd AND codSucursal = v_origen;

        -- Sumar en destino (crear registro si no existe)
        INSERT INTO `DetalleProductoSucursal` (codProducto, codSucursal, stock)
        VALUES (v_codProd, v_destino, CAST(v_cant AS CHAR))
        ON DUPLICATE KEY UPDATE stock = CAST(CAST(stock AS UNSIGNED) + v_cant AS CHAR);

        -- Kardex: salida en origen
        INSERT INTO `MovimientoStock`
            (fechaHora, codProducto, codSucursal, tipo, cantidad, stockAntes, stockDespues, referencia, observacion, usuarioCuenta)
        VALUES
            (NOW(), v_codProd, v_origen, 'traspaso_salida', v_cant,
             v_stock_ori, v_stock_ori - v_cant,
             CONCAT('TRP-', LPAD(p_nro, 6, '0')), 'Salida por traspaso', p_usuario);

        -- Kardex: entrada en destino
        INSERT INTO `MovimientoStock`
            (fechaHora, codProducto, codSucursal, tipo, cantidad, stockAntes, stockDespues, referencia, observacion, usuarioCuenta)
        VALUES
            (NOW(), v_codProd, v_destino, 'traspaso_entrada', v_cant,
             v_stock_dest, v_stock_dest + v_cant,
             CONCAT('TRP-', LPAD(p_nro, 6, '0')), 'Entrada por traspaso', p_usuario);

    END LOOP;
    CLOSE cur;

    UPDATE `Traspaso` SET estado = 'completado' WHERE nro = p_nro;
    COMMIT;
END//

-- ──────────────────────────────────────────────────────────────
-- Cancelar traspaso
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_cancelar_traspaso//
CREATE PROCEDURE sp_cancelar_traspaso(IN p_nro INT)
BEGIN
    DECLARE v_estado VARCHAR(20);
    SELECT estado INTO v_estado FROM `Traspaso` WHERE nro = p_nro;
    IF v_estado = 'completado' THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'No se puede cancelar un traspaso ya completado';
    END IF;
    UPDATE `Traspaso` SET estado = 'cancelado' WHERE nro = p_nro;
END//

-- ──────────────────────────────────────────────────────────────
-- Listar traspasos con nombres de sucursales
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_listar_traspasos//
CREATE PROCEDURE sp_listar_traspasos()
BEGIN
    SELECT
        t.nro,
        t.fechaHora,
        so.nombre AS sucursalOrigen,
        sd.nombre AS sucursalDestino,
        t.estado,
        t.observacion,
        t.usuarioCuenta,
        COUNT(dt.codProducto) AS cantProductos,
        SUM(dt.cantidad)      AS cantUnidades
    FROM `Traspaso` t
    INNER JOIN `Sucursal` so ON so.cod = t.codSucursalOrigen
    INNER JOIN `Sucursal` sd ON sd.cod = t.codSucursalDestino
    LEFT  JOIN `DetalleTraspaso` dt ON dt.nroTraspaso = t.nro
    GROUP BY t.nro, t.fechaHora, so.nombre, sd.nombre, t.estado, t.observacion, t.usuarioCuenta
    ORDER BY t.nro DESC;
END//

-- ──────────────────────────────────────────────────────────────
-- Detalle de un traspaso
-- ──────────────────────────────────────────────────────────────
DROP PROCEDURE IF EXISTS sp_detalle_traspaso//
CREATE PROCEDURE sp_detalle_traspaso(IN p_nro INT)
BEGIN
    SELECT
        dt.codProducto,
        p.nombre AS producto,
        dt.cantidad,
        CAST(dps_ori.stock AS UNSIGNED) AS stockOrigen
    FROM `DetalleTraspaso` dt
    INNER JOIN `Producto` p ON p.cod = dt.codProducto
    INNER JOIN `Traspaso` t ON t.nro = dt.nroTraspaso
    LEFT  JOIN `DetalleProductoSucursal` dps_ori
           ON dps_ori.codProducto = dt.codProducto
          AND dps_ori.codSucursal = t.codSucursalOrigen
    WHERE dt.nroTraspaso = p_nro
    ORDER BY p.nombre;
END//

DELIMITER ;

-- ══════════════════════════════════════════════════════════════
--  Verificación final
-- ══════════════════════════════════════════════════════════════
SELECT 'Migracion completada: MovimientoStock, Traspaso, DetalleTraspaso y SPs creados.' AS resultado;
