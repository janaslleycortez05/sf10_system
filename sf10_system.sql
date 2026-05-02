-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2026 at 11:07 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sf10_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `user_id`, `student_name`, `lrn`, `date_of_birth`, `school_year`, `purpose`, `valid_id`, `status`, `delivery_method`, `email_delivery`, `created_at`) VALUES
(1, 2, 'Aslley', '232', NULL, NULL, 'Transfer', NULL, 'Released', NULL, NULL, '2026-05-02 02:44:47'),
(2, 3, 'Aslley', '2018-12535', NULL, NULL, 'Employment', NULL, 'Released', 'Email', NULL, '2026-05-02 02:44:47'),
(3, 4, 'Zuko', '2008-20133', NULL, NULL, 'Personal', NULL, 'Rejected', 'Pickup', NULL, '2026-05-02 02:44:47'),
(4, 3, 'Jan Aslley Cortez', '123456789', '2005-04-02', '2018', 'Employment', 'id_3_1777690570.jpg', 'Pending', 'Pickup', 'aslley@test.com', '2026-05-02 02:56:10'),
(5, 3, 'Aslley', '7888', '2023-01-31', '2026', 'Employment', 'id_3_1777690757.jpg', 'Rejected', 'Pickup', 'aslley@test.com', '2026-05-02 02:59:17');

-- --------------------------------------------------------

--
-- Table structure for table `sf10_records`
--

CREATE TABLE `sf10_records` (
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
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
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'admin@test.com', '123456', 'admin'),
(2, 'Juan Dela Cruz', 'user@test.com', '123456', 'user'),
(3, 'Aslley', 'aslley@test.com', '123456', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sf10_records`
--
ALTER TABLE `sf10_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_name` (`student_name`),
  ADD KEY `idx_lrn` (`lrn`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `sf10_records`
--
ALTER TABLE `sf10_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
