<?php
/**
 * AsistePro - Controlador de Cursos
 * 
 * Gestiona CRUD de cursos y asignación de docentes.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Curso.php';

class CursoController
{
    private Curso $model;

    public function __construct()
    {
        $this->model = new Curso();
    }

    public function index(): array
    {
        requiereRol(ROL_ADMIN);

        $busqueda = trim($_GET['q'] ?? '');
        $pagina = max(1, (int)($_GET['p'] ?? 1));
        $limite = RECORDS_PER_PAGE;
        $offset = ($pagina - 1) * $limite;

        $total = $this->model->contar($busqueda);
        $cursos = $this->model->listar($busqueda, $limite, $offset);
        $totalPaginas = ceil($total / $limite);
        $docentes = $this->model->listarDocentes();

        return [
            'cursos'       => $cursos,
            'docentes'     => $docentes,
            'busqueda'     => $busqueda,
            'pagina'       => $pagina,
            'totalPaginas' => $totalPaginas,
            'total'        => $total,
        ];
    }

    public function guardar(): void
    {
        requiereRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Solicitud no autorizada.', 'danger');
            redirect('/cursos.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $codigo = trim($_POST['codigo'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $docenteId = !empty($_POST['docente_id']) ? (int)$_POST['docente_id'] : null;
        $periodo = trim($_POST['periodo'] ?? '2026-2027');

        if (empty($nombre) || empty($codigo)) {
            setMensaje('El nombre y el código del curso son obligatorios.', 'danger');
            redirect('/cursos.php');
        }

        if ($this->model->codigoExiste($codigo, $id)) {
            setMensaje('El código de curso ya está registrado.', 'danger');
            redirect('/cursos.php');
        }

        $datos = [
            'nombre'      => $nombre,
            'codigo'      => $codigo,
            'descripcion' => $descripcion,
            'docente_id'  => $docenteId,
            'periodo'     => $periodo,
        ];

        if ($id > 0) {
            if (isset($_POST['activo'])) {
                $datos['activo'] = (int)$_POST['activo'];
            }
            $this->model->actualizar($id, $datos);
            setMensaje("Curso {$nombre} actualizado correctamente.", 'success');
        } else {
            $this->model->crear($datos);
            setMensaje("Curso {$nombre} creado con éxito.", 'success');
        }

        redirect('/cursos.php');
    }

    public function eliminar(): void
    {
        requiereRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Acción no permitida.', 'danger');
            redirect('/cursos.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->model->eliminar($id);
            setMensaje('Curso desactivado.', 'success');
        }

        redirect('/cursos.php');
    }
}

