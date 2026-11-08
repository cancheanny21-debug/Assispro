<?php
/**
 * AsistePro - Ruta pública para Gestión de Estudiantes
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/EstudianteController.php';

$controller = new EstudianteController();
$action = $_GET['action'] ?? 'index';

if ($action === 'guardar') {
    $controller->guardar();
} elseif ($action === 'eliminar') {
    $controller->eliminar();
} elseif ($action === 'matricular') {
    $controller->matricular();
} elseif ($action === 'desmatricular') {
    $controller->desmatricular();
} else {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/estudiantes/index.php';
}

