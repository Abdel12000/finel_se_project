-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2025 at 02:23 PM
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
-- Database: `plants`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `equipment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `employee_id`, `equipment_id`, `user_id`, `assigned_at`) VALUES
(1, 2, 6, 4, '2025-07-22 14:16:26'),
(2, 3, 6, 4, '2025-07-22 14:16:26'),
(3, 4, 6, 4, '2025-07-22 14:16:26'),
(4, 2, 12, 7, '2025-07-22 17:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `plant_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `user_id`) VALUES
(6, 'ALL', 0),
(7, 'INDOOR', 0),
(8, 'OUTDOOR', 0),
(9, 'FLOWERING', 0),
(10, 'SUCCULENTS', 0),
(11, 'CACTUS', 0),
(12, 'HERBS', 0),
(14, 'apple', 4);

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `delivery_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `status` enum('preparing','in_transit','delivered') DEFAULT 'preparing',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deliveries`
--

INSERT INTO `deliveries` (`delivery_id`, `order_id`, `name`, `phone`, `address`, `status`, `updated_at`) VALUES
(1, 1, '', '', '', 'in_transit', '2025-07-23 14:06:15'),
(2, 5, 'amin', '+96181818181', 'akakr', 'preparing', '2025-07-15 13:37:26'),
(3, 6, 'amin', '+96181818181', 'akakr', 'delivered', '2025-07-23 14:06:12'),
(4, 8, 'marie', '+971262727', 'akkar lebanon', 'preparing', '2025-07-17 10:49:56'),
(5, 9, 'marie', '+971262727', 'akkar lebanon', 'preparing', '2025-07-17 10:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `employee_salary`
--

CREATE TABLE `employee_salary` (
  `salary_id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `base_salary` decimal(10,2) NOT NULL,
  `bonus` decimal(10,2) DEFAULT 0.00,
  `deductions` decimal(10,2) DEFAULT 0.00,
  `total_salary` decimal(10,2) GENERATED ALWAYS AS (`base_salary` + `bonus` - `deductions`) STORED,
  `salary_month` varchar(20) NOT NULL,
  `payment_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_salary`
--

INSERT INTO `employee_salary` (`salary_id`, `user_id`, `base_salary`, `bonus`, `deductions`, `salary_month`, `payment_date`, `created_at`, `updated_at`) VALUES
(1, 3, 40.00, 0.00, 0.00, '2025-08', '2025-07-17', '2025-07-16 17:51:27', '2025-07-16 17:51:27'),
(2, 3, 40.00, 0.00, 0.00, '2025-08', '2025-07-17', '2025-07-16 17:52:18', '2025-07-16 17:52:18'),
(3, 4, 20.00, 0.00, 0.00, '2025-07', '2025-07-18', '2025-07-18 13:24:32', '2025-07-18 13:24:32'),
(4, 2, 12.00, 0.00, 0.00, '2025-07', '2025-07-22', '2025-07-21 13:59:48', '2025-07-21 13:59:48');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `description` text DEFAULT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `area` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `land_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `name`, `description`, `hourly_rate`, `area`, `image`, `location`, `land_image`) VALUES
(1, 'mnsms', 'mm', 5.00, NULL, NULL, NULL, NULL),
(2, 'Greenhouse Heater', 'Portable electric heater for greenhouse temperature', 15.00, NULL, NULL, NULL, NULL),
(6, 'aaaaa', 'aaa', 2.00, NULL, NULL, NULL, NULL),
(12, 'aaaaa', 'ss', 2.00, NULL, 'equipment/1753206450_bots.jpg', 'akkar', 'equipment/1753206450_land_bots.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_bookings`
--

CREATE TABLE `equipment_bookings` (
  `booking_id` int(10) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `equipment_bookings`
--

INSERT INTO `equipment_bookings` (`booking_id`, `equipment_id`, `user_id`, `start_at`, `end_at`, `status`, `created_at`) VALUES
(1, 1, 2, '2025-07-08 19:05:00', '2025-07-08 19:05:00', 'pending', '2025-07-08 17:05:43'),
(6, 6, 4, '2025-07-16 20:17:00', '2025-07-17 20:17:00', 'pending', '2025-07-15 19:17:43'),
(12, 12, 7, '2025-07-18 18:47:00', '2025-07-24 18:47:00', 'pending', '2025-07-22 17:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_payments`
--

CREATE TABLE `equipment_payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `equipment_id` int(10) UNSIGNED NOT NULL,
  `payment_method` enum('wish','omt') NOT NULL,
  `payment_number` varchar(50) NOT NULL,
  `payment_image` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `feedback_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `admin_response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` varchar(10) DEFAULT 'Unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`feedback_id`, `user_id`, `message`, `admin_response`, `created_at`, `is_read`) VALUES
(37, 4, 'An employee has been assigned to your equipment: aaaaa.', 'Employee Name: amin, Phone: +96181818181 assigned.', '2025-07-21 14:05:32', 'Unread'),
(38, 2, 'You have been assigned to equipment: aaaaa.', 'Please proceed with the assignment.', '2025-07-21 14:05:51', 'Unread'),
(39, 4, 'You have been assigned to equipment: aaaaa.', 'Please proceed with the assignment.', '2025-07-21 14:08:25', 'Unread'),
(41, 7, 'z', 'z', '2025-07-21 14:28:02', 'Unread'),
(42, 2, 'ss', 'hello omar', '2025-07-21 16:31:08', 'Unread'),
(43, 3, 'You have been assigned to equipment: aaaaa.', 'Please proceed with the assignment.', '2025-07-22 14:16:26', 'Unread'),
(44, 7, 'An employee has been assigned to your equipment: aaaaa.', 'Employee Name: omar, Phone: +96181818181 assigned.', '2025-07-22 17:48:39', 'Unread'),
(46, 4, 'Delivery status updated to in_transit', 'in_transit', '2025-07-23 14:06:07', 'Unread'),
(47, 4, 'Delivery status updated to delivered', 'delivered', '2025-07-23 14:06:12', 'Unread'),
(48, 2, 'Delivery status updated to in_transit', 'in_transit', '2025-07-23 14:06:15', 'Unread'),
(50, 9, 's', 'aaaaa', '2025-07-24 12:23:06', 'Unread');

-- --------------------------------------------------------

--
-- Table structure for table `monthly_subscriptions`
--

CREATE TABLE `monthly_subscriptions` (
  `subscription_id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','cancelled','expired') DEFAULT 'active',
  `amount` decimal(10,2) NOT NULL,
  `sub_image` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monthly_subscriptions`
--

INSERT INTO `monthly_subscriptions` (`subscription_id`, `user_id`, `start_date`, `end_date`, `status`, `amount`, `sub_image`, `created_at`, `updated_at`) VALUES
(1, 4, '2025-07-15', '2025-08-15', 'active', 20.00, 'uploads/subscriptions/sub_1752597418_687683aa93923', '2025-07-15 16:36:58', '2025-07-15 16:36:58'),
(2, 7, '2025-07-17', '2025-08-17', 'active', 20.00, 'uploads/subscriptions/sub_1752745444_6878c5e4dd959', '2025-07-17 09:44:04', '2025-07-17 09:44:04');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(10) UNSIGNED NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(120) NOT NULL,
  `body` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `sender_id`, `title`, `body`, `created_at`, `read_at`) VALUES
(1, 2, 'feedback about services', 'hello how are you', '2025-07-09 07:55:01', NULL),
(2, 2, 'account activation ', 'please activate my account', '2025-07-10 08:02:44', NULL),
(3, 3, 'equipment services bad ', 'bad exp', '2025-07-11 16:44:51', NULL),
(4, 3, 'equipment services bad ', 'bad exp', '2025-07-12 09:09:07', NULL),
(5, 3, 'Equipment Assignment', 'You have been assigned to Equipment ID: 6. Please check your dashboard for details.', '2025-07-16 17:37:34', NULL),
(6, 7, 'activate', 'account', '2025-07-17 10:28:14', NULL),
(7, 7, 'ss', 's', '2025-07-17 10:31:09', NULL),
(8, 2, 'ss', 'ss', '2025-07-21 16:30:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 20.00, 'pending', '2025-07-09 07:30:40', '2025-07-09 07:30:40'),
(3, 4, 40.00, 'pending', '2025-07-15 13:16:12', '2025-07-15 13:16:12'),
(4, 4, 20.00, 'pending', '2025-07-15 13:17:15', '2025-07-15 13:17:15'),
(5, 4, 20.00, 'pending', '2025-07-15 13:37:26', '2025-07-15 13:37:26'),
(6, 4, 40.00, 'pending', '2025-07-16 16:52:37', '2025-07-16 16:52:37'),
(7, 7, 1.00, 'pending', '2025-07-17 09:34:44', '2025-07-17 09:34:44'),
(8, 7, 60.00, 'pending', '2025-07-17 10:49:56', '2025-07-17 10:49:56'),
(9, 7, 20.00, 'pending', '2025-07-17 10:52:01', '2025-07-17 10:52:01'),
(10, 9, 20.00, 'pending', '2025-07-24 11:15:52', '2025-07-24 11:15:52'),
(11, 9, 20.00, 'pending', '2025-07-24 11:18:23', '2025-07-24 11:18:23'),
(12, 9, 40.00, 'pending', '2025-07-24 12:20:47', '2025-07-24 12:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `plant_id` int(10) UNSIGNED NOT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `price_each` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `plant_id`, `quantity`, `price_each`) VALUES
(1, 1, 7, 3, 30.00),
(3, 3, 10, 1, 20.00),
(7, 6, 11, 1, 20.00),
(8, 7, 15, 1, 1.00),
(11, 10, 12, 1, 20.00),
(12, 11, 13, 1, 20.00),
(13, 12, 12, 1, 20.00),
(14, 12, 13, 1, 20.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `method` enum('wish','omt') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `address` varchar(50) NOT NULL,
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_image` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `user_id`, `method`, `amount`, `address`, `status`, `payment_image`, `created_at`) VALUES
(7, 11, 9, 'wish', 20.00, 'ss', 'pending', 'payment/6882167fcfced-coin.jpg', '2025-07-24 11:19:50'),
(8, 12, 9, 'wish', 80.00, 'akakr', 'pending', 'payment/6882251fd2237-coin.jpg', '2025-07-24 12:20:47');

-- --------------------------------------------------------

--
-- Table structure for table `plants`
--

CREATE TABLE `plants` (
  `plant_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_qty` int(10) UNSIGNED DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `status` enum('active','not_active') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plants`
--

INSERT INTO `plants` (`plant_id`, `category_id`, `name`, `description`, `image_url`, `price`, `stock_qty`, `created_at`, `updated_at`, `user_id`, `status`) VALUES
(7, NULL, 'plants1', 'ss', 'plant/686d4c16a79013.66385474.jpg', 20.00, 2, '2025-07-08 16:49:26', '2025-07-08 16:49:26', 1, 'active'),
(10, 9, 'Peace lily', 'w', 'plant/6874074cdc7328.97577574.jpg', 20.00, 10, '2025-07-13 19:21:48', '2025-07-24 11:46:28', 1, 'not_active'),
(11, 12, 'Peace lily', 'beautiful gardenia', 'plant/6874076ece4d38.57189297.jpg', 20.00, 10, '2025-07-13 19:22:22', '2025-07-24 11:47:03', 1, 'not_active'),
(12, 7, 'Peace lily', 's', 'plant/6874077f9dc757.16797712.jpg', 20.00, 8, '2025-07-13 19:22:39', '2025-07-24 12:20:47', 1, 'active'),
(13, 8, 'Peace lily', 's', 'plant/68740791dc9012.24200092.jpg', 20.00, 8, '2025-07-13 19:22:57', '2025-07-24 12:20:47', 1, 'active'),
(14, 10, 'Peace lily', 's', 'plant/687407a80984f5.40025280.jpg', 20.00, 10, '2025-07-13 19:23:20', '2025-07-13 19:23:20', 1, 'active'),
(15, 14, 'plants1', 'ss', 'plant/6876896705e481.28135460.gif', 1.00, 1, '2025-07-15 17:01:27', '2025-07-17 09:34:44', 4, 'active'),
(16, NULL, '505', '505', 'plant/6878c6236b7267.89205050.gif', 30.00, 4, '2025-07-17 09:45:07', '2025-07-17 09:45:07', 7, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `plant_ratings`
--

CREATE TABLE `plant_ratings` (
  `rating_id` int(10) UNSIGNED NOT NULL,
  `plant_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `stars` tinyint(3) UNSIGNED DEFAULT NULL CHECK (`stars` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `role` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('inactive','active') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `phone` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password_hash`, `role`, `status`, `created_at`, `updated_at`, `phone`, `address`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$Wsy4i8zTQhS/lVRqrnXi8eAG1BCtEtC6QVRV34Cjsi2tj8izpWtPa', 1, 'active', '2025-07-07 18:36:25', '2025-07-07 17:36:39', '', ''),
(2, 'omar', 'omar@gmail.com', '$2y$10$dGMsxucBWYOdE7oZOFlSB.s5MNEQB6pkyyWfs6EuGPV9WszIzuRri', 2, 'active', '2025-07-08 14:32:17', '2025-07-15 19:13:35', '+96181818181', ''),
(3, 'amina', 'amina@gmail.com', '$2y$10$e/P97wjTTnW8fkWCfj1zyuau2Md2PfXHlgnwtMOAEEiP1UpuRcZKi', 2, 'active', '2025-07-10 09:15:58', '2025-07-16 17:26:14', '+96181818181', 'akkar'),
(4, 'amin', 'amin@gmail.com', '$2y$10$MFmI6u7CeGzlNkmKifcGWuRIMKlFMyF94OSH/WTm85t1NYSoE9v.y', 2, 'active', '2025-07-14 14:05:36', '2025-07-18 13:23:54', '+96181818181', 'akakr'),
(5, 'mohamad', 'mo@gmail.com', '$2y$10$Yn2b.x77zT0itzD4DYLbjOgDHk0kaltlBg0x6tatmXC7WEYgPZWS.', 0, 'active', '2025-07-16 10:37:17', '2025-07-16 10:37:17', '+96182181811', 'akkar'),
(6, '7amoudi', '7amoudi@gmail.com', '$2y$10$sD9EegO8KZs5VP45B3Yx.ek5X7HbnCLcw869OwbvGhRSwTaCifieG', 0, 'active', '2025-07-16 17:57:36', '2025-07-16 17:57:36', '+96181818181', 'akkar'),
(7, 'marie', 'marie@gmail.com', '$2y$10$Js7Gh/YtctMU8cQX5UoOF.6H2LvSKbWUBx0ESwJQyYDC1V9PXnj6y', 0, 'active', '2025-07-17 10:31:19', '2025-07-17 10:29:08', '+971262727', 'akkar lebanon'),
(8, 'amir', 'amira@gmail.com', '$2y$10$3V.EsoFw5aqvuEFUHw4nyOfFjx7Iu97MfuP.vIyiR8WQsM0quTzR.', 0, 'active', '2025-07-17 10:48:10', '2025-07-17 10:48:10', '+9617172727', 'Tripoli, Lebanon, Lebanon, akkar, 122, Lebanon'),
(9, 'khaled ', 'khaled@gmail.com', '$2y$10$iJlyC3rlvcOOt7bhl0kO/e8FAoLqLMdyIvEeQ9DRkWnS7P3OqS04K', 0, 'active', '2025-07-24 12:15:11', '2025-07-24 12:15:11', '+96181818181', 'akakr');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `plant_id` (`plant_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`delivery_id`),
  ADD KEY `fk_delivery_order` (`order_id`);

--
-- Indexes for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD PRIMARY KEY (`salary_id`),
  ADD KEY `fk_user_salary` (`user_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`);

--
-- Indexes for table `equipment_bookings`
--
ALTER TABLE `equipment_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_booking_equipment` (`equipment_id`),
  ADD KEY `fk_booking_user` (`user_id`);

--
-- Indexes for table `equipment_payments`
--
ALTER TABLE `equipment_payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_equipment_payment_equipment` (`equipment_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `fk_feedback_user` (`user_id`);

--
-- Indexes for table `monthly_subscriptions`
--
ALTER TABLE `monthly_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `fk_notif_sender` (`sender_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `fk_item_order` (`order_id`),
  ADD KEY `fk_item_plant` (`plant_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payment_order` (`order_id`),
  ADD KEY `fk_payment_user` (`user_id`);

--
-- Indexes for table `plants`
--
ALTER TABLE `plants`
  ADD PRIMARY KEY (`plant_id`),
  ADD KEY `fk_plant_category` (`category_id`),
  ADD KEY `fk_plants_user` (`user_id`);

--
-- Indexes for table `plant_ratings`
--
ALTER TABLE `plant_ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD UNIQUE KEY `plant_id` (`plant_id`,`user_id`),
  ADD KEY `fk_rating_user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `delivery_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_salary`
--
ALTER TABLE `employee_salary`
  MODIFY `salary_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `equipment_bookings`
--
ALTER TABLE `equipment_bookings`
  MODIFY `booking_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `equipment_payments`
--
ALTER TABLE `equipment_payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `feedback_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `monthly_subscriptions`
--
ALTER TABLE `monthly_subscriptions`
  MODIFY `subscription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `plants`
--
ALTER TABLE `plants`
  MODIFY `plant_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `plant_ratings`
--
ALTER TABLE `plant_ratings`
  MODIFY `rating_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`plant_id`) ON DELETE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `fk_delivery_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee_salary`
--
ALTER TABLE `employee_salary`
  ADD CONSTRAINT `fk_user_salary` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `equipment_bookings`
--
ALTER TABLE `equipment_bookings`
  ADD CONSTRAINT `fk_booking_equipment` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `equipment_payments`
--
ALTER TABLE `equipment_payments`
  ADD CONSTRAINT `fk_equipment_payment_equipment` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`equipment_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_feedback_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `monthly_subscriptions`
--
ALTER TABLE `monthly_subscriptions`
  ADD CONSTRAINT `monthly_subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_item_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_item_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`plant_id`) ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payment_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_payment_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `plants`
--
ALTER TABLE `plants`
  ADD CONSTRAINT `fk_plant_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_plants_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `plant_ratings`
--
ALTER TABLE `plant_ratings`
  ADD CONSTRAINT `fk_rating_plant` FOREIGN KEY (`plant_id`) REFERENCES `plants` (`plant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rating_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
