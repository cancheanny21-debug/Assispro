<?php
/**
 * AsistePro - Archivo de Inicialización
 * 
 * Carga configuración, establece sesión segura y define funciones globales.
 * Este archivo debe incluirse al inicio de cada punto de entrada.
 */

// Cargar configuración de la aplicación primero (define constantes)
require_once __DIR__ . '/app.php';
require_once __DIR__ . '/database.php';

// Iniciar sesión segura
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start([
        'cookie_httponly'  => true,
        'cookie_samesite'  => 'Strict',
        'use_strict_mode'  => true,
        'gc_maxlifetime'   => SESSION_LIFETIME,
        'cookie_lifetime'  => SESSION_LIFETIME,
    ]);
}

// Configurar reporte de errores según modo
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ======================================================================
// FUNCIONES DE SEGURIDAD
// ======================================================================

/**
 * Genera un token CSRF y lo almacena en sesión
 *
 * @return string Token CSRF
 */
function generarTokenCSRF(): string
{
    if (empty($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

/**
 * Verifica que el token CSRF enviado sea válido
 *
 * @param string $token Token recibido del formulario
 * @return bool True si el token es válido
 */
function verificarTokenCSRF(string $token): bool
{
    if (empty($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
        return false;
    }
    $valido = hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    // Regenerar token tras la verificación
    unset($_SESSION[CSRF_TOKEN_NAME]);
    return $valido;
}

/**
 * Escapa texto para salida HTML segura (previene XSS)
 *
 * @param ?string $texto Texto a escapar
 * @return string Texto escapado
 */
function e(?string $texto): string
{
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

// ======================================================================
// FUNCIONES DE NAVEGACIÓN
// ======================================================================

/**
 * Redirige a una ruta relativa de la aplicación
 *
 * @param string $ruta Ruta destino (ej: '/login.php')
 */
function redirect(string $ruta): void
{
    header("Location: " . APP_URL . $ruta);
    exit;
}

/**
 * Genera una URL absoluta para un recurso de la aplicación
 *
 * @param string $ruta Ruta relativa
 * @return string URL completa
 */
function url(string $ruta = ''): string
{
    return APP_URL . $ruta;
}

// ======================================================================
// FUNCIONES DE AUTENTICACIÓN
// ======================================================================

/**
 * Verifica si el usuario ha iniciado sesión
 *
 * @return bool
 */
function estaAutenticado(): bool
{
    return isset($_SESSION['usuario_id']) && !empty($_SESSION['usuario_id']);
}

/**
 * Verifica si el usuario tiene un rol específico
 *
 * @param int $rolId ID del rol a verificar
 * @return bool
 */
function tieneRol(int $rolId): bool
{
    return isset($_SESSION['rol_id']) && (int)$_SESSION['rol_id'] === $rolId;
}

/**
 * Requiere autenticación; redirige al login si no hay sesión
 */
function requiereAutenticacion(): void
{
    if (!estaAutenticado()) {
        setMensaje('Debe iniciar sesión para acceder.', 'warning');
        redirect('/login.php');
    }
}

/**
 * Requiere un rol específico; redirige si no coincide
 *
 * @param int|array $roles Rol(es) permitido(s)
 */
function requiereRol(int|array $roles): void
{
    requiereAutenticacion();
    $roles = is_array($roles) ? $roles : [$roles];
    if (!isset($_SESSION['rol_id']) || !in_array((int)$_SESSION['rol_id'], $roles, true)) {
        setMensaje('No tiene permisos para acceder a esta sección.', 'danger');
        redirect('/dashboard.php');
    }
}

// ======================================================================
// FUNCIONES DE MENSAJES FLASH
// ======================================================================

/**
 * Establece un mensaje flash en la sesión
 *
 * @param string $texto Texto del mensaje
 * @param string $tipo  Tipo Bootstrap: success, danger, warning, info
 */
function setMensaje(string $texto, string $tipo = 'info'): void
{
    $_SESSION['mensaje'] = ['texto' => $texto, 'tipo' => $tipo];
}

/**
 * Obtiene y elimina el mensaje flash de la sesión
 *
 * @return array|null Arreglo con 'texto' y 'tipo', o null
 */
function getMensaje(): ?array
{
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        unset($_SESSION['mensaje']);
        return $mensaje;
    }
    return null;
}

