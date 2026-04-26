<?php
/**
 * Migration runner — ejecutar desde la raíz del proyecto:
 *   php migrations/run.php
 *   php migrations/run.php 002   <- solo una migración
 */
require_once __DIR__ . '/../config/database.php';
$db = Database::conectar();

$solo = $argv[1] ?? null;
$archivos = glob(__DIR__ . '/*.sql');
sort($archivos);

foreach ($archivos as $archivo) {
    $nombre = basename($archivo, '.sql');
    if ($solo && strpos($nombre, $solo) === false) continue;

    echo "\n▶ Ejecutando: $nombre\n";
    $sql = file_get_contents($archivo);

    // Separar por ; respetando bloques BEGIN...END
    $db->multi_query($sql);
    $ok = true;
    do {
        if ($db->errno) {
            echo "  ✗ Error: " . $db->error . "\n";
            $ok = false;
        }
        if ($res = $db->store_result()) $res->free();
    } while ($db->more_results() && $db->next_result());

    echo $ok ? "  ✓ Migración aplicada.\n" : "  ! Migración con errores.\n";
}
echo "\nListo.\n";
