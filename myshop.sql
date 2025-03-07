-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 06:26 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile_no` varchar(15) NOT NULL,
  `pin_code` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `city_village` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `customer_id`, `name`, `mobile_no`, `pin_code`, `address`, `city_village`, `district`, `state`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 'Prasanjit Roy', '6295078770', '735224', 'Nirendrapur, Tekatully', 'Mainaguri', 'Jalpaiguri', 'West Bengal', 1, '2025-02-28 08:47:24', '2025-03-06 03:47:48'),
(2, 1, 'Prafulla Roy', '9609756837', '736182', 'Subhas Pally ', 'Jaigaon', 'Alipurduar', 'West Bengal', 0, '2025-02-28 08:50:21', '2025-03-06 03:47:48'),
(3, 1, 'Papiya Roy', '9593307342', '734001', 'Hil Cart Road', 'Siliguri', 'Darjeeling', 'West Bengal', 0, '2025-02-28 12:54:38', '2025-03-05 02:53:59');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `first_name`, `last_name`, `email`, `phone`, `role`, `password`) VALUES
(1, 'Prasanjit', 'Roy', 'admin@myshop.com', '6295078770', 'Super Admin', 'eb43d76a3e2ba5c71b32ece145fb6bb1'),
(2, 'Dipesh', 'Guragain', 'admin-dipesh@myshop.com', '8250658013', 'Admin', '26b5c3f86027614d7c3bbec4238a97f8');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_image` varchar(255) DEFAULT NULL,
  `brand_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `brand_image`, `brand_url`) VALUES
(1, 'Nike', 'uploads/brands/brand-image.jpg', 'nike'),
(2, 'Adidas', 'uploads/brands/brand-image.jpg', 'adidas'),
(3, 'Puma', 'uploads/brands/brand-image.jpg', 'puma'),
(4, 'Reebok', 'uploads/brands/brand-image.jpg', 'reebok'),
(5, 'Under Armour', 'uploads/brands/brand-image.jpg', 'under-armour'),
(6, 'Gucci', 'uploads/brands/brand-image.jpg', 'gucci'),
(7, 'Louis Vuitton', 'uploads/brands/brand-image.jpg', 'louis-vuitton'),
(8, 'Chanel', 'uploads/brands/brand-image.jpg', 'chanel'),
(9, 'Prada', 'uploads/brands/brand-image.jpg', 'prada'),
(10, 'Balenciaga', 'uploads/brands/brand-image.jpg', 'balenciaga');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cartid` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `combination_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` date DEFAULT curdate(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cartid`, `customer_id`, `combination_id`, `quantity`, `created_at`, `updated_at`) VALUES
(4, 2, 15, 1, '2025-02-13', '2025-02-13 07:51:15'),
(7, 2, 3, 1, '2025-02-14', '2025-02-14 18:00:47'),
(8, 2, 9, 1, '2025-02-14', '2025-02-14 18:14:08');

-- --------------------------------------------------------

--
-- Table structure for table `colour`
--

CREATE TABLE `colour` (
  `cid` int(11) NOT NULL,
  `colour_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `colour`
--

INSERT INTO `colour` (`cid`, `colour_name`) VALUES
(1, 'Red'),
(2, 'Blue'),
(3, 'Black'),
(4, 'White');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `facebook_link` varchar(255) DEFAULT NULL,
  `instagram_link` varchar(255) DEFAULT NULL,
  `twitter_link` varchar(255) DEFAULT NULL,
  `footer_text` text DEFAULT NULL,
  `about_company` text DEFAULT NULL,
  `linked_link` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `title`, `logo`, `phone_number`, `email`, `website`, `address`, `facebook_link`, `instagram_link`, `twitter_link`, `footer_text`, `about_company`, `linked_link`, `youtube_link`) VALUES
(1, 'EBazar', 'uploads/67a2400b2ddc8_EBazar.png', '+916295078770', 'contact@ebazar.com', 'www.ebazar.com', 'Siliguri, India', 'https://www.faceboook.com/ebazar', 'https://www.instagram.com/ebazar', 'https://www.twitter.com/ebazar', 'Copyrights @ 2025', 'We bring the best products at affordable prices.', 'https://www.linked.com/ebazar', 'https://www.youtube.com/ebazar');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `shipping_address` text DEFAULT NULL,
  `billing_address` text DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `date_registered` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `first_name`, `last_name`, `date_of_birth`, `email`, `phone_number`, `password`, `shipping_address`, `billing_address`, `gender`, `date_registered`) VALUES
(1, 'Prasanjit', 'Roy', '1999-12-08', 'hiprasanjitroy@gmail.com', '6295078770', 'eb43d76a3e2ba5c71b32ece145fb6bb1', NULL, NULL, 'Male', '2025-02-13'),
(2, 'Dipesh', 'Guragain', NULL, 'idipeshguragain@gmail.com', '8250658013', 'eb43d76a3e2ba5c71b32ece145fb6bb1', NULL, NULL, NULL, '2025-02-13');

-- --------------------------------------------------------

--
-- Table structure for table `discount`
--

CREATE TABLE `discount` (
  `coupon_id` int(11) NOT NULL,
  `coupon_name` varchar(255) NOT NULL,
  `discount_type` enum('flat','percentage') NOT NULL,
  `flat` decimal(10,2) DEFAULT 0.00,
  `percentage` decimal(5,2) DEFAULT 0.00,
  `product_id` int(11) DEFAULT NULL,
  `validity_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discount`
--

INSERT INTO `discount` (`coupon_id`, `coupon_name`, `discount_type`, `flat`, `percentage`, `product_id`, `validity_date`) VALUES
(1, 'FLAT10', 'flat', 10.00, 0.00, 3, '2025-04-01'),
(2, 'PERCENT10', 'percentage', 0.00, 10.00, 5, '2025-04-01'),
(3, 'FLAT20', 'flat', 20.00, 0.00, NULL, '2025-04-01'),
(4, 'PERCENT20', 'percentage', 0.00, 20.00, NULL, '2025-04-01');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int(11) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `combination_id` int(11) NOT NULL,
  `stock_action` varchar(255) DEFAULT NULL,
  `added_stock_quantity` int(11) DEFAULT 0,
  `removed_stock_quantity` int(11) DEFAULT 0,
  `date` date NOT NULL DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory`
--

INSERT INTO `inventory` (`inventory_id`, `main_category_id`, `sub_category_id`, `product_id`, `combination_id`, `stock_action`, `added_stock_quantity`, `removed_stock_quantity`, `date`) VALUES
(1, 1, 1, 1, 1, 'Added', 100, 0, '2025-02-04'),
(2, 1, 1, 1, 2, 'Added', 100, 0, '2025-02-04'),
(3, 1, 1, 2, 3, 'Added', 100, 0, '2025-02-04'),
(4, 1, 1, 2, 4, 'Added', 100, 0, '2025-02-04'),
(5, 1, 2, 3, 5, 'Added', 100, 0, '2025-02-04'),
(6, 1, 2, 3, 6, 'Added', 100, 0, '2025-02-04'),
(7, 1, 2, 4, 7, 'Added', 100, 0, '2025-02-04'),
(8, 1, 2, 4, 8, 'Added', 100, 0, '2025-02-04'),
(9, 2, 3, 5, 9, 'Added', 100, 0, '2025-02-04'),
(10, 2, 3, 5, 10, 'Added', 100, 0, '2025-02-04'),
(11, 2, 3, 6, 11, 'Added', 100, 0, '2025-02-04'),
(12, 2, 3, 6, 12, 'Added', 100, 0, '2025-02-04'),
(13, 2, 4, 7, 13, 'Added', 100, 0, '2025-02-04'),
(14, 2, 4, 7, 14, 'Added', 100, 0, '2025-02-04'),
(15, 2, 4, 8, 15, 'Added', 100, 0, '2025-02-04'),
(16, 2, 4, 8, 16, 'Added', 100, 0, '2025-02-04'),
(17, 3, 5, 9, 17, 'Added', 100, 0, '2025-02-04'),
(18, 3, 5, 9, 18, 'Added', 100, 0, '2025-02-04'),
(19, 3, 5, 10, 19, 'Added', 100, 0, '2025-02-04'),
(20, 3, 5, 10, 20, 'Added', 100, 0, '2025-02-04'),
(21, 3, 6, 11, 21, 'Added', 100, 0, '2025-02-04'),
(22, 3, 6, 11, 22, 'Added', 100, 0, '2025-02-04'),
(23, 3, 6, 12, 23, 'Added', 100, 0, '2025-02-04'),
(24, 3, 6, 12, 24, 'Added', 100, 0, '2025-02-04');

-- --------------------------------------------------------

--
-- Table structure for table `main_category`
--

CREATE TABLE `main_category` (
  `cid` int(11) NOT NULL,
  `main_category_name` varchar(255) NOT NULL,
  `main_category_url` varchar(255) NOT NULL,
  `main_category_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `main_category`
--

INSERT INTO `main_category` (`cid`, `main_category_name`, `main_category_url`, `main_category_image`) VALUES
(1, 'Men\'s Clothing', 'men-s-clothing', 'uploads/categories/main-category-image.jpg'),
(2, 'Women\'s Clothing', 'women-s-clothing', 'uploads/categories/main-category-image.jpg'),
(3, 'Kids Clothing', 'kids-clothing', 'uploads/categories/main-category-image.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `city_village` varchar(255) NOT NULL,
  `district` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `product_sub_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gst_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `gst_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_charges` decimal(10,2) NOT NULL DEFAULT 0.00,
  `net_total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','online','card','net-banking') NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'processing',
  `is_requested_cancel` tinyint(4) NOT NULL DEFAULT 0,
  `pg_order_id` varchar(255) DEFAULT NULL,
  `pg_order_id_1` varchar(255) DEFAULT NULL,
  `razorpay_payment_id` varchar(255) DEFAULT NULL,
  `pg_response` text DEFAULT NULL,
  `order_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `name`, `phone_number`, `email`, `address`, `city_village`, `district`, `state`, `pincode`, `product_sub_total`, `discount_amount`, `gst_rate`, `gst_amount`, `delivery_charges`, `net_total_amount`, `payment_method`, `paid_amount`, `due_amount`, `is_paid`, `status`, `is_requested_cancel`, `pg_order_id`, `pg_order_id_1`, `razorpay_payment_id`, `pg_response`, `order_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Papiya Roy', '9593307342', 'hiprasanjitroy@gmail.com', 'Hil Cart Road', 'Siliguri', 'Darjeeling', 'West Bengal', '734001', 79.98, 8.00, 0.05, 3.60, 30.00, 105.58, 'online', 105.58, 0.00, 1, 'processing', 1, 'pg-papiy-67c6b792028dd', 'order_Q2e4CCVCn2w5Qd', 'pay_Q2e3juhYhkYpf8', '{\"razorpay_payment_id\":\"pay_Q2e3juhYhkYpf8\",\"razorpay_order_id\":\"order_Q2e380bHhYfnwg\",\"razorpay_signature\":\"6bdbd83e5552593b080ffca4399a0cb7f4dc144e896bd02dc8c8f14db5089e94\"}', '2025-03-04', '2025-03-04 08:19:29', '2025-03-06 09:08:59'),
(2, 1, 'Prafulla Roy', '9609756837', 'hiprasanjitroy@gmail.com', 'Subhas Pally ', 'Jaigaon', 'Alipurduar', 'West Bengal', '736182', 19.99, 0.00, 0.05, 1.00, 30.00, 50.99, 'cash', 0.00, 50.99, 0, 'cancelled', 1, 'pg-prafu-67c6b87978a5a', NULL, NULL, NULL, '2025-03-04', '2025-03-04 08:23:21', '2025-03-06 04:48:48'),
(3, 1, 'Prasanjit Roy', '6295078770', 'hiprasanjitroy@gmail.com', 'Nirendrapur, Tekatully', 'Mainaguri', 'Jalpaiguri', 'West Bengal', '735224', 39.99, 0.00, 0.05, 2.00, 30.00, 71.99, 'cash', 0.00, 71.99, 0, 'shipped', 0, 'pg-prasa-67c91aeec7419', NULL, NULL, NULL, '2025-03-06', '2025-03-06 03:47:58', '2025-03-06 04:48:14'),
(4, 1, 'Prasanjit Roy', '6295078770', 'hiprasanjitroy@gmail.com', 'Nirendrapur, Tekatully', 'Mainaguri', 'Jalpaiguri', 'West Bengal', '735224', 15.99, 0.00, 0.05, 0.80, 30.00, 46.79, 'cash', 0.00, 46.79, 0, 'processing', 1, 'pg-prasa-67c92b5022a3a', NULL, NULL, NULL, '2025-03-06', '2025-03-06 04:57:52', '2025-03-06 08:27:27');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `order_details_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `combination_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `size_id` int(11) NOT NULL,
  `size_name` varchar(255) NOT NULL,
  `colour_id` int(11) NOT NULL,
  `colour_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`order_details_id`, `order_id`, `combination_id`, `product_id`, `product_name`, `size_id`, `size_name`, `colour_id`, `colour_name`, `quantity`, `rate`, `amount`, `created_at`, `updated_at`) VALUES
(1, 1, 13, 7, 'Women\'s Top 1', 1, 'Small', 2, 'Blue', 1, 29.99, 29.99, '2025-03-04 08:19:29', '2025-03-04 08:19:29'),
(2, 1, 9, 5, 'Women\'s Dress 1', 1, 'Small', 2, 'Blue', 1, 49.99, 49.99, '2025-03-04 08:19:30', '2025-03-04 08:19:30'),
(3, 2, 1, 1, 'Men\'s T-Shirt 1', 1, 'Small', 1, 'Red', 1, 19.99, 19.99, '2025-03-04 08:23:21', '2025-03-04 08:23:21'),
(4, 3, 8, 4, 'Men\'s Jeans 2', 3, 'Large', 2, 'Blue', 1, 39.99, 39.99, '2025-03-06 03:47:58', '2025-03-06 03:47:58'),
(5, 4, 20, 10, 'Boys T-Shirt 2', 3, 'Large', 2, 'Blue', 1, 15.99, 15.99, '2025-03-06 04:57:52', '2025-03-06 04:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `order_status_details`
--

CREATE TABLE `order_status_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'processing',
  `remarks` text DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_status_details`
--

INSERT INTO `order_status_details` (`id`, `order_id`, `status`, `remarks`, `date_time`) VALUES
(1, 3, 'shipped', '', '2025-03-06 04:48:14'),
(2, 2, 'cancelled', '', '2025-03-06 04:48:48');

-- --------------------------------------------------------

--
-- Table structure for table `price_combination`
--

CREATE TABLE `price_combination` (
  `combination_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `size_id` int(11) NOT NULL,
  `colour_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_stock` int(11) NOT NULL DEFAULT 0,
  `used_stock` int(11) NOT NULL DEFAULT 0,
  `remaining_stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `price_combination`
--

INSERT INTO `price_combination` (`combination_id`, `product_id`, `size_id`, `colour_id`, `price`, `total_stock`, `used_stock`, `remaining_stock`) VALUES
(1, 1, 1, 1, 19.99, 100, 0, 100),
(2, 1, 2, 2, 29.99, 100, 0, 100),
(3, 2, 2, 1, 19.99, 100, 0, 100),
(4, 2, 3, 4, 19.99, 100, 0, 100),
(5, 3, 1, 3, 39.99, 100, 0, 100),
(6, 3, 2, 4, 39.99, 100, 0, 100),
(7, 4, 2, 4, 39.99, 100, 0, 100),
(8, 4, 3, 2, 39.99, 100, 1, 99),
(9, 5, 1, 2, 49.99, 100, 1, 99),
(10, 5, 2, 3, 49.99, 100, 0, 100),
(11, 6, 2, 4, 49.99, 100, 0, 100),
(12, 6, 3, 1, 49.99, 100, 0, 100),
(13, 7, 1, 2, 29.99, 100, 1, 99),
(14, 7, 2, 3, 29.99, 100, 0, 100),
(15, 8, 2, 3, 29.99, 100, 0, 100),
(16, 8, 3, 4, 29.99, 100, 0, 100),
(17, 9, 1, 3, 15.99, 100, 0, 100),
(18, 9, 2, 1, 15.99, 100, 0, 100),
(19, 10, 2, 4, 15.99, 100, 0, 100),
(20, 10, 3, 2, 15.99, 100, 1, 99),
(21, 11, 1, 2, 24.99, 100, 0, 100),
(22, 11, 2, 3, 24.99, 100, 0, 100),
(23, 12, 2, 4, 24.99, 100, 0, 100),
(24, 12, 3, 1, 24.99, 100, 0, 100);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `main_category_id` int(11) NOT NULL,
  `sub_category_id` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `is_stock` tinyint(1) NOT NULL DEFAULT 0,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_trending` tinyint(1) NOT NULL DEFAULT 0,
  `is_best_seller` tinyint(1) NOT NULL DEFAULT 0,
  `is_under_special_offer` tinyint(1) NOT NULL DEFAULT 0,
  `image_one` varchar(255) DEFAULT NULL,
  `image_two` varchar(255) DEFAULT NULL,
  `image_three` varchar(255) DEFAULT NULL,
  `product_url` varchar(255) DEFAULT NULL,
  `brand_id` int(11) NOT NULL,
  `size_ids` varchar(255) DEFAULT NULL,
  `colour_ids` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `main_category_id`, `sub_category_id`, `description`, `is_stock`, `is_featured`, `is_trending`, `is_best_seller`, `is_under_special_offer`, `image_one`, `image_two`, `image_three`, `product_url`, `brand_id`, `size_ids`, `colour_ids`, `created_at`) VALUES
(1, 'Men\'s T-Shirt 1', 1, 1, 'Comfortable cotton T-shirt', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'mens-t-shirt-1', 1, '1,2', '1,2', '2025-02-15 00:00:00'),
(2, 'Men\'s T-Shirt 2', 1, 1, 'Stylish T-shirt for casual wear', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'mens-t-shirt-2', 1, '2,3', '1,4', '2025-02-15 00:00:00'),
(3, 'Men\'s Jeans 1', 1, 2, 'Slim fit denim jeans', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'mens-jeans-1', 2, '1,3', '2,4', '2025-02-15 00:00:00'),
(4, 'Men\'s Jeans 2', 1, 2, 'Comfortable stretchable jeans', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'mens-jeans-2', 2, '2,4', '2,3', '2025-02-15 00:00:00'),
(5, 'Women\'s Dress 1', 2, 3, 'Elegant evening dress', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'womens-dress-1', 3, '1,2', '1,3', '2025-02-15 00:00:00'),
(6, 'Women\'s Dress 2', 2, 3, 'Casual summer dress', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'womens-dress-2', 3, '2,4', '1,4', '2025-02-15 00:00:00'),
(7, 'Women\'s Top 1', 2, 4, 'Trendy floral top', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'womens-top-1', 4, '1,2', '2,3', '2025-02-15 00:00:00'),
(8, 'Women\'s Top 2', 2, 4, 'Comfortable cotton top', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'womens-top-2', 4, '2,3', '2,4', '2025-02-15 00:00:00'),
(9, 'Boys T-Shirt 1', 3, 5, 'Fun graphic T-shirt for boys', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'boys-t-shirt-1', 5, '1,3', '1,2', '2025-02-15 00:00:00'),
(10, 'Boys T-Shirt 2', 3, 5, 'Cool cotton T-shirt for kids', 1, 1, 1, 1, 1, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'boys-t-shirt-2', 5, '2,4', '1,4', '2025-02-15 00:00:00'),
(11, 'Girls Dress 1', 3, 6, 'Cute dress for girls', 1, 0, 0, 0, 0, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'girls-dress-1', 6, '1,2', '2,3', '2025-02-15 00:00:00'),
(12, 'Girls Dress 2', 3, 6, 'Lovely party dress for girls', 1, 0, 0, 0, 0, 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'uploads/product-image.jpg', 'girls-dress-2', 6, '2,3', '2,4', '2025-02-15 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `size`
--

CREATE TABLE `size` (
  `sid` int(11) NOT NULL,
  `size_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `size`
--

INSERT INTO `size` (`sid`, `size_name`) VALUES
(1, 'Small'),
(2, 'Medium'),
(3, 'Large'),
(4, 'X-Large');

-- --------------------------------------------------------

--
-- Table structure for table `sub_category`
--

CREATE TABLE `sub_category` (
  `sid` int(11) NOT NULL,
  `sub_category_name` varchar(255) NOT NULL,
  `sub_category_url` varchar(255) NOT NULL,
  `sub_category_image` varchar(255) NOT NULL,
  `cid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sub_category`
--

INSERT INTO `sub_category` (`sid`, `sub_category_name`, `sub_category_url`, `sub_category_image`, `cid`) VALUES
(1, 'T-Shirts', 't-shirts', 'uploads/sub_categories/sub-categories.jpg', 1),
(2, 'Jeans', 'jeans', 'uploads/sub_categories/sub-categories.jpg', 1),
(3, 'Dresses', 'dresses', 'uploads/sub_categories/sub-categories.jpg', 2),
(4, 'Tops', 'tops', 'uploads/sub_categories/sub-categories.jpg', 2),
(5, 'Boys Clothing', 'boys-clothing', 'uploads/sub_categories/sub-categories.jpg', 3),
(6, 'Girls Clothing', 'girls-clothing', 'uploads/sub_categories/sub-categories.jpg', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartid`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `combination_id` (`combination_id`);

--
-- Indexes for table `colour`
--
ALTER TABLE `colour`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `discount`
--
ALTER TABLE `discount`
  ADD PRIMARY KEY (`coupon_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `combination_id` (`combination_id`);

--
-- Indexes for table `main_category`
--
ALTER TABLE `main_category`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_ibfk_1` (`customer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`order_details_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `combination_id` (`combination_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `colour_id` (`colour_id`);

--
-- Indexes for table `order_status_details`
--
ALTER TABLE `order_status_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `price_combination`
--
ALTER TABLE `price_combination`
  ADD PRIMARY KEY (`combination_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `size_id` (`size_id`),
  ADD KEY `colour_id` (`colour_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `main_category_id` (`main_category_id`),
  ADD KEY `sub_category_id` (`sub_category_id`),
  ADD KEY `product_ibfk_3` (`brand_id`);

--
-- Indexes for table `size`
--
ALTER TABLE `size`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `cid` (`cid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cartid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `colour`
--
ALTER TABLE `colour`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discount`
--
ALTER TABLE `discount`
  MODIFY `coupon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `inventory`
--
ALTER TABLE `inventory`
  MODIFY `inventory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `main_category`
--
ALTER TABLE `main_category`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `order_details_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `order_status_details`
--
ALTER TABLE `order_status_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `price_combination`
--
ALTER TABLE `price_combination`
  MODIFY `combination_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `size`
--
ALTER TABLE `size`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sub_category`
--
ALTER TABLE `sub_category`
  MODIFY `sid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`combination_id`) REFERENCES `price_combination` (`combination_id`) ON DELETE CASCADE;

--
-- Constraints for table `discount`
--
ALTER TABLE `discount`
  ADD CONSTRAINT `discount_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`combination_id`) REFERENCES `price_combination` (`combination_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_4` FOREIGN KEY (`size_id`) REFERENCES `size` (`sid`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_5` FOREIGN KEY (`colour_id`) REFERENCES `colour` (`cid`) ON DELETE CASCADE;

--
-- Constraints for table `order_status_details`
--
ALTER TABLE `order_status_details`
  ADD CONSTRAINT `order_status_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `price_combination`
--
ALTER TABLE `price_combination`
  ADD CONSTRAINT `price_combination_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `price_combination_ibfk_2` FOREIGN KEY (`size_id`) REFERENCES `size` (`sid`) ON DELETE CASCADE,
  ADD CONSTRAINT `price_combination_ibfk_3` FOREIGN KEY (`colour_id`) REFERENCES `colour` (`cid`) ON DELETE CASCADE;

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`main_category_id`) REFERENCES `main_category` (`cid`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ibfk_2` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_category` (`sid`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_ibfk_3` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`) ON DELETE CASCADE;

--
-- Constraints for table `sub_category`
--
ALTER TABLE `sub_category`
  ADD CONSTRAINT `sub_category_ibfk_1` FOREIGN KEY (`cid`) REFERENCES `main_category` (`cid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
