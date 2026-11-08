<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte_Asistencia_<?= e($cursoActual['codigo'] ?? 'ISTAE') ?>_<?= date('Ymd') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            background: #ffffff;
            padding: 30px;
            font-size: 13px;
        }
        .header-reporte {
            border-bottom: 2px solid #0f2440;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .table th {
            background-color: #0f2440 !important;
            color: #ffffff !important;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
            padding: 8px 10px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table td {
            padding: 7px 10px;
            vertical-align: middle;
            border-color: #e2e8f0;
        }
        .progress-print {
            height: 10px;
            border-radius: 4px;
            background-color: #e2e8f0;
            overflow: hidden;
            display: inline-block;
            width: 70px;
            vertical-align: middle;
        }
        .progress-bar-print {
            height: 100%;
        }
        .bg-ok { background-color: #16a34a !important; }
        .bg-warn { background-color: #d97706 !important; }
        .bg-danger { background-color: #dc2626 !important; }
        @media print {
            body { padding: 0; }
            .btn-imprimir { display: none; }
        }
    </style>
</head>
<body>

<div class="d-flex justify-content-between align-items-center mb-3 btn-imprimir">
    <a href="<?= url('/reportes.php') ?>" class="btn btn-outline-secondary btn-sm">&larr; Volver a Reportes</a>
    <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ Imprimir / Guardar como PDF</button>
</div>

<div class="header-reporte text-center">
    <h4 class="fw-bold mb-1" style="color: #0f2440;">INSTITUTO SUPERIOR TECNOLÓGICO ALBERTO ENRÍQUEZ</h4>
    <h6 class="text-secondary mb-2">AsistePro — Sistema de Control y Seguimiento de Asistencia Estudiantil</h6>
    <div class="d-flex justify-content-center gap-4 small mt-2">
        <span><strong>Curso:</strong> <?= e($cursoActual['nombre'] ?? '') ?> (<?= e($cursoActual['codigo'] ?? '') ?>)</span>
        <span><strong>Docente:</strong> <?= e($cursoActual['docente_nombre'] ?? 'Sin asignar') ?></span>
        <span><strong>Periodo:</strong> <?= date('d/m/Y', strtotime($fechaInicio)) ?> al <?= date('d/m/Y', strtotime($fechaFin)) ?></span>
        <span><strong>Generado:</strong> <?= date('d/m/Y H:i') ?></span>
    </div>
</div>

<table class="table table-bordered table-striped align-middle mb-4">
    <thead>
        <tr>
            <th style="width: 35px;" class="text-center">#</th>
            <th>Estudiante</th>
            <th>Correo Electrónico</th>
            <th class="text-center">Total Clases</th>
            <th class="text-center">Presentes</th>
            <th class="text-center">Atrasos</th>
            <th class="text-center">Ausencias</th>
            <th class="text-center">Justif.</th>
            <th class="text-center" style="width: 130px;">% Asistencia</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($datosReporte)): ?>
            <tr>
                <td colspan="9" class="text-center py-4 text-muted">No existen registros en el rango seleccionado.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($datosReporte as $i => $r): ?>
            <?php 
                $pct = (float)($r['porcentaje_asistencia'] ?? 0);
                $color = $pct >= 80 ? 'bg-ok' : ($pct >= 70 ? 'bg-warn' : 'bg-danger');
            ?>
            <tr>
                <td class="text-center text-muted"><?= $i + 1 ?></td>
                <td class="fw-bold"><?= e($r['apellido'] . ' ' . $r['nombre']) ?></td>
                <td><small><?= e($r['email']) ?></small></td>
                <td class="text-center"><?= $r['total_clases'] ?></td>
                <td class="text-center text-success fw-bold"><?= $r['presentes'] ?></td>
                <td class="text-center text-warning"><?= $r['atrasados'] ?></td>
                <td class="text-center text-danger fw-bold"><?= $r['ausentes'] ?></td>
                <td class="text-center text-info"><?= $r['justificados'] ?></td>
                <td class="text-center">
                    <span class="progress-print me-1">
                        <span class="progress-bar-print <?= $color ?>" style="width: <?= $pct ?>%; display: block;"></span>
                    </span>
                    <strong><?= $pct ?>%</strong>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<div class="row text-center mt-5 pt-4">
    <div class="col-6">
        <div class="border-top border-dark mx-auto pt-2" style="width: 250px;">
            <strong>Docente Responsable</strong>
            <br><small><?= e($cursoActual['docente_nombre'] ?? 'Firma') ?></small>
        </div>
    </div>
    <div class="col-6">
        <div class="border-top border-dark mx-auto pt-2" style="width: 250px;">
            <strong>Coordinación Académica</strong>
            <br><small>ISTAE 2026</small>
        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', function() {
    const params = new URLSearchParams(window.location.search);
    if (params.get('print') === '1') {
        setTimeout(() => window.print(), 500);
    }
});
</script>

</body>
</html>

