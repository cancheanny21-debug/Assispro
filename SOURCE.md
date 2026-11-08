# SOURCE.md — AsistePro

## Identificación del proyecto
- **Nombre:** AsistePro
- **Tipo:** Simulación del lanzamiento de una startup de software para el control de asistencia.
- **Institución:** Instituto Superior Tecnológico Alberto Enríquez (ISTAE).
- **Carrera:** Desarrollo de Software, cuarto nivel, paralelo A.
- **Asignatura:** Emprendimiento.
- **Integrantes:** Darwin Cabezas y Anny Canche.
- **Documento de referencia:** Informe_APA_AsistePro.pdf (5 de noviembre de 2026).

## Problema identificado
En algunas instituciones educativas la asistencia se registra manualmente, lo que puede provocar errores, pérdida de registros y demoras en los reportes. La detección tardía de inasistencias dificulta el seguimiento oportuno.

## Solución propuesta
Sistema web de control de asistencia, sencillo y de bajo costo, que permita:
1. Registrar asistencia estudiantil.
2. Consultar el historial de asistencia.
3. Detectar y mostrar alertas de inasistencia.
4. Generar reportes claros.

## Propuesta de valor
Registro rápido, reportes claros y costos accesibles. Diferenciadores: interfaz sencilla, enfoque económico y alertas de inasistencia.

## Público objetivo
Colegios, institutos, academias y centros de capacitación.

## Tecnologías definidas en el informe
- PHP.
- MySQL.
- XAMPP como entorno de desarrollo local.

## Prototipo y pruebas
El prototipo debe ejecutarse localmente con XAMPP. Utilizar exclusivamente datos ficticios para las demostraciones y pruebas, protegiendo la información personal de estudiantes.

## Modelo de negocio académico
Suscripciones mensuales propuestas de **USD 10, USD 20 y USD 35**. Son estimaciones académicas sujetas a validación; el informe no define funcionalidades concretas por plan.

## Organización del equipo
- **Darwin Cabezas:** CEO y responsabilidad financiera.
- **Anny Canche:** CTO, marketing y operaciones.

## Funciones futuras contempladas
- Registro mediante códigos QR.
- Envío de notificaciones.
- Ampliación a otras instituciones.

Estas posibilidades de crecimiento no deben tratarse como requisitos obligatorios del prototipo inicial.

## Riesgos y validación
- Competencia con hojas de cálculo y plataformas existentes.
- Protección de datos personales.
- Capacidad limitada del equipo de dos personas.
- Necesidad de validar precios y costos con clientes reales.

Se propone validar necesidades con docentes y autoridades, realizar un piloto y ajustar la propuesta a partir de resultados.

## Ampliaciones técnicas propuestas para el desarrollo
Los siguientes puntos **no están especificados como requisitos en el informe original**; se proponen para implementar el prototipo y deben confirmarse durante el desarrollo:
- Arquitectura MVC y conexión PDO.
- Roles de administrador, docente y estudiante.
- Gestión CRUD de estudiantes, docentes y cursos.
- Dashboard con estadísticas.
- Estados de asistencia: presente, ausente, atrasado y justificado.
- Exportación de reportes a PDF y Excel.
- Bootstrap 5 y Chart.js para la interfaz.
- Base de datos denominada `asistepro_db`.

## Instrucciones para el agente de desarrollo
1. Usar este archivo como referencia del alcance y propósito de AsistePro.
2. Consultar `.agents/skills/asistepro-development/SKILL.md` para las reglas de implementación, si existe.
3. No confundir funciones futuras con requisitos ya aprobados.
4. Desarrollar por fases y comprobar cada módulo en XAMPP.
5. No eliminar archivos existentes ni alterar la configuración del repositorio GitHub sin autorización.
6. Documentar las decisiones de diseño que amplíen el alcance del informe.

## Resultado esperado
Un prototipo funcional y demostrable de AsistePro que permita explicar el problema, la solución, la propuesta de valor y su viabilidad como startup académica.
