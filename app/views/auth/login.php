<?php
/**
 * AsistePro - Vista de Inicio de Sesión
 */
$titulo = 'Iniciar Sesión';
ob_start();
?>

<div class="login-wrapper">
    <div class="login-card-container">
        <!-- Logo / Marca -->
        <div class="login-header text-center">
            <div class="login-brand-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <h1 class="login-brand-title">AsistePro</h1>
            <p class="login-brand-subtitle">Sistema Integral de Control de Asistencia</p>
        </div>

        <div class="login-card">
            <div class="login-card-header">
                <h3>Iniciar Sesión</h3>
                <p>Ingresa tus credenciales para acceder al sistema</p>
            </div>

            <?php $mensaje = getMensaje(); ?>
            <?php if ($mensaje): ?>
            <div class="alert alert-<?= e($mensaje['tipo']) ?> alert-dismissible fade show modern-alert" role="alert">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= e($mensaje['texto']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= url('/login.php') ?>" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(generarTokenCSRF()) ?>">

                <div class="form-group-modern mb-3">
                    <label for="email" class="form-label-modern">
                        Correo Electrónico
                    </label>
                    <div class="input-modern-group">
                        <span class="input-modern-icon"><i class="bi bi-envelope"></i></span>
                        <input 
                            type="email" 
                            class="form-control form-control-modern" 
                            id="email" 
                            name="email" 
                            placeholder="usuario@asistepro.local"
                            value="<?= e($_POST['email'] ?? '') ?>"
                            required 
                            autofocus>
                    </div>
                </div>

                <div class="form-group-modern mb-4">
                    <label for="password" class="form-label-modern">
                        Contraseña
                    </label>
                    <div class="input-modern-group">
                        <span class="input-modern-icon"><i class="bi bi-lock"></i></span>
                        <input 
                            type="password" 
                            class="form-control form-control-modern pe-5" 
                            id="password" 
                            name="password" 
                            placeholder="Ingrese su contraseña"
                            required>
                        <button class="btn btn-toggle-eye" type="button" id="togglePassword" title="Mostrar/ocultar contraseña">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-login-submit w-100">
                    <span>Acceder a la Plataforma</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            <!-- Credenciales de prueba estilizadas -->
            <div class="login-demo-box mt-4">
                <div class="demo-box-header">
                    <i class="bi bi-key-fill text-brand"></i>
                    <span>Credenciales de Demostración</span>
                </div>
                <div class="demo-credentials-grid">
                    <div class="demo-badge">
                        <span class="badge-role badge-admin">Admin</span>
                        <code>admin@asistepro.local</code>
                    </div>
                    <div class="demo-badge">
                        <span class="badge-role badge-teacher">Docente</span>
                        <code>carlos.mendoza@asistepro.local</code>
                    </div>
                    <div class="demo-badge">
                        <span class="badge-role badge-student">Estudiante</span>
                        <code>juan.perez@asistepro.local</code>
                    </div>
                </div>
                <div class="demo-pass text-center mt-2">
                    <small>Contraseña general: <strong>Demo1234!</strong></small>
                </div>
            </div>
        </div>

        <!-- Footer institucional -->
        <div class="login-footer text-center mt-4">
            <p class="institution-note">
                <i class="bi bi-building me-1"></i>
                <strong>ISTAE</strong> &bull; Instituto Superior Tecnológico Alberto Enríquez
            </p>
            <p class="copyright-note">&copy; <?= date('Y') ?> AsistePro. Todos los derechos reservados.</p>
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
    <!-- Fuentes Google: Plus Jakarta Sans e Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('/css/style.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body class="login-page-body">
    <?= $contenido ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

