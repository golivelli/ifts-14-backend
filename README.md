# Backend IFTS 14 - PHP API

Backend en PHP para el panel de administración del IFTS 14.

## 📁 Estructura del Proyecto

```
ifts-14-backend/
├── api/                          # API REST en PHP (para cPanel)
│   ├── config/
│   │   └── database.php         # Configuración de MySQL
│   ├── anuncios/                # Endpoints de anuncios
│   │   ├── index.php           # GET todos
│   │   ├── get.php             # GET uno
│   │   ├── create.php          # POST crear
│   │   ├── update.php          # PUT actualizar
│   │   └── delete.php          # DELETE eliminar
│   ├── horarios/                # Endpoints de horarios
│   │   ├── index.php           # GET todos
│   │   ├── create.php          # POST crear
│   │   ├── update.php          # PUT actualizar
│   │   └── delete.php          # DELETE eliminar
│   ├── uploads/                 # Carpeta para imágenes
│   └── .htaccess               # Configuración Apache
├── database-migration.sql       # Script para crear tablas
├── RESUMEN-IMPLEMENTACION.md    # Guía de deployment
├── .env                         # Credenciales (NO subir a Git)
└── README.md                    # Este archivo
```

## 🚀 Deployment a cPanel

### 1. Preparar Base de Datos

```sql
-- Ejecutar en cPanel → phpMyAdmin
-- Copiar y pegar el contenido de: database-migration.sql
```

### 2. Subir Archivos

**Opción A: Via File Manager (cPanel)**
1. Comprimir carpeta `api/` en un .zip
2. Ir a cPanel → File Manager
3. Navegar a `public_html/`
4. Subir `api.zip`
5. Descomprimir

**Opción B: Via FTP**
1. Conectar con FileZilla/WinSCP
2. Subir carpeta `api/` a `public_html/api/`

### 3. Configurar Permisos

```bash
chmod 755 api/
chmod 755 api/uploads/
chmod 644 api/**/*.php
```

## 🧪 Probar API

### Endpoints de Anuncios

```bash
# Listar todos
GET https://tudominio.com/api/anuncios/

# Obtener uno
GET https://tudominio.com/api/anuncios/get.php?id=1

# Crear
POST https://tudominio.com/api/anuncios/create.php
Content-Type: application/json
{
  "titulo": "Título del anuncio",
  "contenido": "Contenido del anuncio",
  "estado": "publicado",
  "destacado": 1
}

# Actualizar
PUT https://tudominio.com/api/anuncios/update.php
Content-Type: application/json
{
  "id": 1,
  "titulo": "Título actualizado"
}

# Eliminar
DELETE https://tudominio.com/api/anuncios/delete.php?id=1
```

### Endpoints de Horarios

```bash
# Listar todos
GET https://tudominio.com/api/horarios/

# Filtrar por carrera
GET https://tudominio.com/api/horarios/?carrera=sistemas

# Crear
POST https://tudominio.com/api/horarios/create.php
Content-Type: application/json
{
  "carrera": "sistemas",
  "anio": "1° Año",
  "materia": "Matemática I",
  "dia": "Lunes",
  "horario": "18:00 - 20:00",
  "profesor": "Prof. García",
  "aula": "Aula 101"
}
```

## 🔧 Configuración

### Variables de Entorno

El archivo `.env` contiene las credenciales de la base de datos:

```env
DB_HOST=186.22.245.92
DB_USER=ifts14c8
DB_PASSWORD=pb9V5tbhvE9kBPW
DB_NAME=ifts14c8_db
```

**⚠️ IMPORTANTE:** Este archivo NO debe subirse a Git (ya está en `.gitignore`)

### CORS

El archivo `.htaccess` ya está configurado para permitir peticiones desde cualquier origen. En producción, considera restringir a tu dominio:

```apache
Header set Access-Control-Allow-Origin "https://tudominio.com"
```

## 📝 Notas

- Todas las respuestas son en formato JSON
- Los errores incluyen detalles para debugging
- La base de datos usa charset `utf8mb4` para soportar emojis y caracteres especiales

## 🔗 Recursos

- [Documentación completa](./RESUMEN-IMPLEMENTACION.md)
- [Script de base de datos](./database-migration.sql)