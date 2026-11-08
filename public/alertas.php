<?php
/**
 * AsistePro - Ruta pública para Gestión de Alertas
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/AlertaController.php';

$controller = new AlertaController();
$action = $_GET['action'] ?? 'index';

if ($action === 'marcar_leida') {
    $controller->marcarLeida();
} elseif ($action === 'marcar_todas') {
    $controller->marcarTodas();
} else {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/alertas/index.php';
}

