<?php
/**
 * AsistePro - Modelo de Asistencia
 * 
 * Gestiona el registro, consulta, modificación e historial de asistencias,
 * previniendo duplicados y calculando estadísticas.
 */

require_once __DIR__ . '/Model.php';

class Asistencia extends Model
{
    /**
     * Registra o actualiza la asistencia de múltiples estudiantes en un curso y fecha
     *
     * @param int    $cursoId       ID del curso
     * @param string $fecha         Fecha en formato Y-m-d
     * @param array  $registros     Arreglo con [estudiante_id => ['estado' => '...', 'observacion' => '...']]
     * @param int    $registradoPor ID del usuario que registra (docente/admin)
     * @return array Resultado con 'exito' (bool), 'insertados' (int), 'actualizados' (int)
     */
    public function guardarLote(int $cursoId, string $fecha, array $registros, int $registradoPor): array
    {
        $insertados = 0;
        $actualizados = 0;

        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO asistencias (estudiante_id, curso_id, fecha, estado, observacion, registrado_por, created_at)
                    VALUES (:estudiante_id, :curso_id, :fecha, :estado, :observacion, :registrado_por, NOW())
                    ON DUPLICATE KEY UPDATE 
                        estado = VALUES(estado),
                        observacion = VALUES(observacion),
                        registrado_por = VALUES(registrado_por),
                        updated_at = NOW()";

            $stmt = $this->db->prepare($sql);

            foreach ($registros as $estudianteId => $datos) {
                $estado = in_array($datos['estado'] ?? '', ['presente', 'ausente', 'atrasado', 'justificado'])
                    ? $datos['estado']
                    : 'presente';
                $observacion = trim($datos['observacion'] ?? '');

                // Verificar si ya existía para contabilizar
                $existe = $this->queryOne(
                    "SELECT id FROM asistencias WHERE estudiante_id = :e AND curso_id = :c AND fecha = :f",
                    ['e' => $estudianteId, 'c' => $cursoId, 'f' => $fecha]
                );

                $stmt->execute([
                    'estudiante_id'  => $estudianteId,
                    'curso_id'       => $cursoId,
                    'fecha'          => $fecha,
                    'estado'         => $estado,
                    'observacion'    => $observacion ?: null,
                    'registrado_por' => $registradoPor,
                ]);

                if ($existe) {
                    $actualizados++;
                } else {
                    $insertados++;
                }
            }

            $this->db->commit();
            return ['exito' => true, 'insertados' => $insertados, 'actualizados' => $actualizados];
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Error al guardar asistencia: " . $e->getMessage());
            return ['exito' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Verifica si ya existe asistencia registrada para un curso y fecha
     */
    public function existeRegistro(int $cursoId, string $fecha): bool
    {
        $row = $this->queryOne(
            "SELECT COUNT(*) AS total FROM asistencias WHERE curso_id = :c AND fecha = :f",
            ['c' => $cursoId, 'f' => $fecha]
        );
        return ((int)($row['total'] ?? 0)) > 0;
    }

    /**
     * Obtiene la nómina de estudiantes de un curso con su estado de asistencia para una fecha
     */
    public function obtenerParaRegistro(int $cursoId, string $fecha): array
    {
        $sql = "SELECT 
                    u.id AS estudiante_id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    a.id AS asistencia_id,
                    COALESCE(a.estado, 'presente') AS estado,
                    a.observacion,
                    a.updated_at
                FROM estudiantes_cursos ec
                INNER JOIN usuarios u ON ec.estudiante_id = u.id
                LEFT JOIN asistencias a ON a.estudiante_id = u.id AND a.curso_id = ec.curso_id AND a.fecha = :fecha
                WHERE ec.curso_id = :curso_id AND ec.activo = 1 AND u.activo = 1
                ORDER BY u.apellido, u.nombre";

        return $this->query($sql, ['curso_id' => $cursoId, 'fecha' => $fecha]);
    }

    /**
     * Obtiene el historial de asistencias con filtros
     */
    public function obtenerHistorial(int $cursoId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    a.fecha,
                    COUNT(a.id) AS total_alumnos,
                    SUM(CASE WHEN a.estado = 'presente' THEN 1 ELSE 0 END) AS presentes,
                    SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes,
                    SUM(CASE WHEN a.estado = 'atrasado' THEN 1 ELSE 0 END) AS atrasados,
                    SUM(CASE WHEN a.estado = 'justificado' THEN 1 ELSE 0 END) AS justificados,
                    CONCAT(u.nombre, ' ', u.apellido) AS registrado_por_nombre
                FROM asistencias a
                LEFT JOIN usuarios u ON a.registrado_por = u.id
                WHERE a.curso_id = :curso_id";
        $params = ['curso_id' => $cursoId];

        if (!empty($fechaInicio)) {
            $sql .= " AND a.fecha >= :inicio";
            $params['inicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND a.fecha <= :fin";
            $params['fin'] = $fechaFin;
        }

        $sql .= " GROUP BY a.fecha, a.curso_id ORDER BY a.fecha DESC";
        return $this->query($sql, $params);
    }

    /**
     * Obtiene el detalle de asistencia de un estudiante
     */
    public function obtenerPorEstudiante(int $estudianteId, ?int $cursoId = null, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    a.id,
                    a.fecha,
                    a.estado,
                    a.observacion,
                    c.nombre AS curso_nombre,
                    c.codigo AS curso_codigo
                FROM asistencias a
                INNER JOIN cursos c ON a.curso_id = c.id
                WHERE a.estudiante_id = :estudiante_id";
        $params = ['estudiante_id' => $estudianteId];

        if (!empty($cursoId)) {
            $sql .= " AND a.curso_id = :curso_id";
            $params['curso_id'] = $cursoId;
        }
        if (!empty($fechaInicio)) {
            $sql .= " AND a.fecha >= :inicio";
            $params['inicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND a.fecha <= :fin";
            $params['fin'] = $fechaFin;
        }

        $sql .= " ORDER BY a.fecha DESC";
        return $this->query($sql, $params);
    }

    /**
     * Resumen de asistencia de un estudiante (porcentajes)
     */
    public function resumenEstudiante(int $estudianteId, ?int $cursoId = null): array
    {
        $sql = "SELECT 
                    COUNT(*) AS total_dias,
                    SUM(CASE WHEN estado = 'presente' THEN 1 ELSE 0 END) AS presentes,
                    SUM(CASE WHEN estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes,
                    SUM(CASE WHEN estado = 'atrasado' THEN 1 ELSE 0 END) AS atrasados,
                    SUM(CASE WHEN estado = 'justificado' THEN 1 ELSE 0 END) AS justificados
                FROM asistencias
                WHERE estudiante_id = :estudiante_id";
        $params = ['estudiante_id' => $estudianteId];

        if (!empty($cursoId)) {
            $sql .= " AND curso_id = :curso_id";
            $params['curso_id'] = $cursoId;
        }

        $res = $this->queryOne($sql, $params);
        $total = (int)($res['total_dias'] ?? 0);
        $presentes = (int)($res['presentes'] ?? 0);
        $atrasados = (int)($res['atrasados'] ?? 0);
        $ausentes = (int)($res['ausentes'] ?? 0);
        $justificados = (int)($res['justificados'] ?? 0);

        $porcentaje = $total > 0 ? round((($presentes + $atrasados) / $total) * 100, 1) : 0;

        return [
            'total_dias'   => $total,
            'presentes'    => $presentes,
            'ausentes'     => $ausentes,
            'atrasados'    => $atrasados,
            'justificados' => $justificados,
            'porcentaje'   => $porcentaje,
        ];
    }

    /**
     * Reporte general consolidado por estudiante según filtros
     */
    public function reportePorEstudiantes(int $cursoId, ?string $fechaInicio = null, ?string $fechaFin = null): array
    {
        $sql = "SELECT 
                    u.id AS estudiante_id,
                    u.nombre,
                    u.apellido,
                    u.email,
                    COUNT(a.id) AS total_clases,
                    SUM(CASE WHEN a.estado = 'presente' THEN 1 ELSE 0 END) AS presentes,
                    SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) AS ausentes,
                    SUM(CASE WHEN a.estado = 'atrasado' THEN 1 ELSE 0 END) AS atrasados,
                    SUM(CASE WHEN a.estado = 'justificado' THEN 1 ELSE 0 END) AS justificados,
                    ROUND(
                        (SUM(CASE WHEN a.estado IN ('presente', 'atrasado') THEN 1 ELSE 0 END) / NULLIF(COUNT(a.id), 0)) * 100, 
                        1
                    ) AS porcentaje_asistencia
                FROM estudiantes_cursos ec
                INNER JOIN usuarios u ON ec.estudiante_id = u.id
                LEFT JOIN asistencias a ON a.estudiante_id = u.id 
                    AND a.curso_id = ec.curso_id";

        $params = ['curso_id' => $cursoId];

        if (!empty($fechaInicio)) {
            $sql .= " AND a.fecha >= :inicio";
            $params['inicio'] = $fechaInicio;
        }
        if (!empty($fechaFin)) {
            $sql .= " AND a.fecha <= :fin";
            $params['fin'] = $fechaFin;
        }

        $sql .= " WHERE ec.curso_id = :curso_id AND ec.activo = 1 AND u.activo = 1
                 GROUP BY u.id
                 ORDER BY u.apellido, u.nombre";

        return $this->query($sql, $params);
    }
}

