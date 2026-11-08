<?php
/**
 * AsistePro - Vista de Historial de Asistencia
 */
$titulo = 'Historial de Asistencia';
$pagina_actual = 'asistencia';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Historial de Clases</h4>
        <p class="text-muted mb-0">Consulta los días en los que se tomó asistencia y sus estadísticas resumidas.</p>
    </div>
    <div>
        <a href="<?= url('/asistencia.php' . ($cursoSeleccionado ? '?curso_id=' . $cursoSeleccionado : '')) ?>" class="btn btn-primary">
            <i class="bi bi-pencil-square me-1"></i>Tomar Asistencia
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('/asistencia_historial.php') ?>" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Curso</label>
                <select name="curso_id" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $c['id'] == $cursoSeleccionado ? 'selected' : '' ?>>
                            <?= e($c['nombre']) ?> (<?= e($c['codigo']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Desde</label>
                <input type="date" name="fecha_inicio" class="form-control" value="<?= e($fechaInicio) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Hasta</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= e($fechaFin) ?>">
            </div>
            <div class="col-md-1 d-flex">
                <button type="submit" class="btn btn-primary w-100" title="Filtrar">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de historial -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Alumnos</th>
                    <th>Presentes</th>
                    <th>Atrasados</th>
                    <th>Ausentes</th>
                    <th>Justificados</th>
                    <th>Registrado Por</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                            No hay registros de asistencia en el rango de fechas seleccionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historial as $h): ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                        <td><span class="badge bg-light text-dark border"><?= $h['total_alumnos'] ?></span></td>
                        <td><span class="badge bg-success-subtle text-success"><?= $h['presentes'] ?></span></td>
                        <td><span class="badge bg-warning-subtle text-warning"><?= $h['atrasados'] ?></span></td>
                        <td><span class="badge bg-danger-subtle text-danger"><?= $h['ausentes'] ?></span></td>
                        <td><span class="badge bg-info-subtle text-info"><?= $h['justificados'] ?></span></td>
                        <td><small class="text-muted"><?= e($h['registrado_por_nombre'] ?: 'Sistema') ?></small></td>
                        <td class="text-end">
                            <a href="<?= url('/asistencia.php?curso_id=' . $cursoSeleccionado . '&fecha=' . $h['fecha']) ?>" 
                               class="btn btn-sm btn-outline-primary" title="Ver / Modificar">
                                <i class="bi bi-eye me-1"></i>Ver / Editar
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

