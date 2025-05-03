-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 02, 2025 at 01:27 PM
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
-- Table structure for table `account_logs`
--

CREATE TABLE `account_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('deactivate','delete') NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `account_logs`
--

INSERT INTO `account_logs` (`id`, `user_id`, `action`, `reason`, `created_at`) VALUES
(6, 58, 'deactivate', 'You have violate some rules and policy!', '2025-04-23 18:39:46');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` datetime DEFAULT current_timestamp(),
  `type` enum('login','logout') DEFAULT 'login'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `login_time`, `type`) VALUES
(9, 58, '2025-04-28 18:56:32', 'login'),
(10, 58, '2025-04-28 18:56:39', 'logout'),
(11, 60, '2025-04-28 18:58:38', 'login'),
(12, 60, '2025-04-28 18:59:08', 'logout'),
(13, 58, '2025-04-28 18:59:48', 'login'),
(14, 57, '2025-04-28 19:08:19', 'logout'),
(15, 55, '2025-04-28 20:08:17', 'logout'),
(16, 58, '2025-04-28 21:13:26', 'login'),
(17, 58, '2025-04-29 00:17:16', 'logout'),
(18, 55, '2025-04-29 00:17:38', 'logout'),
(19, 57, '2025-04-29 00:47:46', 'logout'),
(20, 55, '2025-04-29 00:47:54', 'logout'),
(21, 58, '2025-04-30 13:07:35', 'login'),
(22, 58, '2025-04-30 14:58:39', 'logout'),
(23, 57, '2025-04-30 15:02:43', 'logout'),
(24, 58, '2025-04-30 15:02:51', 'login'),
(25, 58, '2025-04-30 16:19:17', 'logout');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `recipient_id`, `message`, `created_at`, `is_read`) VALUES
(57, 58, 0, 'Hello', '2025-04-17 09:19:13', 0),
(58, 58, 0, 'ssdsds', '2025-04-17 09:20:41', 0),
(59, 58, 57, 'Hello', '2025-04-17 09:33:05', 0),
(60, 58, 57, 'What are you doing?', '2025-04-17 09:33:36', 0),
(61, 60, 57, 'hey', '2025-04-17 09:38:04', 0),
(62, 57, 58, 'Hello', '2025-04-17 09:42:55', 0),
(63, 57, 60, 'What is it?', '2025-04-17 09:43:07', 0),
(64, 57, 60, 'Hey what\'s up?', '2025-04-17 09:56:31', 0),
(65, 57, 60, 'Hello', '2025-04-17 10:32:53', 0),
(66, 60, 57, 'Hello admin?', '2025-04-17 10:58:24', 0),
(67, 57, 58, 'Hello employee', '2025-04-22 23:10:26', 0),
(68, 57, 58, 'Your account has been deactivated. Reason: Deactivate', '2025-04-22 23:12:04', 0),
(69, 57, 58, 'Your account has been deactivated. Reason: Deactivate', '2025-04-22 23:12:55', 0),
(70, 57, 58, 'Your account has been deactivated. Reason: Account on hold\r\n', '2025-04-22 23:21:57', 0),
(71, 57, 58, 'Your account has been deactivated. Reason: Your account is temporarily being hold due to a violation of our policy rights.', '2025-04-22 23:33:47', 0),
(72, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-22 23:43:17', 0),
(73, 57, 58, 'Your account has been deactivated. Reason: Your account has been deactivated.\r\n', '2025-04-22 23:52:12', 0),
(74, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:03:29', 0),
(75, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:08:47', 0),
(76, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:08:52', 0),
(77, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:12:29', 0),
(78, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:14:57', 0),
(79, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:19:22', 0),
(80, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:38:39', 0),
(81, 57, 58, 'Your account has been deactivated. Reason: \r\n', '2025-04-23 00:40:49', 0),
(82, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:42:15', 0),
(83, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:42:29', 0),
(84, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:45:43', 0),
(85, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:46:07', 0),
(86, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 00:49:44', 0),
(87, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 18:37:43', 0),
(88, 57, 58, 'Your account has been deactivated. Reason: You have violate some rules and policy!', '2025-04-23 18:39:41', 0),
(89, 57, 60, 'Your account has been deactivated. Reason: ', '2025-04-23 18:43:18', 0),
(90, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 18:43:51', 0),
(91, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 19:03:46', 0),
(92, 57, 55, 'Your account has been deactivated. Reason: ', '2025-04-23 19:22:01', 0),
(93, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 19:25:01', 0),
(94, 57, 55, 'Your account has been deactivated. Reason: ', '2025-04-23 19:25:56', 0),
(95, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 19:26:10', 0),
(96, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:11:16', 0),
(97, 57, 55, 'Your account has been deactivated. Reason: ', '2025-04-23 20:15:13', 0),
(98, 57, 60, 'Your account has been deactivated. Reason: ', '2025-04-23 20:15:29', 0),
(99, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:19:49', 0),
(100, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:23:45', 0),
(101, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:24:31', 0),
(102, 57, 55, 'Your account has been deactivated. Reason: ', '2025-04-23 20:24:40', 0),
(103, 57, 60, 'Your account has been deactivated. Reason: ', '2025-04-23 20:24:51', 0),
(104, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:25:57', 0),
(105, 57, 58, 'hi', '2025-04-23 20:26:32', 0),
(106, 57, 58, 'hi', '2025-04-23 20:26:33', 0),
(107, 57, 58, 'Hey', '2025-04-23 20:26:46', 0),
(108, 57, 60, 'Heloo', '2025-04-23 20:26:58', 0),
(109, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:27:53', 0),
(110, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:30:13', 0),
(111, 57, 55, 'Your account has been deactivated. Reason: ', '2025-04-23 20:31:27', 0),
(112, 57, 60, 'Your account has been deactivated. Reason: ', '2025-04-23 20:31:40', 0),
(113, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:33:25', 0),
(114, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:35:24', 0),
(115, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:39:56', 0),
(116, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:40:54', 0),
(117, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:40:59', 0),
(118, 57, 60, 'Your account has been deactivated. Reason: ', '2025-04-23 20:41:14', 0),
(119, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:41:47', 0),
(120, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:44:57', 0),
(121, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:45:25', 0),
(122, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:48:54', 0),
(123, 57, 58, 'Your account has been deactivated. Reason: ', '2025-04-23 20:52:48', 0),
(124, 57, 55, 'Your account has been deactivated. Reason: ', '2025-04-23 20:53:26', 0),
(125, 58, 57, 'hi', '2025-04-28 14:26:59', 0),
(126, 58, 57, 'hi', '2025-04-28 14:27:11', 0),
(127, 58, 57, 'hi', '2025-04-28 14:27:28', 0),
(128, 58, 57, 'Hello', '2025-04-28 14:27:32', 0),
(129, 58, 57, 'Hello', '2025-04-28 14:27:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `status` enum('pending','shipped','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `price`, `stock`, `image`, `employee_id`) VALUES
(9, 'Fries', 'yummy fries', 'Vegetable', 85.00, 0, 'Fries.jpg', 58),
(10, 'Mang Juan', 'A spicy junk food that is obssessed by many', 'Flowers', 250.00, 0, 'Cookies and Cream.jpg', 58),
(11, 'Burger Steak', 'Yummy Burger Stake', 'Miscellaneous', 85.00, 0, 'Burger Steak.jpg', 58),
(12, 'Coke Float', 'Drink Soda Coke Float', 'Vegetable', 75.00, 0, 'Jollibee Coke Float.jpg', 58),
(13, 'Jollibee Spaghetti', 'Jollibee Spaghetti', 'Herb', 90.00, 0, 'Jollibee Spaghetti.jpg', 58),
(14, 'Choco Sundae', 'Jollibee Choco Sundae', 'Herb', 65.00, 0, 'Choco Sundae.jpg', 58);

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
  `session_status` enum('active','inactive') DEFAULT 'active',
  `contact` varchar(15) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `created_at`, `firstname`, `middlename`, `lastname`, `reset_otp`, `reset_expires_at`, `profile_image`, `reset_token`, `status`, `deactivation_reason`, `session_status`, `contact`, `address`) VALUES
(55, 'Pablito', '$2y$10$/X1KgBjpXOiWHjK2JILqb.tKjU9Oy3MK7/2wd5y7heZnQtMS6DXMO', 'customer', 'maximoffquicksilver8@gmail.com', '2025-04-09 16:18:46', 'Brandon Kyle ', 'Craft', 'Rojas', NULL, NULL, 'Kyle.jpg', NULL, 'active', NULL, 'active', '09979431921', 'Phase 2 Lunzuran Zamboanga City'),
(57, 'Kyle', '$2y$10$zH6oG1dyK361hK/07fPX3ONyYJ2865MEZSkyXodiOC3UtEvuhZo1O', 'admin', 'admin@gmail.com', '2025-04-09 16:26:28', 'Admin', NULL, 'Master', NULL, NULL, 'WIN_20250319_00_11_02_Pro.jpg', NULL, 'active', NULL, 'active', NULL, NULL),
(58, 'Jackie', '$2y$10$dZIW3oymXq6pOsjJGKcmx.j0vhpre.ZIml6ClZT27Gz/vih2OzGXG', 'employee', 'maribelmontiague04@gmail.com', '2025-04-09 16:28:40', 'Maribel', NULL, 'Montiague', NULL, NULL, 'jackie.jpg', NULL, 'active', NULL, 'active', NULL, NULL),
(60, 'MyLoves', '$2y$10$6f8dW0krAiuNgHfi2FHXD.o3UKuZmvf.9SK6yAPUk6XtuvdJ4IcXu', 'employee', 'brandonkylerojas1@gmail.com', '2025-04-17 09:36:26', 'Wanda', NULL, 'Maximoff', NULL, NULL, 'OLY_7256 FINAL.jpg', NULL, 'active', NULL, 'active', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account_logs`
--
ALTER TABLE `account_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

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
-- AUTO_INCREMENT for table `account_logs`
--
ALTER TABLE `account_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_logs`
--
ALTER TABLE `account_logs`
  ADD CONSTRAINT `account_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
