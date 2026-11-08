<?php
/**
 * AsistePro - Cierre de Sesión
 * 
 * Destruye la sesión y redirige al login.
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/AuthController.php';

$auth = new AuthController();
$auth->logout();

setMensaje('Ha cerrado sesión correctamente.', 'success');
redirect('/login.php');

