---
name: asistepro-development
description: Desarrollar, probar y mantener AsistePro, un sistema web de control de asistencia estudiantil con PHP, MySQL y XAMPP.
---

# AsistePro — Habilidad de desarrollo

## Propósito
Actúa como ingeniero de software Full Stack responsable de desarrollar un prototipo funcional de **AsistePro**, una startup académica de control de asistencia para colegios, institutos, academias y centros de capacitación.

## Contexto del proyecto
- Institución académica: Instituto Superior Tecnológico Alberto Enríquez (ISTAE).
- Asignatura: Emprendimiento.
- Integrantes: Darwin Cabezas y Anny Canche.
- Problema: el registro manual puede ocasionar errores, pérdida de información, retrasos en reportes y detección tardía de inasistencias.
- Propuesta de valor: registro rápido, reportes claros, alertas de inasistencia y costos accesibles.
- El prototipo utiliza **datos ficticios**.

## Tecnologías
- PHP 8, programación orientada a objetos y PDO.
- MySQL y phpMyAdmin.
- Apache y XAMPP para ejecución local.
- HTML5, CSS3, JavaScript y Bootstrap 5.
- Chart.js para estadísticas cuando sea necesario.
- Arquitectura MVC simple, sin frameworks pesados.

## Alcance funcional mínimo (según la propuesta)
1. Registrar asistencia estudiantil.
2. Consultar el historial de asistencia.
3. Identificar y mostrar alertas de inasistencia.
4. Generar reportes de asistencia.
5. Ofrecer una interfaz sencilla y accesible.

## Ampliaciones técnicas propuestas
Estas características amplían el prototipo y deben implementarse por fases, sin presentarlas como requisitos originales del informe:
- Autenticación con roles administrador, docente y estudiante.
- CRUD de estudiantes, docentes, cursos y matrículas.
- Dashboard con indicadores y gráficos.
- Estados de asistencia: presente, ausente, atrasado y justificado.
- Reportes filtrados por fechas y exportación PDF/Excel.
- Configuración institucional y registro de acciones críticas.
- QR y notificaciones externas quedan como mejoras futuras opcionales.

## Reglas de trabajo en Google Antigravity
1. Trabaja sobre el repositorio GitHub **ya clonado y abierto**; no lo clones ni lo reinicialices.
2. Antes de editar, revisa los archivos existentes y lee `SOURCE.md` si está disponible.
3. No borres ni sobrescribas trabajo existente sin explicar la necesidad y obtener autorización.
4. Desarrolla una sola fase por vez; espera confirmación para continuar.
5. Crea archivos reales y código ejecutable, no solo maquetas.
6. Explica brevemente los cambios, archivos afectados y pasos para probarlos.
7. No hagas `git commit` ni `git push` sin autorización expresa.
8. No publiques credenciales, archivos `.env` ni datos personales.
9. Mantén compatibilidad con XAMPP y documenta cualquier dependencia adicional.

## Estructura recomendada
```text
AsistePro/
├── .agents/skills/asistepro-development/SKILL.md
├── app/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── middleware/
├── config/
├── database/asistepro_db.sql
├── public/
│   ├── css/
│   ├── js/
│   └── index.php
├── docs/
├── SOURCE.md
├── README.md
└── .gitignore
```
Adapta la estructura si el repositorio ya contiene una organización válida.

## Diseño de datos
Diseña las entidades necesarias para usuarios, roles, estudiantes, docentes, cursos, matrículas, asistencias y alertas. Usa claves primarias, foráneas, índices y restricciones para evitar registros duplicados de asistencia por estudiante, curso y fecha cuando corresponda.

## Seguridad obligatoria
- Consultas preparadas con PDO.
- `password_hash()` y `password_verify()`.
- Validación en servidor y escape de salida HTML.
- Protección CSRF para acciones de cambio.
- Sesiones seguras y autorización por rol en backend.
- Manejo de errores sin exponer detalles sensibles.
- Datos ficticios en las demostraciones.

## Fases de desarrollo
1. Inspección del repositorio y preparación de instrucciones.
2. Estructura MVC y configuración local.
3. Esquema MySQL e importación en phpMyAdmin.
4. Autenticación y autorización.
5. CRUD de estudiantes, docentes, cursos y matrículas.
6. Registro e historial de asistencia.
7. Alertas y dashboard.
8. Reportes y exportaciones.
9. Pruebas, correcciones y documentación.

## Criterios de aceptación
- La aplicación se inicia desde Apache/XAMPP.
- La conexión a MySQL funciona sin credenciales incrustadas en el código público.
- El registro de asistencia se almacena y consulta correctamente.
- No se duplican registros por error.
- Los permisos impiden accesos no autorizados.
- Los reportes muestran información coherente con la base de datos.
- Existe un `README.md` con instalación y pruebas.

## Primera instrucción al agente
«Analiza el repositorio AsistePro que ya está abierto. Lee este SKILL.md y SOURCE.md si existe. Indica qué archivos hay, qué falta y propón la Fase 1. No cambies archivos hasta que te autorice».
