<?php
/**
 * AsistePro - Modelo de Usuario
 * 
 * Gestiona las operaciones de base de datos relacionadas con usuarios,
 * incluyendo autenticación, consulta por rol y gestión de perfiles.
 */

require_once __DIR__ . '/Model.php';

class Usuario extends Model
{
    /**
     * Busca un usuario por su email
     *
     * @param string $email Correo electrónico
     * @return array|false Datos del usuario o false
     */
    public function buscarPorEmail(string $email): array|false
    {
        return $this->queryOne(
            "SELECT u.*, r.nombre AS rol_nombre 
             FROM usuarios u 
             INNER JOIN roles r ON u.rol_id = r.id 
             WHERE u.email = :email AND u.activo = 1",
            ['email' => $email]
        );
    }

    /**
     * Busca un usuario por su ID
     *
     * @param int $id ID del usuario
     * @return array|false Datos del usuario o false
     */
    public function buscarPorId(int $id): array|false
    {
        return $this->queryOne(
            "SELECT u.*, r.nombre AS rol_nombre 
             FROM usuarios u 
             INNER JOIN roles r ON u.rol_id = r.id 
             WHERE u.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Actualiza la fecha del último acceso
     *
     * @param int $id ID del usuario
     */
    public function actualizarUltimoAcceso(int $id): void
    {
        $this->execute(
            "UPDATE usuarios SET ultimo_acceso = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Obtiene todos los usuarios de un rol específico
     *
     * @param int $rolId ID del rol
     * @return array Lista de usuarios
     */
    public function listarPorRol(int $rolId): array
    {
        return $this->query(
            "SELECT u.*, r.nombre AS rol_nombre 
             FROM usuarios u 
             INNER JOIN roles r ON u.rol_id = r.id 
             WHERE u.rol_id = :rol_id 
             ORDER BY u.apellido, u.nombre",
            ['rol_id' => $rolId]
        );
    }

    /**
     * Cuenta usuarios por rol
     *
     * @param int $rolId ID del rol
     * @return int Total de usuarios
     */
    public function contarPorRol(int $rolId): int
    {
        $result = $this->queryOne(
            "SELECT COUNT(*) AS total FROM usuarios WHERE rol_id = :rol_id AND activo = 1",
            ['rol_id' => $rolId]
        );
        return (int)($result['total'] ?? 0);
    }

    /**
     * Registra una acción en el log
     *
     * @param int    $usuarioId ID del usuario
     * @param string $accion    Descripción de la acción
     * @param string $detalle   Detalle adicional
     * @param string $ip        Dirección IP
     */
    public function registrarAccion(int $usuarioId, string $accion, string $detalle = '', string $ip = ''): void
    {
        $this->execute(
            "INSERT INTO log_acciones (usuario_id, accion, detalle, ip, created_at) 
             VALUES (:usuario_id, :accion, :detalle, :ip, NOW())",
            [
                'usuario_id' => $usuarioId,
                'accion'     => $accion,
                'detalle'    => $detalle,
                'ip'         => $ip ?: ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
            ]
        );
    }
}

