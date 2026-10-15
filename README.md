# Sistema de Gestión de Biblioteca - API REST & Frontend

Prueba técnica para el cargo de Desarrollador Backend / Fullstack. La aplicación consiste en un sistema de gestión de biblioteca que permite administrar libros, autores y préstamos mediante una API REST protegida con Laravel Sanctum y una interfaz web interactiva construida con Blade y JavaScript vanilla.

---

## 🛠 Requisitos Previos

Asegúrate de contar con el siguiente entorno instalado en tu máquina:

- **PHP**: >= 8.2
- **Composer**: >= 2.x
- **Gestor de Base de Datos**: MySQL (>= 8.0) o PostgreSQL (>= 14.x)
- **Node.js & NPM**: (Opcional, las vistas usan CSS/JS vanilla sin build steps obligatorios)
- **Git**
- **Postman**: Para la importación y ejecución de la suite de pruebas automatizadas

---

## 🚀 Instalación y Puesta en Marcha

Sigue estos pasos para clonar, configurar e iniciar el proyecto localmente:

### 1. Clonar el repositorio
```bash
git clone <URL_DEL_REPOSITORIO>
cd biblioteca
```

### 2. Instalar dependencias de PHP
```bash
composer install
```

### 3. Configuración del entorno (`.env`)
Copia el archivo de ejemplo para crear tu entorno local:

```bash
cp .env.example .env
```

Genera la clave de cifrado de la aplicación:
```bash
php artisan key:generate
```

### 4. Configurar la Base de Datos
Abre el archivo `.env` y ajusta las credenciales de conexión.

#### Configuración para MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=root
DB_PASSWORD=
```

#### Configuración para PostgreSQL (Alternativa según enunciado):
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=biblioteca
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

> **Nota:** Crea previamente la base de datos `biblioteca` en tu motor de base de datos antes de continuar.

### 5. Ejecutar Migraciones y Seeders
Este comando creará todas las tablas requeridas (autores, libros, usuarios, préstamos, tabla pivot `autor_libro` con `orden_autor`, y tokens de Sanctum) y poblará la base de datos con los datos de prueba exigidos (10 autores, 20 libros, 15 usuarios y 10 préstamos con estados variados):

```bash
php artisan migrate:fresh --seed
```

### 6. Iniciar el Servidor de Desarrollo
```bash
php artisan serve
```

La aplicación quedará disponible en:
```text
http://127.0.0.1:8000
```

---

## 🔐 Credenciales de Prueba por Defecto

Para autenticación tanto en Postman como en el panel web:

| Campo | Valor |
|---|---|
| **Email** | `admin@biblioteca.test` |
| **Password** | `password` |

---

## 💻 Acceso al Frontend (Blade + JS)

Ingresa desde tu navegador a:
```text
http://127.0.0.1:8000/
```

**Funcionalidades de la interfaz:**
1. **Inicio de sesión interactivo:** Consume `POST /api/login` y almacena el Bearer Token en `localStorage`.
2. **Dashboard de Estadísticas:** Indicadores de total de libros, stock disponible, préstamos activos y usuarios.
3. **Búsqueda en tiempo real:** Catálogo de libros con filtro reactivo por título (debounce de 300 ms).
4. **Formulario de Préstamos:** Selección de usuario y libro disponible, validando reglas de negocio con retroalimentación visual de errores y éxitos.
5. **Cierre de sesión:** Endpoint `POST /api/logout` e invalidación del token local.

---

## ⚙️ Comandos Artisan Personalizados

El sistema incluye comandos de consola para automatización y reportería:

### 1. Marcar Préstamos Vencidos Automáticamente
Evalúa los préstamos en estado `activo` cuya fecha estimada de devolución haya expirado (> 15 días) y los actualiza al estado `vencido`:
```bash
php artisan biblioteca:marcar-vencidos
```

### 2. Generar Reporte Consolidado de Biblioteca
Genera tablas formateadas en terminal con:
- Top 5 libros más prestados
- Usuarios con préstamos actualmente vencidos y libros adeudados
- Libros sin existencias en stock (`stock_disponible <= 0`)
```bash
php artisan biblioteca:reporte
```

---

## 📮 Colección de Postman y Pruebas Automatizadas

El repositorio incluye el archivo exportado con la colección completa: `biblioteca.postman_collection.json`.

### Cómo importar y probar:
1. Abre **Postman** y haz clic en **Import**.
2. Selecciona el archivo `biblioteca.postman_collection.json`.
3. Configura la variable de colección `base_url` con:
   ```text
   http://127.0.0.1:8000/api
   ```
   *(O `http://127.0.0.1:8000` dependiendo del prefijo de tus endpoints).*
4. Ejecuta la petición **Auth > Login**: el script de prueba guardará automáticamente el token en `{{token}}` para todas las peticiones restantes de la colección.
5. Haz clic derecho sobre la colección **biblioteca** > **Run collection** > **Run biblioteca** para correr toda la suite de tests automatizados (`pm.test`).

---

## 📋 Resumen de Endpoints de la API

### Autenticación (Pública / Protegida)
- `POST /api/login` - Inicio de sesión y generación de token Sanctum.
- `POST /api/logout` - Revocación del token actual.

### Libros (`auth:sanctum`)
- `GET /api/libros` - Listado paginado con filtros (`titulo`, `autor`, `anio`, `per_page`).
- `GET /api/libros/{id}` - Detalle de un libro específico con sus autores ordenados.
- `POST /api/libros` - Registro de libro y asociación en tabla pivot `autor_libro`.
- `PUT /api/libros/{id}` - Actualización de datos de libro y sincronización de autores.
- `DELETE /api/libros/{id}` - Eliminación lógica (*Soft Delete*).

### Préstamos (`auth:sanctum`)
- `GET /api/prestamos` - Listado de préstamos con relaciones `libro` y `usuario`.
- `POST /api/prestamos` - Registro de nuevo préstamo (valida stock y límite de 3 préstamos por usuario).
- `PUT /api/prestamos/{id}/devolver` - Marca el préstamo como `devuelto`, registra fecha real y repone stock.