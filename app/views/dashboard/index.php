<?php
/**
 * AsistePro - Vista del Dashboard
 * 
 * Muestra estadísticas generales, gráficos y alertas.
 * Este archivo es incluido por public/dashboard.php que le pasa $stats.
 */
$titulo = 'Dashboard';
$pagina_actual = 'dashboard';
ob_start();
?>

<!-- Tarjetas estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value"><?= number_format($stats['total_estudiantes'] ?? 0) ?></div>
                    <div class="stat-label">Estudiantes Activos</div>
                </div>
                <div class="stat-icon bg-blue">
                    <i class="bi bi-mortarboard"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value"><?= number_format($stats['total_docentes'] ?? 0) ?></div>
                    <div class="stat-label">Docentes</div>
                </div>
                <div class="stat-icon bg-green">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value"><?= number_format($stats['total_cursos'] ?? 0) ?></div>
                    <div class="stat-label">Cursos Activos</div>
                </div>
                <div class="stat-icon bg-orange">
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value"><?= $stats['porcentaje_asistencia'] ?? 0 ?>%</div>
                    <div class="stat-label">Asistencia General</div>
                </div>
                <div class="stat-icon bg-green">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Segunda fila: registros y alertas -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value"><?= number_format($stats['total_asistencias'] ?? 0) ?></div>
                    <div class="stat-label">Registros de Asistencia</div>
                </div>
                <div class="stat-icon bg-blue">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-value"><?= number_format($stats['alertas_pendientes'] ?? 0) ?></div>
                    <div class="stat-label">Alertas Pendientes</div>
                </div>
                <div class="stat-icon bg-red">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-4">
    <!-- Gráfico de distribución de estados -->
    <div class="col-lg-5">
        <div class="card-custom">
            <div class="card-header">
                <i class="bi bi-pie-chart me-2"></i>Distribución de Asistencia
            </div>
            <div class="card-body">
                <canvas id="chartDistribucion" height="260"></canvas>
            </div>
        </div>
    </div>

    <!-- Gráfico semanal -->
    <div class="col-lg-7">
        <div class="card-custom">
            <div class="card-header">
                <i class="bi bi-graph-up me-2"></i>Asistencia Últimos 7 Días
            </div>
            <div class="card-body">
                <canvas id="chartSemanal" height="260"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Fila de widgets: Alertas Recientes y Cursos -->
<div class="row g-3 mb-4">
    <!-- Alertas recientes -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-bell-fill text-danger me-2"></i>Alertas Recientes</span>
                <a href="<?= url('/alertas.php') ?>" class="small text-decoration-none">Ver todas &rarr;</a>
            </div>
            <div class="list-group list-group-flush">
                <?php if (empty($stats['ultimas_alertas'])): ?>
                    <div class="p-3 text-center text-muted small">Sin alertas pendientes.</div>
                <?php else: ?>
                    <?php foreach ($stats['ultimas_alertas'] as $alt): ?>
                    <div class="list-group-item p-3 d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill text-<?= $alt['tipo'] === 'inasistencia' ? 'danger' : 'warning' ?> mt-1"></i>
                        <div class="flex-grow-1">
                            <div class="fw-semibold small text-dark">
                                <?= e($alt['estudiante_apellido'] . ' ' . $alt['estudiante_nombre']) ?>
                                <?php if (!empty($alt['curso_codigo'])): ?>
                                    <span class="badge bg-secondary-subtle text-secondary"><?= e($alt['curso_codigo']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small"><?= e($alt['mensaje']) ?></div>
                        </div>
                        <small class="text-muted text-nowrap"><?= date('d/m', strtotime($alt['created_at'])) ?></small>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Cursos activos -->
    <div class="col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-book-fill text-primary me-2"></i>Cursos y Acceso Rápido</span>
                <a href="<?= url('/cursos.php') ?>" class="small text-decoration-none">Administrar cursos &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Curso</th>
                            <th>Docente</th>
                            <th class="text-center">Alumnos</th>
                            <th class="text-end">Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($stats['cursos_resumen'] ?? []) as $cur): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= e($cur['nombre']) ?></div>
                                <span class="badge bg-light text-muted border"><?= e($cur['codigo']) ?></span>
                            </td>
                            <td><?= e($cur['docente_nombre'] ?: 'Sin asignar') ?></td>
                            <td class="text-center fw-semibold"><?= (int)($cur['total_estudiantes'] ?? 0) ?></td>
                            <td class="text-end">
                                <a href="<?= url('/asistencia.php?curso_id=' . $cur['id']) ?>" class="btn btn-sm btn-outline-success" title="Tomar Asistencia">
                                    <i class="bi bi-calendar-check"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Scripts de gráficos -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de distribución
    const distribucion = <?= json_encode($stats['distribucion_estados'] ?? []) ?>;

    if (distribucion.length > 0) {
        const labels = distribucion.map(d => d.estado.charAt(0).toUpperCase() + d.estado.slice(1));
        const valores = distribucion.map(d => parseInt(d.total));
        const colores = {
            'Presente':    '#28a745',
            'Ausente':     '#e53e3e',
            'Atrasado':    '#ed8936',
            'Justificado': '#17a2b8'
        };

        new Chart(document.getElementById('chartDistribucion'), {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: valores,
                    backgroundColor: labels.map(l => colores[l] || '#6c757d'),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Datos semanales
    const semanal = <?= json_encode($stats['asistencia_semanal'] ?? []) ?>;

    if (semanal.length > 0) {
        const fechas = semanal.map(d => d.fecha);

        new Chart(document.getElementById('chartSemanal'), {
            type: 'line',
            data: {
                labels: fechas,
                datasets: [
                    {
                        label: 'Presentes',
                        data: semanal.map(d => parseInt(d.presentes)),
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40,167,69,0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Ausentes',
                        data: semanal.map(d => parseInt(d.ausentes)),
                        borderColor: '#e53e3e',
                        backgroundColor: 'rgba(229,62,62,0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Atrasados',
                        data: semanal.map(d => parseInt(d.atrasados)),
                        borderColor: '#ed8936',
                        backgroundColor: 'rgba(237,137,54,0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }
});
</script>

<?php
$contenido = ob_get_clean();
require_once VIEW_PATH . '/layout/main.php';
?>

