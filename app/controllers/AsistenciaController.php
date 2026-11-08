<?php
/**
 * AsistePro - Controlador de Asistencia
 * 
 * Gestiona el registro diario de asistencia, consulta de historial
 * y control de estados (Presente, Ausente, Atrasado, Justificado).
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Asistencia.php';
require_once APP_PATH . '/models/Curso.php';
require_once APP_PATH . '/models/Alerta.php';

class AsistenciaController
{
    private Asistencia $model;
    private Curso $cursoModel;

    public function __construct()
    {
        $this->model = new Asistencia();
        $this->cursoModel = new Curso();
    }

    public function index(): array
    {
        requiereAutenticacion();

        $rolId = (int)$_SESSION['rol_id'];
        $usuarioId = (int)$_SESSION['usuario_id'];

        // Si es estudiante, redirigir a su historial personal
        if ($rolId === ROL_ESTUDIANTE) {
            $cursoId = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : null;
            $resumen = $this->model->resumenEstudiante($usuarioId, $cursoId);
            $historial = $this->model->obtenerPorEstudiante($usuarioId, $cursoId);

            return [
                'esEstudiante' => true,
                'resumen'      => $resumen,
                'historial'    => $historial,
            ];
        }

        // Obtener lista de cursos accesibles
        if ($rolId === ROL_ADMIN) {
            $cursos = $this->cursoModel->listar('', 100, 0);
        } else {
            // Docente
            require_once APP_PATH . '/models/Docente.php';
            $docenteModel = new Docente();
            $cursos = $docenteModel->obtenerCursos($usuarioId);
        }

        $cursoSeleccionado = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : ($cursos[0]['id'] ?? 0);
        $fecha = !empty($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

        $estudiantes = [];
        $yaRegistrado = false;

        if ($cursoSeleccionado > 0) {
            $yaRegistrado = $this->model->existeRegistro($cursoSeleccionado, $fecha);
            $estudiantes = $this->model->obtenerParaRegistro($cursoSeleccionado, $fecha);
        }

        return [
            'esEstudiante'      => false,
            'cursos'            => $cursos,
            'cursoSeleccionado' => $cursoSeleccionado,
            'fecha'             => $fecha,
            'yaRegistrado'      => $yaRegistrado,
            'estudiantes'       => $estudiantes,
        ];
    }

    public function guardar(): void
    {
        requiereRol([ROL_ADMIN, ROL_DOCENTE]);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Solicitud inválida o expirada.', 'danger');
            redirect('/asistencia.php');
        }

        $cursoId = (int)($_POST['curso_id'] ?? 0);
        $fecha = $_POST['fecha'] ?? date('Y-m-d');
        $asistenciasInput = $_POST['asistencia'] ?? [];
        $observacionesInput = $_POST['observacion'] ?? [];
        $registradoPor = (int)$_SESSION['usuario_id'];

        if ($cursoId <= 0 || empty($asistenciasInput)) {
            setMensaje('No se recibieron datos de asistencia para procesar.', 'warning');
            redirect("/asistencia.php?curso_id={$cursoId}&fecha={$fecha}");
        }

        $registros = [];
        foreach ($asistenciasInput as $estudianteId => $estado) {
            $registros[$estudianteId] = [
                'estado'      => $estado,
                'observacion' => $observacionesInput[$estudianteId] ?? '',
            ];
        }

        $res = $this->model->guardarLote($cursoId, $fecha, $registros, $registradoPor);

        if ($res['exito']) {
            // Evaluar alertas automáticas tras el registro
            $alertaModel = new Alerta();
            $alertaModel->procesarAlertasAutomaticas();

            setMensaje(
                "Asistencia guardada correctamente. ({$res['insertados']} nuevos registros, {$res['actualizados']} actualizados).",
                'success'
            );
        } else {
            setMensaje('Ocurrió un error al registrar la asistencia: ' . e($res['error'] ?? ''), 'danger');
        }

        redirect("/asistencia.php?curso_id={$cursoId}&fecha={$fecha}");
    }

    public function historial(): array
    {
        requiereAutenticacion();

        $rolId = (int)$_SESSION['rol_id'];
        $usuarioId = (int)$_SESSION['usuario_id'];

        if ($rolId === ROL_ADMIN) {
            $cursos = $this->cursoModel->listar('', 100, 0);
        } elseif ($rolId === ROL_DOCENTE) {
            require_once APP_PATH . '/models/Docente.php';
            $docenteModel = new Docente();
            $cursos = $docenteModel->obtenerCursos($usuarioId);
        } else {
            redirect('/asistencia.php');
        }

        $cursoSeleccionado = !empty($_GET['curso_id']) ? (int)$_GET['curso_id'] : ($cursos[0]['id'] ?? 0);
        $fechaInicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
        $fechaFin = $_GET['fecha_fin'] ?? date('Y-m-d');

        $historial = [];
        if ($cursoSeleccionado > 0) {
            $historial = $this->model->obtenerHistorial($cursoSeleccionado, $fechaInicio, $fechaFin);
        }

        return [
            'cursos'            => $cursos,
            'cursoSeleccionado' => $cursoSeleccionado,
            'fechaInicio'       => $fechaInicio,
            'fechaFin'          => $fechaFin,
            'historial'         => $historial,
        ];
    }
}

