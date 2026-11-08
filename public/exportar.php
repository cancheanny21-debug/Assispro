<?php
/**
 * AsistePro - Ruta pública para Exportación de Reportes
 */

require_once __DIR__ . '/../config/init.php';
require_once APP_PATH . '/controllers/ReporteController.php';

$controller = new ReporteController();
$tipo = $_GET['tipo'] ?? 'excel';

if ($tipo === 'excel') {
    $controller->exportarExcel();
} elseif ($tipo === 'pdf') {
    $datos = $controller->index();
    extract($datos);
    require_once VIEW_PATH . '/reportes/imprimir.php';
} else {
    redirect('/reportes.php');
}
