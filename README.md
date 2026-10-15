# Sistema de Gestión de Biblioteca - API REST & Frontend

Sistema integral de gestión de libros, autores y préstamos desarrollado con **Laravel**, **MySQL**, **JavaScript (Fetch API)** y **Blade**, conforme a los requerimientos de la prueba técnica.

---

## 📁 Estructura de Entregables del Repositorio

El repositorio incluye todos los componentes y entregables solicitados organizados de la siguiente manera:

* **Documento de Preguntas Teóricas:** `Preguntas teoricas.pdf`
* **Colección de Postman con Tests Automatizados:** `biblioteca.postman_collection.json` (incluye variables de entorno, autenticación Bearer token a nivel de colección, pruebas `pm.test`, casos de éxito, validaciones de error y ejemplos de respuesta documentados).
* **Base de Datos y Diagrama:** Carpeta `'modelo BD y script'` (contiene el Diagrama Entidad-Relación y los scripts SQL de creación con constraints).
* **Frontend Web Interactivo:** `resources/views/biblioteca.blade.php` (login interactivo, gestión de token en el cliente, catálogo reactivo con búsqueda en tiempo real, formulario de préstamo y dashboard de estadísticas).
* **Lógica de Negocio y Reportería:** Comandos personalizados en `app/Console/Commands/`.

---

## 🛠️ Requisitos Previos

Asegúrate de contar con el siguiente entorno configurado:

* **PHP**: >= 8.2 (con extensiones activas: `pdo_mysql`, `mbstring`, `openssl`, `bcmath`)
* **Composer**: >= 2.x
* **Motor de Base de Datos**: MySQL (>= 8.0)
* **Git**
* **Postman**: Para la ejecución y verificación de los endpoints de la API

---

## 🚀 Guía de Instalación y Puesta en Marcha

Sigue estos pasos en tu consola para levantar el proyecto localmente:

### 1. Clonar el repositorio
```bash
git clone <URL_DEL_REPOSITORIO>
cd biblioteca
```

### 2. Instalar dependencias del proyecto
```bash
composer install
```

### 3. Configuración del archivo de entorno (`.env`)
Genera el archivo local a partir del template de ejemplo:
```bash
cp .env.example .env
```

Genera la clave de cifrado de Laravel:
```bash
php artisan key:generate
```

### 4. Configurar la Conexión a la Base de Datos
Crea una base de datos vacía llamada `biblioteca` en tu servidor MySQL y verifica que el archivo `.env` contenga los datos de conexión apropiados:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=biblioteca
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Ejecutar Migraciones y Poblado de Datos (Seeders)
Ejecuta el siguiente comando para crear las tablas (incluyendo la tabla pivot `autor_libro` con `orden_autor`, `soft deletes` y tokens de Sanctum) y cargar los datos de prueba requeridos (10 autores, 20 libros, 15 usuarios y 10 préstamos):

```bash
php artisan migrate:fresh --seed
```

#### Credenciales de Acceso por Defecto:
* **Email:** `admin@biblioteca.test`
* **Password:** `password`

### 6. Iniciar el Servidor Local
```bash
php artisan serve
```

La aplicación web y la API quedarán disponibles en:
```text
http://127.0.0.1:8000
```

---

## 💻 Interfaz Web (Frontend con Blade + Vanilla JS)

Ingresa desde tu navegador a: `http://127.0.0.1:8000/`

**Flujo de la interfaz:**
1. **Autenticación en el Cliente:** Si no existe una sesión previa, se presentará el formulario de inicio de sesión. Al presionar *Ingresar*, se consume `POST /api/login` y se guarda automáticamente el token en `localStorage`.
2. **Dashboard de Estadísticas:** Muestra en tiempo real los contadores de libros totales, stock disponible, préstamos activos y usuarios.
3. **Búsqueda en Tiempo Real:** Filtro reactivo en el catálogo de libros por título (optimizado con *debounce* de 300 ms).
4. **Formulario de Préstamos:** Selección de usuario y libro disponible con validación del lado del cliente y consumo asíncrono (AJAX) del endpoint `POST /api/prestamos`.
5. **Cierre de Sesión:** Botón que ejecuta `POST /api/logout`, revoca el token en el backend y limpia el almacenamiento del navegador.

---

## ⚙️ Comandos de Consola Artisan

Se implementaron comandos para automatizar la lógica de negocio y generar reportes:

### 1. Marcar Préstamos Vencidos Automáticamente
Evalúa los préstamos activos con fecha estimada de devolución superada (> 15 días) y los actualiza al estado `vencido`:
```bash
php artisan biblioteca:marcar-vencidos
```

### 2. Generar Reporte de Biblioteca
Muestra tablas en consola con los tres indicadores solicitados:
* Top 5 libros más prestados
* Usuarios con préstamos actualmente vencidos y libros adeudados
* Libros sin existencias en stock (`stock_disponible <= 0`)
```bash
php artisan biblioteca:reporte
```

---

## 📮 Pruebas Automatizadas con Postman

El archivo `biblioteca.postman_collection.json` se encuentra en la raíz del proyecto.

### Pasos para ejecutar:
1. Abre **Postman** y haz clic en **Import**.
2. Selecciona `biblioteca.postman_collection.json`.
3. Verifica que la variable de colección `base_url` apunte a:
   ```text
   http://127.0.0.1:8000/api
   ```
   *(o `http://127.0.0.1:8000` si tus rutas no llevan prefijo adicional).*
4. Abre la carpeta **Auth** y ejecuta **Login**: el script de prueba validará el estado `200 OK` y fijará automáticamente el token en la variable `{{token}}` de la colección.
5. Ejecuta la colección completa haciendo clic en los tres puntos `...` de la colección > **Run collection** > **Run biblioteca**.

---

## 📋 Resumen de Endpoints de la API REST

Todos los endpoints esperan y devuelven respuestas con encabezado `Accept: application/json`.

| Método | Endpoint | Descripción | Autenticación |
| :--- | :--- | :--- | :--- |
| **POST** | `/api/login` | Inicia sesión y genera token Sanctum | Pública |
| **POST** | `/api/logout` | Invalida el token del usuario actual | Bearer Token |
| **GET** | `/api/libros` | Listado con paginación y filtros (`titulo`, `autor`, `anio`) | Bearer Token |
| **GET** | `/api/libros/{id}` | Detalle de libro con autores ordenados | Bearer Token |
| **POST** | `/api/libros` | Crear libro y vincular autores en pivot | Bearer Token |
| **PUT** | `/api/libros/{id}` | Actualizar datos del libro | Bearer Token |
| **DELETE**| `/api/libros/{id}` | Eliminación lógica (*Soft Delete*) | Bearer Token |
| **GET** | `/api/prestamos` | Listado de préstamos con libro y usuario | Bearer Token |
| **POST** | `/api/prestamos` | Registrar préstamo (valida stock y límite de 3) | Bearer Token |
| **PUT** | `/api/prestamos/{id}/devolver`| Marcar como devuelto y reponer stock | Bearer Token |