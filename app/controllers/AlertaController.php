<?php
/**
 * AsistePro - Controlador de Alertas
 * 
 * Gestiona la visualización y resolución de alertas de inasistencia.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Alerta.php';

class AlertaController
{
    private Alerta $model;

    public function __construct()
    {
        $this->model = new Alerta();
    }

    public function index(): array
    {
        requiereAutenticacion();

        $rolId = (int)$_SESSION['rol_id'];
        $usuarioId = (int)$_SESSION['usuario_id'];

        // Si es estudiante, solo ve sus propias alertas
        $estudianteId = ($rolId === ROL_ESTUDIANTE) ? $usuarioId : null;

        // Ejecutar verificación automática de alertas
        if ($rolId === ROL_ADMIN || $rolId === ROL_DOCENTE) {
            $this->model->procesarAlertasAutomaticas();
        }

        $alertas = $this->model->listar($estudianteId);

        return [
            'alertas' => $alertas,
        ];
    }

    public function marcarLeida(): void
    {
        requiereAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Operación no permitida.', 'danger');
            redirect('/alertas.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->model->marcarLeida($id);
            setMensaje('Alerta marcada como leída.', 'success');
        }

        redirect('/alertas.php');
    }

    public function marcarTodas(): void
    {
        requiereAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Operación no permitida.', 'danger');
            redirect('/alertas.php');
        }

        $rolId = (int)$_SESSION['rol_id'];
        $usuarioId = (int)$_SESSION['usuario_id'];
        $estudianteId = ($rolId === ROL_ESTUDIANTE) ? $usuarioId : null;

        $this->model->marcarTodasLeidas($estudianteId);
        setMensaje('Todas las alertas han sido marcadas como leídas.', 'success');
        redirect('/alertas.php');
    }
}

