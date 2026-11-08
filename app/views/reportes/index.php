<?php
/**
 * AsistePro - Vista de Reportes y Estadísticas
 */
$titulo = 'Reportes de Asistencia';
$pagina_actual = 'reportes';
ob_start();
?>

<!-- Encabezado que se oculta al imprimir -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 d-print-none">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Reportes de Asistencia</h4>
        <p class="text-muted mb-0">Consolidado general de asistencia, ausencias y justificaciones por curso.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= url('/exportar.php?tipo=excel&curso_id=' . $cursoSeleccionado . '&fecha_inicio=' . $fechaInicio . '&fecha_fin=' . $fechaFin) ?>" 
           class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i>Exportar Excel
        </a>
        <a href="<?= url('/exportar.php?tipo=pdf&curso_id=' . $cursoSeleccionado . '&fecha_inicio=' . $fechaInicio . '&fecha_fin=' . $fechaFin . '&print=1') ?>" 
           target="_blank" class="btn btn-outline-primary">
            <i class="bi bi-printer me-1"></i>Imprimir / Guardar PDF
        </a>
    </div>
</div>

<!-- Filtros (ocultos al imprimir) -->
<div class="card-custom mb-4 d-print-none">
    <div class="card-body">
        <form method="GET" action="<?= url('/reportes.php') ?>" class="row g-3 align-items-end">
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
                <button type="submit" class="btn btn-primary w-100" title="Generar reporte">
                    <i class="bi bi-funnel"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Encabezado especial para impresión oficial -->
<div class="d-none d-print-block mb-4 text-center border-bottom pb-3">
    <h3 class="fw-bold mb-1">INSTITUTO SUPERIOR TECNOLÓGICO ALBERTO ENRÍQUEZ</h3>
    <h5 class="text-secondary mb-2">AsistePro — Reporte Consolidado de Asistencia Estudiantil</h5>
    <p class="mb-0 small text-muted">
        <strong>Curso:</strong> <?= e($cursoActual['nombre'] ?? '') ?> (<?= e($cursoActual['codigo'] ?? '') ?>) &nbsp;|&nbsp; 
        <strong>Docente:</strong> <?= e($cursoActual['docente_nombre'] ?? 'Sin asignar') ?> &nbsp;|&nbsp; 
        <strong>Periodo:</strong> <?= date('d/m/Y', strtotime($fechaInicio)) ?> al <?= date('d/m/Y', strtotime($fechaFin)) ?>
    </p>
</div>

<!-- Tabla del reporte -->
<div class="card-custom">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h6 class="mb-0 fw-bold text-primary">
            <i class="bi bi-table me-2"></i><?= e($cursoActual['nombre'] ?? 'Curso') ?> — Consolidado de Estudiantes
        </h6>
        <span class="badge bg-light text-dark border d-print-none"><?= count($datosReporte) ?> alumnos</span>
    </div>
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 40px;">#</th>
                    <th>Estudiante</th>
                    <th>Email</th>
                    <th class="text-center">Clases</th>
                    <th class="text-center text-success">Presentes</th>
                    <th class="text-center text-warning">Atrasos</th>
                    <th class="text-center text-danger">Ausencias</th>
                    <th class="text-center text-info">Justif.</th>
                    <th style="min-width: 180px;">% Asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($datosReporte)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            No existen registros de asistencia para este curso en las fechas seleccionadas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($datosReporte as $idx => $fila): ?>
                    <?php 
                        $pct = (float)($fila['porcentaje_asistencia'] ?? 0);
                        $colorClase = $pct >= 80 ? 'bg-success' : ($pct >= 70 ? 'bg-warning' : 'bg-danger');
                        $badgeClase = $pct >= 80 ? 'text-success' : ($pct >= 70 ? 'text-warning' : 'text-danger');
                    ?>
                    <tr>
                        <td class="text-muted text-center"><?= $idx + 1 ?></td>
                        <td class="fw-semibold text-dark"><?= e($fila['nombre'] . ' ' . $fila['apellido']) ?></td>
                        <td><small class="text-muted"><?= e($fila['email']) ?></small></td>
                        <td class="text-center fw-bold"><?= $fila['total_clases'] ?></td>
                        <td class="text-center text-success fw-bold"><?= $fila['presentes'] ?></td>
                        <td class="text-center text-warning"><?= $fila['atrasados'] ?></td>
                        <td class="text-center text-danger fw-bold"><?= $fila['ausentes'] ?></td>
                        <td class="text-center text-info"><?= $fila['justificados'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar <?= $colorClase ?>" role="progressbar" style="width: <?= $pct ?>%;"></div>
                                </div>
                                <span class="fw-bold <?= $badgeClase ?> small"><?= $pct ?>%</span>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pie de página para impresión con firmas -->
    <div class="d-none d-print-block mt-5 pt-5 px-4">
        <div class="row text-center">
            <div class="col-6">
                <div class="border-top border-dark mx-auto pt-2" style="width: 220px;">
                    <strong>Docente de la Asignatura</strong>
                    <br><small><?= e($cursoActual['docente_nombre'] ?? 'Firma y Sello') ?></small>
                </div>
            </div>
            <div class="col-6">
                <div class="border-top border-dark mx-auto pt-2" style="width: 220px;">
                    <strong>Coordinación Académica</strong>
                    <br><small>ISTAE</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

