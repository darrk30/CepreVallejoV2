# CEPRE Vallejo — Sistema de Gestión Académica

Sistema web para la gestión integral de un centro de estudios preuniversitarios: matrícula y pagos de alumnos, gestión de ciclos académicos y cursos, aula virtual con contenido y exámenes por curso, exámenes ordinarios de admisión con corrección automática, y un sitio público informativo (banners, anuncios, videoteca, biblioteca, podcasts, convenios).

Construido con **Laravel 13** + **Filament 5** (paneles de administración) y **Livewire 4** (sitio público y páginas interactivas).

---

## Tabla de contenidos

- [Stack tecnológico](#stack-tecnológico)
- [Estructura del sistema](#estructura-del-sistema)
- [Funcionalidades](#funcionalidades)
  - [Panel Administrador](#panel-administrador-admin)
  - [Panel Profesor](#panel-profesor-profesor)
  - [Panel Alumno](#panel-alumno-alumno)
  - [Sitio público](#sitio-público)
  - [Transversal](#transversal-a-todo-el-sistema)
- [Requisitos previos](#requisitos-previos)
- [Instalación](#instalación)
- [Configuración del .env](#configuración-del-env)
- [Comandos útiles del día a día](#comandos-útiles-del-día-a-día)
- [Solución de problemas comunes](#solución-de-problemas-comunes)

---

## Stack tecnológico

| Componente | Tecnología |
|---|---|
| Backend | PHP 8.3+, Laravel 13 |
| Panel de administración | Filament 5 |
| Interactividad / SPA | Livewire 4 (incluye Alpine.js) |
| Frontend build | Vite 8 + Tailwind CSS 4 |
| Base de datos | MySQL (vía XAMPP u otro gestor) |
| Roles y permisos | Spatie Laravel-Permission |
| Importación de Excel | Maatwebsite/Excel (claves de examen ordinario) |
| Optimización de imágenes | danihidayatx/image-optimizer |

## Estructura del sistema

La aplicación expone **tres paneles Filament** (cada uno con su propio login y permisos) más un **sitio público**:

| Panel | Ruta | Para quién |
|---|---|---|
| Administrador | `/admin` | Personal administrativo |
| Profesor | `/profesor` | Docentes |
| Alumno | `/alumno` | Estudiantes |
| Sitio público | `/` | Visitantes / postulantes |

El acceso a cada panel se controla por permisos (`access_admin_panel`, `access_teacher_panel`, `access_student_panel`) asignados mediante roles (Administrador, Profesor, Alumno), no por el guard de autenticación — un mismo usuario solo ve el panel para el que tiene permiso.

## Funcionalidades

### Panel Administrador (`/admin`)

- **Dashboard** con widgets de cuenta y anuncios del sistema.
- **Ciclos académicos**: creación de ciclos, vinculación/desvinculación de cursos por ciclo y turno, asignación de docentes por curso (con *soft delete*: al desvincular un curso o un docente, su contenido —secciones, temas, exámenes, intentos de alumnos— no se pierde, y se restaura automáticamente si se vuelve a vincular el mismo curso/docente/turno/ciclo).
- **Cursos**: catálogo de cursos del ciclo.
- **Contenido de curso (vista admin)**: gestión de secciones, temas y material educativo por curso.
- **Matrícula**: registro de inscripciones de alumnos a un ciclo/carrera, con control de pagos (cuotas, vencimientos, saldo) y recálculo automático de deuda al registrar/editar/eliminar un pago.
- **Estudiantes** y **Docentes**: fichas de datos personales y de contacto; pagos a docentes por ciclo académico (con comprobante).
- **Usuarios y Roles**: gestión de cuentas del sistema y de roles/permisos granulares (Spatie Permission) por cada acción del sistema.
- **Exámenes** (por curso/tema): gestión de exámenes que crean los profesores dentro del contenido del curso.
- **Exámenes Ordinarios** (examen de admisión):
  - Registro del examen (título, PDF, duración, estado activo/inactivo).
  - Cartilla de 100+ preguntas con clave de respuesta, asignatura y bloque, editable manualmente o importando un Excel/CSV.
  - Cálculo de puntaje por área/carrera y bloque al momento de crear la cartilla.
- **Carreras**: catálogo de carreras/especialidades a las que puede postular un alumno, con su área académica y puntaje mínimo aprobatorio.
- **Configuración del sitio público**: Anuncios, Banners, Convenios, Videos, Podcasts, Autores, Libros (biblioteca digital), Especialidades/Categorías.
- **Configuración de la institución**: nombre, RUC, logo, datos de contacto, redes sociales, misión/visión (se muestran en el sitio público).
- **Sesiones activas / historial de inicio de sesión**: auditoría de accesos por usuario (IP, dispositivo, fecha).

### Panel Profesor (`/profesor`)

- **Mi Aula Virtual**: listado de cursos asignados al docente, agrupados por ciclo académico.
- **Gestión de contenido del curso**: creación de secciones y temas, subida de material (documentos/videos), reordenamiento.
- **Exámenes por tema**: creación de exámenes con preguntas de opción múltiple, soporte de imágenes en pregunta y opciones (con ajuste automático de tamaño/columnas según haya o no imágenes y largo del texto), previsualización del examen tal como lo verá el alumno.
- **Resultados de examen**: revisión de los intentos y calificaciones de los alumnos por examen.
- **Mis pagos**: historial de pagos recibidos por ciclo académico.
- Navegación tipo SPA (sin recargar la página completa) entre aula, contenido y exámenes.

### Panel Alumno (`/alumno`)

- **Mi Aula Virtual**: cursos matriculados por ciclo, acceso al contenido y material del curso.
- **Exámenes del curso**: rendición de los exámenes creados por el profesor dentro de cada tema.
- **Exámenes Ordinarios** (simulacro de examen de admisión):
  - Selección de carrera antes de iniciar.
  - Rendición con temporizador, visor de PDF del examen y hoja de respuestas (A–E) tipo cartilla física.
  - Envío automático al agotarse el tiempo.
  - Calificación automática por área/carrera, bloque y asignatura (con descuento por respuesta incorrecta y sin descuento por pregunta en blanco).
  - Ranking de mejores puntajes por examen y por carrera.
  - **Mis Intentos**: historial de intentos rendidos, con detalle pregunta por pregunta (respuesta marcada vs. respuesta correcta). Al ver el detalle de un intento se advierte al alumno (mediante un modal) que, dado que se revelan las respuestas correctas, **ya no podrá volver a rendir ese examen**; la restricción queda registrada a nivel de base de datos.
- **Videoteca**: reproductor de clases en video, con videos relacionados.
- **Biblioteca**: catálogo de libros digitales.
- **Podcasts**: reproductor de podcasts con streaming propio y páginas por autor.
- **Favoritos**: marcar libros/videos como favoritos.

### Sitio público

- **Página de inicio**: presentación de la institución, banners, anuncios, convenios, cursos destacados.
- **Detalle de curso**: información pública de cada curso ofertado.
- Página de **cuenta suspendida** para usuarios inhabilitados que intentan iniciar sesión.

### Transversal a todo el sistema

- Autenticación y autorización por roles/permisos (Spatie Laravel-Permission) integrada a nivel de panel, recurso y acción individual.
- Registro de anuncios visibles como widget en el dashboard de cada panel.
- Navegación SPA con Livewire (`wire:navigate`) en los flujos principales de Profesor y Alumno.
- Carga de imágenes/archivos optimizada (compresión automática de imágenes al subirlas).

## Requisitos previos

Antes de instalar, asegúrate de tener:

- **XAMPP** (o equivalente) con:
  - **PHP 8.3 o superior**, con las extensiones: `openssl`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`, `zip` (todas vienen habilitadas por defecto en XAMPP; verifica `zip` y `gd` en `php.ini` si algo falla).
  - **MySQL / MariaDB** en ejecución (el gestor de base de datos puede ser phpMyAdmin, HeidiSQL, DBeaver, etc. — cualquiera funciona, solo se necesita crear la base de datos).
  - Apache no es obligatorio para desarrollo local: se puede usar el servidor embebido de Laravel (`php artisan serve`).
- **Composer** (última versión estable).
- **Node.js 20 LTS o superior** y **npm** (para compilar los assets con Vite/Tailwind).
- **Git** (para clonar el repositorio).

## Instalación

1. **Crear la base de datos** en tu gestor de MySQL de preferencia (por ejemplo, desde phpMyAdmin de XAMPP):

   ```sql
   CREATE DATABASE ceprevallejov2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

   > El nombre puede ser el que prefieras, solo debe coincidir con lo que pongas en el `.env` (ver más abajo).

2. **Clonar el repositorio** dentro de la carpeta `htdocs` de XAMPP:

   ```bash
   cd C:/xampp/htdocs
   git clone <URL-del-repositorio> CepreVallejoV2
   cd CepreVallejoV2
   ```

3. **Instalar las dependencias de PHP:**

   ```bash
   composer install
   ```

4. **Instalar las dependencias de Node:**

   ```bash
   npm install
   ```

5. **Crear el archivo de entorno** a partir del ejemplo y configurarlo (ver [sección siguiente](#configuración-del-env)):

   ```bash
   cp .env.example .env
   ```

   > En Windows (PowerShell) usa `Copy-Item .env.example .env` si `cp` no está disponible.

6. **Generar la clave de la aplicación:**

   ```bash
   php artisan key:generate
   ```

7. **Ejecutar las migraciones** (crea todas las tablas del sistema):

   ```bash
   php artisan migrate
   ```

8. **Ejecutar los seeders** (crea los permisos, los roles Administrador/Profesor/Alumno, un usuario administrador inicial y los turnos por defecto):

   ```bash
   php artisan db:seed
   ```

   > Revisa `database/seeders/DatabaseSeeder.php` para ver/editar las credenciales del usuario administrador que se crea por defecto, y cámbialas después de tu primer ingreso.

9. **Crear el enlace simbólico de almacenamiento** (necesario para que se vean las imágenes, PDFs y demás archivos subidos desde el sistema — logos, banners, material de curso, exámenes en PDF, etc.):

   ```bash
   php artisan storage:link
   ```

10. **Compilar los assets del frontend:**

    ```bash
    npm run build
    ```

    Para desarrollo (recompila en caliente al guardar cambios), usa en su lugar:

    ```bash
    npm run dev
    ```

11. **Levantar la aplicación:**

    ```bash
    php artisan serve
    ```

    Y con `npm run dev` corriendo en otra terminal en paralelo. Si prefieres un solo comando que levante servidor + cola + Vite al mismo tiempo:

    ```bash
    composer run dev
    ```

12. Abre el navegador en `http://localhost:8000` (sitio público) o `http://localhost:8000/admin` (panel administrador) e inicia sesión con el usuario creado por el seeder.

## Configuración del `.env`

Las variables más importantes a revisar tras copiar `.env.example`:

```env
APP_NAME="CEPRE Vallejo"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ceprevallejov2
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

- `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` deben coincidir con la base de datos creada en el paso 1 (en una instalación típica de XAMPP, el usuario es `root` sin contraseña).
- `FILESYSTEM_DISK=public` es requerido para que las subidas de archivos (imágenes de curso, banners, PDFs de exámenes, comprobantes de pago, etc.) se guarden donde el `storage:link` del paso 9 las expone públicamente.
- `SESSION_DRIVER`, `CACHE_STORE` y `QUEUE_CONNECTION` usan la base de datos (`database`) por defecto; las tablas necesarias ya se crean con `php artisan migrate`, no requieren configuración adicional para desarrollo local.
- Si vas a probar el envío de correos, configura las variables `MAIL_*`; por defecto quedan en `log` (los correos se escriben en `storage/logs/laravel.log` en vez de enviarse).

## Comandos útiles del día a día

```bash
# Limpiar cachés de configuración/rutas/vistas (útil tras cambiar código de providers o .env)
php artisan optimize:clear

# Ver el estado de las migraciones
php artisan migrate:status

# Revertir la última tanda de migraciones
php artisan migrate:rollback

# Volver a crear la base de datos desde cero y sembrar los datos base
php artisan migrate:fresh --seed

# Abrir una consola interactiva de Laravel (Tinker)
php artisan tinker

# Levantar servidor + cola + Vite en un solo comando (definido en composer.json)
composer run dev
```

## Solución de problemas comunes

- **Los estilos/íconos no cargan o la página se ve "sin diseño"**: corre `npm run build` (o mantén `npm run dev` activo) y luego `php artisan optimize:clear`.
- **Las imágenes o PDFs subidos dan error 404**: falta ejecutar `php artisan storage:link`, o el enlace apunta a una carpeta antigua tras mover el proyecto — bórralo y vuelve a crearlo.
- **Error de conexión a la base de datos**: verifica que MySQL esté corriendo en XAMPP y que las credenciales `DB_*` del `.env` sean correctas.
- **"No application encryption key has been specified"**: falta correr `php artisan key:generate`.
- **No puedo iniciar sesión en ningún panel tras crear un usuario manualmente**: asegúrate de asignarle un rol (`Administrador`, `Profesor` o `Alumno`) mediante Spatie Permission; sin un rol con el permiso `access_*_panel` correspondiente, Filament bloqueará el acceso al panel.
