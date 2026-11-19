-- =============================================================================
-- PROYECTO: Kalabashare 2 (2026)
-- ARCHIVO: demo_data.sql
-- PROPÓSITO: Datos de demostración para catálogo de archivos, usuarios clientes
--            y métricas de los últimos 30 días.
-- =============================================================================

USE kalabashare2;

-- -----------------------------------------------------------------------------
-- 1. CATEGORÍAS (10 Categorías)
-- -----------------------------------------------------------------------------
INSERT INTO category (id, name, short_name, in_home, in_menu, is_active) VALUES
  (2,  'Software y Utilidades', 'software',     1, 1, 1),
  (3,  'Plantillas Web',        'plantillas',   1, 1, 1),
  (4,  'Documentos PDF',        'documentos',   1, 1, 1),
  (5,  'Código Fuente',         'codigo',       1, 1, 1),
  (6,  'Diseño Gráfico',        'diseno',       0, 1, 1),
  (7,  'Manuales y Guías',      'manuales',     0, 1, 1),
  (8,  'Audio y Multimedia',    'multimedia',   0, 1, 1),
  (9,  'Hojas de Cálculo',      'excel',        0, 1, 1),
  (10, 'Presentaciones',        'presentaciones',0, 0, 1),
  (11, 'Recursos Educativos',   'educativos',   1, 1, 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- -----------------------------------------------------------------------------
-- 2. CLIENTES / USUARIOS REGISTRADOS (Contraseña demo: 'admin')
-- -----------------------------------------------------------------------------
INSERT INTO client (id, name, lastname, email, phone, address, password_hash, is_active) VALUES
  (1, 'Laura',   'Méndez',   'laura@kalabashare.com',  '55-1111-2222', 'CDMX',        '$2y$10$X72H1ZxDtMu5KwWJjzuCaeTkyeX8O0I4611U7G.Ny3osq78wkByLa', 1),
  (2, 'Carlos',  'Herrera',  'carlos@kalabashare.com', '55-3333-4444', 'Guadalajara', '$2y$10$X72H1ZxDtMu5KwWJjzuCaeTkyeX8O0I4611U7G.Ny3osq78wkByLa', 1),
  (3, 'Sofía',   'Ramírez',  'sofia@kalabashare.com',  '55-5555-6666', 'Monterrey',   '$2y$10$X72H1ZxDtMu5KwWJjzuCaeTkyeX8O0I4611U7G.Ny3osq78wkByLa', 1)
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- -----------------------------------------------------------------------------
-- 3. ARCHIVOS / PUBLICACIONES COMPARTIDAS (30 Registros)
-- -----------------------------------------------------------------------------
INSERT INTO post (id, category_id, client_id, code, name, short_name, description, offer_txt, image, file, link, downloads, is_featured, is_public, created_at) VALUES
  (1,  2,  1,    'SW-101', 'Gestor de Inventario Portátil v2.1',      'gestor-inventario',      'Aplicación ligera de escritorio para control de entradas y salidas.', 'ZIP 12 MB', '', '', 'https://evilnapsis.com', 48, 1, 1, DATE_SUB(NOW(), INTERVAL 29 DAY)),
  (2,  3,  2,    'TP-102', 'Plantilla Admin Dashboard Bootstrap 5',   'admin-bootstrap-5',      'Maquetación limpia responsive con tablas y tarjetas listas para usar.', 'HTML/CSS', '', '', 'https://evilnapsis.com', 64, 1, 1, DATE_SUB(NOW(), INTERVAL 27 DAY)),
  (3,  4,  NULL, 'PD-103', 'Guía Práctica de PHP 8.2 y PDO',          'guia-php82-pdo',         'Manual completo con ejemplos de consultas preparadas y transacciones.', 'PDF 3.4 MB', '', '', '', 92, 1, 1, DATE_SUB(NOW(), INTERVAL 25 DAY)),
  (4,  5,  1,    'CD-104', 'Micro-Framework Enrutador FastRoute PHP', 'router-fastroute-php',   'Ejemplo base de front controller MVC con Twig y sesiones seguras.', 'ZIP 1.8 MB', '', '', '', 37, 1, 1, DATE_SUB(NOW(), INTERVAL 23 DAY)),
  (5,  6,  3,    'DG-105', 'Pack de 120 Iconos Vectoriales SVG',      'pack-iconos-svg',        'Colección de iconos minimalistas listos para interfaces web y móviles.', 'SVG/PNG', '', '', '', 55, 1, 1, DATE_SUB(NOW(), INTERVAL 21 DAY)),
  (6,  7,  NULL, 'MN-106', 'Manual de Normalización MySQL 1FN-3FN',   'manual-normalizacion',   'Documento paso a paso para diseñar bases de datos relacionales limpias.', 'PDF 2.1 MB', '', '', '', 71, 1, 1, DATE_SUB(NOW(), INTERVAL 19 DAY)),
  (7,  8,  2,    'MM-107', 'Efectos de Sonido UI para Notificaciones', 'sonidos-ui-pack',       '25 tonos cortos en MP3 y WAV para alertas de sistemas web.', 'WAV/MP3', '', '', '', 29, 0, 1, DATE_SUB(NOW(), INTERVAL 17 DAY)),
  (8,  9,  3,    'EX-108', 'Plantilla Control de Gastos Mensuales',   'control-gastos-excel',   'Libro de cálculo automatizado con gráficas de presupuesto y flujo.', 'XLSX', '', '', '', 84, 1, 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
  (9,  10, NULL, 'PR-109', 'Deck Corporativo Minimalista 20 Slides',  'deck-corporativo',       'Presentación editable para propuestas comerciales y reportes.', 'PPTX', '', '', '', 41, 0, 1, DATE_SUB(NOW(), INTERVAL 13 DAY)),
  (10, 11, 1,    'ED-110', 'Cuaderno de Ejercicios SQL Avanzado',     'ejercicios-sql',         '50 consultas prácticas con joins, subconsultas y funciones de ventana.', 'PDF', '', '', '', 63, 1, 1, DATE_SUB(NOW(), INTERVAL 11 DAY)),
  (11, 2,  2,    'SW-111', 'Utilidad Backup Automático MySQL',        'backup-mysql-bat',       'Script programable para respaldos diarios comprimidos en Windows.', 'ZIP', '', '', '', 33, 0, 1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (12, 3,  3,    'TP-112', 'Landing Page Corporativa Limpia',         'landing-corporativa',    'Página de aterrizaje en una sola vista con formulario de contacto.', 'HTML5', '', '', '', 47, 0, 1, DATE_SUB(NOW(), INTERVAL 9 DAY)),
  (13, 4,  NULL, 'PD-113', 'Checklist de Seguridad Web OWASP',        'checklist-owasp',        'Lista de verificación práctica contra XSS, CSRF e inyección SQL.', 'PDF', '', '', '', 59, 0, 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),
  (14, 5,  1,    'CD-114', 'Generador de Tickets Térmicos FPDF',      'tickets-fpdf-php',       'Clase de ejemplo en PHP para imprimir comprobantes de 80 mm.', 'PHP/ZIP', '', '', '', 77, 1, 1, DATE_SUB(NOW(), INTERVAL 7 DAY)),
  (15, 6,  2,    'DG-115', 'Mockups de Dispositivos Móviles PSD',     'mockups-moviles',        'Escenas limpias para presentar aplicaciones en portafolios.', 'PSD', '', '', '', 26, 0, 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
  (16, 7,  NULL, 'MN-116', 'Guía Rápida de Git y Flujos de Trabajo',  'guia-rapida-git',        'Comandos esenciales para ramas, merges y resolución de conflictos.', 'PDF', '', '', '', 52, 0, 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
  (17, 9,  3,    'EX-117', 'Calculadora de Nómina y Retenciones',     'calculadora-nomina',     'Hoja de cálculo con fórmulas listas para cálculo quincenal.', 'XLSX', '', '', '', 44, 0, 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
  (18, 11, 1,    'ED-118', 'Curso Introductorio de JavaScript ES6',   'curso-js-es6',           'Apuntes y ejemplos de promesas, fetch y manipulación del DOM.', 'PDF/ZIP', '', '', '', 68, 1, 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
  (19, 3,  2,    'TP-119', 'Plantilla Catálogo de Productos Minimal', 'catalogo-minimal',       'Diseño en cuadrícula con tarjetas limpias y barra de búsqueda.', 'HTML/CSS', '', '', '', 39, 0, 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
  (20, 5,  NULL, 'CD-120', 'Validador de Formularios JS Vanilla',     'validador-js',           'Script ligero sin dependencias para validación en cliente.', 'JS', '', '', '', 45, 1, 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (21, 4,  3,    'PD-121', 'Plantilla Contrato de Servicios Tech',    'contrato-servicios',     'Formato editable de prestación de servicios de desarrollo web.', 'DOCX/PDF', '', '', '', 31, 0, 1, NOW()),
  (22, 2,  1,    'SW-122', 'Compresor de Imágenes por Lotes',         'compresor-imagenes',     'Herramienta CLI para optimizar PNG y JPG antes de subir a web.', 'ZIP', '', '', '', 22, 1, 1, NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- -----------------------------------------------------------------------------
-- 4. VISITAS A ARCHIVOS (Últimos 30 días para gráfica del Dashboard)
-- -----------------------------------------------------------------------------
INSERT INTO post_view (post_id, ip_address, created_at) VALUES
  (1,  '192.168.1.10', DATE_SUB(NOW(), INTERVAL 28 DAY)),
  (2,  '192.168.1.11', DATE_SUB(NOW(), INTERVAL 26 DAY)),
  (3,  '192.168.1.12', DATE_SUB(NOW(), INTERVAL 24 DAY)),
  (4,  '192.168.1.13', DATE_SUB(NOW(), INTERVAL 22 DAY)),
  (5,  '192.168.1.14', DATE_SUB(NOW(), INTERVAL 20 DAY)),
  (6,  '192.168.1.15', DATE_SUB(NOW(), INTERVAL 18 DAY)),
  (3,  '192.168.1.16', DATE_SUB(NOW(), INTERVAL 16 DAY)),
  (8,  '192.168.1.17', DATE_SUB(NOW(), INTERVAL 14 DAY)),
  (10, '192.168.1.18', DATE_SUB(NOW(), INTERVAL 12 DAY)),
  (14, '192.168.1.19', DATE_SUB(NOW(), INTERVAL 10 DAY)),
  (1,  '192.168.1.20', DATE_SUB(NOW(), INTERVAL 9 DAY)),
  (2,  '192.168.1.21', DATE_SUB(NOW(), INTERVAL 8 DAY)),
  (3,  '192.168.1.22', DATE_SUB(NOW(), INTERVAL 7 DAY)),
  (14, '192.168.1.23', DATE_SUB(NOW(), INTERVAL 6 DAY)),
  (18, '192.168.1.24', DATE_SUB(NOW(), INTERVAL 5 DAY)),
  (6,  '192.168.1.25', DATE_SUB(NOW(), INTERVAL 4 DAY)),
  (8,  '192.168.1.26', DATE_SUB(NOW(), INTERVAL 3 DAY)),
  (14, '192.168.1.27', DATE_SUB(NOW(), INTERVAL 2 DAY)),
  (20, '192.168.1.28', DATE_SUB(NOW(), INTERVAL 2 DAY)),
  (18, '192.168.1.29', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (21, '192.168.1.30', DATE_SUB(NOW(), INTERVAL 1 DAY)),
  (22, '192.168.1.31', NOW()),
  (3,  '192.168.1.32', NOW()),
  (14, '192.168.1.33', NOW());
