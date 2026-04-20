-- ============================================================
-- JOLLY BEAUTY — Base de données MySQL
-- Compatible XAMPP / MariaDB
-- Importer via phpMyAdmin ou : mysql -u root jollybeauty < database.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `jollybeauty`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `jollybeauty`;

-- ── UTILISATEURS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `users` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name`         VARCHAR(120) NOT NULL,
  `email`        VARCHAR(180) NOT NULL UNIQUE,
  `password`     VARCHAR(255) NOT NULL,
  `role`         ENUM('customer','admin') DEFAULT 'customer',
  `promo_code`   VARCHAR(30) DEFAULT NULL,
  `newsletter`   TINYINT(1) DEFAULT 0,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_email (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── CATÉGORIES ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `categories` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`        VARCHAR(80) NOT NULL UNIQUE,
  `name`        VARCHAR(80) NOT NULL,
  `label`       VARCHAR(120) DEFAULT NULL,
  `sort_order`  INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── PRODUITS ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `products` (
  `id`           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug`         VARCHAR(160) NOT NULL UNIQUE,
  `category_id`  INT UNSIGNED NOT NULL,
  `sub`          VARCHAR(80) DEFAULT NULL,
  `name`         VARCHAR(200) NOT NULL,
  `short`        VARCHAR(300) DEFAULT NULL,
  `description`  TEXT DEFAULT NULL,
  `price`        DECIMAL(10,2) NOT NULL,
  `old_price`    DECIMAL(10,2) DEFAULT NULL,
  `badge`        VARCHAR(50) DEFAULT NULL,
  `rating`       TINYINT DEFAULT 5,
  `reviews`      INT DEFAULT 0,
  `stock`        INT DEFAULT 0,
  `featured`     TINYINT(1) DEFAULT 0,
  `active`       TINYINT(1) DEFAULT 1,
  `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  INDEX idx_slug  (`slug`),
  INDEX idx_cat   (`category_id`),
  INDEX idx_featured (`featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── IMAGES PRODUITS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_images` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `url`         VARCHAR(500) NOT NULL,
  `sort_order`  INT DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── MATÉRIAUX / CARACTÉRISTIQUES ────────────────────────────
CREATE TABLE IF NOT EXISTS `product_materials` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `value`       VARCHAR(200) NOT NULL,
  `sort_order`  INT DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── TAILLES ─────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `product_sizes` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `value`       VARCHAR(80) NOT NULL,
  `sort_order`  INT DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── COMMANDES ───────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `orders` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_ref`       VARCHAR(20) NOT NULL UNIQUE,
  `user_id`         INT UNSIGNED DEFAULT NULL,
  `guest_name`      VARCHAR(120) DEFAULT NULL,
  `guest_email`     VARCHAR(180) DEFAULT NULL,
  `total`           DECIMAL(10,2) NOT NULL,
  `status`          ENUM('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_name`   VARCHAR(120) DEFAULT NULL,
  `shipping_addr`   VARCHAR(300) DEFAULT NULL,
  `shipping_city`   VARCHAR(100) DEFAULT NULL,
  `shipping_zip`    VARCHAR(20) DEFAULT NULL,
  `promo_code`      VARCHAR(30) DEFAULT NULL,
  `discount`        DECIMAL(10,2) DEFAULT 0.00,
  `notes`           TEXT DEFAULT NULL,
  `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at`      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_ref    (`order_ref`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── LIGNES DE COMMANDE ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `order_items` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id`    INT UNSIGNED NOT NULL,
  `product_id`  INT UNSIGNED DEFAULT NULL,
  `name`        VARCHAR(200) NOT NULL,
  `price`       DECIMAL(10,2) NOT NULL,
  `qty`         INT NOT NULL DEFAULT 1,
  `size`        VARCHAR(80) DEFAULT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── CODES PROMO ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `promo_codes` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `code`        VARCHAR(40) NOT NULL UNIQUE,
  `discount`    TINYINT NOT NULL,  -- pourcentage
  `max_uses`    INT DEFAULT NULL,  -- NULL = illimité
  `used_count`  INT DEFAULT 0,
  `active`      TINYINT(1) DEFAULT 1,
  `expires_at`  DATETIME DEFAULT NULL,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── NEWSLETTER ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `newsletter` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email`       VARCHAR(180) NOT NULL UNIQUE,
  `subscribed`  TINYINT(1) DEFAULT 1,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── AVIS CLIENTS ────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `reviews` (
  `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `product_id`  INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED DEFAULT NULL,
  `author`      VARCHAR(80) NOT NULL,
  `location`    VARCHAR(80) DEFAULT NULL,
  `rating`      TINYINT NOT NULL DEFAULT 5,
  `body`        TEXT NOT NULL,
  `verified`    TINYINT(1) DEFAULT 0,
  `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── PARAMÈTRES BOUTIQUE ─────────────────────────────────────
CREATE TABLE IF NOT EXISTS `settings` (
  `key_name`    VARCHAR(80) PRIMARY KEY,
  `value`       TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DONNÉES DE DÉMONSTRATION
-- ============================================================

-- Catégories
INSERT INTO `categories` (`slug`, `name`, `label`, `sort_order`) VALUES
  ('bijoux',   'Bijoux',         'Toute la Collection', 1),
  ('soins',    'Soins & Rituels','Prendre soin de vous', 2),
  ('coffrets', 'Coffrets',       'Idées Cadeaux',        3),
  ('produits', 'Produits Rituels','Rituels de beauté',   4);

-- Comptes démo (mot de passe pour tous : demo1234 — hash bcrypt valide)
INSERT INTO `users` (`name`, `email`, `password`, `role`) VALUES
  ('Admin Jolly', 'admin@jollybeauty.fr', '$2y$10$V1u.osGOtiEMq4vdObYynO6KN88RN9bIqh02PjW7avAbj6Huur68m', 'admin'),
  ('Sophie Martin','sophie@example.com', '$2y$10$V1u.osGOtiEMq4vdObYynO6KN88RN9bIqh02PjW7avAbj6Huur68m', 'customer'),
  ('Amina Kone',   'amina@example.com',  '$2y$10$V1u.osGOtiEMq4vdObYynO6KN88RN9bIqh02PjW7avAbj6Huur68m', 'customer');

-- Note : mot de passe par défaut des comptes ci-dessus = demo1234
-- Connexion admin back-office : admin / JollyBeauty2025! (voir includes/config.php ADMIN_*)

-- Produits
INSERT INTO `products` (`slug`,`category_id`,`sub`,`name`,`short`,`description`,`price`,`old_price`,`badge`,`rating`,`reviews`,`stock`,`featured`) VALUES
('bracelet-charms-eclat',  1,'Bracelets','Bracelet Charms Éclat',
 'Plaqué or 18k, charms symboliques, finition miroir.',
 'Le Bracelet Charms Éclat est la pièce signature de Jolly Beauty. Pensé pour s'accumuler avec d'autres bracelets, il met en valeur chaque poignet avec sa chaîne fine et ses charms délicats. Fabriqué en acier inoxydable plaqué or 18k, il résiste à l'eau et ne noircit pas.',
 36.90, NULL,'Best-seller',5,142,24,1),

('bague-coeur-douceur', 1,'Bagues','Bague Cœur Douceur',
 'Bague ajustable ornée d'un cœur zirconia rose.',
 'La Bague Cœur Douceur est un symbole d'amour porté chaque jour. Son cœur serti de zirconia rose apporte une touche précieuse. Ajustable des tailles 52 à 58.',
 29.90,39.90,'Promo',5,89,15,1),

('collier-lumiere', 1,'Colliers','Collier Lumière',
 'Chaîne fine dorée, pendentif cristal solitaire.',
 'Le Collier Lumière capture la lumière à chaque mouvement. Son pendentif cristal solitaire, serti sur une chaîne ultra-fine plaquée or, crée un effet de lumière naturelle sur le décolleté. Longueur ajustable de 38 à 42 cm.',
 33.90, NULL,'Nouveau',5,67,20,1),

('beurre-karite-fouette', 2,'Corps','Beurre de Karité Fouetté',
 'Texture aérienne, nourrit & sublime en douceur.',
 'Notre Beurre de Karité Fouetté est une invitation au cocooning. Sa texture légère comme une mousse fond instantanément sur la peau, la laissant douce, nourrie et délicatement parfumée.',
 32.90, NULL,'Best-seller',5,203,18,1),

('coffret-rituel-douceur', 3,'Coffrets','Coffret Rituel Douceur',
 'Le coffret signature Jolly Beauty — édition limitée.',
 'Le Coffret Rituel Douceur réunit tout ce qui fait l'âme de Jolly Beauty : un bracelet charms signature, un beurre de karité fouetté, un musc Tahara El Nabil et un sachet de roses séchées.',
 79.90,99.90,'Édition Limitée',5,55,10,1),

('bracelet-stack-amour', 1,'Bracelets','Set Bracelets Stack Amour',
 'Trio de bracelets assortis à porter ensemble.',
 'Le Set Stack Amour est composé de trois bracelets pensés pour s'harmoniser parfaitement : un bracelet serpent, un bracelet chain et un bracelet charm cœur.',
 49.90, NULL, NULL,4,38,12,0);

-- Images produits
INSERT INTO `product_images` (`product_id`,`url`,`sort_order`) VALUES
(1,'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?w=800&q=80',0),
(1,'https://images.unsplash.com/photo-1526290766257-c4db411a4e95?w=800&q=80',1),
(2,'https://images.unsplash.com/photo-1605100804763-247f67b3557e?w=800&q=80',0),
(2,'https://images.unsplash.com/photo-1588444837495-c6cfeb53f32d?w=800&q=80',1),
(3,'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=800&q=80',0),
(3,'https://images.unsplash.com/photo-1506630448388-4e683c67ddb0?w=800&q=80',1),
(4,'https://images.unsplash.com/photo-1619451334792-150fd785ee74?w=800&q=80',0),
(4,'https://images.unsplash.com/photo-1608248543803-ba4f8c70ae0b?w=800&q=80',1),
(5,'https://images.unsplash.com/photo-1607083206869-4c7672e72a8a?w=800&q=80',0),
(5,'https://images.unsplash.com/photo-1549465220-1a8b9238cd48?w=800&q=80',1),
(6,'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?w=800&q=80',0),
(6,'https://images.unsplash.com/photo-1602173574767-37ac01994b2a?w=800&q=80',1);

-- Matériaux
INSERT INTO `product_materials` (`product_id`,`value`,`sort_order`) VALUES
(1,'Acier inoxydable plaqué or 18k',0),(1,'Résistant à l'eau',1),(1,'Hypoallergénique',2),
(2,'Acier inoxydable plaqué or',0),(2,'Zirconia rose',1),(2,'Hypoallergénique',2),
(3,'Acier inoxydable plaqué or',0),(3,'Cristal transparent',1),(3,'Fermoir homard',2),
(4,'Beurre de karité 100% pur',0),(4,'Sans paraben',1),(4,'Sans silicone',2),(4,'200g',3),
(5,'1 Bracelet Charms',0),(5,'1 Beurre Fouetté 200g',1),(5,'1 Musc Tahara 30ml',2),(5,'Sachet roses séchées',3),(5,'Boîte cadeau premium',4),
(6,'3 bracelets inclus',0),(6,'Acier inoxydable plaqué or',1),(6,'Résistant à l'eau',2);

-- Tailles
INSERT INTO `product_sizes` (`product_id`,`value`,`sort_order`) VALUES
(1,'S — 16 cm',0),(1,'M — 18 cm',1),(1,'L — 20 cm',2),
(2,'Ajustable 52–58',0),
(3,'38–42 cm ajustable',0),
(4,'200g',0),(4,'400g',1),
(5,'Taille unique',0),
(6,'S',0),(6,'M',1),(6,'L',2);

-- Codes promo
INSERT INTO `promo_codes` (`code`,`discount`,`max_uses`,`used_count`,`active`) VALUES
('JOLLY15',15,NULL,47,1),
('BIENVENUE10',10,NULL,0,1);

-- Paramètres
INSERT INTO `settings` (`key_name`,`value`) VALUES
('shop_name','Jolly Beauty'),
('shop_email','contact@jollybeauty.fr'),
('currency','EUR'),
('free_shipping_from','60.00'),
('shipping_delay','48h ouvrées'),
('instagram','@jollybeauty'),
('maintenance','0');

-- Avis
INSERT INTO `reviews` (`product_id`,`user_id`,`author`,`location`,`rating`,`body`,`verified`) VALUES
(1,2,'Sophie M.','Paris',5,'Magnifique, tout est parfait ! Les bijoux sont encore plus beaux en vrai. Je recommande à 100%.',1),
(4,3,'Amina K.','Lyon',5,'Le beurre fouetté sent divinement bon. Une vraie parenthèse de douceur !',1),
(5,NULL,'Juliette T.','Marseille',5,'Coffret parfaitement emballé, cadeau idéal. Je reviendrai !',1);

-- Commandes de démo
INSERT INTO `orders` (`order_ref`,`user_id`,`total`,`status`,`shipping_name`,`created_at`) VALUES
('#JB1042',2,79.90,'delivered','Sophie Martin','2025-03-14 10:30:00'),
('#JB1041',3,36.90,'shipped','Amina Kone','2025-03-13 15:00:00'),
('#JB1040',NULL,49.90,'processing','Juliette Tran','2025-03-12 09:00:00'),
('#JB1039',2,32.90,'delivered','Sophie Martin','2025-03-10 11:45:00');