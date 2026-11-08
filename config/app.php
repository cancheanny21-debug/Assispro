<?php
/**
 * AsistePro - Configuración General de la Aplicación
 * 
 * Define constantes globales utilizadas en todo el sistema.
 */

// Zona horaria de Ecuador
date_default_timezone_set('America/Guayaquil');

// Información de la aplicación
define('APP_NAME', 'AsistePro');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/Assispro/public');

// Rutas base del proyecto
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('APP_PATH', BASE_PATH . '/app');
define('VIEW_PATH', APP_PATH . '/views');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Configuración de sesión
define('SESSION_NAME', 'asistepro_session');
define('SESSION_LIFETIME', 3600); // 1 hora

// Configuración de seguridad
define('CSRF_TOKEN_NAME', 'csrf_token');
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutos

// Roles del sistema
define('ROL_ADMIN', 1);
define('ROL_DOCENTE', 2);
define('ROL_ESTUDIANTE', 3);

// Paginación
define('RECORDS_PER_PAGE', 15);

// Modo de depuración (desactivar en producción)
define('DEBUG_MODE', true);

