<?php
/**
 * AsistePro - Vista de Perfil de Usuario
 */
$titulo = 'Mi Perfil';
$pagina_actual = 'perfil';
ob_start();
?>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card-custom text-center p-4">
            <div class="mb-3">
                <i class="bi bi-person-circle text-primary" style="font-size: 5rem;"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1"><?= e($usuario['nombre'] . ' ' . $usuario['apellido']) ?></h5>
            <p class="text-muted mb-2"><?= e($usuario['email']) ?></p>
            <span class="badge bg-primary-subtle text-primary mb-3">
                <i class="bi bi-shield-check me-1"></i><?= e($usuario['rol_nombre']) ?>
            </span>
            <hr>
            <div class="text-start small text-muted">
                <p class="mb-1"><strong>ID Usuario:</strong> #<?= $usuario['id'] ?></p>
                <p class="mb-1"><strong>Registrado el:</strong> <?= date('d/m/Y H:i', strtotime($usuario['created_at'])) ?></p>
                <p class="mb-0"><strong>Último Acceso:</strong> <?= $usuario['ultimo_acceso'] ? date('d/m/Y H:i', strtotime($usuario['ultimo_acceso'])) : 'Primer acceso' ?></p>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-bold text-primary">
                    <i class="bi bi-key me-2"></i>Seguridad y Cambio de Contraseña
                </h6>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="<?= url('/perfil.php?action=cambiar_password') ?>">
                    <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Contraseña Actual *</label>
                        <input type="password" name="password_actual" class="form-control" required placeholder="Tu contraseña actual">
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nueva Contraseña *</label>
                            <input type="password" name="password_nueva" class="form-control" minlength="6" required placeholder="Mínimo 6 caracteres">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Confirmar Nueva Contraseña *</label>
                            <input type="password" name="password_confirmar" class="form-control" minlength="6" required placeholder="Repite la nueva contraseña">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-shield-lock me-1"></i>Actualizar Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

