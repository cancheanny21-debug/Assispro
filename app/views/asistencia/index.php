<?php
/**
 * AsistePro - Vista de Toma de Asistencia y Vista Estudiante
 */
$titulo = 'Control de Asistencia';
$pagina_actual = 'asistencia';
ob_start();
?>

<?php if (!empty($esEstudiante)): ?>
<!-- ========================================== -->
<!-- VISTA PARA ESTUDIANTES                     -->
<!-- ========================================== -->
<div class="mb-4">
    <h4 class="text-primary fw-bold mb-1">Mi Historial de Asistencia</h4>
    <p class="text-muted mb-0">Consulta tus asistencias, atrasos y justificaciones en tiempo real.</p>
</div>

<!-- Métricas del estudiante -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-value text-success"><?= $resumen['porcentaje'] ?>%</div>
            <div class="stat-label">Porcentaje de Asistencia</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-value"><?= $resumen['presentes'] ?></div>
            <div class="stat-label">Clases Presente</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-value text-warning"><?= $resumen['atrasados'] ?></div>
            <div class="stat-label">Atrasos</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card">
            <div class="stat-value text-danger"><?= $resumen['ausentes'] ?></div>
            <div class="stat-label">Inasistencias</div>
        </div>
    </div>
</div>

<!-- Tabla de asistencias personales -->
<div class="card-custom">
    <div class="card-header bg-white">
        <i class="bi bi-clock-history me-1"></i>Detalle de Asistencias
    </div>
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Curso</th>
                    <th>Estado</th>
                    <th>Observación</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($historial)): ?>
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No tienes registros de asistencia aún.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($historial as $h): ?>
                    <tr>
                        <td class="fw-semibold"><?= date('d/m/Y', strtotime($h['fecha'])) ?></td>
                        <td><?= e($h['curso_nombre']) ?> <small class="text-muted">(<?= e($h['curso_codigo']) ?>)</small></td>
                        <td>
                            <?php if ($h['estado'] === 'presente'): ?>
                                <span class="badge badge-presente"><i class="bi bi-check-circle me-1"></i>Presente</span>
                            <?php elseif ($h['estado'] === 'ausente'): ?>
                                <span class="badge badge-ausente"><i class="bi bi-x-circle me-1"></i>Ausente</span>
                            <?php elseif ($h['estado'] === 'atrasado'): ?>
                                <span class="badge badge-atrasado"><i class="bi bi-clock me-1"></i>Atrasado</span>
                            <?php else: ?>
                                <span class="badge badge-justificado"><i class="bi bi-file-earmark-text me-1"></i>Justificado</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted"><?= e($h['observacion'] ?: '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php else: ?>
<!-- ========================================== -->
<!-- VISTA PARA DOCENTES Y ADMINISTRADORES      -->
<!-- ========================================== -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Registro de Asistencia</h4>
        <p class="text-muted mb-0">Selecciona el curso y la fecha para tomar o modificar la asistencia.</p>
    </div>
    <div>
        <a href="<?= url('/asistencia_historial.php' . ($cursoSeleccionado ? '?curso_id=' . $cursoSeleccionado : '')) ?>" class="btn btn-outline-primary">
            <i class="bi bi-calendar3 me-1"></i>Ver Historial del Curso
        </a>
    </div>
</div>

<!-- Selector de Curso y Fecha -->
<div class="card-custom mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('/asistencia.php') ?>" class="row g-3 align-items-end">
            <div class="col-md-6 col-lg-5">
                <label class="form-label fw-semibold">Curso / Asignatura</label>
                <select name="curso_id" class="form-select" onchange="this.form.submit()">
                    <?php if (empty($cursos)): ?>
                        <option value="">No tienes cursos disponibles</option>
                    <?php else: ?>
                        <?php foreach ($cursos as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= $c['id'] == $cursoSeleccionado ? 'selected' : '' ?>>
                                <?= e($c['nombre']) ?> (<?= e($c['codigo']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4 col-lg-4">
                <label class="form-label fw-semibold">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="<?= e($fecha) ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-2 col-lg-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-arrow-repeat me-1"></i>Cargar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if ($yaRegistrado): ?>
<div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
    <i class="bi bi-info-circle-fill fs-4 me-2"></i>
    <div>
        <strong>Atención:</strong> Ya existe un registro de asistencia para este curso el día <strong><?= date('d/m/Y', strtotime($fecha)) ?></strong>. Si realizas cambios y guardas, los registros existentes serán actualizados sin duplicarse.
    </div>
</div>
<?php endif; ?>

<!-- Formulario de toma de asistencia -->
<form method="POST" action="<?= url('/asistencia.php?action=guardar') ?>">
    <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
    <input type="hidden" name="curso_id" value="<?= (int)$cursoSeleccionado ?>">
    <input type="hidden" name="fecha" value="<?= e($fecha) ?>">

    <div class="card-custom mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 flex-wrap gap-2">
            <div>
                <span class="fw-bold text-primary me-3">
                    <i class="bi bi-people me-1"></i>Nómina (<?= count($estudiantes) ?> matriculados)
                </span>
                <span class="d-inline-flex align-items-center gap-2 mt-1 mt-md-0">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                        Presentes: <strong id="cnt-presente">0</strong>
                    </span>
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1">
                        Atrasos: <strong id="cnt-atrasado">0</strong>
                    </span>
                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                        Ausentes: <strong id="cnt-ausente">0</strong>
                    </span>
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">
                        Justif.: <strong id="cnt-justificado">0</strong>
                    </span>
                </span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-success" onclick="marcarTodos('presente')">
                    <i class="bi bi-check-all me-1"></i>Todos Presentes
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="marcarTodos('ausente')">
                    <i class="bi bi-x-circle me-1"></i>Todos Ausentes
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-custom table-hover align-middle mb-0 w-100">
                <thead>
                    <tr>
                        <th style="width: 48px;" class="text-center">#</th>
                        <th style="width: 32%;">Estudiante</th>
                        <th style="width: 42%;">Estado de Asistencia</th>
                        <th style="width: 26%;">Observación</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($estudiantes)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <i class="bi bi-person-x fs-1 d-block mb-2"></i>
                                No hay estudiantes matriculados en este curso.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($estudiantes as $idx => $est): ?>
                        <tr>
                            <td class="text-muted text-center fw-semibold"><?= $idx + 1 ?></td>
                            <td>
                                <div class="fw-semibold text-dark"><?= e($est['nombre'] . ' ' . $est['apellido']) ?></div>
                                <small class="text-muted"><?= e($est['email']) ?></small>
                            </td>
                            <td>
                                <div class="btn-group attendance-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="asistencia[<?= $est['estudiante_id'] ?>]" 
                                           id="p_<?= $est['estudiante_id'] ?>" value="presente" 
                                           <?= $est['estado'] === 'presente' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-success btn-sm" for="p_<?= $est['estudiante_id'] ?>">
                                        <i class="bi bi-check me-1"></i>Presente
                                    </label>

                                    <input type="radio" class="btn-check" name="asistencia[<?= $est['estudiante_id'] ?>]" 
                                           id="a_<?= $est['estudiante_id'] ?>" value="atrasado" 
                                           <?= $est['estado'] === 'atrasado' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-warning btn-sm" for="a_<?= $est['estudiante_id'] ?>">
                                        <i class="bi bi-clock me-1"></i>Atrasado
                                    </label>

                                    <input type="radio" class="btn-check" name="asistencia[<?= $est['estudiante_id'] ?>]" 
                                           id="au_<?= $est['estudiante_id'] ?>" value="ausente" 
                                           <?= $est['estado'] === 'ausente' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-danger btn-sm" for="au_<?= $est['estudiante_id'] ?>">
                                        <i class="bi bi-x me-1"></i>Ausente
                                    </label>

                                    <input type="radio" class="btn-check" name="asistencia[<?= $est['estudiante_id'] ?>]" 
                                           id="j_<?= $est['estudiante_id'] ?>" value="justificado" 
                                           <?= $est['estado'] === 'justificado' ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-info btn-sm" for="j_<?= $est['estudiante_id'] ?>">
                                        <i class="bi bi-file-text me-1"></i>Justificado
                                    </label>
                                </div>
                            </td>
                            <td>
                                <input type="text" name="observacion[<?= $est['estudiante_id'] ?>]" 
                                       class="form-control form-control-sm" 
                                       placeholder="Nota opcional..." 
                                       value="<?= e($est['observacion'] ?? '') ?>">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($estudiantes)): ?>
        <div class="card-footer bg-white p-3 text-end">
            <button type="submit" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-save me-2"></i>Guardar Asistencia
            </button>
        </div>
        <?php endif; ?>
    </div>
</form>

<script>
function actualizarContadores() {
    const estados = ['presente', 'atrasado', 'ausente', 'justificado'];
    estados.forEach(st => {
        const total = document.querySelectorAll(`input[type="radio"][value="${st}"]:checked`).length;
        const el = document.getElementById(`cnt-${st}`);
        if (el) el.innerText = total;
    });
}

function marcarTodos(estado) {
    const radios = document.querySelectorAll(`input[type="radio"][value="${estado}"]`);
    radios.forEach(r => r.checked = true);
    actualizarContadores();
}

document.addEventListener('DOMContentLoaded', function() {
    actualizarContadores();
    document.querySelectorAll('input[type="radio"].btn-check').forEach(radio => {
        radio.addEventListener('change', actualizarContadores);
    });
});
</script>

<?php endif; ?>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

