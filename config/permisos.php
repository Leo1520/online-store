<?php
/**
 * Helpers de autorización para el panel administrativo.
 * Depende de $_SESSION['rol'] y $_SESSION['permisos'] (array de strings).
 * Los admins bypass toda verificación.
 */

function tienePermiso(string $permiso): bool {
    if (!isset($_SESSION['usuario'])) return false;
    if (($_SESSION['rol'] ?? '') === 'admin') return true;
    return in_array($permiso, $_SESSION['permisos'] ?? [], true);
}

function tieneAlgunPermiso(array $permisos): bool {
    foreach ($permisos as $p) {
        if (tienePermiso($p)) return true;
    }
    return false;
}

function requierePermiso(string $permiso): void {
    if (!tienePermiso($permiso)) {
        header('Location: index.php?page=inicio&msg=' . urlencode('No tienes permiso para acceder a esa sección.'));
        exit();
    }
}
