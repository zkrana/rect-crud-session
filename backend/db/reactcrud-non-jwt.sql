-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2024 at 11:30 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reactcrud-non-jwt`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_logs`
--

CREATE TABLE `access_logs` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `access_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `access_logs`
--

INSERT INTO `access_logs` (`id`, `ip_address`, `access_time`) VALUES
(4, '127.0.0.1', '2024-01-27 09:15:02'),
(5, '::1', '2024-01-27 09:49:24'),
(6, '127.0.0.1', '2024-01-27 09:51:18'),
(7, '127.0.0.1', '2024-01-27 10:23:02'),
(8, '127.0.0.1', '2024-01-29 02:00:16'),
(9, '127.0.0.1', '2024-01-29 03:38:57'),
(10, '127.0.0.1', '2024-01-29 09:17:21'),
(11, '127.0.0.1', '2024-01-29 09:58:06'),
(12, '127.0.0.1', '2024-01-30 01:50:15'),
(13, '127.0.0.1', '2024-01-30 05:44:45');

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `profile_photo` varchar(255) NOT NULL,
  `is_admin` tinyint(1) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `profile_photo`, `is_admin`, `date`) VALUES
(21, 'zkrana', '$2y$10$kiRvG1wE418aRMjXZkCmuemut2iZLCOZTTZzMnglnZXLdLcyfTwAC', 'zkranao@gmail.com', 'super-admin/zkrana/handsome-man-with-laptop.jpg', 0, '2024-01-26 05:42:11');

-- --------------------------------------------------------

--
-- Table structure for table `banner_photos`
--

CREATE TABLE `banner_photos` (
  `id` int(11) NOT NULL,
  `photo_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner_photos`
--

INSERT INTO `banner_photos` (`id`, `photo_name`, `created_at`) VALUES
(4, '65b8cd010d0cd_photocomposition-horizontal-shopping-banner-with-woman-big-smartphone.jpg', '2024-01-30 10:18:41');

-- --------------------------------------------------------

--
-- Table structure for table `blocked_ips`
--

CREATE TABLE `blocked_ips` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `blocked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  `category_description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `level` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `parent_category_id`, `category_description`, `created_at`, `updated_at`, `level`) VALUES
(1, 'Men\'s & Boy\'s Fashion', NULL, 'All mens samples', '2024-01-26 10:51:49', '2024-01-29 06:24:23', 0),
(3, 'Women\'s & Girl\'s Fashion', NULL, 'All womens samples are avaolable', '2024-01-26 10:59:06', '2024-01-29 06:23:50', 0),
(7, 'Winter', NULL, 'Winter collections', '2024-01-26 11:25:48', '2024-01-26 11:25:48', 0),
(8, 'Kids', NULL, 'All kids samples', '2024-01-26 11:27:08', '2024-01-27 04:21:16', 0),
(11, 'Electronics', NULL, 'All electronics device are available.', '2024-01-27 10:20:15', '2024-01-27 10:20:15', 0),
(14, 'Muslim Wear', 3, 'All religious muslim wear are available.', '2024-01-29 05:45:27', '2024-01-29 05:45:27', 1),
(21, 'Outside Wear', 14, 'It\'s a sub category of muslim wear', '2024-01-29 06:33:01', '2024-01-29 06:33:01', 2),
(30, 'Health & Beauty', NULL, 'All health and beauty items are will be added here', '2024-01-29 06:51:53', '2024-01-29 06:52:16', 0),
(31, 'Skin care', 30, 'It\'s a sub category of health & beauty category', '2024-01-29 06:52:53', '2024-01-29 06:52:53', 1),
(32, 'Shoes', 3, 'All ladies shoes are will be with category', '2024-01-30 02:04:26', '2024-01-30 02:04:26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total_price` decimal(10,2) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `product_photo` blob NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT NULL,
  `currency_code` varchar(3) DEFAULT 'BDT',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `product_photo`, `description`, `price`, `category_id`, `stock_quantity`, `currency_code`, `created_at`, `updated_at`) VALUES
(3, ' Apple iPhone 14 Pro Max', 0x6950686f6e652d31342d50726f2d446565702d507572706c652d373330302e6a7067, 'iPhone 14 Pro Max', 1500.00, 1, 15, 'BDT', '2024-01-27 09:24:18', '2024-01-29 04:54:14'),
(5, 'Laikou California Vitamin C Serum ', 0x62303638626332346230633063633134376338636165623465356533363864612e6a70675f33303078307137352e77656270, 'badgeLaikou California Vitamin C Serum Antioxidant Remove Spots -17 ml', 2500.00, 31, 25, 'BDT', '2024-01-27 09:34:01', '2024-01-29 06:53:29'),
(6, 'রুম স্লিপার শীতকালীন রুম স্লিপার', 0x66633734333462633734616464646664626537316563393666353565653330622e6a70675f373530783735302e6a70675f2e77656270, 'রুম স্লিপার শীতকালীন রুম স্লিপার শীতকালীন উষ্ণ রুম স্লিপার শীতকালীন জুতা পুরুষ/মহিলাদের জন্য ঘরের জুতা', 125.00, 3, 35, 'BDT', '2024-01-29 04:47:03', '2024-01-29 04:47:03'),
(56, 'Dexe Hair Building Fiber-22g', 0x31312d382e6a7067, 'খাঁটি dexe চুলের বিল্ডিং ফাইবার 22g-কালো', 125.00, 30, 20, 'BDT', '2024-01-30 06:06:31', '2024-01-30 06:06:31'),
(57, 'Irani Party Abaya Burkha Set', 0x61626179612e77656270, 'New Attractive Premium-Quality Step Contrast Irani Party Abaya Burkha Set, Dubai Charry Fabric, Muslim Outerwear Collection 2023', 1172.00, 21, 35, 'BDT', '2024-01-30 06:22:58', '2024-01-30 06:22:58');

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `request_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `ip_address`, `username`, `password`, `email`, `photo`, `request_time`) VALUES
(1, '::1', 'zkrana', '$argon2id$v=19$m=2048,t=4,p=2$dmIvdzUyRks1ZWE2NnlSSQ$X2rHdqP70DKClYidAr4nsArZ7bJsojpQ77b/ZolZl3c', 'emtyYW5hb0BnbWFpbC5jb20=', '../assets/user-profile/zkrana/management.png', '2024-01-24 03:37:50'),
(2, '127.0.0.1', 'test', '$argon2id$v=19$m=2048,t=4,p=2$RUdCbUphYnN4MVc1WkV1RA$nNcizjgY0o5TN9UZ4nEyvZzEslEfOL2DO3bV+S77XIs', 'dGVzdEBnbWFpbC5jb20=', '../assets/user-profile/test/hs3.jpg', '2024-01-24 09:43:41');

-- --------------------------------------------------------

--
-- Table structure for table `variations`
--

CREATE TABLE `variations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `sim` varchar(255) DEFAULT NULL,
  `storage` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `variations`
--

INSERT INTO `variations` (`id`, `product_id`, `color`, `sim`, `storage`, `type`, `image_path`) VALUES
(32, 57, '#800000', NULL, NULL, NULL, '../../assets/products/57/variation_1/maroon-abaya.webp'),
(33, 57, '#006a4e', NULL, NULL, NULL, '../../assets/products/57/variation_2/79bbc951655b6ff3761ea4500e0ce5a5.jpg_750x750.jpg_.webp');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_logs`
--
ALTER TABLE `access_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner_photos`
--
ALTER TABLE `banner_photos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_parent_category` (`parent_category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `variations`
--
ALTER TABLE `variations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variations_ibfk_1` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_logs`
--
ALTER TABLE `access_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `banner_photos`
--
ALTER TABLE `banner_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `blocked_ips`
--
ALTER TABLE `blocked_ips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `variations`
--
ALTER TABLE `variations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_parent_category` FOREIGN KEY (`parent_category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admin_users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `variations`
--
ALTER TABLE `variations`
  ADD CONSTRAINT `variations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
