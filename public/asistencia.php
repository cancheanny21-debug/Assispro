<?php
/**
 * AsistePro - Ruta pública para Registro de Asistencia
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/AsistenciaController.php';

$controller = new AsistenciaController();
$action = $_GET['action'] ?? 'index';

if ($action === 'guardar') {
    $controller->guardar();
} else {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/asistencia/index.php';
}

