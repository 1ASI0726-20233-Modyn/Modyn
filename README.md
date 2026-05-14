# Modyn App

Aplicación web para gestionar bases de datos MySQL con interfaz intuitiva. Permite visualizar tablas, insertar registros y explorar la estructura de la BD.

## 📋 Requisitos Previos

- **PHP 7.4+** instalado
- **MySQL/MariaDB** en funcionamiento
- **Servidor Web** (Apache, Nginx, o PHP Built-in)
- **Credenciales BD:** Host, usuario, contraseña y nombre de la base de datos

## ⚙️ Instalación y Configuración

### 1️⃣ Clonar o descargar el proyecto

```bash
git clone <repositorio> Modyn
cd Modyn
```

### 2️⃣ Configurar la conexión a BD

Edita el archivo `app/modynConnection.php`:

```php
<?php
$host = "158.23.145.99";      // Tu servidor MySQL
$user = "admin";               // Tu usuario
$pass = "Admin123";            // Tu contraseña
$db   = "Modyn_DB";            // Tu base de datos

$link = mysqli_connect($host, $user, $pass, $db);
// ...
```

### 3️⃣ Configurar el servidor web

**Opción A: Usando PHP Built-in (más fácil)**

```bash
cd app
php -S localhost:8000
```

Luego abre: `http://localhost:8000/`

**Opción B: Usando Apache (Windows/VM)**

Configura el **DocumentRoot** de Apache para que apunte a la carpeta `app/`:

- En `httpd.conf`:
```apache
DocumentRoot "D:\Developer\Uni\5°Ciclo\2.-Sistemas\Modyn\app"
<Directory "D:\Developer\Uni\5°Ciclo\2.-Sistemas\Modyn\app">
    AllowOverride All
    Require all granted
</Directory>
```

Luego abre: `http://localhost/`

## 🎯 Entendiendo las URLs

⚠️ **IMPORTANTE:** La ruta `/modyn/` que ves en el código NO es un directorio físico. Es parte de la URL web.

**Ejemplo del flujo:**

```
Carpeta del PC:  D:\Developer\Uni\5°Ciclo\2.-Sistemas\Modyn\app\
DocumentRoot:    → apunta a la carpeta app/
URL en navegador: http://localhost:8000/ ← Aquí NO va /modyn/
```

Si tu Apache está configurado diferente:
- **DocumentRoot a Modyn/app** → URL: `http://localhost/`
- **DocumentRoot a Modyn** → URL: `http://localhost/app/`
- **DocumentRoot a otra carpeta** → Ajusta según la ruta

## 🚀 Cómo Usar

### 📊 Ver Tablas

1. Ve a `http://localhost:8000/tables.php`
2. Selecciona una tabla con el botón **"Ver"**
3. Se mostrarán todos los registros

### ➕ Insertar Registros

1. En la vista de datos de una tabla, haz clic en **"+ Insertar Nuevo Registro"**
2. Completa el formulario con los datos
3. Respeta los formatos:
   - **Fechas:** `YYYY-MM-DD` (Ej: `2026-05-14`)
   - **Datetime:** `YYYY-MM-DD HH:MM:SS` (Ej: `2026-05-14 14:30:00`)
   - **Números:** Sin comillas
   - **Texto:** Sin caracteres especiales sin escapar

### ⚠️ Errores Comunes

- **"Not Found"** → Revisa la URL y el DocumentRoot
- **"Error de conexión"** → Verifica credenciales en `modynConnection.php`
- **"Duplicate entry"** → No repitas IDs (PRIMARY KEY únicos)

## 📁 Estructura del Proyecto

```
Modyn/
├── app/
│   ├── index.php              # Página de inicio
│   ├── tables.php             # Vista de tablas y datos
│   ├── modynConnection.php    # Conexión a BD
│   ├── header.php             # Header HTML común
│   ├── footer.php             # Footer HTML común
│   ├── css/
│   │   └── styles.css         # Estilos
│   └── features/
│       └── insert.php         # Insertar registros
├── README.md                  # Este archivo
```

## 🔧 Troubleshooting

**Problema:** Las rutas se rompen al navegar
- **Solución:** Verifica que `header.php` detecte correctamente si estás en `/features/`

**Problema:** El CSS no se carga
- **Solución:** Revisa que la ruta en `header.php` sea correcta según tu ubicación

**Problema:** No puedo conectar a la BD
- **Solución:** Verifica que MySQL esté ejecutándose y las credenciales sean correctas

## 📝 Nota Importante para VM

Si estás en una Máquina Virtual:
- Asegúrate que MySQL esté corriendo: `service mysql status`
- Verifica permisos de carpetas: `chmod 755 app/`
- Si usas IP diferente, actualiza `$host` en `modynConnection.php`

---

**Versión:** 1.0  
**Última actualización:** 2026-05-14


