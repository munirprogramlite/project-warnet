-- ============================================================
-- Warnet Booking - struktur database dan data contoh
-- Import lewat phpMyAdmin (tab Import) atau: mysql -u root < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS `app_warnet`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `app_warnet`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `pcs`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
-- users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id`         int          NOT NULL AUTO_INCREMENT,
  `username`   varchar(50)  NOT NULL,
  `password`   varchar(255) NOT NULL,
  `nama`       varchar(100) NOT NULL,
  `role`       enum('user','admin') DEFAULT 'user',
  `token`      varchar(64)  DEFAULT NULL,
  `token_exp`  datetime     DEFAULT NULL,
  `created_at` datetime     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ------------------------------------------------------------
-- pcs
-- ------------------------------------------------------------
CREATE TABLE `pcs` (
  `id`            int          NOT NULL AUTO_INCREMENT,
  `nama`          varchar(20)  NOT NULL,
  `spesifikasi`   varchar(150) DEFAULT NULL,
  `harga_per_jam` int          NOT NULL,
  `status`        enum('tersedia','maintenance') DEFAULT 'tersedia',
  `created_at`    datetime     DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ------------------------------------------------------------
-- bookings
-- ------------------------------------------------------------
CREATE TABLE `bookings` (
  `id`          int  NOT NULL AUTO_INCREMENT,
  `user_id`     int  NOT NULL,
  `pc_id`       int  NOT NULL,
  `tanggal`     date NOT NULL,
  `jam_mulai`   time NOT NULL,
  `durasi_jam`  int  NOT NULL,
  `total_harga` int  NOT NULL,
  `status`      enum('pending','confirmed','done','cancelled') DEFAULT 'pending',
  `catatan`     text,
  `created_at`  datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `pc_id` (`pc_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`pc_id`)   REFERENCES `pcs`   (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ============================================================
-- DATA CONTOH
-- Akun uji: admin / admin123  dan  user1 / user123
-- (password disimpan sebagai hash bcrypt, token dikosongkan)
-- ============================================================

INSERT INTO `users` (`id`, `username`, `password`, `nama`, `role`, `token`, `token_exp`) VALUES
(1, 'admin', '$2y$10$FZscsv01RWmAKxmZfD5X1.xFF/WE5mmQVfEV.IAlKpP40Jk8zRC0O', 'Administrator', 'admin', NULL, NULL),
(2, 'user1', '$2y$10$q0mWQ6gqQsSa4fydhM0jqueSGWuk0LDi8woORgd0.n4hlazGiUgmi', 'User Satu',     'user',  NULL, NULL);

INSERT INTO `pcs` (`id`, `nama`, `spesifikasi`, `harga_per_jam`, `status`) VALUES
(1, 'PC-01', 'RTX 3060, i5-12400, 16GB', 6000,  'tersedia'),
(2, 'PC-02', 'RTX 3060, i5-12400, 16GB', 6000,  'tersedia'),
(3, 'PC-03', 'GTX 1650, i3-10100, 8GB',  4000,  'tersedia'),
(4, 'PC-04', 'GTX 1650, i3-10100, 8GB',  4000,  'tersedia'),
(5, 'PC-05', 'RTX 4070, i7-13700, 32GB', 10000, 'tersedia'),
(6, 'PC-06', 'RTX 4070, i7-13700, 32GB', 10000, 'maintenance');

INSERT INTO `bookings` (`id`, `user_id`, `pc_id`, `tanggal`, `jam_mulai`, `durasi_jam`, `total_harga`, `status`, `catatan`) VALUES
(1, 2, 1, '2026-10-17', '13:00:00', 2, 12000, 'pending', 'Test booking');