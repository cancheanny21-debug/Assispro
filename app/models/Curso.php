<?php
/**
 * AsistePro - Modelo de Curso
 * 
 * Gestiona las operaciones CRUD de cursos.
 */

require_once __DIR__ . '/Model.php';

class Curso extends Model
{
    /**
     * Lista todos los cursos con información del docente
     */
    public function listar(string $busqueda = '', int $limite = 15, int $offset = 0): array
    {
        $sql = "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) AS docente_nombre,
                       (SELECT COUNT(*) FROM estudiantes_cursos ec WHERE ec.curso_id = c.id AND ec.activo = 1) AS total_estudiantes
                FROM cursos c
                LEFT JOIN usuarios u ON c.docente_id = u.id
                WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (c.nombre LIKE :busq OR c.codigo LIKE :busq2 OR u.nombre LIKE :busq3 OR u.apellido LIKE :busq4)";
            $params['busq']  = "%{$busqueda}%";
            $params['busq2'] = "%{$busqueda}%";
            $params['busq3'] = "%{$busqueda}%";
            $params['busq4'] = "%{$busqueda}%";
        }

        $sql .= " ORDER BY c.nombre LIMIT :limite OFFSET :offset";

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
     * Cuenta el total de cursos
     */
    public function contar(string $busqueda = ''): int
    {
        $sql = "SELECT COUNT(*) AS total FROM cursos c
                LEFT JOIN usuarios u ON c.docente_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($busqueda)) {
            $sql .= " AND (c.nombre LIKE :busq OR c.codigo LIKE :busq2 OR u.nombre LIKE :busq3 OR u.apellido LIKE :busq4)";
            $params['busq']  = "%{$busqueda}%";
            $params['busq2'] = "%{$busqueda}%";
            $params['busq3'] = "%{$busqueda}%";
            $params['busq4'] = "%{$busqueda}%";
        }

        $row = $this->queryOne($sql, $params);
        return (int)($row['total'] ?? 0);
    }

    /**
     * Obtiene un curso por ID con datos del docente
     */
    public function obtenerPorId(int $id): array|false
    {
        return $this->queryOne(
            "SELECT c.*, CONCAT(u.nombre, ' ', u.apellido) AS docente_nombre
             FROM cursos c
             LEFT JOIN usuarios u ON c.docente_id = u.id
             WHERE c.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Crea un nuevo curso
     */
    public function crear(array $datos): int
    {
        $this->execute(
            "INSERT INTO cursos (nombre, codigo, descripcion, docente_id, periodo, activo, created_at)
             VALUES (:nombre, :codigo, :descripcion, :docente_id, :periodo, 1, NOW())",
            [
                'nombre'      => $datos['nombre'],
                'codigo'      => $datos['codigo'],
                'descripcion' => $datos['descripcion'] ?? '',
                'docente_id'  => !empty($datos['docente_id']) ? (int)$datos['docente_id'] : null,
                'periodo'     => $datos['periodo'] ?? date('Y') . '-' . (date('Y') + 1),
            ]
        );
        return (int)$this->lastInsertId();
    }

    /**
     * Actualiza un curso
     */
    public function actualizar(int $id, array $datos): bool
    {
        $sql = "UPDATE cursos SET nombre = :nombre, codigo = :codigo, descripcion = :descripcion,
                docente_id = :docente_id, periodo = :periodo";
        $params = [
            'id'          => $id,
            'nombre'      => $datos['nombre'],
            'codigo'      => $datos['codigo'],
            'descripcion' => $datos['descripcion'] ?? '',
            'docente_id'  => !empty($datos['docente_id']) ? (int)$datos['docente_id'] : null,
            'periodo'     => $datos['periodo'] ?? '',
        ];

        if (isset($datos['activo'])) {
            $sql .= ", activo = :activo";
            $params['activo'] = (int)$datos['activo'];
        }

        $sql .= " WHERE id = :id";
        return $this->execute($sql, $params) > 0;
    }

    /**
     * Elimina un curso (eliminación lógica)
     */
    public function eliminar(int $id): bool
    {
        return $this->execute(
            "UPDATE cursos SET activo = 0 WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }

    /**
     * Verifica si un código de curso ya existe
     */
    public function codigoExiste(string $codigo, int $excluirId = 0): bool
    {
        $sql = "SELECT COUNT(*) AS total FROM cursos WHERE codigo = :codigo";
        $params = ['codigo' => $codigo];

        if ($excluirId > 0) {
            $sql .= " AND id != :id";
            $params['id'] = $excluirId;
        }

        $row = $this->queryOne($sql, $params);
        return (int)$row['total'] > 0;
    }

    /**
     * Lista los docentes disponibles para asignar
     */
    public function listarDocentes(): array
    {
        return $this->query(
            "SELECT id, nombre, apellido, email
             FROM usuarios
             WHERE rol_id = :rol_id AND activo = 1
             ORDER BY apellido, nombre",
            ['rol_id' => ROL_DOCENTE]
        );
    }

    /**
     * Lista los estudiantes matriculados en un curso
     */
    public function listarEstudiantes(int $cursoId): array
    {
        return $this->query(
            "SELECT u.id, u.nombre, u.apellido, u.email, ec.fecha_matricula
             FROM estudiantes_cursos ec
             INNER JOIN usuarios u ON ec.estudiante_id = u.id
             WHERE ec.curso_id = :curso_id AND ec.activo = 1
             ORDER BY u.apellido, u.nombre",
            ['curso_id' => $cursoId]
        );
    }
}

