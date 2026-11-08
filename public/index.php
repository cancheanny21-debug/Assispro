<?php
/**
 * AsistePro - Punto de Entrada Principal
 * 
 * Todas las peticiones se dirigen aquí.
 * Carga la configuración y redirige al login o dashboard según la sesión.
 */

// Cargar inicialización
require_once __DIR__ . '/../config/init.php';

// Si el usuario está autenticado, ir al dashboard; si no, al login
if (estaAutenticado()) {
    redirect('/dashboard.php');
} else {
    redirect('/login.php');
}

