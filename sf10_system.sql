SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ======================
-- ADMIN TABLE
-- ======================
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Optional: ensure columns exist (MySQL 8+)
ALTER TABLE `admin`
  ADD COLUMN IF NOT EXISTS `username` varchar(50),
  ADD COLUMN IF NOT EXISTS `password` varchar(50);

-- ======================
-- USERS TABLE
-- ======================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_relation` varchar(50) DEFAULT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `users`
  ADD COLUMN IF NOT EXISTS `phone` varchar(20),
  ADD COLUMN IF NOT EXISTS `date_of_birth` date,
  ADD COLUMN IF NOT EXISTS `address` text,
  ADD COLUMN IF NOT EXISTS `guardian_name` varchar(100),
  ADD COLUMN IF NOT EXISTS `guardian_relation` varchar(50),
  ADD COLUMN IF NOT EXISTS `guardian_phone` varchar(20);

-- ======================
-- REQUESTS TABLE
-- ======================
CREATE TABLE IF NOT EXISTS `requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `lrn` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `purpose` varchar(100) DEFAULT NULL,
  `valid_id` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `delivery_method` varchar(20) DEFAULT NULL,
  `email_delivery` varchar(150) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ======================
-- SF10 RECORDS TABLE
-- ======================
CREATE TABLE IF NOT EXISTS `sf10_records` (
  `id` int(11) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `lrn` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `sf10_file` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_student_name` (`student_name`),
  KEY `idx_lrn` (`lrn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ======================
-- AUTO INCREMENT FIX
-- ======================
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `sf10_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

COMMIT;