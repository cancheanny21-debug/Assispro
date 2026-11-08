<?php
/**
 * AsistePro - Ruta pública para Historial de Asistencia
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/AsistenciaController.php';

$controller = new AsistenciaController();
$datos = $controller->historial();
extract($datos);
require_once VIEW_PATH . '/asistencia/historial.php';

