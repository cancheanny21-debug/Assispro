<?php
/**
 * AsistePro - Modelo de Docente
 * 
 * Gestiona consultas de docentes (usuarios con rol_id = 2).
 */

require_once __DIR__ . '/Model.php';

class Docente extends Model
{
    /**
     * Lista todos los docentes con opción de búsqueda
     */
    public function listar(string $busqueda = '', int $limite = 15, int $offset = 0): array
    {
        $sql = "SELECT u.id, u.nombre, u.apellido, u.email, u.activo, u.created_at,
                       COUNT(c.id) AS total_cursos
                FROM usuarios u
                LEFT JOIN cursos c ON u.id = c.docente_id AND c.activo = 1
                WHERE u.rol_id = :rol_id";
        $params = ['rol_id' => ROL_DOCENTE];

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
     * Cuenta el total de docentes
     */
    public function contar(string $busqueda = ''): int
    {
        $sql = "SELECT COUNT(*) AS total FROM usuarios WHERE rol_id = :rol_id";
        $params = ['rol_id' => ROL_DOCENTE];

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
     * Obtiene un docente por ID
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->queryOne(
            "SELECT u.*, r.nombre AS rol_nombre
             FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.id
             WHERE u.id = :id AND u.rol_id = :rol_id",
            ['id' => $id, 'rol_id' => ROL_DOCENTE]
        );
    }

    /**
     * Crea un nuevo docente
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
                'rol_id'   => ROL_DOCENTE,
            ]
        );
        return (int)$this->lastInsertId();
    }

    /**
     * Actualiza los datos de un docente
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

        if (!empty($datos['password'])) {
            $sql .= ", password = :password";
            $params['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        }

        if (isset($datos['activo'])) {
            $sql .= ", activo = :activo";
            $params['activo'] = (int)$datos['activo'];
        }

        $sql .= " WHERE id = :id AND rol_id = " . ROL_DOCENTE;
        return $this->execute($sql, $params) > 0;
    }

    /**
     * Elimina un docente (eliminación lógica)
     */
    public function eliminar(int $id): bool
    {
        return $this->execute(
            "UPDATE usuarios SET activo = 0 WHERE id = :id AND rol_id = :rol_id",
            ['id' => $id, 'rol_id' => ROL_DOCENTE]
        ) > 0;
    }

    /**
     * Verifica si un email ya existe
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
     * Obtiene los cursos asignados a un docente
     */
    public function obtenerCursos(int $docenteId): array
    {
        return $this->query(
            "SELECT c.id, c.nombre, c.codigo, c.periodo,
                    (SELECT COUNT(*) FROM estudiantes_cursos ec WHERE ec.curso_id = c.id AND ec.activo = 1) AS total_estudiantes
             FROM cursos c
             WHERE c.docente_id = :id AND c.activo = 1
             ORDER BY c.nombre",
            ['id' => $docenteId]
        );
    }
}

