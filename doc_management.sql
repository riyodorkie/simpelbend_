-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 05:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `doc_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `activity` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `activity`, `created_at`) VALUES
(26, 1, 'User login', '2025-06-30 08:27:21'),
(27, 1, 'User login', '2025-07-01 00:48:33'),
(28, 3, 'User login', '2025-07-01 00:50:46'),
(29, 3, 'Mengajukan dokumen: A', '2025-07-01 00:56:10'),
(30, 3, 'User login', '2025-07-01 00:56:42'),
(31, 4, 'User login', '2025-07-01 00:56:59'),
(32, 4, 'Mengajukan dokumen: asdf', '2025-07-01 00:57:15'),
(33, 1, 'User login', '2025-07-01 00:57:26'),
(34, 1, 'Mengupdate status dokumen ID 5 menjadi approved', '2025-07-01 00:57:55'),
(35, 3, 'User login', '2025-07-01 00:58:20'),
(36, 4, 'User login', '2025-07-01 00:58:54'),
(37, 1, 'Mengupdate status dokumen ID 6 menjadi rejected', '2025-07-01 01:00:37'),
(38, 1, 'Menghapus dokumen ID 6', '2025-07-01 01:42:15'),
(39, 1, 'Menghapus dokumen ID 5', '2025-07-01 01:42:34'),
(40, 3, 'User login', '2025-07-01 01:43:21'),
(41, 3, 'Mengajukan dokumen: AA', '2025-07-01 01:43:37'),
(42, 1, 'User login', '2025-07-01 03:54:29');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `user_id`, `title`, `description`, `file_path`, `original_filename`, `status`, `admin_notes`, `uploaded_at`) VALUES
(7, 3, 'AA (Dokumen 1)', 'AA', '../uploads/1751334217_0_KKPD.xlsx', 'KKPD.xlsx', 'pending', NULL, '2025-07-01 01:43:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'creator', '$2y$10$dKyB6LHjCMT4jHfI49Aip.dGlWLWNhZPT2EH1snBw/ZwkUQMXKoUy', 'Creator', 'admin', '2025-06-30 07:14:10'),
(3, 'test', '$2y$10$AoKsn8rNBv0yGqXXoko2XeiQ5g3xpgRDyfgI8SFGWYXbofi2iD7ri', 'Test', 'user', '2025-06-30 07:19:39'),
(4, 'test2', '$2y$10$JOIcjrrB5SLDduKDrk3qtO322K7a05122YEsxxi40SfLZGb77vJS2', 'TESTER 2', 'user', '2025-06-30 08:25:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
