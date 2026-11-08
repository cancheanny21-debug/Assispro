<?php
/**
 * AsistePro - Vista de Gestión de Docentes
 */
$titulo = 'Gestión de Docentes';
$pagina_actual = 'docentes';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Docentes</h4>
        <p class="text-muted mb-0">Total registrado: <?= number_format($total ?? 0) ?> docentes</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDocente" onclick="nuevoDocente()">
            <i class="bi bi-person-plus me-1"></i>Nuevo Docente
        </button>
    </div>
</div>

<!-- Buscador -->
<div class="card-custom search-card mb-4">
    <div class="card-body">
        <form method="GET" action="<?= url('/docentes.php') ?>" class="row g-2 align-items-center">
            <div class="col-md-9 col-sm-8">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" class="form-control" placeholder="Buscar por nombre, apellido o correo..." value="<?= e($busqueda ?? '') ?>">
                </div>
            </div>
            <div class="col-md-3 col-sm-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i>Buscar</button>
                <?php if (!empty($busqueda)): ?>
                    <a href="<?= url('/docentes.php') ?>" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Tabla de docentes -->
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Docente</th>
                    <th>Correo Electrónico</th>
                    <th>Cursos Asignados</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($docentes)): ?>
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                        No se encontraron docentes registrados.
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($docentes as $i => $doc): ?>
                    <tr>
                        <td class="text-muted"><?= $i + 1 ?></td>
                        <td>
                            <div class="fw-semibold text-dark"><?= e($doc['nombre'] . ' ' . $doc['apellido']) ?></div>
                            <small class="text-muted">ID: #<?= $doc['id'] ?></small>
                        </td>
                        <td><?= e($doc['email']) ?></td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary">
                                <?= $doc['total_cursos'] ?> <?= $doc['total_cursos'] == 1 ? 'curso' : 'cursos' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($doc['activo']): ?>
                                <span class="badge bg-success-subtle text-success">Activo</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger">Inactivo</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 justify-content-end">
                                <button type="button" class="btn btn-outline-primary btn-action" 
                                    onclick='editarDocente(<?= json_encode($doc) ?>)' title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="<?= url('/docentes.php?action=eliminar') ?>" class="d-inline" onsubmit="return confirm('¿Desactivar a este docente?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                                    <input type="hidden" name="id" value="<?= $doc['id'] ?>">
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
                    <a class="page-link" href="<?= url('/docentes.php?p=' . $p . (!empty($busqueda) ? '&q=' . urlencode($busqueda) : '')) ?>"><?= $p ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </div>
    <?php endif; ?>
</div>

<!-- Modal Crear / Editar Docente -->
<div class="modal fade" id="modalDocente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= url('/docentes.php?action=guardar') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="id" id="doc_id" value="0">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalDocTitulo">Nuevo Docente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nombres *</label>
                        <input type="text" name="nombre" id="doc_nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Apellidos *</label>
                        <input type="text" name="apellido" id="doc_apellido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Correo Electrónico *</label>
                        <input type="email" name="email" id="doc_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contraseña <small class="text-muted">(dejar en blanco para conservar actual o usar Demo1234!)</small></label>
                        <input type="password" name="password" id="doc_password" class="form-control" placeholder="Contraseña">
                    </div>
                    <div class="form-check form-switch" id="doc_div_activo" style="display:none;">
                        <input class="form-check-input" type="checkbox" name="activo" value="1" id="doc_activo" checked>
                        <label class="form-check-label" for="doc_activo">Cuenta Activa</label>
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
function nuevoDocente() {
    document.getElementById('modalDocTitulo').innerText = 'Nuevo Docente';
    document.getElementById('doc_id').value = '0';
    document.getElementById('doc_nombre').value = '';
    document.getElementById('doc_apellido').value = '';
    document.getElementById('doc_email').value = '';
    document.getElementById('doc_password').value = '';
    document.getElementById('doc_div_activo').style.display = 'none';
}

function editarDocente(doc) {
    document.getElementById('modalDocTitulo').innerText = 'Editar Docente';
    document.getElementById('doc_id').value = doc.id;
    document.getElementById('doc_nombre').value = doc.nombre;
    document.getElementById('doc_apellido').value = doc.apellido;
    document.getElementById('doc_email').value = doc.email;
    document.getElementById('doc_password').value = '';
    document.getElementById('doc_div_activo').style.display = 'block';
    document.getElementById('doc_activo').checked = (parseInt(doc.activo) === 1);

    const modal = new bootstrap.Modal(document.getElementById('modalDocente'));
    modal.show();
}
</script>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

