<?php
/**
 * AsistePro - Ruta pública para Gestión de Cursos
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/CursoController.php';

$controller = new CursoController();
$action = $_GET['action'] ?? 'index';

if ($action === 'guardar') {
    $controller->guardar();
} elseif ($action === 'eliminar') {
    $controller->eliminar();
} else {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/cursos/index.php';
}

