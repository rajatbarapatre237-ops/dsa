-- Upgrade dsaedu_dsa dump for latest LMS (MySQL 5.7 / MariaDB / XAMPP compatible)
-- Run once in phpMyAdmin or: mysql -u root dsaedu_dsa < upgrade_dsaedu_dsa.sql
-- If a step fails with "Duplicate column name", that part is already applied — continue.

USE `dsaedu_dsa`;

-- ---------------------------------------------------------------------------
-- 1) assignement — notes/assignments + subject (Laravel migration 2026_06_09)
-- ---------------------------------------------------------------------------
ALTER TABLE `assignement`
  ADD COLUMN `content_kind` VARCHAR(20) NOT NULL DEFAULT 'assignment' AFTER `status`,
  ADD COLUMN `subject_id` BIGINT UNSIGNED NULL AFTER `content_kind`,
  ADD COLUMN `subject_name` VARCHAR(255) NULL AFTER `subject_id`;

UPDATE `assignement`
SET `content_kind` = 'assignment'
WHERE `content_kind` = '';

-- ---------------------------------------------------------------------------
-- 2) contact_us — read flag + timestamps (Laravel migration 2026_06_06)
-- ---------------------------------------------------------------------------
ALTER TABLE `contact_us`
  ADD COLUMN `is_read` TINYINT(1) NOT NULL DEFAULT 0 AFTER `message`,
  ADD COLUMN `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER `is_read`,
  ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- ---------------------------------------------------------------------------
-- 3) cms_pages — new CMS table (Laravel migration 2026_06_06)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cms_pages` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `slug` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `body` LONGTEXT NULL,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_pages_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------------
-- 4) courses_taken — legacy admin + student web (missing from old dump)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `courses_taken` (
  `sid` INT NOT NULL,
  `courses` VARCHAR(255) NOT NULL DEFAULT '',
  `current_course` VARCHAR(50) NOT NULL DEFAULT '',
  `course_fees` VARCHAR(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `courses_taken` (`sid`, `courses`, `current_course`, `course_fees`)
SELECT
  sd.`id`,
  COALESCE(sd.`CID`, ''),
  COALESCE(sd.`CID`, ''),
  CAST(sd.`course_fees` AS CHAR)
FROM `stud_details` sd
LEFT JOIN `courses_taken` ct ON ct.`sid` = sd.`id`
WHERE ct.`sid` IS NULL;

-- ---------------------------------------------------------------------------
-- 5) Safety nets — PHP creates these at runtime if missing
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `teacher_attendance` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tid` INT NOT NULL,
  `date` DATE NOT NULL,
  `entry_time` DATETIME DEFAULT NULL,
  `exit_time` DATETIME DEFAULT NULL,
  `course` VARCHAR(255) DEFAULT NULL,
  `status` VARCHAR(50) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `idx_tid_date` (`tid`, `date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `academic_sessions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `session_name` VARCHAR(50) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_name` (`session_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Skip if session_name already exists (your dump already has it)
ALTER TABLE `stud_details`
  ADD COLUMN `session_name` VARCHAR(50) NULL DEFAULT NULL AFTER `batch`;

-- ---------------------------------------------------------------------------
-- 6) Laravel infrastructure (optional — or run: php artisan migrate)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` MEDIUMTEXT NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` BIGINT NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
