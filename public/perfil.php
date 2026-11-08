<?php
/**
 * AsistePro - Ruta pública para Mi Perfil
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/PerfilController.php';

$controller = new PerfilController();
$action = $_GET['action'] ?? 'index';

if ($action === 'cambiar_password') {
    $controller->actualizarPassword();
} else {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/perfil/index.php';
}

