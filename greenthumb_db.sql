-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 10:57 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `greenthumb_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','employee','admin') DEFAULT 'customer',
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `firstname` varchar(50) NOT NULL,
  `middlename` varchar(50) DEFAULT NULL,
  `lastname` varchar(50) NOT NULL,
  `reset_otp` varchar(6) DEFAULT NULL,
  `reset_expires_at` datetime DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `deactivation_reason` text DEFAULT NULL,
  `session_status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `created_at`, `firstname`, `middlename`, `lastname`, `reset_otp`, `reset_expires_at`, `profile_image`, `reset_token`, `status`, `deactivation_reason`, `session_status`) VALUES
(55, 'Pablito', '$2y$10$/X1KgBjpXOiWHjK2JILqb.tKjU9Oy3MK7/2wd5y7heZnQtMS6DXMO', 'customer', 'maximoffquicksilver8@gmail.com', '2025-04-09 16:18:46', 'Brandon Kyle ', 'Craft', 'Rojas', NULL, NULL, 'Kyle.jpg', NULL, 'active', NULL, 'active'),
(57, 'Kyle', '$2y$10$zH6oG1dyK361hK/07fPX3ONyYJ2865MEZSkyXodiOC3UtEvuhZo1O', 'admin', 'admin@gmail.com', '2025-04-09 16:26:28', 'Admin', NULL, 'Master', NULL, NULL, 'WIN_20250319_00_11_02_Pro.jpg', NULL, 'active', NULL, 'active'),
(58, 'Jackie', '$2y$10$dZIW3oymXq6pOsjJGKcmx.j0vhpre.ZIml6ClZT27Gz/vih2OzGXG', 'employee', 'maribelmontiague04@gmail.com', '2025-04-09 16:28:40', 'Maribel', NULL, 'Montiague', NULL, NULL, 'jackie.jpg', NULL, 'active', NULL, 'active'),
(60, 'MyLoves', '$2y$10$WS4zev7E3H8ZVzuwtQi2Zu5zrH3Ge2G9x4S9q86X26oaoy90xStqy', 'employee', 'brandonkylerojas1@gmail.com', '2025-04-17 09:36:26', 'Wanda', NULL, 'Maximoff', NULL, NULL, 'OLY_7256 FINAL.jpg', NULL, 'active', NULL, 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
