-- ══════════════════════════════════════════════════════════════
--  CONSULTAS ELECTROHOGAR — Base de datos: mydb
--  Ejecutar en phpMyAdmin, HeidiSQL o MySQL CLI
-- ══════════════════════════════════════════════════════════════

USE mydb;

-- ──────────────────────────────────────────────────────────────
-- 1. SUCURSALES
-- ──────────────────────────────────────────────────────────────
SELECT
    cod          AS ID,
    nombre       AS Sucursal,
    direccion    AS Direccion,
    nroTelefono  AS Telefono
FROM sucursal
ORDER BY cod;


-- ──────────────────────────────────────────────────────────────
-- 2. PRODUCTOS CON STOCK POR SUCURSAL
-- ──────────────────────────────────────────────────────────────
SELECT
    p.cod                                       AS ID_Producto,
    p.nombre                                    AS Producto,
    c.nombre                                    AS Categoria,
    m.nombre                                    AS Marca,
    CONCAT('Bs. ', FORMAT(p.precio, 2))         AS Precio,
    p.estado                                    AS Estado,
    s.nombre                                    AS Sucursal,
    CAST(dps.stock AS UNSIGNED)                 AS Stock_Sucursal
FROM Producto p
JOIN DetalleProductoSucursal dps ON dps.codProducto = p.cod
JOIN sucursal s                   ON s.cod = dps.codSucursal
LEFT JOIN Categoria c             ON c.cod = p.codCategoria
LEFT JOIN Marca m                 ON m.cod = p.codMarca
ORDER BY p.cod, s.cod;


-- ──────────────────────────────────────────────────────────────
-- 3. STOCK TOTAL POR PRODUCTO (todas las sucursales sumadas)
-- ──────────────────────────────────────────────────────────────
SELECT
    p.cod                                       AS ID_Producto,
    p.nombre                                    AS Producto,
    c.nombre                                    AS Categoria,
    CONCAT('Bs. ', FORMAT(p.precio, 2))         AS Precio,
    p.estado                                    AS Estado,
    SUM(CAST(dps.stock AS UNSIGNED))            AS Stock_Total,
    CASE
        WHEN SUM(CAST(dps.stock AS UNSIGNED)) = 0 THEN 'SIN STOCK'
        WHEN SUM(CAST(dps.stock AS UNSIGNED)) <= 5 THEN 'STOCK BAJO'
        ELSE 'DISPONIBLE'
    END                                         AS Disponibilidad
FROM Producto p
JOIN DetalleProductoSucursal dps ON dps.codProducto = p.cod
LEFT JOIN Categoria c             ON c.cod = p.codCategoria
GROUP BY p.cod, p.nombre, c.nombre, p.precio, p.estado
ORDER BY Stock_Total DESC;


-- ──────────────────────────────────────────────────────────────
-- 4. VENTAS CON DETALLE DE CLIENTE Y PRODUCTOS
-- ──────────────────────────────────────────────────────────────
SELECT
    nv.nro                                      AS Nro_Venta,
    nv.fechaHora                                AS Fecha,
    nv.estado                                   AS Estado,
    CONCAT(cl.nombres, ' ', cl.apPaterno)       AS Cliente,
    cl.ci                                       AS CI,
    cl.correo                                   AS Correo,
    p.nombre                                    AS Producto,
    dnv.cant                                    AS Cantidad,
    CONCAT('Bs. ', FORMAT(p.precio, 2))         AS Precio_Unit,
    CONCAT('Bs. ', FORMAT(p.precio * dnv.cant, 2)) AS Subtotal
FROM NotaVenta nv
JOIN DetalleNotaVenta dnv ON dnv.nroNotaVenta = nv.nro
JOIN Producto p           ON p.cod = dnv.codProducto
JOIN cliente cl           ON cl.ci = nv.ciCliente
ORDER BY nv.nro DESC, dnv.item;


-- ──────────────────────────────────────────────────────────────
-- 5. RESUMEN DE VENTAS POR PEDIDO (total por nota de venta)
-- ──────────────────────────────────────────────────────────────
SELECT
    nv.nro                                          AS Nro_Venta,
    nv.fechaHora                                    AS Fecha,
    nv.estado                                       AS Estado,
    CONCAT(cl.nombres, ' ', cl.apPaterno)           AS Cliente,
    cl.ci                                           AS CI,
    COUNT(dnv.codProducto)                          AS Cant_Productos,
    SUM(dnv.cant)                                   AS Total_Unidades,
    CONCAT('Bs. ', FORMAT(SUM(p.precio * dnv.cant), 2)) AS Total_Venta
FROM NotaVenta nv
JOIN DetalleNotaVenta dnv ON dnv.nroNotaVenta = nv.nro
JOIN Producto p           ON p.cod = dnv.codProducto
JOIN cliente cl           ON cl.ci = nv.ciCliente
GROUP BY nv.nro, nv.fechaHora, nv.estado, cl.nombres, cl.apPaterno, cl.ci
ORDER BY nv.nro DESC;


-- ──────────────────────────────────────────────────────────────
-- 6. VENTAS POR FECHA (agrupado por día)
-- ──────────────────────────────────────────────────────────────
SELECT
    DATE(nv.fechaHora)                              AS Dia,
    COUNT(DISTINCT nv.nro)                          AS Total_Pedidos,
    SUM(dnv.cant)                                   AS Unidades_Vendidas,
    CONCAT('Bs. ', FORMAT(SUM(p.precio * dnv.cant), 2)) AS Ingresos_Dia
FROM NotaVenta nv
JOIN DetalleNotaVenta dnv ON dnv.nroNotaVenta = nv.nro
JOIN Producto p           ON p.cod = dnv.codProducto
GROUP BY DATE(nv.fechaHora)
ORDER BY Dia DESC;


-- ──────────────────────────────────────────────────────────────
-- 7. PRODUCTOS MÁS VENDIDOS
-- ──────────────────────────────────────────────────────────────
SELECT
    p.nombre                                        AS Producto,
    c.nombre                                        AS Categoria,
    CONCAT('Bs. ', FORMAT(p.precio, 2))             AS Precio,
    SUM(dnv.cant)                                   AS Total_Vendido,
    CONCAT('Bs. ', FORMAT(SUM(p.precio * dnv.cant), 2)) AS Ingreso_Generado,
    SUM(CAST(dps.stock AS UNSIGNED))                AS Stock_Restante
FROM DetalleNotaVenta dnv
JOIN Producto p           ON p.cod = dnv.codProducto
LEFT JOIN Categoria c     ON c.cod = p.codCategoria
LEFT JOIN DetalleProductoSucursal dps ON dps.codProducto = p.cod
GROUP BY p.cod, p.nombre, c.nombre, p.precio
ORDER BY Total_Vendido DESC
LIMIT 10;


-- ──────────────────────────────────────────────────────────────
-- 8. CLIENTES CON MÁS COMPRAS
-- ──────────────────────────────────────────────────────────────
SELECT
    CONCAT(cl.nombres, ' ', cl.apPaterno)           AS Cliente,
    cl.ci                                           AS CI,
    cl.correo                                       AS Correo,
    cl.nroCelular                                   AS Celular,
    COUNT(DISTINCT nv.nro)                          AS Total_Pedidos,
    SUM(dnv.cant)                                   AS Unidades_Compradas,
    CONCAT('Bs. ', FORMAT(SUM(p.precio * dnv.cant), 2)) AS Total_Gastado
FROM cliente cl
JOIN NotaVenta nv         ON nv.ciCliente = cl.ci
JOIN DetalleNotaVenta dnv ON dnv.nroNotaVenta = nv.nro
JOIN Producto p           ON p.cod = dnv.codProducto
GROUP BY cl.ci, cl.nombres, cl.apPaterno, cl.correo, cl.nroCelular
ORDER BY Total_Pedidos DESC;


-- ──────────────────────────────────────────────────────────────
-- 9. STOCK CRÍTICO (productos con 5 o menos unidades)
-- ──────────────────────────────────────────────────────────────
SELECT
    p.cod                                       AS ID,
    p.nombre                                    AS Producto,
    c.nombre                                    AS Categoria,
    CONCAT('Bs. ', FORMAT(p.precio, 2))         AS Precio,
    SUM(CAST(dps.stock AS UNSIGNED))            AS Stock_Total,
    CASE
        WHEN SUM(CAST(dps.stock AS UNSIGNED)) = 0 THEN '🔴 AGOTADO'
        ELSE '🟡 STOCK BAJO'
    END                                         AS Alerta
FROM Producto p
JOIN DetalleProductoSucursal dps ON dps.codProducto = p.cod
LEFT JOIN Categoria c             ON c.cod = p.codCategoria
WHERE p.estado = 'activo'
GROUP BY p.cod, p.nombre, c.nombre, p.precio
HAVING Stock_Total <= 5
ORDER BY Stock_Total ASC;
