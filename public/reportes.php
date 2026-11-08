<?php
/**
 * AsistePro - Ruta pública para Reportes de Asistencia
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/ReporteController.php';

$controller = new ReporteController();
$datos = $controller->index();
extract($datos);
require_once VIEW_PATH . '/reportes/index.php';

