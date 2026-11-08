<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'AsistePro') ?> — AsistePro</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Estilos personalizados con cache buster -->
    <link href="<?= url('/css/style.css?v=' . time()) ?>" rel="stylesheet">
</head>
<body>

<?php if (estaAutenticado()): ?>
<!-- ======== SIDEBAR ======== -->
<div class="d-flex" id="wrapper">
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4 class="mb-0">
                <i class="bi bi-clipboard-check"></i>
                <span class="sidebar-text">AsistePro</span>
            </h4>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'dashboard' ? 'active' : '' ?>" href="<?= url('/dashboard.php') ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span class="sidebar-text">Dashboard</span>
                    </a>
                </li>

                <?php if (tieneRol(ROL_ADMIN)): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'estudiantes' ? 'active' : '' ?>" href="<?= url('/estudiantes.php') ?>">
                        <i class="bi bi-mortarboard"></i>
                        <span class="sidebar-text">Estudiantes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'docentes' ? 'active' : '' ?>" href="<?= url('/docentes.php') ?>">
                        <i class="bi bi-person-badge"></i>
                        <span class="sidebar-text">Docentes</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'cursos' ? 'active' : '' ?>" href="<?= url('/cursos.php') ?>">
                        <i class="bi bi-book"></i>
                        <span class="sidebar-text">Cursos</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'asistencia' ? 'active' : '' ?>" href="<?= url('/asistencia.php') ?>">
                        <i class="bi bi-calendar-check"></i>
                        <span class="sidebar-text"><?= tieneRol(ROL_ESTUDIANTE) ? 'Mi Asistencia' : 'Asistencia' ?></span>
                    </a>
                </li>

                <?php if (tieneRol(ROL_ADMIN) || tieneRol(ROL_DOCENTE)): ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'reportes' ? 'active' : '' ?>" href="<?= url('/reportes.php') ?>">
                        <i class="bi bi-file-earmark-bar-graph"></i>
                        <span class="sidebar-text">Reportes</span>
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item">
                    <a class="nav-link <?= ($pagina_actual ?? '') === 'alertas' ? 'active' : '' ?>" href="<?= url('/alertas.php') ?>">
                        <i class="bi bi-bell"></i>
                        <span class="sidebar-text">Alertas</span>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="sidebar-footer">
            <small class="sidebar-text text-muted">v<?= APP_VERSION ?></small>
        </div>
    </aside>

    <!-- ======== CONTENIDO PRINCIPAL ======== -->
    <div class="main-content" id="main-content">
        <!-- Barra superior -->
        <header class="topbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-dark me-3 d-md-none" id="btn-toggle-sidebar" type="button">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <h5 class="mb-0 topbar-title"><?= e($titulo ?? 'Dashboard') ?></h5>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary">
                    <i class="bi bi-shield-check"></i>
                    <?= e($_SESSION['rol_nombre'] ?? '') ?>
                </span>
                <div class="dropdown">
                    <button class="btn btn-link text-dark dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= e($_SESSION['usuario_nombre'] ?? 'Usuario') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="<?= url('/perfil.php') ?>"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="<?= url('/logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión</a></li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Mensajes flash -->
        <?php $mensaje = getMensaje(); ?>
        <?php if ($mensaje): ?>
        <div class="alert alert-<?= e($mensaje['tipo']) ?> alert-dismissible fade show mx-4 mt-3" role="alert">
            <?= e($mensaje['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Contenido de la página -->
        <main class="content-area">
            <?= $contenido ?? '' ?>
        </main>
    </div>
</div>

<?php else: ?>
    <!-- Página sin sidebar (login) -->
    <?php $mensaje = getMensaje(); ?>
    <?php if ($mensaje): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($mensaje['tipo']) ?> alert-dismissible fade show" role="alert">
            <?= e($mensaje['texto']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    <?php endif; ?>

    <?= $contenido ?? '' ?>
<?php endif; ?>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Scripts personalizados -->
<script src="<?= url('/js/app.js?v=' . time()) ?>"></script>
</body>
</html>

