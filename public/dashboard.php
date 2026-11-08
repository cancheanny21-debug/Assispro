<?php
/**
 * AsistePro - Dashboard
 * 
 * Punto de entrada del panel principal.
 * Requiere autenticación.
 */

require_once __DIR__ . '/../config/init.php';

// Requiere usuario autenticado
requiereAutenticacion();

// Obtener estadísticas
require_once APP_PATH . '/controllers/DashboardController.php';
$dashController = new DashboardController();
$stats = $dashController->obtenerEstadisticas();

// Mostrar vista
require_once VIEW_PATH . '/dashboard/index.php';

