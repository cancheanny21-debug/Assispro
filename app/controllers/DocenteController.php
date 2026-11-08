<?php
/**
 * AsistePro - Controlador de Docentes
 * 
 * Gestiona el CRUD de docentes y la consulta de cursos asignados.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Docente.php';

class DocenteController
{
    private Docente $model;

    public function __construct()
    {
        $this->model = new Docente();
    }

    public function index(): array
    {
        requiereRol(ROL_ADMIN);

        $busqueda = trim($_GET['q'] ?? '');
        $pagina = max(1, (int)($_GET['p'] ?? 1));
        $limite = RECORDS_PER_PAGE;
        $offset = ($pagina - 1) * $limite;

        $total = $this->model->contar($busqueda);
        $docentes = $this->model->listar($busqueda, $limite, $offset);
        $totalPaginas = ceil($total / $limite);

        return [
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
            redirect('/docentes.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($nombre) || empty($apellido) || empty($email)) {
            setMensaje('Todos los campos obligatorios deben ser completados.', 'danger');
            redirect('/docentes.php');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setMensaje('Correo inválido.', 'danger');
            redirect('/docentes.php');
        }

        if ($this->model->emailExiste($email, $id)) {
            setMensaje('El correo electrónico ya existe.', 'danger');
            redirect('/docentes.php');
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
            setMensaje("Docente {$nombre} {$apellido} actualizado con éxito.", 'success');
        } else {
            $this->model->crear([
                'nombre'   => $nombre,
                'apellido' => $apellido,
                'email'    => $email,
                'password' => !empty($password) ? $password : 'Demo1234!',
            ]);
            setMensaje("Docente {$nombre} {$apellido} creado exitosamente.", 'success');
        }

        redirect('/docentes.php');
    }

    public function eliminar(): void
    {
        requiereRol(ROL_ADMIN);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Acción no permitida.', 'danger');
            redirect('/docentes.php');
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->model->eliminar($id);
            setMensaje('Docente desactivado correctamente.', 'success');
        }

        redirect('/docentes.php');
    }
}

