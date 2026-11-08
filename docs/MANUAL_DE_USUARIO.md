# Manual de Usuario — AsistePro

**AsistePro: Sistema de Control y Seguimiento de Asistencia Estudiantil**  
**Instituto Superior Tecnológico Alberto Enríquez (ISTAE)**  
*Emprendimiento — Startup Tecnológica*

---

## 1. Acceso al Sistema

1. Abra su navegador web y diríjase a la URL:
   ```text
   http://localhost/Assispro/public/
   ```
2. Ingrese con una de las cuentas demostrativas:
   - **Administrador:** `admin@asistepro.local` | Clave: `Demo1234!`
   - **Docente:** `carlos.mendoza@asistepro.local` | Clave: `Demo1234!`
   - **Docente 2:** `maria.lopez@asistepro.local` | Clave: `Demo1234!`
   - **Estudiante:** `juan.perez@asistepro.local` | Clave: `Demo1234!`

---

## 2. Módulos y Funcionalidades

### 2.1 Dashboard Administrativo
- Visualización de tarjetas estadísticas: total de estudiantes, docentes, cursos, % global de asistencia y alertas pendientes.
- **Gráfico de Dona (Chart.js):** Distribución de estados (Presente, Atrasado, Ausente, Justificado).
- **Gráfico de Línea:** Evolución diaria de asistencia de los últimos 7 días.

### 2.2 Gestión de Estudiantes y Docentes (CRUD)
- Búsqueda en tiempo real por nombre, apellido o correo.
- Registro con validación de correo único y contraseñas cifradas con bcrypt.
- Edición rápida mediante ventanas modales.
- Desactivación lógica de cuentas con confirmación.

### 2.3 Gestión de Cursos y Paralelos
- Catálogo de asignaturas con código único (ej: `DS-401`, `BD-401`).
- Asignación de docente responsable y periodo académico.
- Conteo de estudiantes matriculados en tiempo real.

### 2.4 Control de Asistencia Diaria
- Selección de curso y fecha del calendario.
- Detección automática de registros preexistentes para evitar duplicados.
- Estados:
  - 🟢 **Presente**
  - 🟡 **Atrasado**
  - 🔴 **Ausente**
  - 🔵 **Justificado**
- Botones de acción rápida: *"Todos Presentes"* / *"Todos Ausentes"*.
- Campo para notas u observaciones individuales por estudiante.

### 2.5 Historial de Asistencia
- Consulta cronológica de todas las clases tomadas en un curso.
- Resumen por fecha de presentes, ausentes, atrasados y justificados.
- Acceso directo para editar cualquier clase anterior.

### 2.6 Centro de Alertas Inteligentes
- Generación automática de alertas cuando un estudiante supera el 20% de inasistencias.
- Distinción entre alertas no leídas (resaltadas en rojo) y leídas.
- Botón *"Marcar todas como leídas"*.

### 2.7 Reportes y Exportación
- Consolidado por estudiante con porcentaje individual de asistencia y barra de progreso de colores.
- **Exportar a Excel (.csv UTF-8):** Descarga inmediata compatible con Microsoft Excel.
- **Imprimir / Guardar PDF:** Vista optimizada para impresión con encabezado oficial del ISTAE y sección para firmas del docente y coordinación.

