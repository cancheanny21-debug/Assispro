<?php
/**
 * AsistePro - Controlador del Dashboard
 * 
 * Obtiene estadísticas generales para mostrar en el panel principal.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Usuario.php';

class DashboardController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Obtiene las estadísticas generales del sistema
     *
     * @return array Arreglo con totales y porcentajes
     */
    public function obtenerEstadisticas(): array
    {
        $stats = [];

        // Total de estudiantes activos
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total FROM usuarios WHERE rol_id = " . ROL_ESTUDIANTE . " AND activo = 1"
        );
        $stats['total_estudiantes'] = (int)$stmt->fetch()['total'];

        // Total de docentes activos
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total FROM usuarios WHERE rol_id = " . ROL_DOCENTE . " AND activo = 1"
        );
        $stats['total_docentes'] = (int)$stmt->fetch()['total'];

        // Total de cursos activos
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total FROM cursos WHERE activo = 1"
        );
        $stats['total_cursos'] = (int)$stmt->fetch()['total'];

        // Total de asistencias registradas
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total FROM asistencias"
        );
        $stats['total_asistencias'] = (int)$stmt->fetch()['total'];

        // Porcentaje de asistencia general (presentes + atrasados / total)
        $stmt = $this->db->query(
            "SELECT 
                COUNT(*) AS total,
                SUM(CASE WHEN estado IN ('presente', 'atrasado') THEN 1 ELSE 0 END) AS asistieron
             FROM asistencias"
        );
        $row = $stmt->fetch();
        $stats['porcentaje_asistencia'] = $row['total'] > 0
            ? round(($row['asistieron'] / $row['total']) * 100, 1)
            : 0;

        // Distribución de estados de asistencia (para gráfico)
        $stmt = $this->db->query(
            "SELECT estado, COUNT(*) AS total FROM asistencias GROUP BY estado"
        );
        $stats['distribucion_estados'] = $stmt->fetchAll();

        // Alertas no leídas
        $stmt = $this->db->query(
            "SELECT COUNT(*) AS total FROM alertas WHERE leida = 0"
        );
        $stats['alertas_pendientes'] = (int)$stmt->fetch()['total'];

        // Asistencia de los últimos 7 días (para gráfico de línea)
        $stmt = $this->db->query(
            "SELECT 
                fecha,
                COUNT(*) AS total,
                SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes,
                SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes,
                SUM(CASE WHEN estado = 'atrasado' THEN 1 ELSE 0 END) AS atrasados,
                SUM(CASE WHEN estado = 'justificado' THEN 1 ELSE 0 END) AS justificados
             FROM asistencias
             WHERE fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             GROUP BY fecha
             ORDER BY fecha ASC"
        );
        $stats['asistencia_semanal'] = $stmt->fetchAll();

        // Últimas alertas para el widget del dashboard
        require_once APP_PATH . '/models/Alerta.php';
        $alertaModel = new Alerta();
        $stats['ultimas_alertas'] = $alertaModel->listar(null, false, 5);

        // Cursos con su resumen de matriculados
        require_once APP_PATH . '/models/Curso.php';
        $cursoModel = new Curso();
        $stats['cursos_resumen'] = $cursoModel->listar('', 5, 0);

        return $stats;
    }
}

