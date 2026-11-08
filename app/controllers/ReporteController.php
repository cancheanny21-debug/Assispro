<?php
/**
 * AsistePro - Controlador de Reportes
 * 
 * Genera reportes estadísticos de asistencia, consolidados por curso,
 * con exportación a Excel y formato apto para impresión/PDF.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Asistencia.php';
require_once APP_PATH . '/models/Curso.php';

class ReporteController
{
    private Asistencia $asistenciaModel;
    private Curso $cursoModel;

    public function __construct()
    {
        $this->asistenciaModel = new Asistencia();
        $this->cursoModel = new Curso();
    }

    public function index(): array
    {
        requiereRol([ROL_ADMIN, ROL_DOCENTE]);

        $rolId = (int)$_SESSION['rol_id'];
        $usuarioId = (int)$_SESSION['usuario_id'];

        if ($rolId === ROL_ADMIN) {
            $cursos = $this->cursoModel->listar('', 100, 0);
        } else {
            require_once APP_PATH . '/models/Docente.php';
            $docenteModel = new Docente();
            $cursos = $docenteModel->obtenerCursos($usuarioId);
        }

        $cursoSeleccionado = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : ($cursos[0]['id'] ?? 0);
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $datosReporte = [];
        $cursoActual = null;

        if ($cursoSeleccionado > 0) {
            $cursoActual = $this->cursoModel->obtenerPorId($cursoSeleccionado);
            $datosReporte = $this->asistenciaModel->reportePorEstudiantes($cursoSeleccionado, $fechaInicio, $fechaFin);
        }

        return [
            'cursos'            => $cursos,
            'cursoSeleccionado' => $cursoSeleccionado,
            'cursoActual'       => $cursoActual,
            'fechaInicio'       => $fechaInicio,
            'fechaFin'          => $fechaFin,
            'datosReporte'      => $datosReporte,
        ];
    }

    public function exportarExcel(): void
    {
        requiereRol([ROL_ADMIN, ROL_DOCENTE]);

        $cursoId = (int)($_GET['curso_id'] ?? 0);
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $curso = $this->cursoModel->obtenerPorId($cursoId);
        if (!$curso) {
            die('Curso no válido.');
        }

        $datos = $this->asistenciaModel->reportePorEstudiantes($cursoId, $fechaInicio, $fechaFin);

        $nombreArchivo = 'Reporte_Asistencia_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $curso['codigo']) . '_' . date('Ymd') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');

        $salida = fopen('php://output', 'w');
        // UTF-8 BOM para que Excel en español reconozca caracteres y tildes correctamente
        fprintf($salida, chr(0xEF) . chr(0xBB) . chr(0xBF));

        fputcsv($salida, ['INSTITUTO SUPERIOR TECNOLOGICO ALBERTO ENRIQUEZ (ISTAE) - ASISTEPRO'], ';');
        fputcsv($salida, ['Reporte de Asistencia: ' . $curso['nombre'] . ' (' . $curso['codigo'] . ')'], ';');
        fputcsv($salida, ['Periodo: ' . $fechaInicio . ' al ' . $fechaFin], ';');
        fputcsv($salida, ['Generado: ' . date('d/m/Y H:i:s')], ';');
        fputcsv($salida, []);

        fputcsv($salida, ['N°', 'Estudiante', 'Email', 'Total Clases', 'Presentes', 'Ausentes', 'Atrasados', 'Justificados', '% Asistencia'], ';');

        $n = 1;
        foreach ($datos as $fila) {
            fputcsv($salida, [
                $n++,
                $fila['apellido'] . ' ' . $fila['nombre'],
                $fila['email'],
                $fila['total_clases'],
                $fila['presentes'],
                $fila['ausentes'],
                $fila['atrasados'],
                $fila['justificados'],
                ($fila['porcentaje_asistencia'] ?? 0) . '%',
            ], ';');
        }

        fclose($salida);
        exit;
    }
}

