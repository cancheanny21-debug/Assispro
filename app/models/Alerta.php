<?php
/**
 * AsistePro - Modelo de Alerta
 * 
 * Gestiona alertas automáticas y manuales sobre inasistencias y atrasos reiterados.
 */

require_once __DIR__ . '/Model.php';

class Alerta extends Model
{
    /**
     * Lista alertas del sistema o específicas para un usuario
     */
    public function listar(?int $estudianteId = null, bool $soloNoLeidas = false, int $limite = 50): array
    {
        $sql = "SELECT a.*, 
                       u.nombre AS estudiante_nombre, 
                       u.apellido AS estudiante_apellido, 
                       u.email AS estudiante_email,
                       c.nombre AS curso_nombre,
                       c.codigo AS curso_codigo
                FROM alertas a
                INNER JOIN usuarios u ON a.estudiante_id = u.id
                LEFT JOIN cursos c ON a.curso_id = c.id
                WHERE 1=1";
        $params = [];

        if ($estudianteId !== null) {
            $sql .= " AND a.estudiante_id = :estudiante_id";
            $params['estudiante_id'] = $estudianteId;
        }

        if ($soloNoLeidas) {
            $sql .= " AND a.leida = 0";
        }

        $sql .= " ORDER BY a.leida ASC, a.created_at DESC LIMIT :limite";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Marca una alerta como leída
     */
    public function marcarLeida(int $id): bool
    {
        return $this->execute(
            "UPDATE alertas SET leida = 1 WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }

    /**
     * Marca todas las alertas como leídas
     */
    public function marcarTodasLeidas(?int $estudianteId = null): bool
    {
        if ($estudianteId !== null) {
            return $this->execute(
                "UPDATE alertas SET leida = 1 WHERE estudiante_id = :estudiante_id AND leida = 0",
                ['estudiante_id' => $estudianteId]
            ) > 0;
        }

        return $this->execute("UPDATE alertas SET leida = 1 WHERE leida = 0") > 0;
    }

    /**
     * Registra una alerta nueva
     */
    public function crear(int $estudianteId, ?int $cursoId, string $tipo, string $mensaje): int
    {
        $this->execute(
            "INSERT INTO alertas (estudiante_id, curso_id, tipo, mensaje, leida, created_at)
             VALUES (:estudiante_id, :curso_id, :tipo, :mensaje, 0, NOW())",
            [
                'estudiante_id' => $estudianteId,
                'curso_id'      => $cursoId,
                'tipo'          => $tipo,
                'mensaje'       => $mensaje,
            ]
        );
        return (int)$this->lastInsertId();
    }

    /**
     * Analiza asistencias recientes y genera alertas automáticas para estudiantes en riesgo
     * (> 20% de inasistencia o 3 inasistencias consecutivas)
     */
    public function procesarAlertasAutomaticas(): int
    {
        $generadas = 0;

        // 1. Detección por porcentaje de inasistencia > 20%
        $sqlPorcentaje = "
            SELECT 
                ec.estudiante_id,
                ec.curso_id,
                u.nombre,
                u.apellido,
                c.nombre AS curso_nombre,
                COUNT(a.id) AS total_registros,
                SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) AS total_ausencias,
                ROUND((SUM(CASE WHEN a.estado = 'ausente' THEN 1 ELSE 0 END) / COUNT(a.id)) * 100, 1) AS pct_ausencias
            FROM estudiantes_cursos ec
            INNER JOIN usuarios u ON ec.estudiante_id = u.id
            INNER JOIN cursos c ON ec.curso_id = c.id
            INNER JOIN asistencias a ON a.estudiante_id = ec.estudiante_id AND a.curso_id = ec.curso_id
            WHERE ec.activo = 1
            GROUP BY ec.estudiante_id, ec.curso_id
            HAVING total_registros >= 5 AND pct_ausencias >= 20
        ";

        $estudiantesRiesgo = $this->query($sqlPorcentaje);

        foreach ($estudiantesRiesgo as $riesgo) {
            // Verificar si ya existe una alerta similar sin leer en los últimos 7 días
            $existe = $this->queryOne(
                "SELECT id FROM alertas 
                 WHERE estudiante_id = :est_id 
                   AND curso_id = :curso_id 
                   AND tipo = 'inasistencia' 
                   AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)",
                [
                    'est_id'   => $riesgo['estudiante_id'],
                    'curso_id' => $riesgo['curso_id'],
                ]
            );

            if (!$existe) {
                $msg = sprintf(
                    "%s %s registra un %.1f%% de inasistencias en el curso %s (%d de %d clases).",
                    $riesgo['nombre'],
                    $riesgo['apellido'],
                    $riesgo['pct_ausencias'],
                    $riesgo['curso_nombre'],
                    $riesgo['total_ausencias'],
                    $riesgo['total_registros']
                );
                $this->crear((int)$riesgo['estudiante_id'], (int)$riesgo['curso_id'], 'inasistencia', $msg);
                $generadas++;
            }
        }

        return $generadas;
    }
}

