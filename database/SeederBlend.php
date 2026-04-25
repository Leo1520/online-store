<?php
/**
 * Seeder BLEND Muestrario
 * Inserta: Categorias, Industrias (desde SubCategoria), Marcas, Productos
 * Stock: 80% Sucursal Plancity (cod=1), 20% Sucursal Norte (cod=2)
 *
 * Uso: php database/SeederBlend.php
 */

define('ROOT', __DIR__ . '/..');
require_once ROOT . '/config/database.php';

// ── Configuración ──────────────────────────────────────────────────────────
const XLSX_FILE     = ROOT . '/BLEND  Muestrario (2).xlsx';
const SUC_PRINCIPAL = 1;   // Sucursal Plancity  → 80%
const SUC_SECUNDARIA= 2;   // Sucursal Norte     → 20%
const STOCK_DEFAULT = 10;  // Stock para productos con existencia = 0
const IMAGEN_DEFAULT= 'producto.png';

// ── Helpers ────────────────────────────────────────────────────────────────
function log_msg(string $msg): void { echo '[' . date('H:i:s') . '] ' . $msg . PHP_EOL; }

function limpiar(string $v): string {
    return trim(preg_replace('/\s+/', ' ', $v));
}

function esSinDato(string $v): bool {
    $v = strtolower(trim($v));
    return in_array($v, ['', '- ningúna línea -', '- ningún fabricante -', '- ninguna línea -', '- ningún fabricante -', '-']);
}

// ── Leer Excel ────────────────────────────────────────────────────────────
log_msg('Leyendo Excel...');
copy(XLSX_FILE, ROOT . '/database/tmp_blend.zip');
$xml = file_get_contents('phar://' . realpath(ROOT . '/database/tmp_blend.zip') . '/xl/worksheets/sheet1.xml');
unlink(ROOT . '/database/tmp_blend.zip');

$dom = new DOMDocument();
@$dom->loadXML($xml);
$xpath = new DOMXPath($dom);
$xpath->registerNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');

$filas = [];
foreach ($xpath->query('//x:row') as $row) {
    $r = (int)$row->getAttribute('r');
    if ($r < 3) continue;
    $rd = [];
    foreach ($xpath->query('x:c', $row) as $cell) {
        $col = preg_replace('/[0-9]/', '', $cell->getAttribute('r'));
        $t   = $cell->getAttribute('t');
        if ($t === 'inlineStr') {
            $tn  = $xpath->query('x:is/x:t', $cell)->item(0);
            $val = $tn ? trim($tn->textContent) : '';
        } else {
            $vn  = $xpath->query('x:v', $cell)->item(0);
            $val = $vn ? trim($vn->textContent) : '';
        }
        $rd[$col] = $val;
    }
    // Columnas: A=Codigo B=Nombre C=Categoria D=SubCategoria F=Marca H=Existencia K=PrecioVigente L=PrecioPropuesto
    if (!empty($rd['B']) && !empty($rd['C'])) {
        $filas[] = $rd;
    }
}
log_msg('Filas válidas leídas: ' . count($filas));

// ── Extraer únicos ────────────────────────────────────────────────────────
$catNombres = [];
$indNombres = [];
$mrkNombres = [];

foreach ($filas as $f) {
    $cat = limpiar($f['C'] ?? '');
    $ind = limpiar($f['D'] ?? '');
    $mrk = limpiar($f['F'] ?? '');

    if (!empty($cat))               $catNombres[$cat] = true;
    if (!empty($ind) && !esSinDato($ind)) $indNombres[$ind] = true;
    if (!empty($mrk) && !esSinDato($mrk)) $mrkNombres[$mrk] = true;
}

$catNombres = array_keys($catNombres);
$indNombres = array_keys($indNombres);
$mrkNombres = array_keys($mrkNombres);
sort($catNombres); sort($indNombres); sort($mrkNombres);

log_msg('Categorías únicas: '  . count($catNombres));
log_msg('Industrias únicas: '  . count($indNombres));
log_msg('Marcas únicas: '      . count($mrkNombres));

// ── Conectar BD ───────────────────────────────────────────────────────────
$db = Database::conectar();
$db->begin_transaction();

try {
    // ──────────────────────────────────────────────────────────────────────
    // 1. CATEGORÍAS
    // ──────────────────────────────────────────────────────────────────────
    log_msg('Insertando categorías...');
    $catMap = [];   // nombre → cod

    // Cargar las ya existentes
    $res = $db->query('SELECT cod, nombre FROM Categoria');
    while ($row = $res->fetch_assoc()) {
        $catMap[strtoupper(limpiar($row['nombre']))] = (int)$row['cod'];
    }

    $stmtCat = $db->prepare('INSERT IGNORE INTO Categoria (nombre) VALUES (?)');
    foreach ($catNombres as $nombre) {
        $key = strtoupper($nombre);
        if (!isset($catMap[$key])) {
            $stmtCat->bind_param('s', $nombre);
            $stmtCat->execute();
            $catMap[$key] = (int)$db->insert_id;
        }
    }
    $stmtCat->close();
    log_msg('  → ' . count($catMap) . ' categorías disponibles');

    // ──────────────────────────────────────────────────────────────────────
    // 2. INDUSTRIAS (desde SubCategoria)
    // ──────────────────────────────────────────────────────────────────────
    log_msg('Insertando industrias...');
    $indMap = [];

    $res = $db->query('SELECT cod, nombre FROM Industria');
    while ($row = $res->fetch_assoc()) {
        $indMap[strtoupper(limpiar($row['nombre']))] = (int)$row['cod'];
    }

    $stmtInd = $db->prepare('INSERT IGNORE INTO Industria (nombre) VALUES (?)');
    foreach ($indNombres as $nombre) {
        $key = strtoupper($nombre);
        if (!isset($indMap[$key])) {
            $stmtInd->bind_param('s', $nombre);
            $stmtInd->execute();
            $indMap[$key] = (int)$db->insert_id;
        }
    }
    $stmtInd->close();
    log_msg('  → ' . count($indMap) . ' industrias disponibles');

    // ──────────────────────────────────────────────────────────────────────
    // 3. MARCAS
    // ──────────────────────────────────────────────────────────────────────
    log_msg('Insertando marcas...');
    $mrkMap = [];

    $res = $db->query('SELECT cod, nombre FROM Marca');
    while ($row = $res->fetch_assoc()) {
        $mrkMap[strtoupper(limpiar($row['nombre']))] = (int)$row['cod'];
    }

    $stmtMrk = $db->prepare('INSERT IGNORE INTO Marca (nombre) VALUES (?)');
    foreach ($mrkNombres as $nombre) {
        $key = strtoupper($nombre);
        if (!isset($mrkMap[$key])) {
            $stmtMrk->bind_param('s', $nombre);
            $stmtMrk->execute();
            $mrkMap[$key] = (int)$db->insert_id;
        }
    }
    $stmtMrk->close();
    log_msg('  → ' . count($mrkMap) . ' marcas disponibles');

    // ──────────────────────────────────────────────────────────────────────
    // 4. PRODUCTOS
    // ──────────────────────────────────────────────────────────────────────
    log_msg('Insertando productos...');

    $stmtProd = $db->prepare(
        'INSERT INTO Producto (codigo, nombre, descripcion, precioVigente, precioPropuesto, imagen, codMarca, codIndustria, codCategoria, estado)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "activo")'
    );

    // Marcar IDs por defecto cuando no hay industria/marca
    $indDefault = reset($indMap) ?: 1;
    $mrkDefault = reset($mrkMap) ?: 1;

    $productosInsertados = 0;
    $stockPendiente      = [];   // [codProducto => stockTotal]
    $nombresUsados       = [];   // para deduplicar
    $codigosUsados       = [];   // para deduplicar códigos

    foreach ($filas as $f) {
        $nombre = limpiar($f['B'] ?? '');
        if (empty($nombre)) continue;

        // Deduplicar por nombre exacto
        if (isset($nombresUsados[strtoupper($nombre)])) continue;
        $nombresUsados[strtoupper($nombre)] = true;

        $codigo = limpiar($f['A'] ?? '');
        // Si el código está duplicado o vacío, dejarlo en null
        if (!empty($codigo) && isset($codigosUsados[$codigo])) $codigo = '';
        if (!empty($codigo)) $codigosUsados[$codigo] = true;
        $codigoVal = !empty($codigo) ? $codigo : null;

        $catKey    = strtoupper(limpiar($f['C'] ?? ''));
        $indKey    = strtoupper(limpiar($f['D'] ?? ''));
        $mrkKey    = strtoupper(limpiar($f['F'] ?? ''));
        $precioVigente    = (float)($f['K'] ?? 0);   // PrecioVigente  = precio actual de venta
        $precioPropuesto  = (float)($f['L'] ?? 0);   // PrecioPropuesto = precio de referencia/lista
        $stock            = (int)($f['H'] ?? 0);

        $codCat  = $catMap[$catKey]             ?? 1;
        $codInd  = (!esSinDato($f['D'] ?? '') && isset($indMap[$indKey])) ? $indMap[$indKey] : $indDefault;
        $codMrk  = (!esSinDato($f['F'] ?? '') && isset($mrkMap[$mrkKey])) ? $mrkMap[$mrkKey] : $mrkDefault;

        if ($precioVigente   <= 0) $precioVigente   = 1.00;
        if ($precioPropuesto <= 0) $precioPropuesto = $precioVigente;
        $imagen  = IMAGEN_DEFAULT;

        $stmtProd->bind_param('sssddsiii', $codigoVal, $nombre, $nombre, $precioVigente, $precioPropuesto, $imagen, $codMrk, $codInd, $codCat);
        $stmtProd->execute();
        $codProducto = (int)$db->insert_id;

        // Guardar stock para insertar después
        $stockPendiente[$codProducto] = $stock > 0 ? $stock : STOCK_DEFAULT;
        $productosInsertados++;
    }
    $stmtProd->close();
    log_msg('  → ' . $productosInsertados . ' productos insertados');

    // ──────────────────────────────────────────────────────────────────────
    // 5. STOCK — 80% Sucursal Principal / 20% Sucursal Secundaria
    // ──────────────────────────────────────────────────────────────────────
    log_msg('Asignando stock (80% / 20%)...');

    $stmtStock = $db->prepare(
        'INSERT INTO DetalleProductoSucursal (codProducto, codSucursal, stock)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE stock = VALUES(stock)'
    );

    $stockTotal = 0;
    foreach ($stockPendiente as $codProducto => $total) {
        $s1 = (int)ceil($total * 0.80);   // 80% → Sucursal Plancity
        $s2 = (int)floor($total * 0.20);  // 20% → Sucursal Norte

        // Sucursal principal
        $suc1 = SUC_PRINCIPAL;
        $stmtStock->bind_param('iii', $codProducto, $suc1, $s1);
        $stmtStock->execute();

        // Sucursal secundaria (solo si hay stock)
        if ($s2 > 0) {
            $suc2 = SUC_SECUNDARIA;
            $stmtStock->bind_param('iii', $codProducto, $suc2, $s2);
            $stmtStock->execute();
        }

        $stockTotal += $total;
    }
    $stmtStock->close();
    log_msg('  → Stock asignado para ' . count($stockPendiente) . ' productos');
    log_msg('  → Stock total distribuido: ' . $stockTotal . ' unidades');

    // ── Commit ──────────────────────────────────────────────────────────
    $db->commit();
    log_msg('');
    log_msg('✓ Seeder completado exitosamente');
    log_msg('─────────────────────────────────────────');
    log_msg('  Categorías insertadas : ' . count($catNombres));
    log_msg('  Industrias insertadas : ' . count($indNombres));
    log_msg('  Marcas insertadas     : ' . count($mrkNombres));
    log_msg('  Productos insertados  : ' . $productosInsertados);
    log_msg('  Registros de stock    : hasta ' . (count($stockPendiente) * 2) . ' (80/20 por sucursal)');
    log_msg('─────────────────────────────────────────');

} catch (Throwable $e) {
    $db->rollback();
    log_msg('ERROR: ' . $e->getMessage());
    log_msg('Se hizo rollback — no se insertó nada.');
    exit(1);
}
