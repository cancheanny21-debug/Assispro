<?php
/**
 * AsistePro - Vista de Inicio de Sesión
 */
$titulo = 'Iniciar Sesión';
ob_start();
?>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-logo">
            <i class="bi bi-clipboard-check"></i>
            <h2>AsistePro</h2>
            <p>Sistema de Control de Asistencia Estudiantil</p>
        </div>

        <?php $mensaje = getMensaje(); ?>
        <?php if ($mensaje): ?>
        <div class="alert alert-<?= e($mensaje['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= e($mensaje['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('/login.php') ?>" novalidate>
            <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">

            <div class="mb-3">
                <label for="email" class="form-label">
                    <i class="bi bi-envelope me-1"></i>Correo Electrónico
                </label>
                <input 
                    type="email" 
                    class="form-control form-control-lg" 
                    id="email" 
                    name="email" 
                    placeholder="usuario@asistepro.local"
                    value="<?= e($_POST['email'] ?? '') ?>"
                    required 
                    autofocus>
            </div>

            <div class="mb-4">
                <label for="password" class="form-label">
                    <i class="bi bi-lock me-1"></i>Contraseña
                </label>
                <div class="input-group">
                    <input 
                        type="password" 
                        class="form-control form-control-lg" 
                        id="password" 
                        name="password" 
                        placeholder="Ingrese su contraseña"
                        required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="bi bi-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted">
                <i class="bi bi-info-circle me-1"></i>
                ISTAE — Instituto Superior Tecnológico Alberto Enríquez
            </small>
        </div>

        <!-- Credenciales de demostración -->
        <div class="mt-3 p-3 rounded" style="background-color: #f0f4f8; border: 1px dashed #cbd5e0;">
            <small class="text-muted d-block mb-2"><strong>Cuentas de demostración:</strong></small>
            <small class="text-muted d-block"><strong>Admin:</strong> admin@asistepro.local</small>
            <small class="text-muted d-block"><strong>Docente:</strong> carlos.mendoza@asistepro.local</small>
            <small class="text-muted d-block"><strong>Estudiante:</strong> juan.perez@asistepro.local</small>
            <small class="text-muted d-block mt-1"><strong>Contraseña:</strong> Demo1234!</small>
        </div>
    </div>
</div>

<script>
// Toggle para mostrar/ocultar contraseña
document.getElementById('togglePassword')?.addEventListener('click', function() {
    const input = document.getElementById('password');
    const icon  = this.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
});
</script>

<?php
$contenido = ob_get_clean();
// No usamos layout con sidebar para el login; renderizamos directo
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo) ?> — AsistePro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('/css/style.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body>
    <?= $contenido ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

