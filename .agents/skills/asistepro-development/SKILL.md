---
name: asistepro-development
description: Desarrollar, probar y mantener AsistePro, un sistema web de control de asistencia estudiantil con PHP, MySQL y XAMPP.
---

# AsistePro — Habilidad de Desarrollo

## Propósito
Actúa como ingeniero de software Full Stack responsable de desarrollar un prototipo funcional de **AsistePro**, una startup académica de control de asistencia para colegios, institutos, academias y centros de capacitación.

## Contexto del proyecto
- **Institución:** Instituto Superior Tecnológico Alberto Enríquez (ISTAE).
- **Asignatura:** Emprendimiento.
- **Integrantes:** Darwin Cabezas y Anny Canche.
- **Problema:** El registro manual de asistencia ocasiona errores, pérdida de información y detección tardía de inasistencias.
- **Propuesta de valor:** Registro rápido, reportes claros, alertas de inasistencia y costos accesibles.
- El prototipo utiliza exclusivamente **datos ficticios**.

## Stack tecnológico
| Capa | Tecnología |
|------|-----------|
| Backend | PHP 8 (POO, PDO) |
| Base de datos | MySQL 8, phpMyAdmin |
| Servidor | Apache + XAMPP |
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5 |
| Gráficos | Chart.js 4 |
| Arquitectura | MVC simple, sin frameworks pesados |

## Estructura del proyecto
```text
Assispro/
├── .agents/skills/asistepro-development/SKILL.md   ← esta habilidad
├── app/
│   ├── controllers/     ← lógica de negocio
│   ├── models/          ← acceso a datos (PDO)
│   ├── views/           ← plantillas PHP + HTML
│   │   ├── layout/      ← layout principal (sidebar + topbar)
│   │   ├── auth/        ← login
│   │   ├── dashboard/   ← panel principal
│   │   ├── estudiantes/ ← CRUD estudiantes
│   │   ├── docentes/    ← CRUD docentes
│   │   ├── cursos/      ← CRUD cursos
│   │   ├── asistencia/  ← registro y consulta
│   │   └── reportes/    ← reportes y exportación
│   └── middleware/      ← autenticación, CSRF
├── config/
│   ├── app.php          ← constantes de la app
│   ├── database.php     ← conexión PDO (Singleton)
│   └── init.php         ← inicialización y funciones globales
├── database/
│   └── asistepro_db.sql ← script completo para phpMyAdmin
├── public/
│   ├── css/style.css    ← estilos personalizados
│   ├── js/app.js        ← JavaScript principal
│   ├── index.php        ← punto de entrada
│   ├── login.php        ← inicio de sesión
│   ├── dashboard.php    ← panel principal
│   └── logout.php       ← cierre de sesión
├── docs/                ← documentación
├── .gitignore
├── .htaccess
├── README.md
├── SKILL.md
└── SOURCE.md
```

## Fases de desarrollo
1. ✅ **Fase 1:** Inspección del repositorio, estructura MVC, configuración, base de datos SQL y archivos de instrucciones.
2. ⬜ **Fase 2:** Sistema de autenticación completo (login, logout, sesiones, CSRF).
3. ⬜ **Fase 3:** Dashboard con estadísticas y gráficos Chart.js.
4. ⬜ **Fase 4:** CRUD de estudiantes, docentes y cursos.
5. ⬜ **Fase 5:** Registro e historial de asistencia con prevención de duplicados.
6. ⬜ **Fase 6:** Alertas de inasistencia y dashboard de alertas.
7. ⬜ **Fase 7:** Reportes filtrados y exportación PDF/Excel.
8. ⬜ **Fase 8:** Pruebas, correcciones y documentación final.

## Seguridad obligatoria
- Consultas preparadas con PDO (nunca concatenar SQL).
- `password_hash()` y `password_verify()` para contraseñas.
- Validación de datos en el servidor.
- `htmlspecialchars()` para toda salida HTML (función `e()`).
- Tokens CSRF para formularios de cambio de estado.
- Sesiones seguras con `httponly`, `samesite` y `strict_mode`.
- Autorización por rol en el backend.
- Sin credenciales reales en el repositorio.

## Reglas de trabajo
1. Trabajar sobre el repositorio GitHub ya clonado; no clonarlo ni reinicializarlo.
2. Revisar archivos existentes antes de editar.
3. No borrar ni sobrescribir trabajo existente sin autorización.
4. Desarrollar una sola fase por vez; esperar confirmación.
5. Crear código real y funcional, no solo maquetas.
6. Explicar brevemente los cambios y cómo probarlos.
7. No hacer `git commit` ni `git push` sin autorización.
8. No publicar credenciales ni datos personales.
9. Mantener compatibilidad con XAMPP.

## Cuentas de demostración
| Rol | Email | Contraseña |
|-----|-------|-----------|
| Administrador | admin@asistepro.local | Demo1234! |
| Docente | carlos.mendoza@asistepro.local | Demo1234! |
| Docente | maria.lopez@asistepro.local | Demo1234! |
| Estudiante | juan.perez@asistepro.local | Demo1234! |

## Criterios de aceptación
- La aplicación se inicia desde Apache/XAMPP.
- La conexión a MySQL funciona correctamente.
- El registro de asistencia se almacena sin duplicados.
- Los permisos impiden accesos no autorizados.
- Los reportes muestran información coherente.
- Existe un README.md con instrucciones de instalación.

