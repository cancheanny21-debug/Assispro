<?php
/**
 * AsistePro - Vista de Gestión de Estudiantes (Diseño UI/UX SaaS Moderno)
 */
$titulo = 'Gestión de Estudiantes';
$pagina_actual = 'estudiantes';
ob_start();
?>

<!-- Encabezado del Módulo -->
<div class="page-header-box">
    <div>
        <h2 class="page-header-title">Gestión de Estudiantes</h2>
        <p class="page-header-subtitle">
            <i class="bi bi-mortarboard me-1 text-primary"></i> Nómina general: <strong class="text-dark"><?= number_format($total ?? 0) ?> estudiantes registrados</strong>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn-modern-primary" data-bs-toggle="modal" data-bs-target="#modalEstudiante" onclick="nuevoEstudiante()">
            <i class="bi bi-person-plus-fill"></i>
            <span>Nuevo Estudiante</span>
        </button>
    </div>
</div>

<!-- Toolbar de Búsqueda y Filtros -->
<div class="toolbar-card">
    <form method="GET" action="<?= url('/estudiantes.php') ?>" class="row g-2 align-items-center">
        <div class="col-md-9 col-sm-8">
            <div class="search-input-wrapper">
                <i class="bi bi-search search-input-icon"></i>
                <input type="text" name="q" class="search-input-modern" placeholder="Buscar por nombre, apellido, correo institucional o ID..." value="<?= e($busqueda ?? '') ?>" autocomplete="off">
            </div>
        </div>
        <div class="col-md-3 col-sm-4 d-flex gap-2">
            <button type="submit" class="btn-modern-primary w-100 justify-content-center">
                <i class="bi bi-funnel-fill"></i>
                <span>Buscar</span>
            </button>
            <?php if (!empty($busqueda)): ?>
                <a href="<?= url('/estudiantes.php') ?>" class="btn-modern-secondary" title="Limpiar búsqueda">
                    <i class="bi bi-x-lg"></i>
                </a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Tabla de Estudiantes SaaS -->
<div class="card-custom">
    <div class="table-responsive table-responsive-clean">
        <table class="table-modern">
            <thead>
                <tr>
                    <th style="width: 50px;" class="text-center">#</th>
                    <th>Estudiante</th>
                    <th>Correo Electrónico</th>
                    <th>Cursos Matriculados</th>
                    <th style="width: 120px;" class="text-center">Estado</th>
                    <th style="width: 140px;" class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estudiantes)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <div class="py-4">
                            <i class="bi bi-people text-secondary fs-1 d-block mb-3 opacity-50"></i>
                            <h5 class="fw-bold text-dark mb-1">No se encontraron estudiantes</h5>
                            <p class="small text-muted mb-3">Intenta con otro término de búsqueda o registra un nuevo estudiante.</p>
                            <?php if (!empty($busqueda)): ?>
                                <a href="<?= url('/estudiantes.php') ?>" class="btn-modern-secondary">
                                    <i class="bi bi-arrow-left me-1"></i>Ver todos los estudiantes
                                </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($estudiantes as $i => $est): ?>
                    <?php 
                        // Obtener iniciales para el avatar
                        $nombreCompleto = trim($est['nombre'] . ' ' . $est['apellido']);
                        $iniciales = mb_strtoupper(mb_substr($est['nombre'], 0, 1) . mb_substr($est['apellido'], 0, 1));
                    ?>
                    <tr>
                        <td class="text-center text-muted fw-semibold" style="font-size: 0.85rem;"><?= $i + 1 ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <div class="student-avatar-badge">
                                    <?= e($iniciales ?: 'ST') ?>
                                </div>
                                <div>
                                    <div class="student-info-name"><?= e($nombreCompleto) ?></div>
                                    <span class="student-info-id"><i class="bi bi-hash"></i>ID: <?= $est['id'] ?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="student-info-email">
                                <i class="bi bi-envelope text-muted"></i>
                                <?= e($est['email']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($est['cursos'])): ?>
                                <?php 
                                    $listaCursos = explode(',', $est['cursos']);
                                    foreach ($listaCursos as $cursoItem): 
                                        $cursoTrim = trim($cursoItem);
                                        if (empty($cursoTrim)) continue;
                                ?>
                                    <span class="badge-course">
                                        <i class="bi bi-book-half text-primary"></i>
                                        <?= e($cursoTrim) ?>
                                    </span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="badge-course-empty">Sin cursos asignados</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($est['activo']): ?>
                                <span class="status-pill active">
                                    <span class="status-pill-dot"></span>
                                    Activo
                                </span>
                            <?php else: ?>
                                <span class="status-pill inactive">
                                    <span class="status-pill-dot"></span>
                                    Inactivo
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="action-buttons-group">
                                <button type="button" class="btn-action-round enroll" 
                                    onclick='abrirMatricula(<?= $est['id'] ?>, "<?= e(addslashes($nombreCompleto)) ?>")' 
                                    title="Matricular en Curso" data-bs-toggle="tooltip">
                                    <i class="bi bi-journal-plus"></i>
                                </button>
                                <button type="button" class="btn-action-round edit" 
                                    onclick='editarEstudiante(<?= json_encode($est) ?>)' 
                                    title="Editar Información" data-bs-toggle="tooltip">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= url('/estudiantes.php?action=eliminar') ?>" class="d-inline" onsubmit="return confirm('¿Desactivar a este estudiante del sistema?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                                    <input type="hidden" name="id" value="<?= $est['id'] ?>">
                                    <button type="submit" class="btn-action-round delete" title="Desactivar Estudiante" data-bs-toggle="tooltip">
                                        <i class="bi bi-trash3"></i>
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

    <!-- Paginación Moderna -->
    <?php if (($totalPaginas ?? 1) > 1): ?>
    <div class="pagination-modern-wrapper">
        <span class="pagination-counter">
            Mostrando página <strong class="text-dark"><?= $pagina ?></strong> de <strong class="text-dark"><?= $totalPaginas ?></strong> (<?= number_format($total ?? 0) ?> estudiantes)
        </span>
        <ul class="pagination-modern">
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= url('/estudiantes.php?action=guardar') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="id" id="est_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo">
                        <i class="bi bi-person-fill-gear me-2 text-primary"></i>Nuevo Estudiante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombres Completos *</label>
                        <input type="text" name="nombre" id="est_nombre" class="form-control" placeholder="Ej: Juan Andrés" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos Completos *</label>
                        <input type="text" name="apellido" id="est_apellido" class="form-control" placeholder="Ej: Pérez Morales" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" name="email" id="est_email" class="form-control" placeholder="nombre.apellido@asistepro.local" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña <small class="text-muted fw-normal">(opcional, por defecto: Demo1234!)</small></label>
                        <input type="password" name="password" id="est_password" class="form-control" placeholder="••••••••">
                    </div>
                    <div class="form-check form-switch mt-3 pt-2 border-top" id="div_activo" style="display:none;">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="est_activo" checked>
                        <label class="form-check-label fw-semibold" for="est_activo">Cuenta Activa</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-modern-primary">
                        <i class="bi bi-check-lg"></i>
                        <span>Guardar Cambios</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Matricular Estudiante en Curso -->
<div class="modal fade" id="modalMatricula" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="<?= url('/estudiantes.php?action=matricular') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="estudiante_id" id="mat_estudiante_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-journal-plus me-2 text-success"></i>Matricular Estudiante
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <div class="p-3 mb-3 rounded" style="background-color: #f1f5f9; border: 1px solid #e2e8f0;">
                        <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 0.72rem;">Estudiante seleccionado:</small>
                        <span id="mat_estudiante_nombre" class="fw-bold text-dark fs-6"></span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Seleccionar Curso / Asignatura *</label>
                        <select name="curso_id" class="form-select" required>
                            <option value="">-- Elige un curso disponible --</option>
                            <?php foreach (($todosCursos ?? []) as $cur): ?>
                                <option value="<?= $cur['id'] ?>">
                                    <?= e($cur['nombre']) ?> (<?= e($cur['codigo']) ?>) — <?= e($cur['docente_nombre'] ?? 'Sin docente') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-modern-primary" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="bi bi-check-circle-fill"></i>
                        <span>Confirmar Matrícula</span>
                    </button>
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
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-person-plus-fill me-2 text-primary"></i>Nuevo Estudiante';
    document.getElementById('est_id').value = '0';
    document.getElementById('est_nombre').value = '';
    document.getElementById('est_apellido').value = '';
    document.getElementById('est_email').value = '';
    document.getElementById('est_password').value = '';
    document.getElementById('div_activo').style.display = 'none';
}

function editarEstudiante(est) {
    document.getElementById('modalTitulo').innerHTML = '<i class="bi bi-pencil-square me-2 text-primary"></i>Editar Estudiante';
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
