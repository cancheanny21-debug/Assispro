<?php
/**
 * AsistePro - Controlador de Perfil de Usuario
 * 
 * Permite consultar información del perfil y actualizar la contraseña.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Usuario.php';

class PerfilController
{
    private Usuario $model;

    public function __construct()
    {
        $this->model = new Usuario();
    }

    public function index(): array
    {
        requiereAutenticacion();
        $usuario = $this->model->buscarPorId((int)$_SESSION['usuario_id']);
        return ['usuario' => $usuario];
    }

    public function actualizarPassword(): void
    {
        requiereAutenticacion();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verificarTokenCSRF($_POST['csrf_token'] ?? '')) {
            setMensaje('Solicitud no autorizada.', 'danger');
            redirect('/perfil.php');
        }

        $usuarioId = (int)$_SESSION['usuario_id'];
        $actual = $_POST['password_actual'] ?? '';
        $nueva = $_POST['password_nueva'] ?? '';
        $confirmar = $_POST['password_confirmar'] ?? '';

        if (empty($actual) || empty($nueva) || empty($confirmar)) {
            setMensaje('Todos los campos son obligatorios.', 'danger');
            redirect('/perfil.php');
        }

        if ($nueva !== $confirmar) {
            setMensaje('La nueva contraseña y la confirmación no coinciden.', 'danger');
            redirect('/perfil.php');
        }

        if (strlen($nueva) < 6) {
            setMensaje('La nueva contraseña debe tener al menos 6 caracteres.', 'danger');
            redirect('/perfil.php');
        }

        $usuario = $this->model->buscarPorId($usuarioId);
        if (!password_verify($actual, $usuario['password'])) {
            setMensaje('La contraseña actual es incorrecta.', 'danger');
            redirect('/perfil.php');
        }

        // Actualizar
        $db = Database::getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET password = :p WHERE id = :id");
        $stmt->execute([
            'p'  => password_hash($nueva, PASSWORD_BCRYPT),
            'id' => $usuarioId,
        ]);

        $this->model->registrarAccion($usuarioId, 'Cambio de contraseña', 'El usuario actualizó su clave');

        setMensaje('Contraseña actualizada con éxito.', 'success');
        redirect('/perfil.php');
    }
}

