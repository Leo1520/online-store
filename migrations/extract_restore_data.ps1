# Script: extract_restore_data.ps1
# Extrae solo los INSERT de backup_leo.sql en orden FK-seguro
# y genera restore_data.sql listo para importar

$backup  = "C:\xampp\htdocs\online-store\backup_leo.sql"
$output  = "C:\xampp\htdocs\online-store\migrations\restore_data.sql"

# Tablas en orden FK (padre antes que hijo)
$tables = @('categoria','industria','marca','sucursal','cuenta','cliente','producto','detalleproductosucursal')

$lines = [System.IO.File]::ReadAllLines($backup, [System.Text.Encoding]::UTF8)

$out = [System.Collections.Generic.List[string]]::new()

$out.Add("-- ============================================================")
$out.Add("-- restore_data.sql  Datos restaurados desde backup_leo.sql")
$out.Add("-- SOLO datos, sin DROP, sin CREATE, sin SP")
$out.Add("-- ============================================================")
$out.Add("")
$out.Add("USE ``mydb``;")
$out.Add("SET FOREIGN_KEY_CHECKS = 0;")
$out.Add("START TRANSACTION;")
$out.Add("")

foreach ($table in $tables) {
    $pattern   = "^INSERT INTO ``$table``"
    $inBlock   = $false

    foreach ($line in $lines) {
        if (-not $inBlock) {
            if ($line -match $pattern) {
                $inBlock = $true
                $replaced = $line -replace "^INSERT INTO", "INSERT IGNORE INTO"
                $out.Add($replaced)
            }
        } else {
            $out.Add($line)
            if ($line.TrimEnd().EndsWith(";")) {
                $inBlock = $false
            }
        }
    }
    $out.Add("")
    $out.Add("-- --- fin $table ---")
    $out.Add("")
}

$out.Add("COMMIT;")
$out.Add("SET FOREIGN_KEY_CHECKS = 1;")
$out.Add("")
$out.Add("-- Ajustar AUTO_INCREMENT")
$out.Add("ALTER TABLE ``Categoria``          AUTO_INCREMENT = 100;")
$out.Add("ALTER TABLE ``Industria``          AUTO_INCREMENT = 400;")
$out.Add("ALTER TABLE ``Marca``              AUTO_INCREMENT = 600;")
$out.Add("ALTER TABLE ``Sucursal``           AUTO_INCREMENT = 10;")
$out.Add("ALTER TABLE ``Producto``           AUTO_INCREMENT = 15000;")

[System.IO.File]::WriteAllLines($output, $out, [System.Text.Encoding]::UTF8)

Write-Host "Listo: $($out.Count) lineas escritas en $output"
