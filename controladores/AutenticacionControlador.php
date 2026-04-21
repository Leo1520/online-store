<?php
require_once __DIR__ . '/../modelos/Cuenta.php';

class AutenticacionControlador {
    public function login() {
        // Cualquier acceso a pagina=login abre el modal en la página de inicio
        header('Location: index.php?pagina=inicio');
        exit();
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?pagina=inicio');
        exit();
    }
}
