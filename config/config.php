<?php
/**
 * CONFIGURACIÓN GLOBAL DE LA APLICACIÓN
 * Centraliza todas las configuraciones en un solo lugar
 */

// ===== INFORMACIÓN DE LA APLICACIÓN =====
define('APP_NAME', 'Tienda en Línea');
define('APP_VERSION', '1.0.0');
define('APP_DESCRIPTION', 'Sistema de comercio electrónico con arquitectura MVC');

// ===== CONFIGURACIÓN DE BASE DE DATOS =====
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'mydb');
define('DB_CHARSET', 'utf8mb4');

// ===== CONFIGURACIÓN DE SESIÓN =====
define('SESSION_TIMEOUT', 3600); // 1 hora en segundos
define('SESSION_NAME', 'tienda_online');

// ===== CONFIGURACIÓN DE ROLES =====
define('ROLE_ID_ADMIN', 1);
define('ROLE_ID_TRABAJADOR', 2);
define('ROLE_ID_CLIENTE', 3);
define('ROLE_NAME_ADMIN', 'admin');
define('ROLE_NAME_TRABAJADOR', 'trabajador');
define('ROLE_NAME_CLIENTE', 'cliente');

// ===== CONFIGURACIÓN DE SEGURIDAD =====
define('PASSWORD_HASH_ALGO', PASSWORD_BCRYPT);
define('PASSWORD_HASH_COST', 10);
define('SALT_LENGTH', 16);

// ===== CONFIGURACIÓN DE ARCHIVOS =====
define('UPLOAD_DIR', __DIR__ . '/../../Recursos/imagenes/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ===== CONFIGURACIÓN DE PAGOS =====
define('CURRENCY', 'Bs');
define('TAX_RATE', 0.13); // 13%
define('MIN_ORDER_AMOUNT', 10);
define('MAX_ORDER_AMOUNT', 100000);

// ===== CONFIGURACIÓN DE PAGINACIÓN =====
define('ITEMS_PER_PAGE', 12);
define('ADMIN_ITEMS_PER_PAGE', 20);

// ===== CONFIGURACIÓN DE EMAIL =====
define('MAIL_FROM', 'noreply@tienda-online.com');
define('MAIL_FROM_NAME', 'Tienda en Línea');

// ===== RUTAS RELATIVAS =====
define('BASE_URL', '/online-store/public/index.php');
define('ASSETS_URL', '/online-store/Recursos');
define('IMAGES_URL', '/online-store/Recursos/imagenes');

// ===== CONFIGURACIÓN DE ROLES =====
define('ROLE_ADMIN', 'admin');
define('ROLE_CUSTOMER', 'customer');

// ===== MENSAJES PREDEFINIDOS =====
$MESSAGES = [
    'success' => [
        'created' => 'Registro creado exitosamente.',
        'updated' => 'Registro actualizado exitosamente.',
        'deleted' => 'Registro eliminado exitosamente.',
        'login' => '¡Bienvenido!',
        'logout' => 'Has cerrado sesión.',
    ],
    'error' => [
        'db_connection' => 'Error de conexión a la base de datos.',
        'invalid_input' => 'Datos inválidos.',
        'unauthorized' => 'No tienes permiso para realizar esta acción.',
        'not_found' => 'El registro no fue encontrado.',
        'duplicate' => 'El registro ya existe.',
        'password_mismatch' => 'Las contraseñas no coinciden.',
        'weak_password' => 'La contraseña debe tener al menos 6 caracteres.',
    ]
];

// ===== CONFIGURACIÓN DE LOGS =====
define('LOG_DIR', __DIR__ . '/../../logs/');
define('LOG_ERRORS', true);
define('LOG_QUERIES', false);

// ===== VALIDACIONES =====
define('MIN_PASSWORD_LENGTH', 6);
define('MIN_USERNAME_LENGTH', 3);
define('MAX_USERNAME_LENGTH', 50);
define('CI_PATTERN', '/^[0-9]{6,8}$/');
define('PHONE_PATTERN', '/^[0-9\-\+\s\(\)]{7,20}$/');

// ===== ESTADOS PREDEFINIDOS =====
$PRODUCT_STATES = ['Activo', 'Inactivo', 'Descontinuado'];
$ORDER_STATES = ['Pendiente', 'Confirmada', 'Entregada', 'Cancelada'];
$PAYMENT_METHODS = ['Tarjeta de Crédito', 'Tarjeta de Débito', 'Transferencia Bancaria'];

// ===== FUNCIONES AUXILIARES =====

/**
 * Obtiene un mensaje predefinido
 */
function getMensaje($tipo, $clave) {
    global $MESSAGES;
    return $MESSAGES[$tipo][$clave] ?? 'Mensaje no encontrado.';
}

/**
 * Obtiene la configuración de BD
 */
function getDBConfig() {
    return [
        'host' => DB_HOST,
        'user' => DB_USER,
        'pass' => DB_PASS,
        'name' => DB_NAME,
        'charset' => DB_CHARSET
    ];
}

/**
 * Obtiene estados de producto
 */
function getProductStates() {
    global $PRODUCT_STATES;
    return $PRODUCT_STATES;
}

/**
 * Obtiene métodos de pago
 */
function getPaymentMethods() {
    global $PAYMENT_METHODS;
    return $PAYMENT_METHODS;
}

/**
 * Calcula precio con impuesto
 */
function calcularPrecioConImpuesto($precio) {
    return $precio * (1 + TAX_RATE);
}

/**
 * Valida email
 */
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Valida contraseña
 */
function validarPassword($password) {
    return strlen($password) >= MIN_PASSWORD_LENGTH;
}

/**
 * Valida username
 */
function validarUsername($username) {
    $length = strlen($username);
    return $length >= MIN_USERNAME_LENGTH && $length <= MAX_USERNAME_LENGTH;
}

/**
 * Valida CI
 */
function validarCI($ci) {
    return preg_match(CI_PATTERN, $ci);
}

/**
 * Valida teléfono
 */
function validarTelefono($telefono) {
    return preg_match(PHONE_PATTERN, $telefono);
}

/**
 * Genera slug para URLs
 */
function slugify($texto) {
    $texto = strtolower(trim($texto));
    $texto = preg_replace('/[^\w\s-]/', '', $texto);
    $texto = preg_replace('/[\s_]+/', '-', $texto);
    $texto = preg_replace('/^-+|-+$/', '', $texto);
    return $texto;
}

/**
 * Formatea cantidad de dinero
 */
function formatearDinero($cantidad, $moneda = CURRENCY) {
    return $moneda . ' ' . number_format($cantidad, 2, '.', ',');
}

/**
 * Log de actividades
 */
function registrarLog($tipo, $mensaje, $usuario = null) {
    if (!LOG_ERRORS) return;
    
    $timestamp = date('Y-m-d H:i:s');
    $log_message = "[$timestamp] [$tipo] $mensaje";
    if ($usuario) {
        $log_message .= " (Usuario: $usuario)";
    }
    $log_message .= "\n";
    
    @file_put_contents(LOG_DIR . 'app.log', $log_message, FILE_APPEND);
}

// Crear carpeta de logs si no existe
if (LOG_ERRORS && !is_dir(LOG_DIR)) {
    @mkdir(LOG_DIR, 0755, true);
}

?>
