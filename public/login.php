<?php
/**
 * AsistePro - Procesador de Login
 * 
 * Punto de entrada para el inicio de sesión.
 * GET  → Muestra el formulario
 * POST → Procesa las credenciales
 */

require_once __DIR__ . '/../config/init.php';

// Si ya está autenticado, ir al dashboard
if (estaAutenticado()) {
    redirect('/dashboard.php');
}

// Procesar formulario de login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once APP_PATH . '/controllers/AuthController.php';

    $auth = new AuthController();
    $resultado = $auth->login(
        $_POST['email']      ?? '',
        $_POST['password']   ?? '',
        $_POST['csrf_token'] ?? ''
    );

    if ($resultado['exito']) {
        setMensaje($resultado['mensaje'], 'success');
        redirect('/dashboard.php');
    } else {
        setMensaje($resultado['mensaje'], 'danger');
    }
}

// Mostrar vista de login
require_once VIEW_PATH . '/auth/login.php';

