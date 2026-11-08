<?php
/**
 * AsistePro - Vista de Gestión de Cursos
 */
$titulo = 'Gestión de Cursos';
$pagina_actual = 'cursos';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Cursos y Paralelos</h4>
        <p class="text-muted mb-0">Total registrado: <?= number_format($total ?? 0) ?> cursos</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCurso" onclick="nuevoCurso()">
            <i class="bi bi-plus-circle me-1"></i>Nuevo Curso
        </button>
    </div>
</div>

<!-- Buscador -->
<div class="card-custom search-card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('/cursos.php') ?>" class="row g-2 align-items-center">
            <div class="col-md-9 col-sm-8">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, código o docente..." value="<?= e($busqueda ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Buscar</button>
                <?php if (!empty($busqueda)): ?>
                    <a href="<?= url('/cursos.php') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Grid de tarjetas de cursos -->
<div class="row g-4 mb-4">
    <?php if (empty($cursos)): ?>
        <div class="col-12">
            <div class="card-custom p-5 text-center text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                No se encontraron cursos registrados.
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($cursos as $c): ?>
        <div class="col-md-6 col-lg-4">
            <div class="card-custom h-100 d-flex flex-column">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-primary"><?= e($c['codigo']) ?></span>
                    <span class="badge bg-light text-muted border"><?= e($c['periodo'] ?? '2026-2027') ?></span>
                </div>
                <div class="card-body flex-grow-1">
                    <h5 class="card-title text-primary fw-bold mb-2"><?= e($c['nombre']) ?></h5>
                    <p class="card-text text-muted small mb-3">
                        <?= e($c['descripcion'] ?: 'Sin descripción detallada.') ?>
                    </p>

                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-person-badge text-secondary me-2 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Docente Asignado:</small>
                            <span class="fw-semibold text-dark"><?= e($c['docente_nombre'] ?: 'Sin asignar') ?></span>
                        </div>
                    </div>

                    <div class="d-flex align-items-center">
                        <i class="bi bi-people text-primary me-2 fs-5"></i>
                        <div>
                            <small class="text-muted d-block">Matriculados:</small>
                            <span class="fw-semibold text-dark"><?= (int)($c['total_estudiantes'] ?? 0) ?> estudiantes</span>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                    <a href="<?= url('/asistencia.php?curso_id=' . $c['id']) ?>" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-calendar-check me-1"></i>Asistencia
                    </a>
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-outline-primary btn-action" onclick='editarCurso(<?= json_encode($c) ?>)' title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form method="POST" action="<?= url('/cursos.php?action=eliminar') ?>" class="d-inline" onsubmit="return confirm('¿Desactivar este curso?');">
                            <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                            <button type="submit" class="btn btn-outline-danger btn-action" title="Desactivar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- Modal Crear / Editar Curso -->
<div class="modal fade" id="modalCurso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= url('/cursos.php?action=guardar') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="id" id="curso_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalCursoTitulo">Nuevo Curso</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombre del Curso *</label>
                        <input type="text" name="nombre" id="curso_nombre" class="form-control" placeholder="Ej: Desarrollo de Software" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Código Único *</label>
                            <input type="text" name="codigo" id="curso_codigo" class="form-control" placeholder="Ej: DS-401" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Periodo Académico</label>
                            <input type="text" name="periodo" id="curso_periodo" class="form-control" value="2026-2027">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Docente Asignado</label>
                        <select name="docente_id" id="curso_docente" class="form-select">
                            <option value="">-- Sin asignar --</option>
                            <?php foreach ($docentes as $d): ?>
                                <option value="<?= $d['id'] ?>"><?= e($d['apellido'] . ' ' . $d['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion" id="curso_descripcion" class="form-control" rows="3" placeholder="Objetivos o detalles del curso..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function nuevoCurso() {
    document.getElementById('modalCursoTitulo').innerText = 'Nuevo Curso';
    document.getElementById('curso_id').value = '0';
    document.getElementById('curso_nombre').value = '';
    document.getElementById('curso_codigo').value = '';
    document.getElementById('curso_periodo').value = '2026-2027';
    document.getElementById('curso_docente').value = '';
    document.getElementById('curso_descripcion').value = '';
}

function editarCurso(c) {
    document.getElementById('modalCursoTitulo').innerText = 'Editar Curso';
    document.getElementById('curso_id').value = c.id;
    document.getElementById('curso_nombre').value = c.nombre;
    document.getElementById('curso_codigo').value = c.codigo;
    document.getElementById('curso_periodo').value = c.periodo || '2026-2027';
    document.getElementById('curso_docente').value = c.docente_id || '';
    document.getElementById('curso_descripcion').value = c.descripcion || '';

    const modal = new bootstrap.Modal(document.getElementById('modalCurso'));
    modal.show();
}
</script>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

