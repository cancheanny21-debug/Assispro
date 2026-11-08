# 📋 AsistePro

**Sistema Web de Control y Seguimiento de Asistencia Estudiantil**

AsistePro es un prototipo de startup tecnológica desarrollado para la asignatura de Emprendimiento del Instituto Superior Tecnológico Alberto Enríquez (ISTAE). Permite registrar, consultar y monitorear la asistencia de estudiantes de forma rápida, con alertas de inasistencia y reportes claros.

---

## 🚀 Características

- ✅ Autenticación con roles (Administrador, Docente, Estudiante)
- ✅ Dashboard con estadísticas y gráficos interactivos
- ✅ Gestión de estudiantes, docentes y cursos (CRUD)
- ✅ Registro de asistencia por curso y fecha
- ✅ Estados: Presente, Ausente, Atrasado, Justificado
- ✅ Alertas automáticas de inasistencia
- ✅ Reportes filtrados por fecha
- ✅ Diseño responsivo (PC, tablet, celular)
- ✅ Seguridad: CSRF, XSS, contraseñas hasheadas, sesiones seguras

## 🛠️ Tecnologías

| Componente | Tecnología |
|-----------|-----------|
| Backend | PHP 8 |
| Base de datos | MySQL 8 |
| Servidor | Apache (XAMPP) |
| Frontend | HTML5, CSS3, Bootstrap 5 |
| Gráficos | Chart.js 4 |
| Conexión BD | PDO |

## 📦 Requisitos Previos

- [XAMPP](https://www.apachefriends.org/) instalado (PHP 8+, MySQL, Apache)
- Navegador web moderno (Chrome, Firefox, Edge)

## ⚙️ Instalación

### 1. Clonar o copiar el proyecto

Copiar la carpeta `Assispro` dentro de `C:\xampp\htdocs\` o crear un enlace simbólico:

```bash
# Opción A: Si el proyecto está en otra ubicación, crear enlace simbólico
mklink /D C:\xampp\htdocs\Assispro "C:\Users\TU_USUARIO\ruta\al\proyecto\Assispro"

# Opción B: Clonar directamente en htdocs
cd C:\xampp\htdocs
git clone https://github.com/TU_USUARIO/Assispro.git
```

### 2. Iniciar XAMPP

1. Abrir **XAMPP Control Panel**
2. Iniciar **Apache** y **MySQL**

### 3. Importar la Base de Datos

1. Abrir phpMyAdmin: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Ir a la pestaña **"Importar"**
3. Seleccionar el archivo `database/asistepro_db.sql`
4. Hacer clic en **"Ejecutar"**

### 4. Verificar la Configuración

Revisar el archivo `config/database.php` y ajustar si es necesario:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'asistepro_db');
define('DB_USER', 'root');
define('DB_PASS', ''); // Contraseña por defecto de XAMPP
```

### 5. Acceder al Sistema

Abrir en el navegador: **[http://localhost/Assispro/public/](http://localhost/Assispro/public/)**

## 🔐 Cuentas de Demostración

| Rol | Correo Electrónico | Contraseña |
|-----|-------------------|-----------|
| Administrador | admin@asistepro.local | Demo1234! |
| Docente | carlos.mendoza@asistepro.local | Demo1234! |
| Docente | maria.lopez@asistepro.local | Demo1234! |
| Estudiante | juan.perez@asistepro.local | Demo1234! |

> ⚠️ Estas son credenciales ficticias solo para demostración. No usar en producción.

## 📁 Estructura del Proyecto

```
Assispro/
├── app/
│   ├── controllers/     → Lógica de negocio
│   ├── models/          → Acceso a datos (PDO)
│   ├── views/           → Plantillas HTML/PHP
│   └── middleware/      → Autenticación y permisos
├── config/
│   ├── app.php          → Configuración general
│   ├── database.php     → Conexión a MySQL
│   └── init.php         → Inicialización global
├── database/
│   └── asistepro_db.sql → Script SQL para phpMyAdmin
├── public/
│   ├── css/             → Estilos
│   ├── js/              → JavaScript
│   ├── index.php        → Punto de entrada
│   └── login.php        → Inicio de sesión
├── docs/                → Documentación
├── .gitignore
├── README.md
├── SKILL.md
└── SOURCE.md
```

## 👥 Equipo

- **Darwin Cabezas** — CEO, Responsabilidad Financiera
- **Anny Canche** — CTO, Marketing y Operaciones

## 📄 Licencia

Proyecto académico — ISTAE 2026
