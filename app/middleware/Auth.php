<?php
/**
 * AsistePro - Middleware de Autenticación
 * 
 * Funciones auxiliares para proteger rutas y verificar permisos.
 * Las funciones principales están en config/init.php.
 * Este archivo contiene funciones adicionales de middleware.
 */

require_once __DIR__ . '/../../config/init.php';

/**
 * Verifica que la petición sea POST y tenga un token CSRF válido.
 * Redirige con mensaje de error si no se cumple.
 *
 * @param string $redirectUrl URL a la que redirigir en caso de fallo
 */
function requierePostConCSRF(string $redirectUrl = '/login.php'): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        setMensaje('Método no permitido.', 'danger');
        redirect($redirectUrl);
    }

    $token = $_POST[CSRF_TOKEN_NAME] ?? '';
    if (!verificarTokenCSRF($token)) {
        setMensaje('Token de seguridad inválido. Intente nuevamente.', 'danger');
        redirect($redirectUrl);
    }
}

/**
 * Verifica si el usuario actual puede acceder a un recurso de un docente específico
 *
 * @param int $docenteId ID del docente dueño del recurso
 * @return bool
 */
function puedeAccederComoDocente(int $docenteId): bool
{
    if (tieneRol(ROL_ADMIN)) {
        return true;
    }
    return tieneRol(ROL_DOCENTE) && isset($_SESSION['usuario_id']) && (int)$_SESSION['usuario_id'] === $docenteId;
}

