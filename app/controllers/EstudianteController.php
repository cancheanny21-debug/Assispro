<?php
/**
 * AsistePro - Controlador de Estudiantes
 * 
 * Gestiona el CRUD de estudiantes y sus asignaciones a cursos.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Estudiante.php';
require_once APP_PATH . '/models/Curso.php';

class EstudianteController
{
    private Estudiante $model;
    private Curso $cursoModel;

    public function __construct()
    {
        $this->model = new Estudiante();
        $this->cursoModel = new Curso();
    }

    public function index(): array
    {
        requiereRol(ROL_ADMIN);

        $busqueda = trim($_GET['q'] ?? '');
        $pagina = max(1, (int)($_GET['p'] ?? 1));
        $limite = RECORDS_PER_PAGE;
        $offset = ($pagina - 1) * $limite;

        $total = $this->model->contar($busqueda);
        $estudiantes = $this->model->listar($busqueda, $limite, $offset);
        $totalPaginas = ceil($total / $limite);
        $todosCursos = $this->cursoModel->listar('', 100, 0);

        return [
            'estudiantes'  => $estudiantes,
            'todosCursos'  => $todosCursos,
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
            setMensaje('Solicitud no autorizada o token CSRF inválido.', 'danger');
            redirect('/estudiantes.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nombre) || empty($apellido) || empty($email)) {
            setMensaje('Por favor complete todos los campos obligatorios.', 'danger');
            redirect('/estudiantes.php');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setMensaje('El correo electrónico no es válido.', 'danger');
            redirect('/estudiantes.php');
        }

        if ($this->model->emailExiste($email, $id)) {
            setMensaje('El correo electrónico ya está registrado por otro usuario.', 'danger');
            redirect('/estudiantes.php');
        }

        if ($id > 0) {
            $datos = [
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'email'    => $email,
            ];
            if (!empty($password)) {
                $datos['password'] = $password;
            }
            if (isset($_POST['activo'])) {
                $datos['activo'] = (int)$_POST['activo'];
            }

            $this->model->actualizar($id, $datos);
            setMensaje("Estudiante {$nombre} {$apellido} actualizado correctamente.", 'success');
        } else {
            if (empty($password)) {
                $password = 'Demo1234!'; // Clave por defecto si no se ingresa
            }
            $this->model->crear([
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'email'    => $email,
                'password' => $password,
            ]);
            setMensaje("Estudiante {$nombre} {$apellido} registrado con éxito.", 'success');
        }

        redirect('/estudiantes.php');
    }

    public function eliminar(): void
    {
        requiereRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Acción no permitida.', 'danger');
            redirect('/estudiantes.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->model->eliminar($id);
            setMensaje('Estudiante desactivado del sistema.', 'success');
        }

        redirect('/estudiantes.php');
    }

    public function matricular(): void
    {
        requiereRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Acción no permitida.', 'danger');
            redirect('/estudiantes.php');
        }

        $estudianteId = (int)($_POST['estudiante_id'] ?? 0);
        $cursoId = (int)($_POST['curso_id'] ?? 0);

        if ($estudianteId > 0 && $cursoId > 0) {
            $this->model->matricular($estudianteId, $cursoId);
            setMensaje('Estudiante matriculado en el curso.', 'success');
        }

        redirect('/estudiantes.php');
    }

    public function desmatricular(): void
    {
        requiereRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Acción no permitida.', 'danger');
            redirect('/estudiantes.php');
        }

        $estudianteId = (int)($_POST['estudiante_id'] ?? 0);
        $cursoId = (int)($_POST['curso_id'] ?? 0);

        if ($estudianteId > 0 && $cursoId > 0) {
            $this->model->desmatricular($estudianteId, $cursoId);
            setMensaje('Estudiante desvinculado del curso.', 'success');
        }

        redirect('/estudiantes.php');
    }
}

