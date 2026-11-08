<?php
/**
 * AsistePro - Vista de Alertas de Inasistencia
 */
$titulo = 'Centro de Alertas';
$pagina_actual = 'alertas';
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="mb-1 text-primary fw-bold">Alertas del Sistema</h4>
        <p class="text-muted mb-0">Detección oportuna de inasistencias críticas y atrasos reiterados.</p>
    </div>
    <div>
        <form method="POST" action="<?= url('/alertas.php?action=marcar_todas') ?>">
            <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
            <button type="submit" class="btn btn-outline-secondary">
                <i class="bi bi-check2-all me-1"></i>Marcar todas como leídas
            </button>
        </form>
    </div>
</div>

<?php if (empty($alertas)): ?>
    <div class="card-custom p-5 text-center text-muted">
        <i class="bi bi-shield-check fs-1 text-success d-block mb-2"></i>
        No hay alertas registradas en este momento.
    </div>
<?php else: ?>
    <?php foreach ($alertas as $a): ?>
    <div class="alert-item-card <?= $a['leida'] ? 'leida' : '' ?>">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-start gap-3">
                <div class="fs-4 text-<?= $a['tipo'] === 'inasistencia' ? 'danger' : 'warning' ?> mt-1">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold text-dark"><?= e($a['estudiante_nombre'] . ' ' . $a['estudiante_apellido']) ?></span>
                        <?php if (!empty($a['curso_codigo'])): ?>
                            <span class="badge bg-secondary-subtle text-secondary"><?= e($a['curso_codigo']) ?></span>
                        <?php endif; ?>
                        <?php if (!$a['leida']): ?>
                            <span class="badge bg-danger-subtle text-danger">Pendiente</span>
                        <?php else: ?>
                            <span class="badge bg-light text-muted border">Leída</span>
                        <?php endif; ?>
                    </div>
                    <div class="text-dark small mb-1"><?= e($a['mensaje']) ?></div>
                    <small class="text-muted"><i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small>
                </div>
            </div>
            <?php if (!$a['leida']): ?>
            <form method="POST" action="<?= url('/alertas.php?action=marcar_leida') ?>">
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">
                <input type="hidden" name="id" value="<?= $a['id'] ?>">
                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Marcar como leída">
                    <i class="bi bi-check2 me-1"></i>Marcar leída
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

