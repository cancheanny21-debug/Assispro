<?php
/**
 * AsistePro - Modelo de Estudiante
 * 
 * Gestiona consultas de estudiantes (usuarios con rol_id = 3).
 */

require_once __DIR__ . '/Model.php';

class Estudiante extends Model
{
    /**
     * Lista todos los estudiantes con opción de búsqueda
     */
    public function listar(string $busqueda = '', int $limite = 15, int $offset = 0): array
    {
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.activo, u.created_at,
                       GROUP_CONCAT(c.nombre SEPARATOR ', ') AS cursos
                FROM usuarios u
                LEFT JOIN estudiantes_cursos ec ON u.id = ec.estudiante_id AND ec.activo = 1
                LEFT JOIN cursos c ON ec.curso_id = c.id AND c.activo = 1
                WHERE u.rol_id = :rol_id";
        $params = ['rol_id' => ROL_ESTUDIANTE];

        if (!empty($busqueda)) {
            $sql .= " AND (u.nombre LIKE :busq OR u.apellido LIKE :busq2 OR u.email LIKE :busq3)";
            $params['busq']  = "%{$busqueda}%";
            $params['busq2'] = "%{$busqueda}%";
            $params['busq3'] = "%{$busqueda}%";
        }

        $sql .= " GROUP BY u.id ORDER BY u.apellido, u.nombre LIMIT :limite OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cuenta el total de estudiantes (para paginación)
     */
    public function contar(string $busqueda = ''): int
    {
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE rol_id = :rol_id";
        $params = ['rol_id' => ROL_ESTUDIANTE];

        if (!empty($busqueda)) {
            $sql .= " AND (nombre LIKE :busq OR apellido LIKE :busq2 OR email LIKE :busq3)";
            $params['busq']  = "%{$busqueda}%";
            $params['busq2'] = "%{$busqueda}%";
            $params['busq3'] = "%{$busqueda}%";
        }

        $row = $this->queryOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Obtiene un estudiante por ID
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->queryOne(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.id
             WHERE u.id = :id AND u.rol_id = :rol_id",
            ['id' => $id, 'rol_id' => ROL_ESTUDIANTE]
        );
    }

    /**
     * Crea un nuevo estudiante
     */
    public function crear(array $datos): int
    {
        $this->execute(
            "INSERT INTO usuarios (nombre, apellido, email, password, rol_id, activo, created_at)
             VALUES (:nombre, :apellido, :email, :password, :rol_id, 1, NOW())",
            [
                'nombre'   => $datos['nombre'],
                'apellido' => $datos['apellido'],
                'email'    => $datos['email'],
                'password' => password_hash($datos['password'], PASSWORD_BCRYPT),
                'rol_id'   => ROL_ESTUDIANTE,
            ]
        );
        return (int)$this->lastInsertId();
    }

    /**
     * Actualiza los datos de un estudiante
     */
    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE usuarios SET nombre = :nombre, apellido = :apellido, email = :email";
        $params = [
            'id'       => $id,
            'nombre'   => $datos['nombre'],
            'apellido' => $datos['apellido'],
            'email'    => $datos['email'],
        ];

        // Actualizar contraseña solo si se proporcionó una nueva
        if (!empty($datos['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }

        if (isset($datos['activo'])) {
            $sql .= ", activo = :activo";
            $params['activo'] = (int)$datos['activo'];
        }

        $sql .= " WHERE id = :id AND rol_id = " . ROL_ESTUDIANTE;
        return $this->execute($sql, $params) > 0;
    }

    /**
     * Elimina un estudiante (eliminación lógica)
     */
    public function eliminar(int $id): bool
    {
        return $this->execute(
            "UPDATE usuarios SET activo = 0 WHERE id = :id AND rol_id = :rol_id",
            ['id' => $id, 'rol_id' => ROL_ESTUDIANTE]
        ) > 0;
    }

    /**
     * Verifica si un email ya existe (excluyendo un ID)
     */
    public function emailExiste(string $email, int $excluirId = 0): bool
    {
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE email = :email";
        $params = ['email' => $email];

        if ($excluirId > 0) {
            $sql .= " AND id != :id";
            $params['id'] = $excluirId;
        }

        $row = $this->queryOne($sql, $params);
        return (int)$row['total'] > 0;
    }

    /**
     * Obtiene los cursos en los que está matriculado un estudiante
     */
    public function obtenerCursos(int $estudianteId): array
    {
        return $this->query(
            "SELECT c.id, c.nombre, c.codigo, c.periodo,
                    CONCAT(u.nombre, ' ', u.apellido) AS docente_nombre,
                    ec.fecha_matricula
             FROM estudiantes_cursos ec
             INNER JOIN cursos c ON ec.curso_id = c.id
             LEFT JOIN usuarios u ON c.docente_id = u.id
             WHERE ec.estudiante_id = :id AND ec.activo = 1 AND c.activo = 1
             ORDER BY c.nombre",
            ['id' => $estudianteId]
        );
    }

    /**
     * Obtiene cursos disponibles para matricular
     */
    public function cursosDisponibles(int $estudianteId): array
    {
        return $this->query(
            "SELECT c.id, c.nombre, c.codigo
             FROM cursos c
             WHERE c.activo = 1
               AND c.id NOT IN (
                   SELECT curso_id FROM estudiantes_cursos
                   WHERE estudiante_id = :id AND activo = 1
               )
             ORDER BY c.nombre",
            ['id' => $estudianteId]
        );
    }

    /**
     * Matricula un estudiante en un curso
     */
    public function matricular(int $estudianteId, int $cursoId): bool
    {
        try {
            $this->execute(
                "INSERT INTO estudiantes_cursos (estudiante_id, curso_id, fecha_matricula, activo)
                 VALUES (:est_id, :curso_id, CURDATE(), 1)
                 ON DUPLICATE KEY UPDATE activo = 1",
                ['est_id' => $estudianteId, 'curso_id' => $cursoId]
            );
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Desmatricula un estudiante de un curso
     */
    public function desmatricular(int $estudianteId, int $cursoId): bool
    {
        return $this->execute(
            "UPDATE estudiantes_cursos SET activo = 0
             WHERE estudiante_id = :est_id AND curso_id = :curso_id",
            ['est_id' => $estudianteId, 'curso_id' => $cursoId]
        ) > 0;
    }
}

