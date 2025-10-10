-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 06, 2025 at 02:35 PM
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
-- Database: `pwad66`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `photo`, `created_at`) VALUES
(4, 'rayhan', 'rayhan340997@gmail.com', '$2y$10$pnmI4qt/VIA/Wjyq9KwzLuOrGV3z/c1MA/0rcIWZ08aqr7VCDSTxG', '1754687959-t-shirt.jpg', '2025-08-08 21:17:02'),
(5, 'sydul', 'rayhan340997@gmail.com', '$2y$10$ClTv3JZotlu3KJw3Nb0Qfu7XIZysuiEdCYbATe0IJKcN0iBlACqyG', '1755069039-rayhan professional.png', '2025-08-08 21:22:37');

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sizes` varchar(200) DEFAULT NULL,
  `colors` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('open','ordered') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'ordered', '2025-08-22 20:21:02', '2025-08-22 22:33:46'),
(2, 1, 'open', '2025-08-22 22:34:56', '2025-08-22 22:34:56'),
(3, 2, 'ordered', '2025-08-25 11:42:39', '2025-08-26 08:56:41'),
(4, 2, 'ordered', '2025-08-26 09:11:39', '2025-08-26 09:46:29'),
(5, 2, 'ordered', '2025-08-26 09:51:04', '2025-08-26 09:52:56'),
(6, 2, 'ordered', '2025-08-26 09:54:31', '2025-08-26 09:57:49'),
(7, 2, 'ordered', '2025-09-03 10:10:28', '2025-09-03 10:25:50'),
(8, 2, 'open', '2025-09-03 11:08:30', '2025-09-03 11:08:30');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `qty`, `unit_price`, `created_at`) VALUES
(9, 2, 21, 2, 344.00, '2025-08-22 22:36:59');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `create_at` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category_name`, `create_at`) VALUES
(1, 'food', '2025-07-31'),
(2, 'Cloth', '2025-08-04'),
(3, 'books', '2025-08-04'),
(4, 'shoes', '2025-08-04'),
(5, 'fashoin', '2025-08-06');

-- --------------------------------------------------------

--
-- Table structure for table `contact_message`
--

CREATE TABLE `contact_message` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(200) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(4) NOT NULL DEFAULT 0,
  `is_replied` tinyint(1) DEFAULT 0,
  `reply_text` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `contact_message`
--

INSERT INTO `contact_message` (`id`, `name`, `email`, `subject`, `message`, `is_read`, `is_replied`, `reply_text`, `replied_at`, `created_at`) VALUES
(1, 'rayhan', 'rayhan340997@gmail.com', 'test', 'test message', 1, 1, 'testt', '2025-08-21 11:14:46', '2025-08-19 05:02:15'),
(2, 'harun', 'rayhan340997@gmail.com', 'test', 'testt', 1, 0, NULL, NULL, '2025-08-19 05:40:19'),
(4, 'testgdfytq', 'sajhdvusafde@gmail.com', 'wdquyfdue', 'agcysafd', 1, 0, NULL, NULL, '2025-08-19 05:45:55'),
(5, 'harun', 'rayhan340997@gmail.com', 'cyducgydc', 'kdjbcihdbc', 1, 0, NULL, NULL, '2025-08-21 04:45:45');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(64) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `scope` enum('all','product') NOT NULL DEFAULT 'all',
  `usage_count` int(10) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `usage_limit` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `discount_percent`, `scope`, `usage_count`, `status`, `usage_limit`, `start_date`, `end_date`, `created_at`) VALUES
(11, 'all20', 20.00, 'all', 0, 'active', '2', '2025-08-25', '2025-08-27', '2025-08-25 11:21:32'),
(12, 't100', 20.00, 'product', 0, 'active', '2', '2025-09-03', '2025-09-06', '2025-08-25 12:12:15');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_products`
--

CREATE TABLE `coupon_products` (
  `id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coupon_products`
--

INSERT INTO `coupon_products` (`id`, `coupon_id`, `product_id`) VALUES
(10, 12, 19);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_men`
--

CREATE TABLE `delivery_men` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `delivery_men`
--

INSERT INTO `delivery_men` (`id`, `name`, `phone`, `email`) VALUES
(1, 'Md.Sydul islam Rayhan', '01721376414', 'rayhan340997@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `change_type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `remarks` varchar(200) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`id`, `product_id`, `change_type`, `quantity`, `remarks`, `created_at`) VALUES
(2, 19, 'in', 10, 'rayhan', '2025-08-08 20:19:47');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `global_order_id` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `coupon_code` varchar(64) NOT NULL,
  `coupon_discount` decimal(10,2) NOT NULL,
  `payable_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','delivered','completed','canceled') NOT NULL DEFAULT 'pending',
  `area` varchar(255) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_man_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `global_order_id`, `user_id`, `user_name`, `phone`, `address`, `product_id`, `quantity`, `order_date`, `coupon_code`, `coupon_discount`, `payable_amount`, `status`, `area`, `total_amount`, `delivery_man_id`) VALUES
(1, 'ORD68AD2E1537332', 2, 'rayhan', '01721376414', ' dfwefew', 19, 1, '2025-08-26 09:46:29', 't100', 60.00, 240.00, 'pending', 'wefew', 300.00, 0),
(2, 'ORD68AD2F9898E79', 2, 'rayhan', '01721376414', ' gdqyued', 19, 1, '2025-08-26 09:52:56', 't100', 60.00, 240.00, 'pending', 'effh', 300.00, 0),
(3, 'ORD68AD30BD3187A', 2, 'rayhan', '01721376414', ' jhyuvf', 19, 1, '2025-08-26 09:57:49', 't100', 60.00, 240.00, 'pending', 'jgtf', 300.00, 0),
(4, 'ORD68B7C34E18325', 2, 'rayhan', '01721376414', ' edfdef', 19, 4, '2025-09-03 10:25:50', 't100', 240.00, 960.00, 'pending', 'edef ', 1200.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `description` varchar(200) DEFAULT NULL,
  `product_image` varchar(200) DEFAULT NULL,
  `unit_price` int(6) NOT NULL,
  `selling_price` int(6) NOT NULL,
  `stock_amount` int(11) DEFAULT NULL,
  `has_attributes` tinyint(1) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `book_type` varchar(50) DEFAULT NULL,
  `virtual_file` varchar(100) DEFAULT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `description`, `product_image`, `unit_price`, `selling_price`, `stock_amount`, `has_attributes`, `category_id`, `book_type`, `virtual_file`, `create_at`) VALUES
(19, 'T shirt', 'Simple Frebrisc ', '1754561893_t-shirt.jpg', 210, 300, 60, 0, 2, NULL, NULL, '2025-08-07 10:18:13'),
(22, 'cloth', 'efjwebf', '1756874770_Screenshot (1).png', 140, 150, 34, 0, 2, NULL, NULL, '2025-09-03 04:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(12) NOT NULL,
  `password` varchar(255) NOT NULL,
  `token` varchar(150) NOT NULL,
  `verifed` tinyint(1) NOT NULL DEFAULT 0,
  `reset_token` varchar(150) NOT NULL,
  `reset_expires` datetime NOT NULL,
  `create_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `token`, `verifed`, `reset_token`, `reset_expires`, `create_at`) VALUES
(2, 'rayhan', 'rayhan340997@gmail.com', '01721376414', '$2y$10$ukMsuf7McU1m8C2w0Wf0j.JifR6gkDlg/uldm4F1wrYodhnJKHam6', '0031db0e73b22b349cf6408818ff655a', 1, '', '0000-00-00 00:00:00', '2025-08-23 07:17:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_cart_product` (`cart_id`,`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_message`
--
ALTER TABLE `contact_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_contact_email` (`email`),
  ADD KEY `idx_contact_is_replied` (`is_replied`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_code` (`code`);

--
-- Indexes for table `coupon_products`
--
ALTER TABLE `coupon_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_coupon_product` (`coupon_id`,`product_id`);

--
-- Indexes for table `delivery_men`
--
ALTER TABLE `delivery_men`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_message`
--
ALTER TABLE `contact_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `coupon_products`
--
ALTER TABLE `coupon_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `delivery_men`
--
ALTER TABLE `delivery_men`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `coupon_products`
--
ALTER TABLE `coupon_products`
  ADD CONSTRAINT `coupon_products_ibfk_1` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
