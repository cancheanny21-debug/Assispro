<?php
/**
 * AsistePro - Controlador de Autenticación
 * 
 * Gestiona inicio de sesión, cierre de sesión y validación de credenciales.
 */

require_once __DIR__ . '/../../config/init.php';
require_once APP_PATH . '/models/Usuario.php';

class AuthController
{
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new Usuario();
    }

    /**
     * Procesa el inicio de sesión
     *
     * @param string $email    Correo electrónico
     * @param string $password Contraseña en texto plano
     * @param string $csrfToken Token CSRF del formulario
     * @return array Resultado con 'exito' (bool) y 'mensaje' (string)
     */
    public function login(string $email, string $password, string $csrfToken): array
    {
        // Verificar token CSRF
        if (!verificarTokenCSRF($csrfToken)) {
            return ['exito' => false, 'mensaje' => 'Token de seguridad inválido. Recargue la página.'];
        }

        // Validar campos
        $email = trim($email);
        if (empty($email) || empty($password)) {
            return ['exito' => false, 'mensaje' => 'Complete todos los campos.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['exito' => false, 'mensaje' => 'Formato de correo electrónico inválido.'];
        }

        // Buscar usuario
        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if (!$usuario) {
            return ['exito' => false, 'mensaje' => 'Credenciales incorrectas.'];
        }

        // Verificar contraseña
        if (!password_verify($password, $usuario['password'])) {
            return ['exito' => false, 'mensaje' => 'Credenciales incorrectas.'];
        }

        // Verificar que el usuario esté activo
        if (!$usuario['activo']) {
            return ['exito' => false, 'mensaje' => 'Su cuenta está desactivada. Contacte al administrador.'];
        }

        // Regenerar ID de sesión para prevenir session fixation
        session_regenerate_id(true);

        // Almacenar datos en sesión
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'] . ' ' . $usuario['apellido'];
        $_SESSION['usuario_email']  = $usuario['email'];
        $_SESSION['rol_id']         = (int)$usuario['rol_id'];
        $_SESSION['rol_nombre']     = $usuario['rol_nombre'];

        // Actualizar último acceso
        $this->usuarioModel->actualizarUltimoAcceso($usuario['id']);

        // Registrar acción
        $this->usuarioModel->registrarAccion(
            $usuario['id'],
            'Inicio de sesión',
            'Acceso exitoso desde ' . ($_SERVER['REMOTE_ADDR'] ?? 'desconocido')
        );

        return ['exito' => true, 'mensaje' => 'Bienvenido/a, ' . e($usuario['nombre']) . '.'];
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout(): void
    {
        // Registrar acción antes de destruir la sesión
        if (estaAutenticado()) {
            $this->usuarioModel->registrarAccion(
                $_SESSION['usuario_id'],
                'Cierre de sesión'
            );
        }

        // Destruir sesión
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }
}

