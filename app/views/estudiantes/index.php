<?php
/**
 * AsistePro - Vista de Gestión de Estudiantes
 */
$titulo = 'Gestión de Estudiantes';
$pagina_actual = 'estudiantes';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Estudiantes</h4>
        <p class="text-muted mb-0">Total registrado: <?= number_format($total ?? 0) ?> estudiantes</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEstudiante" onclick="nuevoEstudiante()">
            <i class="bi bi-person-plus me-1"></i>Nuevo Estudiante
        </button>
    </div>
</div>

<!-- Barra de búsqueda -->
<div class="card-custom search-card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('/estudiantes.php') ?>" class="row g-2 align-items-center">
            <div class="col-md-9 col-sm-8">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, apellido o correo..." value="<?= e($busqueda ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Buscar</button>
                <?php if (!empty($busqueda)): ?>
                    <a href="<?= url('/estudiantes.php') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de estudiantes -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Estudiante</th>
                    <th>Correo Electrónico</th>
                    <th>Cursos Matriculados</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estudiantes)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        No se encontraron estudiantes registrados.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $i => $est): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?= e($est['nombre'] . ' ' . $est['apellido']) ?></div>
                            <small class="text-muted">ID: #<?= $est['id'] ?></small>
                        </td>
                        <td><?= e($est['email']) ?></td>
                        <td>
                            <?php if (!empty($est['cursos'])): ?>
                                <span class="badge bg-light text-dark border"><?= e($est['cursos']) ?></span>
                            <?php else: ?>
                                <span class="text-muted fst-italic">Sin cursos</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($est['activo']): ?>
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 justify-content-end">
                                <button type="button" class="btn btn-outline-success btn-action" 
                                    onclick='abrirMatricula(<?= $est['id'] ?>, "<?= e($est['nombre'] . ' ' . $est['apellido']) ?>")' title="Matricular en Curso">
                                    <i class="bi bi-journal-plus"></i>
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-action" 
                                    onclick='editarEstudiante(<?= json_encode($est) ?>)' title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= url('/estudiantes.php?action=eliminar') ?>" class="d-inline" onsubmit="return confirm('¿Desactivar a este estudiante?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                                    <input type="hidden" name="id" value="<?= $est['id'] ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-action" title="Desactivar">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if (($totalPaginas ?? 1) > 1): ?>
    <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
        <small class="text-muted">Página <?= $pagina ?> de <?= $totalPaginas ?></small>
        <ul class="pagination pagination-sm mb-0">
            <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="<?= url('/estudiantes.php?p=' . $p . (!empty($busqueda) ? '&q=' . urlencode($busqueda) : '')) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Crear / Editar Estudiante -->
<div class="modal fade" id="modalEstudiante" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= url('/estudiantes.php?action=guardar') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="id" id="est_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">Nuevo Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombres *</label>
                        <input type="text" name="nombre" id="est_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos *</label>
                        <input type="text" name="apellido" id="est_apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" name="email" id="est_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña <small class="text-muted">(dejar en blanco para conservar actual o usar Demo1234!)</small></label>
                        <input type="password" name="password" id="est_password" class="form-control" placeholder="Contraseña">
                    </div>
                    <div class="form-check form-switch" id="div_activo" style="display:none;">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="est_activo" checked>
                        <label class="form-check-label" for="est_activo">Cuenta Activa</label>
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

<!-- Modal Matricular Estudiante en Curso -->
<div class="modal fade" id="modalMatricula" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= url('/estudiantes.php?action=matricular') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="estudiante_id" id="mat_estudiante_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-journal-plus me-2"></i>Matricular Estudiante</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Estudiante: <strong id="mat_estudiante_nombre" class="text-primary"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Seleccionar Curso *</label>
                        <select name="curso_id" class="form-select" required>
                            <option value="">-- Seleccionar curso --</option>
                            <?php foreach (($todosCursos ?? []) as $cur): ?>
                                <option value="<?= $cur['id'] ?>">
                                    <?= e($cur['nombre']) ?> (<?= e($cur['codigo']) ?>) — <?= e($cur['docente_nombre'] ?? 'Sin docente') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-circle me-1"></i>Confirmar Matrícula</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function abrirMatricula(id, nombre) {
    document.getElementById('mat_estudiante_id').value = id;
    document.getElementById('mat_estudiante_nombre').innerText = nombre;
    const modal = new bootstrap.Modal(document.getElementById('modalMatricula'));
    modal.show();
}

function nuevoEstudiante() {
    document.getElementById('modalTitulo').innerText = 'Nuevo Estudiante';
    document.getElementById('est_id').value = '0';
    document.getElementById('est_nombre').value = '';
    document.getElementById('est_apellido').value = '';
    document.getElementById('est_email').value = '';
    document.getElementById('est_password').value = '';
    document.getElementById('div_activo').style.display = 'none';
}

function editarEstudiante(est) {
    document.getElementById('modalTitulo').innerText = 'Editar Estudiante';
    document.getElementById('est_id').value = est.id;
    document.getElementById('est_nombre').value = est.nombre;
    document.getElementById('est_apellido').value = est.apellido;
    document.getElementById('est_email').value = est.email;
    document.getElementById('est_password').value = '';
    document.getElementById('div_activo').style.display = 'block';
    document.getElementById('est_activo').checked = (parseInt(est.activo) === 1);

    const modal = new bootstrap.Modal(document.getElementById('modalEstudiante'));
    modal.show();
}
</script>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

