# Kalabashare

Plataforma web para compartir y descargar archivos digitales, con portal de usuarios registrados y panel administrativo, desarrollada con arquitectura dual (web pública + `/admin/`) bajo **Legobox 5**.

## Tecnologías

- **Backend:** PHP 8.2+ · PDO (`utf8mb4`) · FastRoute
- **Vistas:** Twig 3 · CoreUI v5 / Bootstrap 5 · Bootstrap Icons · SweetAlert2 · DataTables · Select2 · Chart.js
- **Base de datos:** MySQL 8.0+ / MariaDB (`InnoDB`, `utf8mb4_unicode_ci`)

## Estructura del Proyecto

```text
kalabashare2/
├── admin/                        # Panel administrativo
│   ├── assets/                   # Assets del panel
│   ├── core/
│   │   ├── autoload.php          # Autocarga del núcleo administrativo
│   │   ├── controller/           # Database.php (PDO único), Req, Response, Session, ViewEngine
│   │   └── app/
│   │       ├── autoload.php      # Autoloader de modelos, controladores y servicios
│   │       ├── routes.php        # Rutas del panel (/admin/*)
│   │       ├── controller/       # Auth, Home, Post, Category, Client, Slide, User, Configuration, Profile
│   │       ├── model/            # Todos los modelos (*Data.php) centralizados aquí
│   │       └── service/          # AuthService
│   ├── public/                   # Vistas Twig del panel
│   ├── storage/                  # Archivos e imágenes (files/, products/, slides/)
│   ├── .htaccess
│   └── index.php                 # Front Controller del Admin
├── assets/                       # Assets mínimos de la web pública
├── core/
│   ├── autoload.php              # Carga Database.php desde /admin/ sin duplicar
│   ├── controller/               # Model.php (resuelve desde /admin/), Req, Response, Session, ViewEngine
│   └── app/
│       ├── autoload.php
│       ├── routes.php            # Rutas públicas y portal de clientes
│       └── controller/           # Home, Catalog, Contact, ClientAuth, ClientPortal
├── public/                       # Vistas Twig del portal público y clientes
├── vendor/                       # Dependencias Composer (Twig, FastRoute)
├── .htaccess
├── index.php                     # Front Controller Público
├── schema.sql                    # Estructura de base de datos (7 tablas) y datos semilla
├── demo_data.sql                 # Datos de demostración
└── README.md
```

## Base de Datos (`kalabashare2`)

1. `user` — Administradores y editores del panel.
2. `category` — Categorías de archivos y visibilidad en menú/portada.
3. `client` — Usuarios registrados en el portal público para subir archivos.
4. `post` — Publicaciones y archivos descargables compartidos.
5. `post_view` — Registro de visualizaciones para gráficas del Dashboard.
6. `slide` — Diapositivas del carrusel de portada.
7. `configuration` — Parámetros generales del sitio.

## Instalación

1. Importar el esquema y datos semilla en MySQL:
   ```bash
   mysql -u root --default-character-set=utf8mb4 < schema.sql
   ```
2. *(Opcional)* Importar datos de demostración:
   ```bash
   mysql -u root --default-character-set=utf8mb4 < demo_data.sql
   ```
3. Configurar credenciales en `admin/core/controller/Database.php` si difieren de `root` sin contraseña en `localhost`.
4. Habilitar `mod_rewrite` en Apache para el funcionamiento de `.htaccess`.

## Acceso

- **Portal Público:** `http://localhost/update_mv_2026/kalabashare2/`
- **Cliente Demo:** `laura@kalabashare.com` / `admin`
- **Panel Administrativo:** `http://localhost/update_mv_2026/kalabashare2/admin/` (`admin` / `admin`)

---

[Evilnapsis](https://evilnapsis.com) &copy; 2026
