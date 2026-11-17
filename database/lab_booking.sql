-- ============================================================================
-- LAB BOOKING — skrip basis data
-- Nama database : lab_booking
-- Teknologi     : PHP Native + MySQL (Laragon)
--
-- Catatan: mengimpor ulang berkas ini akan mengatur ulang seluruh tabel dan
-- data demo. Untuk penggunaan pertama, impor melalui phpMyAdmin Laragon.
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+07:00';

CREATE DATABASE IF NOT EXISTS `lab_booking`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `lab_booking`;

DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `labs`;
DROP TABLE IF EXISTS `users`;

-- ----------------------------------------------------------------------------
-- Tabel pengguna
-- ----------------------------------------------------------------------------
CREATE TABLE `users` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `role`       ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    `phone`      VARCHAR(30) DEFAULT NULL,
    `department` VARCHAR(120) DEFAULT NULL,
    `status`     ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role` (`role`),
    KEY `idx_users_status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabel laboratorium
-- ----------------------------------------------------------------------------
CREATE TABLE `labs` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(120) NOT NULL,
    `code`        VARCHAR(20) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `location`    VARCHAR(150) NOT NULL,
    `capacity`    SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `facilities`  TEXT DEFAULT NULL,
    `status`      ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_labs_code` (`code`),
    KEY `idx_labs_status` (`status`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabel booking
-- ----------------------------------------------------------------------------
CREATE TABLE `bookings` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `booking_code` VARCHAR(24) NOT NULL,
    `user_id`      INT UNSIGNED NOT NULL,
    `lab_id`       INT UNSIGNED NOT NULL,
    `booking_date` DATE NOT NULL,
    `start_time`   TIME NOT NULL,
    `end_time`     TIME NOT NULL,
    `purpose`      VARCHAR(255) NOT NULL,
    `participants` SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    `status`       ENUM('pending', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `admin_note`   TEXT DEFAULT NULL,
    `reviewed_by`  INT UNSIGNED DEFAULT NULL,
    `reviewed_at`  DATETIME DEFAULT NULL,
    `created_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_bookings_code` (`booking_code`),
    KEY `idx_bookings_lab_date` (`lab_id`, `booking_date`, `start_time`, `end_time`),
    KEY `idx_bookings_user` (`user_id`),
    KEY `idx_bookings_status` (`status`),
    KEY `idx_bookings_date` (`booking_date`),
    CONSTRAINT `fk_bookings_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_bookings_lab`
        FOREIGN KEY (`lab_id`) REFERENCES `labs` (`id`)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT `fk_bookings_reviewer`
        FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Tabel log aktivitas
-- ----------------------------------------------------------------------------
CREATE TABLE `activity_logs` (
    `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`     INT UNSIGNED DEFAULT NULL,
    `action`      VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) NOT NULL,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_activity_user` (`user_id`),
    KEY `idx_activity_created` (`created_at`),
    CONSTRAINT `fk_activity_user`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- Data pengguna demo
-- Password:
--   admin@labbooking.test  → Admin@123
--   user@labbooking.test   → User@123
--   siti/nadia/dimas       → Budi@123
-- ----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `department`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin LAB BOOKING', 'admin@labbooking.test', '$2y$10$iJdoANKoPYULYykfcyxp6OVNXR2b5KgcSER1X.KyJxWtSOr3d9tTC', 'admin', '081200000001', 'Pengelola Laboratorium', 'active', DATE_SUB(NOW(), INTERVAL 30 DAY), NOW()),
(2, 'Budi Santoso', 'user@labbooking.test', '$2y$10$SydrUg8g0u3UPVbFo4bhX.EuHmNcSyfapo/3pZwwEtIFUD2LvdHly', 'user', '081200000002', 'Teknik Informatika', 'active', DATE_SUB(NOW(), INTERVAL 21 DAY), NOW()),
(3, 'Siti Rahmawati', 'siti@labbooking.test', '$2y$10$lsM4FvI950uBl2..z2Mj/.dlQY7A407gB2wol9MEdgtChF6tpsxue', 'user', '081200000003', 'Desain Komunikasi Visual', 'active', DATE_SUB(NOW(), INTERVAL 18 DAY), NOW()),
(4, 'Dimas Pratama', 'dimas@labbooking.test', '$2y$10$lsM4FvI950uBl2..z2Mj/.dlQY7A407gB2wol9MEdgtChF6tpsxue', 'user', '081200000004', 'Teknik Komputer', 'inactive', DATE_SUB(NOW(), INTERVAL 15 DAY), NOW()),
(5, 'Nadia Putri', 'nadia@labbooking.test', '$2y$10$lsM4FvI950uBl2..z2Mj/.dlQY7A407gB2wol9MEdgtChF6tpsxue', 'user', '081200000005', 'Pendidikan Multimedia', 'active', DATE_SUB(NOW(), INTERVAL 3 DAY), NOW());

-- ----------------------------------------------------------------------------
-- Data laboratorium demo
-- ----------------------------------------------------------------------------
INSERT INTO `labs` (`id`, `name`, `code`, `description`, `location`, `capacity`, `facilities`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Lab Komputer 1', 'LK-01', 'Laboratorium komputasi utama untuk praktikum pemrograman, basis data, dan tugas akhir mahasiswa.', 'Gedung A, Lantai 2', 40, '40 PC Workstation, Jaringan Gigabit, Proyektor, AC, Papan Tulis', 'active', DATE_SUB(NOW(), INTERVAL 25 DAY), NOW()),
(2, 'Lab Komputer 2', 'LK-02', 'Ruang komputasi pendukung untuk kelas paralel, responsi, dan pelatihan perangkat lunak.', 'Gedung A, Lantai 2', 36, '36 PC Workstation, Jaringan Gigabit, Proyektor, AC', 'active', DATE_SUB(NOW(), INTERVAL 25 DAY), NOW()),
(3, 'Lab Multimedia', 'LM-01', 'Laboratorium desain dan produksi media digital dengan perangkat kreatif untuk konten visual.', 'Gedung B, Lantai 1', 30, '30 iMac, Pen Tablet, Kamera Mirrorless, Studio Lighting, AC', 'active', DATE_SUB(NOW(), INTERVAL 20 DAY), NOW()),
(4, 'Lab Jaringan', 'LJ-01', 'Laboratorium khusus untuk praktikum jaringan komputer, keamanan siber, dan konfigurasi server.', 'Gedung A, Lantai 3', 24, '24 PC, Cisco Router & Switch, Rack Server, Kabel UTP, AC', 'active', DATE_SUB(NOW(), INTERVAL 20 DAY), NOW()),
(5, 'Lab Robotika', 'LR-01', 'Laboratorium pengembangan robotika dan sistem tertanam untuk penelitian mahasiswa.', 'Gedung C, Lantai 1', 20, 'Kit Robotika, 3D Printer, Mikrokontroler, Meja Kerja, AC', 'inactive', DATE_SUB(NOW(), INTERVAL 5 DAY), NOW());

-- ----------------------------------------------------------------------------
-- Data booking demo (tanggal relatif terhadap hari ini)
-- ----------------------------------------------------------------------------
INSERT INTO `bookings` (`id`, `booking_code`, `user_id`, `lab_id`, `booking_date`, `start_time`, `end_time`, `purpose`, `participants`, `status`, `admin_note`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 'BK-DEMO-001', 3, 4, CURDATE(), '10:00:00', '12:00:00', 'Praktikum Jaringan Komputer — konfigurasi routing statis', 24, 'approved', 'Disetujui. Harap menjaga perangkat tetap rapi.', 1, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), NOW()),
(2, 'BK-DEMO-002', 2, 1, DATE_ADD(CURDATE(), INTERVAL 1 DAY), '09:00:00', '11:00:00', 'Praktikum Pemrograman Web — sesi responsi proyek akhir', 32, 'approved', 'Disetujui. Dosen pendamping sudah dikonfirmasi.', 1, DATE_SUB(NOW(), INTERVAL 1 HOUR), DATE_SUB(NOW(), INTERVAL 1 DAY), NOW()),
(3, 'BK-DEMO-003', 2, 3, DATE_ADD(CURDATE(), INTERVAL 2 DAY), '13:00:00', '15:00:00', 'Workshop desain antarmuka aplikasi mobile', 20, 'pending', NULL, NULL, NULL, DATE_SUB(NOW(), INTERVAL 2 HOUR), NOW()),
(4, 'BK-DEMO-004', 5, 2, DATE_ADD(CURDATE(), INTERVAL 3 DAY), '08:00:00', '10:00:00', 'Pelatihan dasar keamanan siber himpunan mahasiswa', 30, 'pending', NULL, NULL, NULL, DATE_SUB(NOW(), INTERVAL 4 HOUR), NOW()),
(5, 'BK-DEMO-005', 3, 3, DATE_ADD(CURDATE(), INTERVAL 4 DAY), '15:30:00', '17:00:00', 'Produksi konten video profil program studi', 18, 'approved', 'Disetujui. Silakan koordinasi dengan pengelola studio.', 1, DATE_SUB(NOW(), INTERVAL 12 HOUR), DATE_SUB(NOW(), INTERVAL 3 DAY), NOW()),
(6, 'BK-DEMO-006', 3, 2, DATE_SUB(CURDATE(), INTERVAL 3 DAY), '13:00:00', '15:00:00', 'Ujian praktik basis data lanjutan', 35, 'approved', 'Pelaksanaan berjalan lancar.', 1, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), NOW()),
(7, 'BK-DEMO-007', 2, 1, DATE_SUB(CURDATE(), INTERVAL 7 DAY), '10:00:00', '12:00:00', 'Kegiatan studi klub pemrograman', 15, 'rejected', 'Bentrok dengan jadwal pemeliharaan laboratorium. Silakan ajukan tanggal lain.', 1, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), NOW()),
(8, 'BK-DEMO-008', 5, 3, DATE_SUB(CURDATE(), INTERVAL 10 DAY), '09:00:00', '11:00:00', 'Workshop fotografi dan editing', 22, 'completed', 'Terlaksana dengan baik.', 1, DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), NOW()),
(9, 'BK-DEMO-009', 2, 3, CURDATE(), '16:00:00', '18:00:00', 'Rapat koordinasi tim media kampus', 12, 'pending', NULL, NULL, NULL, DATE_SUB(NOW(), INTERVAL 1 HOUR), NOW()),
(10, 'BK-DEMO-010', 2, 4, DATE_ADD(CURDATE(), INTERVAL 5 DAY), '09:00:00', '11:00:00', 'Praktikum sistem operasi — sesi tambahan', 20, 'cancelled', 'Dibatalkan oleh pemohon karena perubahan jadwal kelas.', NULL, NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW());

-- ----------------------------------------------------------------------------
-- Log aktivitas demo
-- ----------------------------------------------------------------------------
INSERT INTO `activity_logs` (`user_id`, `action`, `description`, `created_at`) VALUES
(1, 'booking_approved', 'Admin LAB BOOKING menyetujui booking BK-DEMO-002.', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(2, 'booking_created', 'Budi Santoso mengajukan booking BK-DEMO-003 di Lab Multimedia.', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(5, 'booking_created', 'Nadia Putri mengajukan booking BK-DEMO-004 di Lab Komputer 2.', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(3, 'login', 'Siti Rahmawati masuk ke LAB BOOKING.', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(2, 'booking_created', 'Budi Santoso mengajukan booking BK-DEMO-002 di Lab Komputer 1.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'booking_created', 'Siti Rahmawati mengajukan booking BK-DEMO-005 di Lab Multimedia.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 'lab_created', 'Admin LAB BOOKING menambahkan laboratorium Lab Robotika.', DATE_SUB(NOW(), INTERVAL 5 DAY));
