-- =============================================================================
-- PROYECTO: Kalabashare 2 (2026)
-- DESCRIPCIÓN: Plataforma de Compartición y Descarga de Archivos (Web Pública + /admin)
-- MOTOR: MySQL 8.0+ / MariaDB (InnoDB, utf8mb4_unicode_ci)
-- AUTOR: Evilnapsis / Legobox 5
-- =============================================================================

CREATE DATABASE IF NOT EXISTS kalabashare2
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE kalabashare2;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS post_view;
DROP TABLE IF EXISTS post;
DROP TABLE IF EXISTS client;
DROP TABLE IF EXISTS category;
DROP TABLE IF EXISTS slide;
DROP TABLE IF EXISTS configuration;
DROP TABLE IF EXISTS `user`;
SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- TABLA: user
-- PROPÓSITO: Cuentas de acceso al panel administrativo (/admin) para gestionar
--            archivos, categorías, clientes, sliders y configuración.
-- RELACIONES:
--   - Tabla independiente de administración.
-- 
-- VALORES FIJOS:
--   * role: Perfil de acceso en el panel administrativo:
--       - 'admin': Administrador general con acceso total.
--       - 'editor': Moderador de contenido; gestiona archivos, categorías y slides.
--       DEFAULT: 'admin'
-- -----------------------------------------------------------------------------
CREATE TABLE `user` (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(80)  NOT NULL,
  lastname       VARCHAR(80)  NULL,
  username       VARCHAR(60)  NULL,
  email          VARCHAR(150) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  role           ENUM('admin', 'editor') NOT NULL DEFAULT 'admin',
  is_active      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_user_email (email),
  UNIQUE KEY uq_user_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- TABLA: category
-- PROPÓSITO: Categorías para clasificar los archivos descargables y controlar su
--            visibilidad en el menú superior y en la página de inicio.
-- RELACIONES:
--   - post.category_id -> Archivos pertenecientes a la categoría [1:N]
-- -----------------------------------------------------------------------------
CREATE TABLE category (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(150) NOT NULL,
  short_name  VARCHAR(150) NOT NULL,
  in_home     TINYINT(1)   NOT NULL DEFAULT 0,
  in_menu     TINYINT(1)   NOT NULL DEFAULT 1,
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_category_short_name (short_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- TABLA: client
-- PROPÓSITO: Cuentas de usuarios/miembros públicos que pueden registrarse, iniciar
--            sesión y subir/gestionar sus propios archivos compartidos.
-- RELACIONES:
--   - post.client_id -> Archivos publicados por el usuario [0..1:N]
-- -----------------------------------------------------------------------------
CREATE TABLE client (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(80)  NOT NULL,
  lastname       VARCHAR(80)  NULL,
  email          VARCHAR(150) NOT NULL,
  phone          VARCHAR(30)  NULL,
  address        VARCHAR(255) NULL,
  password_hash  VARCHAR(255) NOT NULL,
  is_active      TINYINT(1)   NOT NULL DEFAULT 1,
  created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_client_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- TABLA: post
-- PROPÓSITO: Publicaciones y archivos descargables compartidos por administradores
--            o usuarios registrados, con portada, descripción y conteo de descargas.
-- RELACIONES:
--   - category_id        -> Categoría del archivo (category.id) [1:N]
--   - client_id          -> Autor registrado o NULL si fue publicado por Admin (client.id) [0..1:N]
--   - post_view.post_id  -> Registro de visualizaciones [1:N]
-- -----------------------------------------------------------------------------
CREATE TABLE post (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id  INT UNSIGNED NOT NULL,
  client_id    INT UNSIGNED NULL,
  code         VARCHAR(80)  NULL,
  name         VARCHAR(200) NOT NULL,
  short_name   VARCHAR(120) NULL,
  description  TEXT         NULL,
  offer_txt    VARCHAR(255) NULL,
  image        VARCHAR(255) NULL,
  file         VARCHAR(255) NULL,
  link         VARCHAR(255) NULL,
  downloads    INT UNSIGNED NOT NULL DEFAULT 0,
  is_featured  TINYINT(1)   NOT NULL DEFAULT 0,
  is_public    TINYINT(1)   NOT NULL DEFAULT 1,
  created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_post_category (category_id),
  KEY idx_post_client (client_id),
  KEY idx_post_public_featured (is_public, is_featured),
  CONSTRAINT fk_post_category FOREIGN KEY (category_id) REFERENCES category (id),
  CONSTRAINT fk_post_client   FOREIGN KEY (client_id)   REFERENCES client (id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- TABLA: post_view
-- PROPÓSITO: Registra las visualizaciones de archivos en la web pública para
--            métricas y gráficas en el Dashboard administrativo.
-- RELACIONES:
--   - post_id -> Archivo visualizado (post.id) [1:N]
-- -----------------------------------------------------------------------------
CREATE TABLE post_view (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  post_id     INT UNSIGNED NOT NULL,
  ip_address  VARCHAR(45)  NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_post_view_post (post_id),
  KEY idx_post_view_created (created_at),
  CONSTRAINT fk_post_view_post FOREIGN KEY (post_id) REFERENCES post (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- TABLA: slide
-- PROPÓSITO: Diapositivas/banners del carrusel principal en la portada pública.
-- RELACIONES:
--   - Tabla independiente de contenido promocional.
-- -----------------------------------------------------------------------------
CREATE TABLE slide (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title       VARCHAR(200) NULL,
  image       VARCHAR(255) NOT NULL,
  link        VARCHAR(255) NULL,
  position    INT UNSIGNED NOT NULL DEFAULT 1,
  is_public   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_slide_public_pos (is_public, position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- TABLA: configuration
-- PROPÓSITO: Parámetros globales del portal de archivos.
-- RELACIONES:
--   - Tabla independiente de configuración clave/valor.
-- 
-- VALORES FIJOS:
--   * kind: Tipo de campo para el formulario de configuración:
--       - 'text': Campo de texto corto.
--       - 'number': Campo numérico.
--       - 'boolean': Interruptor activo/inactivo (1 o 0).
--       - 'textarea': Texto multilínea.
--       DEFAULT: 'text'
-- -----------------------------------------------------------------------------
CREATE TABLE configuration (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name        VARCHAR(100) NOT NULL,
  label       VARCHAR(200) NOT NULL,
  kind        ENUM('text', 'number', 'boolean', 'textarea') NOT NULL DEFAULT 'text',
  val         TEXT         NULL,
  created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_configuration_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================================================
-- DATOS SEMILLA INICIALES (SEED DATA MÍNIMO OBLIGATORIO)
-- =============================================================================

-- 1. Usuario Administrador Inicial (usuario: admin / contraseña: admin)
INSERT INTO `user` (name, lastname, username, email, password_hash, role, is_active) VALUES
  ('Administrador', 'Sistema', 'admin', 'admin@kalabashare.com', '$2y$10$X72H1ZxDtMu5KwWJjzuCaeTkyeX8O0I4611U7G.Ny3osq78wkByLa', 'admin', 1);

-- 2. Categoría Base
INSERT INTO category (name, short_name, in_home, in_menu, is_active) VALUES
  ('General', 'general', 1, 1, 1);

-- 3. Configuración Base
INSERT INTO configuration (name, label, kind, val) VALUES
  ('general_main_title', 'Título Principal del Sitio', 'text',     'KALABASHARE'),
  ('general_subtitle',   'Subtítulo / Eslogan',        'text',     'Plataforma de Descarga de Archivos'),
  ('contact_email',      'Correo de Contacto',         'text',     'contacto@kalabashare.com'),
  ('contact_phone',      'Teléfono de Contacto',       'text',     '55-8000-2026'),
  ('contact_address',    'Dirección',                  'text',     'Av. Reforma 100, CDMX'),
  ('about_text',         'Descripción del Portal',     'textarea', 'Comparte y descarga documentos, plantillas, software y recursos digitales de forma rápida y segura.');
