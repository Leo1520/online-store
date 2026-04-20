-- Procedimientos almacenados para mydb
-- Ejecutar despues de crear el esquema mydb.sql

USE `mydb`;

DELIMITER //

DROP PROCEDURE IF EXISTS sp_guardar_stock_sucursal//
CREATE PROCEDURE sp_guardar_stock_sucursal(
    IN p_cod_producto INT,
    IN p_cod_sucursal INT,
    IN p_stock INT
)
BEGIN
    IF p_stock < 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Stock no puede ser negativo';
    END IF;

    INSERT INTO `DetalleProductoSucursal` (`codProducto`, `codSucursal`, `stock`)
    VALUES (p_cod_producto, p_cod_sucursal, CAST(p_stock AS CHAR))
    ON DUPLICATE KEY UPDATE `stock` = VALUES(`stock`);
END//

DROP PROCEDURE IF EXISTS sp_crear_nota_venta//
CREATE PROCEDURE sp_crear_nota_venta(
    IN p_ci_cliente VARCHAR(20),
    OUT p_nro_venta INT
)
BEGIN
    DECLARE v_ultimo INT DEFAULT 0;

    SELECT COALESCE(MAX(`nro`), 0)
    INTO v_ultimo
    FROM `NotaVenta`;

    SET p_nro_venta = v_ultimo + 1;

    INSERT INTO `NotaVenta` (`nro`, `fechaHora`, `ciCliente`)
    VALUES (p_nro_venta, NOW(), p_ci_cliente);
END//

DROP PROCEDURE IF EXISTS sp_insertar_detalle_venta//
CREATE PROCEDURE sp_insertar_detalle_venta(
    IN p_nro_venta INT,
    IN p_cod_producto INT,
    IN p_item INT,
    IN p_cantidad INT
)
BEGIN
    DECLARE v_existe INT DEFAULT 0;

    SELECT COUNT(*) INTO v_existe
    FROM `Producto`
    WHERE `cod` = p_cod_producto;

    IF v_existe = 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Producto no existe';
    END IF;

    INSERT INTO `DetalleNotaVenta` (`nroNotaVenta`, `codProducto`, `item`, `cant`)
    VALUES (p_nro_venta, p_cod_producto, p_item, p_cantidad);
END//

DROP PROCEDURE IF EXISTS sp_crear_cliente_con_cuenta//
CREATE PROCEDURE sp_crear_cliente_con_cuenta(
    IN p_usuario VARCHAR(40),
    IN p_password VARCHAR(255),
    IN p_ci VARCHAR(20),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(30),
    IN p_direccion VARCHAR(45),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO `Cuenta` (`usuario`, `password`)
    VALUES (p_usuario, p_password);

    INSERT INTO `Cliente` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`)
    VALUES (p_ci, p_nombres, p_ap_paterno, p_ap_materno, p_correo, p_direccion, p_nro_celular, p_usuario);

    COMMIT;
END//

DROP PROCEDURE IF EXISTS sp_actualizar_cliente_y_password//
CREATE PROCEDURE sp_actualizar_cliente_y_password(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(30),
    IN p_direccion VARCHAR(45),
    IN p_nro_celular VARCHAR(30),
    IN p_password VARCHAR(255)
)
BEGIN
    UPDATE `Cliente`
    SET `nombres` = p_nombres,
        `apPaterno` = p_ap_paterno,
        `apMaterno` = p_ap_materno,
        `correo` = p_correo,
        `direccion` = p_direccion,
        `nroCelular` = p_nro_celular
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;

    IF p_password IS NOT NULL AND p_password <> '' THEN
        UPDATE `Cuenta`
        SET `password` = p_password
        WHERE `usuario` = p_usuario;
    END IF;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_cliente_y_cuenta_segura//
CREATE PROCEDURE sp_eliminar_cliente_y_cuenta_segura(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40)
)
BEGIN
    DECLARE v_relaciones INT DEFAULT 0;

    IF p_usuario IN ('admin', 'cliente_demo') THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cuenta protegida';
    END IF;

    DELETE FROM `Cliente`
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;

    SELECT COUNT(*) INTO v_relaciones
    FROM `Cliente`
    WHERE `usuarioCuenta` = p_usuario;

    IF v_relaciones = 0 THEN
        DELETE FROM `Cuenta`
        WHERE `usuario` = p_usuario;
    END IF;
END//

DROP PROCEDURE IF EXISTS sp_listar_productos_con_stock_total//
CREATE PROCEDURE sp_listar_productos_con_stock_total()
BEGIN
    SELECT
        p.`cod` AS id_producto,
        p.`nombre`,
        p.`descripcion`,
        p.`precio`,
        p.`imagen`,
        p.`estado`,
        p.`codMarca`,
        p.`codIndustria`,
        p.`codCategoria`,
        COALESCE(SUM(CAST(dps.`stock` AS UNSIGNED)), 0) AS stock,
        m.`nombre` AS marca,
        c.`nombre` AS categoria,
        i.`nombre` AS industria
    FROM `Producto` p
    LEFT JOIN `DetalleProductoSucursal` dps ON dps.`codProducto` = p.`cod`
    LEFT JOIN `Marca` m ON m.`cod` = p.`codMarca`
    LEFT JOIN `Categoria` c ON c.`cod` = p.`codCategoria`
    LEFT JOIN `Industria` i ON i.`cod` = p.`codIndustria`
    GROUP BY p.`cod`, p.`nombre`, p.`descripcion`, p.`precio`, p.`imagen`, p.`estado`, p.`codMarca`, p.`codIndustria`, p.`codCategoria`, m.`nombre`, c.`nombre`, i.`nombre`
    ORDER BY p.`cod` DESC;
END//

DROP PROCEDURE IF EXISTS sp_obtener_producto_por_id//
CREATE PROCEDURE sp_obtener_producto_por_id(IN p_cod_producto INT)
BEGIN
    SELECT
        p.`cod` AS id_producto,
        p.`nombre`,
        p.`descripcion`,
        p.`precio`,
        p.`imagen`,
        p.`estado`,
        p.`codMarca`,
        p.`codIndustria`,
        p.`codCategoria`,
        COALESCE(SUM(CAST(dps.`stock` AS UNSIGNED)), 0) AS stock,
        m.`nombre` AS marca,
        c.`nombre` AS categoria,
        i.`nombre` AS industria
    FROM `Producto` p
    LEFT JOIN `DetalleProductoSucursal` dps ON dps.`codProducto` = p.`cod`
    LEFT JOIN `Marca` m ON m.`cod` = p.`codMarca`
    LEFT JOIN `Categoria` c ON c.`cod` = p.`codCategoria`
    LEFT JOIN `Industria` i ON i.`cod` = p.`codIndustria`
    WHERE p.`cod` = p_cod_producto
    GROUP BY p.`cod`, p.`nombre`, p.`descripcion`, p.`precio`, p.`imagen`, p.`estado`, p.`codMarca`, p.`codIndustria`, p.`codCategoria`, m.`nombre`, c.`nombre`, i.`nombre`;
END//

DROP PROCEDURE IF EXISTS sp_crear_producto//
CREATE PROCEDURE sp_crear_producto(
    IN p_nombre VARCHAR(50),
    IN p_descripcion VARCHAR(200),
    IN p_precio FLOAT,
    IN p_imagen VARCHAR(200),
    IN p_estado VARCHAR(20),
    IN p_cod_marca INT,
    IN p_cod_industria INT,
    IN p_cod_categoria INT
)
BEGIN
    INSERT INTO `Producto` (`nombre`, `descripcion`, `precio`, `imagen`, `estado`, `codMarca`, `codIndustria`, `codCategoria`)
    VALUES (p_nombre, p_descripcion, p_precio, p_imagen, p_estado, p_cod_marca, p_cod_industria, p_cod_categoria);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_producto//
CREATE PROCEDURE sp_actualizar_producto(
    IN p_cod_producto INT,
    IN p_nombre VARCHAR(50),
    IN p_descripcion VARCHAR(200),
    IN p_precio FLOAT,
    IN p_imagen VARCHAR(200),
    IN p_estado VARCHAR(20),
    IN p_cod_marca INT,
    IN p_cod_industria INT,
    IN p_cod_categoria INT
)
BEGIN
    UPDATE `Producto`
    SET `nombre` = p_nombre,
        `descripcion` = p_descripcion,
        `precio` = p_precio,
        `imagen` = p_imagen,
        `estado` = p_estado,
        `codMarca` = p_cod_marca,
        `codIndustria` = p_cod_industria,
        `codCategoria` = p_cod_categoria
    WHERE `cod` = p_cod_producto;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_producto//
CREATE PROCEDURE sp_eliminar_producto(IN p_cod_producto INT)
BEGIN
    DELETE FROM `Producto`
    WHERE `cod` = p_cod_producto;
END//

DROP PROCEDURE IF EXISTS sp_resumen_ventas//
CREATE PROCEDURE sp_resumen_ventas()
BEGIN
    SELECT nv.`nro`, nv.`fechaHora`, nv.`ciCliente`,
           CONCAT(cl.`nombres`, ' ', cl.`apPaterno`, ' ', cl.`apMaterno`) AS cliente,
           COALESCE(SUM(dnv.`cant`), 0) AS totalItems
    FROM `NotaVenta` nv
    INNER JOIN `Cliente` cl ON cl.`ci` = nv.`ciCliente`
    LEFT JOIN `DetalleNotaVenta` dnv ON dnv.`nroNotaVenta` = nv.`nro`
    GROUP BY nv.`nro`, nv.`fechaHora`, nv.`ciCliente`, cl.`nombres`, cl.`apPaterno`, cl.`apMaterno`
    ORDER BY nv.`nro` DESC;
END//

DROP PROCEDURE IF EXISTS sp_detalle_venta//
CREATE PROCEDURE sp_detalle_venta(IN p_nro_venta INT)
BEGIN
    SELECT dnv.`nroNotaVenta`, dnv.`item`, dnv.`cant`, dnv.`codProducto`, p.`nombre` AS producto, p.`precio`
    FROM `DetalleNotaVenta` dnv
    INNER JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    WHERE dnv.`nroNotaVenta` = p_nro_venta
    ORDER BY dnv.`item` ASC;
END//

DROP PROCEDURE IF EXISTS sp_listar_marcas//
CREATE PROCEDURE sp_listar_marcas()
BEGIN
    SELECT `cod`, `nombre`
    FROM `Marca`
    ORDER BY `cod` DESC;
END//

DROP PROCEDURE IF EXISTS sp_crear_marca//
CREATE PROCEDURE sp_crear_marca(IN p_nombre VARCHAR(30))
BEGIN
    INSERT INTO `Marca` (`nombre`)
    VALUES (p_nombre);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_marca//
CREATE PROCEDURE sp_actualizar_marca(
    IN p_cod INT,
    IN p_nombre VARCHAR(30)
)
BEGIN
    UPDATE `Marca`
    SET `nombre` = p_nombre
    WHERE `cod` = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_marca//
CREATE PROCEDURE sp_eliminar_marca(IN p_cod INT)
BEGIN
    DELETE FROM `Marca`
    WHERE `cod` = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_listar_categorias//
CREATE PROCEDURE sp_listar_categorias()
BEGIN
    SELECT `cod`, `nombre`
    FROM `Categoria`
    ORDER BY `cod` DESC;
END//

DROP PROCEDURE IF EXISTS sp_crear_categoria//
CREATE PROCEDURE sp_crear_categoria(IN p_nombre VARCHAR(30))
BEGIN
    INSERT INTO `Categoria` (`nombre`)
    VALUES (p_nombre);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_categoria//
CREATE PROCEDURE sp_actualizar_categoria(
    IN p_cod INT,
    IN p_nombre VARCHAR(30)
)
BEGIN
    UPDATE `Categoria`
    SET `nombre` = p_nombre
    WHERE `cod` = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_categoria//
CREATE PROCEDURE sp_eliminar_categoria(IN p_cod INT)
BEGIN
    DELETE FROM `Categoria`
    WHERE `cod` = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_listar_industrias//
CREATE PROCEDURE sp_listar_industrias()
BEGIN
    SELECT `cod`, `nombre`
    FROM `Industria`
    ORDER BY `cod` DESC;
END//

DROP PROCEDURE IF EXISTS sp_crear_industria//
CREATE PROCEDURE sp_crear_industria(IN p_nombre VARCHAR(30))
BEGIN
    INSERT INTO `Industria` (`nombre`)
    VALUES (p_nombre);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_industria//
CREATE PROCEDURE sp_actualizar_industria(
    IN p_cod INT,
    IN p_nombre VARCHAR(30)
)
BEGIN
    UPDATE `Industria`
    SET `nombre` = p_nombre
    WHERE `cod` = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_industria//
CREATE PROCEDURE sp_eliminar_industria(IN p_cod INT)
BEGIN
    DELETE FROM `Industria`
    WHERE `cod` = p_cod;
END//

-- =============================================
-- SUCURSAL
-- =============================================

DROP PROCEDURE IF EXISTS sp_listar_sucursales//
CREATE PROCEDURE sp_listar_sucursales()
BEGIN
    SELECT `cod`, `nombre`, `direccion`, `nroTelefono`
    FROM `Sucursal`
    ORDER BY `cod` DESC;
END//

DROP PROCEDURE IF EXISTS sp_obtener_sucursal_por_cod//
CREATE PROCEDURE sp_obtener_sucursal_por_cod(IN p_cod INT)
BEGIN
    SELECT `cod`, `nombre`, `direccion`, `nroTelefono`
    FROM `Sucursal`
    WHERE `cod` = p_cod
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS sp_crear_sucursal//
CREATE PROCEDURE sp_crear_sucursal(
    IN p_nombre VARCHAR(50),
    IN p_direccion VARCHAR(100),
    IN p_nro_telefono VARCHAR(30)
)
BEGIN
    INSERT INTO `Sucursal` (`nombre`, `direccion`, `nroTelefono`)
    VALUES (p_nombre, p_direccion, p_nro_telefono);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_sucursal//
CREATE PROCEDURE sp_actualizar_sucursal(
    IN p_cod INT,
    IN p_nombre VARCHAR(50),
    IN p_direccion VARCHAR(100),
    IN p_nro_telefono VARCHAR(30)
)
BEGIN
    UPDATE `Sucursal`
    SET `nombre` = p_nombre,
        `direccion` = p_direccion,
        `nroTelefono` = p_nro_telefono
    WHERE `cod` = p_cod;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_sucursal//
CREATE PROCEDURE sp_eliminar_sucursal(IN p_cod INT)
BEGIN
    DELETE FROM `Sucursal`
    WHERE `cod` = p_cod;
END//

-- =============================================
-- CUENTA
-- =============================================

DROP PROCEDURE IF EXISTS sp_listar_cuentas//
CREATE PROCEDURE sp_listar_cuentas()
BEGIN
    SELECT `usuario`, `password`
    FROM `Cuenta`
    ORDER BY `usuario` ASC;
END//

DROP PROCEDURE IF EXISTS sp_obtener_cuenta_por_usuario//
CREATE PROCEDURE sp_obtener_cuenta_por_usuario(IN p_usuario VARCHAR(40))
BEGIN
    SELECT `usuario`, `password`
    FROM `Cuenta`
    WHERE `usuario` = p_usuario
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS sp_crear_cuenta//
CREATE PROCEDURE sp_crear_cuenta(
    IN p_usuario VARCHAR(40),
    IN p_password VARCHAR(255)
)
BEGIN
    INSERT INTO `Cuenta` (`usuario`, `password`)
    VALUES (p_usuario, p_password);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_password_cuenta//
CREATE PROCEDURE sp_actualizar_password_cuenta(
    IN p_usuario VARCHAR(40),
    IN p_password VARCHAR(255)
)
BEGIN
    UPDATE `Cuenta`
    SET `password` = p_password
    WHERE `usuario` = p_usuario;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_cuenta//
CREATE PROCEDURE sp_eliminar_cuenta(IN p_usuario VARCHAR(40))
BEGIN
    DELETE FROM `Cuenta`
    WHERE `usuario` = p_usuario;
END//

DROP PROCEDURE IF EXISTS sp_verificar_cliente_asociado//
CREATE PROCEDURE sp_verificar_cliente_asociado(IN p_usuario VARCHAR(40))
BEGIN
    SELECT COUNT(*) AS total
    FROM `Cliente`
    WHERE `usuarioCuenta` = p_usuario
    LIMIT 1;
END//

-- =============================================
-- CLIENTE (métodos faltantes)
-- =============================================

DROP PROCEDURE IF EXISTS sp_listar_clientes//
CREATE PROCEDURE sp_listar_clientes()
BEGIN
    SELECT `ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`
    FROM `Cliente`
    ORDER BY `ci` DESC;
END//

DROP PROCEDURE IF EXISTS sp_obtener_cliente_por_clave//
CREATE PROCEDURE sp_obtener_cliente_por_clave(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40)
)
BEGIN
    SELECT `ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`
    FROM `Cliente`
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS sp_crear_cliente//
CREATE PROCEDURE sp_crear_cliente(
    IN p_ci VARCHAR(20),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(30),
    IN p_direccion VARCHAR(45),
    IN p_nro_celular VARCHAR(30),
    IN p_usuario_cuenta VARCHAR(40)
)
BEGIN
    INSERT INTO `Cliente` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `direccion`, `nroCelular`, `usuarioCuenta`)
    VALUES (p_ci, p_nombres, p_ap_paterno, p_ap_materno, p_correo, p_direccion, p_nro_celular, p_usuario_cuenta);
END//

DROP PROCEDURE IF EXISTS sp_actualizar_cliente//
CREATE PROCEDURE sp_actualizar_cliente(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(30),
    IN p_direccion VARCHAR(45),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    UPDATE `Cliente`
    SET `nombres` = p_nombres,
        `apPaterno` = p_ap_paterno,
        `apMaterno` = p_ap_materno,
        `correo` = p_correo,
        `direccion` = p_direccion,
        `nroCelular` = p_nro_celular
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_cliente//
CREATE PROCEDURE sp_eliminar_cliente(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40)
)
BEGIN
    DELETE FROM `Cliente`
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;
END//

-- =============================================
-- USUARIO
-- =============================================

DROP PROCEDURE IF EXISTS sp_verificar_credenciales_usuario//
CREATE PROCEDURE sp_verificar_credenciales_usuario(
    IN p_usuario VARCHAR(40),
    IN p_password VARCHAR(255)
)
BEGIN
    SELECT `usuario`
    FROM `Cuenta`
    WHERE `usuario` = p_usuario AND `password` = p_password
    LIMIT 1;
END//

-- =============================================
-- DESCUENTO DE STOCK
-- =============================================

DROP PROCEDURE IF EXISTS sp_descontar_stock_producto//
CREATE PROCEDURE sp_descontar_stock_producto(
    IN p_cod_producto INT,
    IN p_cantidad     INT
)
BEGIN
    DECLARE v_stock_total  INT DEFAULT 0;
    DECLARE v_restante     INT DEFAULT p_cantidad;
    DECLARE v_cod_sucursal INT;
    DECLARE v_stock_actual INT;
    DECLARE done           INT DEFAULT FALSE;

    DECLARE cur CURSOR FOR
        SELECT `codSucursal`, CAST(`stock` AS UNSIGNED)
        FROM `DetalleProductoSucursal`
        WHERE `codProducto` = p_cod_producto
          AND CAST(`stock` AS UNSIGNED) > 0
        ORDER BY `codSucursal` ASC;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    -- Verificar stock total disponible
    SELECT COALESCE(SUM(CAST(`stock` AS UNSIGNED)), 0)
    INTO v_stock_total
    FROM `DetalleProductoSucursal`
    WHERE `codProducto` = p_cod_producto;

    IF v_stock_total < p_cantidad THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Stock insuficiente para completar la compra';
    END IF;

    -- Descontar secuencialmente por sucursal
    OPEN cur;
    descuento_loop: LOOP
        FETCH cur INTO v_cod_sucursal, v_stock_actual;
        IF done OR v_restante <= 0 THEN
            LEAVE descuento_loop;
        END IF;

        IF v_stock_actual >= v_restante THEN
            UPDATE `DetalleProductoSucursal`
            SET `stock` = CAST(CAST(`stock` AS UNSIGNED) - v_restante AS CHAR)
            WHERE `codProducto` = p_cod_producto
              AND `codSucursal`  = v_cod_sucursal;
            SET v_restante = 0;
        ELSE
            UPDATE `DetalleProductoSucursal`
            SET `stock` = '0'
            WHERE `codProducto` = p_cod_producto
              AND `codSucursal`  = v_cod_sucursal;
            SET v_restante = v_restante - v_stock_actual;
        END IF;
    END LOOP;
    CLOSE cur;
END//

-- =============================================
-- BUSQUEDA DE PRODUCTOS
-- =============================================

DROP PROCEDURE IF EXISTS sp_buscar_productos//
CREATE PROCEDURE sp_buscar_productos(
    IN p_nombre        VARCHAR(50),
    IN p_cod_categoria INT,
    IN p_precio_min    DECIMAL(10,2),
    IN p_precio_max    DECIMAL(10,2)
)
BEGIN
    SELECT
        p.`cod`          AS id_producto,
        p.`nombre`,
        p.`descripcion`,
        p.`precio`,
        p.`imagen`,
        p.`estado`,
        p.`codMarca`,
        p.`codIndustria`,
        p.`codCategoria`,
        COALESCE(SUM(CAST(dps.`stock` AS UNSIGNED)), 0) AS stock,
        m.`nombre`  AS marca,
        c.`nombre`  AS categoria,
        i.`nombre`  AS industria
    FROM `Producto` p
    LEFT JOIN `DetalleProductoSucursal` dps ON dps.`codProducto` = p.`cod`
    LEFT JOIN `Marca`     m ON m.`cod` = p.`codMarca`
    LEFT JOIN `Categoria` c ON c.`cod` = p.`codCategoria`
    LEFT JOIN `Industria` i ON i.`cod` = p.`codIndustria`
    WHERE p.`estado` = 'activo'
      AND (p_nombre        = ''  OR p.`nombre`       LIKE CONCAT('%', p_nombre, '%'))
      AND (p_cod_categoria  = 0   OR p.`codCategoria` = p_cod_categoria)
      AND (p_precio_min     = 0   OR p.`precio`       >= p_precio_min)
      AND (p_precio_max     = 0   OR p.`precio`       <= p_precio_max)
    GROUP BY p.`cod`, p.`nombre`, p.`descripcion`, p.`precio`, p.`imagen`, p.`estado`,
             p.`codMarca`, p.`codIndustria`, p.`codCategoria`,
             m.`nombre`, c.`nombre`, i.`nombre`
    ORDER BY p.`cod` DESC;
END//

-- =============================================
-- VENDEDOR
-- =============================================

DROP PROCEDURE IF EXISTS sp_listar_vendedores//
CREATE PROCEDURE sp_listar_vendedores()
BEGIN
    SELECT `ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `nroCelular`, `usuarioCuenta`
    FROM `Vendedor`
    ORDER BY `ci` DESC;
END//

DROP PROCEDURE IF EXISTS sp_obtener_vendedor_por_clave//
CREATE PROCEDURE sp_obtener_vendedor_por_clave(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40)
)
BEGIN
    SELECT `ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `nroCelular`, `usuarioCuenta`
    FROM `Vendedor`
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS sp_crear_vendedor_con_cuenta//
CREATE PROCEDURE sp_crear_vendedor_con_cuenta(
    IN p_usuario VARCHAR(40),
    IN p_password VARCHAR(255),
    IN p_ci VARCHAR(20),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(50),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    START TRANSACTION;

    INSERT INTO `Cuenta` (`usuario`, `password`)
    VALUES (p_usuario, p_password);

    INSERT INTO `Vendedor` (`ci`, `nombres`, `apPaterno`, `apMaterno`, `correo`, `nroCelular`, `usuarioCuenta`)
    VALUES (p_ci, p_nombres, p_ap_paterno, p_ap_materno, p_correo, p_nro_celular, p_usuario);

    COMMIT;
END//

DROP PROCEDURE IF EXISTS sp_actualizar_vendedor//
CREATE PROCEDURE sp_actualizar_vendedor(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(50),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    UPDATE `Vendedor`
    SET `nombres` = p_nombres,
        `apPaterno` = p_ap_paterno,
        `apMaterno` = p_ap_materno,
        `correo` = p_correo,
        `nroCelular` = p_nro_celular
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;
END//

DROP PROCEDURE IF EXISTS sp_actualizar_vendedor_y_password//
CREATE PROCEDURE sp_actualizar_vendedor_y_password(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(50),
    IN p_nro_celular VARCHAR(30),
    IN p_password VARCHAR(255)
)
BEGIN
    UPDATE `Vendedor`
    SET `nombres` = p_nombres,
        `apPaterno` = p_ap_paterno,
        `apMaterno` = p_ap_materno,
        `correo` = p_correo,
        `nroCelular` = p_nro_celular
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;

    IF p_password IS NOT NULL AND p_password <> '' THEN
        UPDATE `Cuenta`
        SET `password` = p_password
        WHERE `usuario` = p_usuario;
    END IF;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_vendedor_y_cuenta//
CREATE PROCEDURE sp_eliminar_vendedor_y_cuenta(
    IN p_ci VARCHAR(20),
    IN p_usuario VARCHAR(40)
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        RESIGNAL;
    END;

    IF p_usuario = 'admin' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Cuenta protegida';
    END IF;

    START TRANSACTION;

    DELETE FROM `Vendedor`
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;

    DELETE FROM `Cuenta`
    WHERE `usuario` = p_usuario;

    COMMIT;
END//

-- =============================================
-- DETALLE PRODUCTO SUCURSAL
-- =============================================

DROP PROCEDURE IF EXISTS sp_listar_stock_sucursales//
CREATE PROCEDURE sp_listar_stock_sucursales()
BEGIN
    SELECT dps.`codProducto`, p.`nombre` AS producto,
           dps.`codSucursal`, s.`nombre` AS sucursal, dps.`stock`
    FROM `DetalleProductoSucursal` dps
    INNER JOIN `Producto` p ON p.`cod` = dps.`codProducto`
    INNER JOIN `Sucursal` s ON s.`cod` = dps.`codSucursal`
    ORDER BY dps.`codProducto` DESC, dps.`codSucursal` ASC;
END//

DROP PROCEDURE IF EXISTS sp_eliminar_stock_sucursal//
CREATE PROCEDURE sp_eliminar_stock_sucursal(
    IN p_cod_producto INT,
    IN p_cod_sucursal INT
)
BEGIN
    DELETE FROM `DetalleProductoSucursal`
    WHERE `codProducto` = p_cod_producto AND `codSucursal` = p_cod_sucursal;
END//

-- =============================================
-- VENTA
-- =============================================

DROP PROCEDURE IF EXISTS sp_existe_producto//
CREATE PROCEDURE sp_existe_producto(IN p_cod_producto INT)
BEGIN
    SELECT COUNT(*) AS existe
    FROM `Producto`
    WHERE `cod` = p_cod_producto
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS sp_obtener_ci_cliente_por_usuario//
CREATE PROCEDURE sp_obtener_ci_cliente_por_usuario(IN p_usuario VARCHAR(40))
BEGIN
    SELECT `ci`
    FROM `Cliente`
    WHERE `usuarioCuenta` = p_usuario
    LIMIT 1;
END//

DROP PROCEDURE IF EXISTS sp_crear_vendedor_con_cuenta//
CREATE PROCEDURE sp_crear_vendedor_con_cuenta(
    IN p_usuario VARCHAR(40),
    IN p_password VARCHAR(255),
    IN p_ci VARCHAR(20),
    IN p_nombres VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo VARCHAR(50),
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

DELIMITER ;

-- =============================================
-- ALTERACIONES DE ESQUEMA (rol en Cuenta)
-- =============================================
ALTER TABLE `Cuenta` ADD COLUMN IF NOT EXISTS `rol` VARCHAR(20) NOT NULL DEFAULT 'cliente';
UPDATE `Cuenta` SET `rol` = 'admin' WHERE `usuario` = 'admin' AND `rol` = 'cliente';

DELIMITER //

-- =============================================
-- REGISTRO PUBLICO DE CLIENTE
-- =============================================

DROP PROCEDURE IF EXISTS sp_registrar_cliente_con_cuenta//
CREATE PROCEDURE sp_registrar_cliente_con_cuenta(
    IN p_usuario    VARCHAR(40),
    IN p_password   VARCHAR(255),
    IN p_ci         VARCHAR(20),
    IN p_nombres    VARCHAR(50),
    IN p_ap_paterno VARCHAR(20),
    IN p_ap_materno VARCHAR(20),
    IN p_correo     VARCHAR(50),
    IN p_direccion  VARCHAR(45),
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

-- =============================================
-- MI CUENTA: obtener datos del cliente por usuario
-- =============================================

DROP PROCEDURE IF EXISTS sp_obtener_cliente_por_usuario//
CREATE PROCEDURE sp_obtener_cliente_por_usuario(IN p_usuario VARCHAR(40))
BEGIN
    SELECT cl.`ci`, cl.`nombres`, cl.`apPaterno`, cl.`apMaterno`,
           cl.`correo`, cl.`direccion`, cl.`nroCelular`, cl.`usuarioCuenta`
    FROM `Cliente` cl
    WHERE cl.`usuarioCuenta` = p_usuario
    LIMIT 1;
END//

-- =============================================
-- MI CUENTA: historial de compras del cliente
-- =============================================

DROP PROCEDURE IF EXISTS sp_historial_compras_cliente//
CREATE PROCEDURE sp_historial_compras_cliente(IN p_usuario VARCHAR(40))
BEGIN
    SELECT nv.`nro`, nv.`fechaHora`,
           COALESCE(SUM(dnv.`cant`), 0)                              AS totalItems,
           COALESCE(SUM(dnv.`cant` * p.`precio`), 0)                 AS totalMonto
    FROM `NotaVenta` nv
    INNER JOIN `Cliente` cl ON cl.`ci` = nv.`ciCliente` AND cl.`usuarioCuenta` = p_usuario
    LEFT  JOIN `DetalleNotaVenta` dnv ON dnv.`nroNotaVenta` = nv.`nro`
    LEFT  JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    GROUP BY nv.`nro`, nv.`fechaHora`
    ORDER BY nv.`nro` DESC;
END//

-- =============================================
-- MI CUENTA: actualizar perfil del cliente
-- =============================================

DROP PROCEDURE IF EXISTS sp_actualizar_perfil_cliente//
CREATE PROCEDURE sp_actualizar_perfil_cliente(
    IN p_ci         VARCHAR(20),
    IN p_usuario    VARCHAR(40),
    IN p_correo     VARCHAR(50),
    IN p_direccion  VARCHAR(45),
    IN p_nro_celular VARCHAR(30)
)
BEGIN
    UPDATE `Cliente`
    SET `correo`     = p_correo,
        `direccion`  = p_direccion,
        `nroCelular` = p_nro_celular
    WHERE `ci` = p_ci AND `usuarioCuenta` = p_usuario;
END//

-- =============================================
-- VENTAS: resumen con total monetario
-- =============================================

DROP PROCEDURE IF EXISTS sp_resumen_ventas//
CREATE PROCEDURE sp_resumen_ventas()
BEGIN
    SELECT nv.`nro`, nv.`fechaHora`, nv.`ciCliente`,
           CONCAT(cl.`nombres`, ' ', cl.`apPaterno`, ' ', cl.`apMaterno`) AS cliente,
           COALESCE(SUM(dnv.`cant`), 0)                                    AS totalItems,
           COALESCE(SUM(dnv.`cant` * p.`precio`), 0)                       AS totalMonto
    FROM `NotaVenta` nv
    INNER JOIN `Cliente` cl ON cl.`ci` = nv.`ciCliente`
    LEFT JOIN `DetalleNotaVenta` dnv ON dnv.`nroNotaVenta` = nv.`nro`
    LEFT JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    GROUP BY nv.`nro`, nv.`fechaHora`, nv.`ciCliente`, cl.`nombres`, cl.`apPaterno`, cl.`apMaterno`
    ORDER BY nv.`nro` DESC;
END//

-- =============================================
-- PANEL VENDEDOR: estadísticas del dashboard
-- =============================================

DROP PROCEDURE IF EXISTS sp_resumen_dashboard//
CREATE PROCEDURE sp_resumen_dashboard()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM `NotaVenta`
         WHERE DATE(`fechaHora`) = CURDATE())                                                AS ventasHoy,
        (SELECT COUNT(*) FROM `NotaVenta`
         WHERE `fechaHora` >= DATE_SUB(NOW(), INTERVAL 7 DAY))                              AS ventasSemana,
        (SELECT COUNT(*) FROM `NotaVenta`
         WHERE MONTH(`fechaHora`) = MONTH(NOW()) AND YEAR(`fechaHora`) = YEAR(NOW()))       AS ventasMes,
        (SELECT COALESCE(SUM(dnv.`cant` * p.`precio`), 0)
         FROM `DetalleNotaVenta` dnv
         INNER JOIN `Producto`  p  ON p.`cod`  = dnv.`codProducto`
         INNER JOIN `NotaVenta` nv ON nv.`nro` = dnv.`nroNotaVenta`
         WHERE MONTH(nv.`fechaHora`) = MONTH(NOW()) AND YEAR(nv.`fechaHora`) = YEAR(NOW())) AS ingresosMes,
        (SELECT COALESCE(SUM(dnv.`cant` * p.`precio`), 0)
         FROM `DetalleNotaVenta` dnv
         INNER JOIN `Producto` p ON p.`cod` = dnv.`codProducto`)                           AS ingresosTotal;
END//

DROP PROCEDURE IF EXISTS sp_productos_mas_vendidos//
CREATE PROCEDURE sp_productos_mas_vendidos(IN p_limite INT)
BEGIN
    SELECT p.`nombre`,
           SUM(dnv.`cant`)              AS totalVendido,
           SUM(dnv.`cant` * p.`precio`) AS totalIngresos
    FROM `DetalleNotaVenta` dnv
    INNER JOIN `Producto` p ON p.`cod` = dnv.`codProducto`
    GROUP BY p.`cod`, p.`nombre`
    ORDER BY totalVendido DESC
    LIMIT p_limite;
END//

DELIMITER ;
