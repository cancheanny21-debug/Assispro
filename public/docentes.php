<?php
/**
 * AsistePro - Ruta pública para Gestión de Docentes
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/DocenteController.php';

$controller = new DocenteController();
$action = $_GET['action'] ?? 'index';

if ($action === 'guardar') {
    $controller->guardar();
} elseif ($action === 'eliminar') {
    $controller->eliminar();
} else {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/docentes/index.php';
}

