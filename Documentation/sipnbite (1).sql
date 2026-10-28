-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 26, 2026 at 02:57 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sipnbite`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

DROP TABLE IF EXISTS `addresses`;
CREATE TABLE IF NOT EXISTS `addresses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Home',
  `address_line` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Café City',
  `pincode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `addresses_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `user_id`, `title`, `address_line`, `city`, `pincode`, `latitude`, `longitude`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 2, 'Home', 'Flat 402, Sunshine Apartments, Green Park', 'New Delhi', '110016', 28.55620000, 77.20100000, 1, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(2, 2, 'Katargam', 'Katargam road, surat', 'City Center', '395004', NULL, NULL, 0, '2026-08-18 03:07:49', '2026-08-18 03:07:49');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Burgers', 'burgers-wraps', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&auto=format&fit=crop&q=60', 'Juicy, flame-grilled artisan burgers and crispy handcrafted wraps.', 'active', '2026-08-17 12:08:12', '2026-10-17 08:55:38'),
(2, 'Wood-Fired Pizzas', 'pizzas', 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=60', 'Authentic Italian wood-fired pizza topped with rich mozzarella and fresh basil.', 'active', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(3, 'Artisanal Beverages', 'beverages', 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=600&auto=format&fit=crop&q=60', 'Freshly brewed coffees, thick shakes, refreshers, and signature mocktails.', 'active', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(4, 'Decadent Desserts', 'desserts', 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=60', 'Gourmet cakes, warm waffles, lava brownies, and artisanal gelato.', 'active', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(7, 'Pasta & Italian', 'pasta-italian-1792333257', 'uploads/categories/1792333257_Pmk72G9N.jpg', 'Delicious pasta tossed with fresh ingredients, tasty flavors, and creamy sauces.', 'active', '2026-10-18 08:50:57', '2026-10-18 08:50:57'),
(6, 'Wraps', 'wraps', 'uploads/categories/1792248191_TULSX4sf.jpg', 'Delicious wraps filled with fresh veggies, tasty fillings, and flavorful sauces.', 'active', '2026-10-17 09:13:11', '2026-10-17 09:13:45'),
(8, 'Fries', 'fries-1792333359', 'uploads/categories/1792333359_SZh9en0K.jpg', 'Crispy golden fries seasoned with tasty spices and flavorful dips.', 'active', '2026-10-18 08:52:39', '2026-10-18 08:52:39'),
(9, 'Sandwhich', 'sandwhich-1792959963', 'uploads/categories/1792959963_yYwsN0uh.jpg', 'Fresh bread filled with delicious ingredients and tasty sauces.', 'active', '2026-10-25 14:56:03', '2026-10-25 14:56:03');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_persons`
--

DROP TABLE IF EXISTS `delivery_persons`;
CREATE TABLE IF NOT EXISTS `delivery_persons` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('available','on_delivery','offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `current_lat` decimal(10,8) DEFAULT NULL,
  `current_lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `delivery_persons_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `delivery_persons`
--

INSERT INTO `delivery_persons` (`id`, `name`, `phone`, `email`, `vehicle_number`, `status`, `current_lat`, `current_lng`, `created_at`, `updated_at`) VALUES
(1, 'Rahul Patel', '+91 9811223344', 'rahul.delivery@sipnbite.com', 'GJ05AB0011', 'on_delivery', 28.55600000, 77.20150000, '2026-08-17 12:08:12', '2026-09-05 04:40:12'),
(2, 'Priya Sharma', '+91 9822334455', 'priya.delivery@sipnbite.com', 'GJ05AN0051', 'on_delivery', 28.55700000, 77.20250000, '2026-08-17 12:08:12', '2026-09-05 00:47:39'),
(3, 'Parth', '9685741245', 'parth@gmail.com', 'GJ05KF9854', 'available', NULL, NULL, '2026-10-25 21:12:21', '2026-10-25 21:12:21'),
(4, 'Deep', '987654123', 'deep@gmail.com', 'GJ05AK6524', 'available', NULL, NULL, '2026-10-25 21:12:54', '2026-10-25 21:12:54'),
(5, 'Smit', '6957841247', 'smit@gmail.com', 'GJ05AQ3627', 'available', NULL, NULL, '2026-10-25 21:13:25', '2026-10-25 21:13:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `rating` int NOT NULL DEFAULT '5',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `feedback_user_id_foreign` (`user_id`),
  KEY `feedback_order_id_foreign` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `order_id`, `rating`, `comment`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 5, 'The truffle burger was absolutely sublime and delivered burning hot! 5 stars!', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(2, 4, NULL, 5, NULL, '2026-09-08 14:36:18', '2026-09-08 14:36:18'),
(3, 5, 22, 5, NULL, '2026-10-11 23:53:35', '2026-10-11 23:53:35');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE IF NOT EXISTS `menu_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `is_veg` tinyint(1) NOT NULL DEFAULT '1',
  `rating` decimal(3,2) NOT NULL DEFAULT '4.50',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_items_category_id_foreign` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `category_id`, `name`, `description`, `price`, `image`, `is_available`, `is_veg`, `rating`, `created_at`, `updated_at`) VALUES
(8, 6, 'Grilled veg Wrap', 'Delicious wraps filled with fresh veggies, tasty fillings, and flavorful sauces.', 150.00, 'uploads/menu/1792248557_grilled_veg_wrap.jpg', 1, 1, 4.80, '2026-10-17 09:16:15', '2026-10-17 09:19:17'),
(9, 9, 'Cheese Corn Sandwich', 'Creamy cheese and sweet corn stuffed in crispy, golden bread.', 150.00, 'uploads/menu/1792960028_HA8fFF5G.jpg', 1, 1, 4.90, '2026-10-25 14:57:08', '2026-10-25 14:57:08'),
(2, 1, 'Crispy Cottage Cheese Supreme Burger', 'Golden fried paneer patty stuffed with jalapenos, layered with tangy chipotle mayonnaise.', 249.00, 'https://images.unsplash.com/photo-1550547660-d9450f859349?w=600&auto=format&fit=crop&q=60', 1, 1, 4.60, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(3, 2, 'Margherita Basilico Wood-Fired Pizza', 'San Marzano tomato sauce, fresh buffalo mozzarella, extra virgin olive oil, and organic basil.', 399.00, 'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600&auto=format&fit=crop&q=60', 1, 1, 4.90, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(4, 2, 'Fiery Pepperoni Feast Pizza', 'Crispy spicy pepperoni, mozzarella, crushed chilli flakes, and roasted garlic.', 479.00, 'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=600&auto=format&fit=crop&q=60', 1, 0, 4.70, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(5, 3, 'Signature Iced Caramel Macchiato', 'Freshly pulled espresso poured over cold milk, ice, and homemade buttery caramel drip.', 189.00, 'https://images.unsplash.com/photo-1517701604599-bb29b565090c?w=600&auto=format&fit=crop&q=60', 1, 1, 4.80, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(6, 3, 'Nutella Hazelnut Thick Shake', 'Rich creamy blend of real Nutella, roasted hazelnuts, vanilla ice cream, and whipped topping.', 219.00, 'https://images.unsplash.com/photo-1572490122747-3968b75cc699?w=600&auto=format&fit=crop&q=60', 1, 1, 4.90, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(7, 4, 'Molten Chocolate Lava Cake', 'Warm Belgian chocolate cake with a gooey oozing liquid center served with vanilla gelato.', 199.00, 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?w=600&auto=format&fit=crop&q=60', 1, 1, 4.90, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(10, 9, 'Grilled Veg Sandwich', 'Crispy grilled bread filled with delicious veggies, cheese, and flavorful sauces.', 99.00, 'uploads/menu/1792960076_1m9fXXhi.jpg', 1, 1, 4.80, '2026-10-25 14:57:56', '2026-10-25 14:57:56'),
(11, 2, 'Cheese Corn Pizza', 'Cheesy pizza topped with sweet corn, melted cheese, and flavorful herbs.', 150.00, 'uploads/menu/1792961264_Jm54dnSo.jpg', 1, 1, 4.80, '2026-10-25 15:17:44', '2026-10-25 15:17:44'),
(12, 2, 'Veg Farmhouse Pizza', 'Loaded with fresh veggies, melted cheese, and delicious Italian herbs.', 200.00, 'uploads/menu/1792961318_wcwfgQjj.jpg', 1, 1, 4.80, '2026-10-25 15:18:38', '2026-10-25 15:18:38'),
(13, 2, 'Veg Panner Pizza', 'Topped with soft paneer, fresh veggies, melted cheese, and flavorful herbs.', 150.00, 'uploads/menu/1792961439_tm5I1F9j.jpg', 1, 1, 4.80, '2026-10-25 15:20:39', '2026-10-25 15:20:39'),
(14, 8, 'Cheesy Loaded Fries', 'Crispy golden fries loaded with melted cheese and delicious sauces.', 200.00, 'uploads/menu/1792961512_N4IvDeN0.jpg', 1, 1, 4.90, '2026-10-25 15:21:52', '2026-10-25 15:21:52'),
(15, 8, 'Chicken-Cheese-Balls', 'Crispy chicken balls stuffed with gooey melted cheese and flavorful spices.', 149.00, 'http://localhost:8000/uploads/menu/1792961548_lfMp631Q.jpg', 1, 0, 4.80, '2026-10-25 15:22:28', '2026-10-25 15:22:47'),
(16, 8, 'Classic Dip Fries', 'Crispy golden fries served with delicious classic dips.', 99.00, 'uploads/menu/1792961606_6Vu5APeT.jpg', 1, 1, 4.80, '2026-10-25 15:23:26', '2026-10-25 15:23:26'),
(17, 8, 'Golden Basket Fries', 'Crispy golden fries served in a basket with tasty seasoning.', 119.00, 'uploads/menu/1792961643_kCSFFNyy.jpg', 1, 1, 4.80, '2026-10-25 15:24:03', '2026-10-25 15:24:03'),
(18, 1, 'Aaloo tikki', 'Crispy potato patties seasoned with Indian spices and served hot.', 79.00, 'uploads/menu/1792961714_6Iu6g16m.jpg', 1, 1, 4.80, '2026-10-25 15:25:14', '2026-10-25 15:25:14'),
(19, 1, 'Cheese burger', 'Juicy burger layered with melted cheese, fresh veggies, and tasty sauces.', 109.00, 'uploads/menu/1792961757_QWSwgt6B.jpg', 1, 1, 4.80, '2026-10-25 15:25:57', '2026-10-25 15:25:57'),
(20, 1, 'Panner burger', 'Soft paneer patty layered with fresh veggies, cheese, and flavorful sauces.', 149.00, 'uploads/menu/1792961923_2Su3BFds.jpg', 1, 1, 4.80, '2026-10-25 15:28:43', '2026-10-25 15:28:43'),
(21, 1, 'Veg Burger', 'Crispy veggie patty topped with fresh veggies and delicious sauces.', 79.00, 'uploads/menu/1792961971_h4AS1zNa.jpg', 1, 1, 4.80, '2026-10-25 15:29:31', '2026-10-25 15:29:31'),
(22, 6, 'Red Cabbage Slaw Wrap', 'Fresh red cabbage slaw wrapped with crunchy veggies and creamy dressing.', 179.00, 'uploads/menu/1792962190_ZMEIK0J0.jpg', 1, 1, 4.80, '2026-10-25 15:33:10', '2026-10-25 15:33:10'),
(23, 6, 'Spiced Salad Wrap', 'Fresh veggies tossed in flavorful spices and wrapped in a soft tortilla.', 179.00, 'uploads/menu/1792962226_IkSex0OQ.jpg', 1, 1, 4.80, '2026-10-25 15:33:46', '2026-10-25 15:33:46'),
(24, 6, 'Panner Wrap', 'Soft paneer tossed in flavorful spices, wrapped with fresh veggies and sauces.', 179.00, 'uploads/menu/1792962264_qFe0qfE3.jpg', 1, 1, 4.80, '2026-10-25 15:34:24', '2026-10-25 15:34:24'),
(25, 7, 'Creamy White Pasta', 'Creamy pasta tossed in rich white sauce, herbs, and delicious flavors.', 149.00, 'uploads/menu/1792962313_Ww5msyta.jpg', 1, 1, 4.80, '2026-10-25 15:35:14', '2026-10-25 15:35:14'),
(26, 7, 'Masala Ribbon Pasta', 'Ribbon pasta tossed in spicy masala, herbs, and flavorful seasonings.', 149.00, 'uploads/menu/1792962346_NKA15NLB.jpg', 1, 1, 4.80, '2026-10-25 15:35:46', '2026-10-25 15:35:46'),
(27, 7, 'Red Sauce Pasta', 'Pasta tossed in tangy red tomato sauce, herbs, and delicious spices.', 149.00, 'uploads/menu/1792962377_xg538EfH.jpg', 1, 1, 4.80, '2026-10-25 15:36:17', '2026-10-25 15:36:17'),
(28, 3, 'Blueberry Mojito', 'Refreshing blueberry mojito with mint, lime, and fizzy soda.', 79.00, 'uploads/menu/1792962429_KgwuyDe7.jpg', 1, 1, 4.80, '2026-10-25 15:37:09', '2026-10-25 15:37:09'),
(29, 3, 'Cold Coffee', 'Chilled coffee blended with creamy milk and rich coffee flavor.', 49.00, 'uploads/menu/1792962479_mUbhKeQm.jpg', 1, 1, 4.80, '2026-10-25 15:37:59', '2026-10-25 15:37:59'),
(30, 3, 'Strawberry Frappuccino', 'Creamy strawberry frappuccino blended with sweet strawberries and chilled milk.', 79.00, 'uploads/menu/1792962512_77Bpwu3D.jpg', 1, 1, 4.80, '2026-10-25 15:38:32', '2026-10-25 15:38:32');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_01_000001_create_categories_table', 1),
(5, '2026_01_01_000002_create_menu_items_table', 1),
(6, '2026_01_01_000003_create_tables_table', 1),
(7, '2026_01_01_000004_create_table_bookings_table', 1),
(8, '2026_01_01_000005_create_delivery_persons_table', 1),
(9, '2026_01_01_000006_create_addresses_table', 1),
(10, '2026_01_01_000007_create_orders_table', 1),
(11, '2026_01_01_000008_create_order_items_table', 1),
(12, '2026_01_01_000009_create_payments_table', 1),
(13, '2026_01_01_000010_create_feedback_table', 1),
(14, '2026_01_01_000011_create_settings_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `delivery_person_id` bigint UNSIGNED DEFAULT NULL,
  `address_id` bigint UNSIGNED DEFAULT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('COD','Razorpay') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'COD',
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `razorpay_order_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_status` enum('Pending','Preparing','Out for Delivery','Delivered','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `orders_order_number_unique` (`order_number`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_delivery_person_id_foreign` (`delivery_person_id`),
  KEY `orders_address_id_foreign` (`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `delivery_person_id`, `address_id`, `subtotal`, `tax`, `delivery_fee`, `total_amount`, `payment_method`, `payment_status`, `razorpay_order_id`, `razorpay_payment_id`, `order_status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'SNB-49FEF5', 2, 1, 1, 488.00, 24.40, 40.00, 552.40, 'COD', 'paid', NULL, NULL, 'Delivered', 'Please bring extra napkins.', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(2, 'SNB-B75FA9', 2, 1, 2, 299.00, 14.95, 40.00, 353.95, 'Razorpay', 'paid', NULL, NULL, 'Pending', NULL, '2026-08-18 03:08:03', '2026-08-18 03:08:03'),
(3, 'SNB-33D576', 2, 1, 1, 249.00, 12.45, 40.00, 301.45, 'COD', 'pending', NULL, NULL, 'Out for Delivery', NULL, '2026-08-18 03:08:43', '2026-08-18 03:09:24'),
(4, 'SNB-033F0C', 2, 2, 2, 219.00, 10.95, 40.00, 269.95, 'COD', 'pending', NULL, NULL, 'Pending', NULL, '2026-08-18 03:23:36', '2026-08-18 03:23:36'),
(5, 'SNB-FC71E2', 2, 1, NULL, 399.00, 19.95, 40.00, 458.95, 'COD', 'paid', NULL, NULL, 'Delivered', NULL, '2026-08-18 03:32:23', '2026-08-18 03:33:41'),
(6, 'SNB-4308AF', 3, 1, NULL, 219.00, 10.95, 40.00, 269.95, 'COD', 'pending', NULL, NULL, 'Out for Delivery', 'hello', '2026-08-18 03:38:20', '2026-08-18 03:38:36'),
(7, 'SNB-8CBA79', 2, 1, 2, 479.00, 23.95, 40.00, 542.95, 'Razorpay', 'paid', NULL, NULL, 'Pending', NULL, '2026-09-04 05:04:32', '2026-09-04 05:04:32'),
(8, 'SNB-957FD9', 2, 2, 1, 399.00, 19.95, 40.00, 458.95, 'Razorpay', 'paid', NULL, 'pay_TXvl4Ce83asVa4', 'Preparing', NULL, '2026-09-04 05:12:33', '2026-09-04 05:15:44'),
(9, 'SNB-6871DD', 2, 1, NULL, 299.00, 14.95, 40.00, 353.95, 'Razorpay', 'paid', NULL, 'rzp_test_1788521860677', 'Delivered', NULL, '2026-09-04 06:07:42', '2026-09-04 06:14:25'),
(10, 'SNB-EECE44', 2, 1, NULL, 299.00, 14.95, 40.00, 353.95, 'COD', 'pending', NULL, NULL, 'Cancelled', NULL, '2026-09-05 00:28:39', '2026-09-05 00:29:53'),
(11, 'SNB-5E9B75', 2, 2, 2, 219.00, 10.95, 40.00, 269.95, 'Razorpay', 'paid', NULL, 'pay_TYFgYYiUa6GcPj', 'Out for Delivery', NULL, '2026-09-05 00:42:37', '2026-09-05 00:44:32'),
(12, 'SNB-8BE461', 4, 2, NULL, 249.00, 12.45, 40.00, 301.45, 'COD', 'paid', NULL, NULL, 'Out for Delivery', NULL, '2026-09-05 00:47:12', '2026-09-05 00:47:39'),
(13, 'SNB-505D7B', 4, NULL, NULL, 399.00, 19.95, 40.00, 458.95, 'COD', 'pending', NULL, NULL, 'Pending', NULL, '2026-09-05 02:40:13', '2026-09-05 02:40:13'),
(14, 'SNB-807614', 4, 1, NULL, 399.00, 19.95, 40.00, 458.95, 'COD', 'pending', NULL, NULL, 'Pending', NULL, '2026-09-05 04:26:08', '2026-09-05 04:40:39'),
(15, 'SNB-1E6199', 4, 2, NULL, 399.00, 19.95, 40.00, 458.95, 'Razorpay', 'paid', NULL, 'rzp_test_1788897265593', 'Preparing', NULL, '2026-09-08 14:24:25', '2026-09-08 14:26:15'),
(16, 'SNB-62917C', 4, NULL, NULL, 6615.00, 330.75, 40.00, 6985.75, 'COD', 'pending', NULL, NULL, 'Cancelled', NULL, '2026-09-08 14:34:54', '2026-09-08 14:35:46'),
(17, 'SNB-3776D6', 2, NULL, NULL, 399.00, 19.95, 40.00, 458.95, 'Razorpay', 'paid', NULL, 'pay_rzp_1791401650037_25', 'Pending', NULL, '2026-10-07 14:04:11', '2026-10-07 14:04:11'),
(18, 'SNB-01290B', 2, NULL, NULL, 399.00, 19.95, 40.00, 458.95, 'Razorpay', 'paid', NULL, 'pay_rzp_1791401662745_355', 'Pending', NULL, '2026-10-07 14:04:24', '2026-10-07 14:04:24'),
(19, 'SNB-5B38A7', 2, NULL, NULL, 399.00, 19.95, 40.00, 458.95, 'Razorpay', 'paid', NULL, 'pay_rzp_1791403220347_109', 'Pending', NULL, '2026-10-07 14:30:21', '2026-10-07 14:30:21'),
(20, 'SNB-5C1BCC', 2, NULL, NULL, 219.00, 10.95, 40.00, 269.95, 'Razorpay', 'paid', NULL, 'pay_rzp_1791403236283_888', 'Pending', NULL, '2026-10-07 14:30:37', '2026-10-07 14:30:37'),
(21, 'SNB-3A8751', 2, NULL, NULL, 479.00, 23.95, 40.00, 542.95, 'Razorpay', 'paid', NULL, 'pay_rzp_1791403250220_409', 'Pending', NULL, '2026-10-07 14:30:51', '2026-10-07 14:30:51'),
(22, 'SNB-F191C0', 5, NULL, NULL, 219.00, 10.95, 40.00, 269.95, 'Razorpay', 'paid', NULL, 'pay_rzp_1791782605040_184', 'Pending', NULL, '2026-10-11 23:53:27', '2026-10-11 23:53:27'),
(23, 'SNB-849975', 5, NULL, NULL, 479.00, 23.95, 40.00, 542.95, 'COD', 'pending', NULL, NULL, 'Pending', NULL, '2026-10-11 23:58:08', '2026-10-11 23:58:08'),
(24, 'SNB-A90BB9', 2, NULL, 2, 149.00, 7.45, 40.00, 196.45, 'Razorpay', 'paid', NULL, 'pay_TsHsJDMLjbNq1f', 'Pending', NULL, '2026-10-25 15:50:42', '2026-10-25 15:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `menu_item_id` bigint UNSIGNED DEFAULT NULL,
  `item_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_menu_item_id_foreign` (`menu_item_id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `item_name`, `price`, `quantity`, `subtotal`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Classic Truffle Cheese Burger', 299.00, 1, 299.00, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(2, 1, 5, 'Signature Iced Caramel Macchiato', 189.00, 1, 189.00, '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(3, 2, 1, 'Classic Truffle Cheese Burger', 299.00, 1, 299.00, '2026-08-18 03:08:03', '2026-08-18 03:08:03'),
(4, 3, 2, 'Crispy Cottage Cheese Supreme Burger', 249.00, 1, 249.00, '2026-08-18 03:08:43', '2026-08-18 03:08:43'),
(5, 4, 6, 'Nutella Hazelnut Thick Shake', 219.00, 1, 219.00, '2026-08-18 03:23:36', '2026-08-18 03:23:36'),
(6, 5, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-08-18 03:32:23', '2026-08-18 03:32:23'),
(7, 6, 6, 'Nutella Hazelnut Thick Shake', 219.00, 1, 219.00, '2026-08-18 03:38:20', '2026-08-18 03:38:20'),
(8, 7, 4, 'Fiery Pepperoni Feast Pizza', 479.00, 1, 479.00, '2026-09-04 05:04:32', '2026-09-04 05:04:32'),
(9, 8, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-09-04 05:12:33', '2026-09-04 05:12:33'),
(10, 9, 1, 'Classic Truffle Cheese Burger', 299.00, 1, 299.00, '2026-09-04 06:07:42', '2026-09-04 06:07:42'),
(11, 10, 1, 'Classic Truffle Cheese Burger', 299.00, 1, 299.00, '2026-09-05 00:28:39', '2026-09-05 00:28:39'),
(12, 11, 6, 'Nutella Hazelnut Thick Shake', 219.00, 1, 219.00, '2026-09-05 00:42:38', '2026-09-05 00:42:38'),
(13, 12, 2, 'Crispy Cottage Cheese Supreme Burger', 249.00, 1, 249.00, '2026-09-05 00:47:12', '2026-09-05 00:47:12'),
(14, 13, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-09-05 02:40:13', '2026-09-05 02:40:13'),
(15, 14, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-09-05 04:26:08', '2026-09-05 04:26:08'),
(16, 15, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-09-08 14:24:25', '2026-09-08 14:24:25'),
(17, 16, 5, 'Signature Iced Caramel Macchiato', 189.00, 35, 6615.00, '2026-09-08 14:34:54', '2026-09-08 14:34:54'),
(18, 17, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-10-07 14:04:11', '2026-10-07 14:04:11'),
(19, 18, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-10-07 14:04:24', '2026-10-07 14:04:24'),
(20, 19, 3, 'Margherita Basilico Wood-Fired Pizza', 399.00, 1, 399.00, '2026-10-07 14:30:21', '2026-10-07 14:30:21'),
(21, 20, 6, 'Nutella Hazelnut Thick Shake', 219.00, 1, 219.00, '2026-10-07 14:30:37', '2026-10-07 14:30:37'),
(22, 21, 4, 'Fiery Pepperoni Feast Pizza', 479.00, 1, 479.00, '2026-10-07 14:30:51', '2026-10-07 14:30:51'),
(23, 22, 6, 'Nutella Hazelnut Thick Shake', 219.00, 1, 219.00, '2026-10-11 23:53:27', '2026-10-11 23:53:27'),
(24, 23, 4, 'Fiery Pepperoni Feast Pizza', 479.00, 1, 479.00, '2026-10-11 23:58:08', '2026-10-11 23:58:08'),
(25, 24, 27, 'Red Sauce Pasta', 149.00, 1, 149.00, '2026-10-25 15:50:42', '2026-10-25 15:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `razorpay_payment_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `razorpay_order_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_order_id_foreign` (`order_id`),
  KEY `payments_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `user_id`, `razorpay_payment_id`, `razorpay_order_id`, `amount`, `payment_method`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 'rzp_test_6a8419eb7f619', 'order_6a8419eb7f683', 353.95, 'Razorpay', 'Success', '2026-08-18 03:08:03', '2026-08-18 03:08:03'),
(2, 7, 2, 'rzp_test_6a9a9eb8d8129', 'order_6a9a9eb8d815b', 542.95, 'Razorpay', 'Success', '2026-09-04 05:04:32', '2026-09-04 05:04:32'),
(3, 8, 2, 'pay_TXvl4Ce83asVa4', 'order_6a9aa09960e08', 458.95, 'Razorpay', 'Success', '2026-09-04 05:12:33', '2026-09-04 05:12:33'),
(4, 9, 2, 'rzp_test_1788521860677', 'order_6a9aad868e5c9', 353.95, 'Razorpay', 'Success', '2026-09-04 06:07:42', '2026-09-04 06:07:42'),
(5, 11, 2, 'pay_TYFgYYiUa6GcPj', 'order_6a9bb2d603a9c', 269.95, 'Razorpay', 'Success', '2026-09-05 00:42:38', '2026-09-05 00:42:38'),
(6, 15, 4, 'rzp_test_1788897265593', 'order_6aa067f1e7ff6', 458.95, 'Razorpay', 'Success', '2026-09-08 14:24:25', '2026-09-08 14:24:25'),
(7, 17, 2, 'pay_rzp_1791401650037_25', 'order_6ac69eb37bf1d', 458.95, 'Razorpay', 'Success', '2026-10-07 14:04:11', '2026-10-07 14:04:11'),
(8, 18, 2, 'pay_rzp_1791401662745_355', 'order_6ac69ec01622e', 458.95, 'Razorpay', 'Success', '2026-10-07 14:04:24', '2026-10-07 14:04:24'),
(9, 19, 2, 'pay_rzp_1791403220347_109', 'order_6ac6a4d5b87a0', 458.95, 'Razorpay', 'Success', '2026-10-07 14:30:21', '2026-10-07 14:30:21'),
(10, 20, 2, 'pay_rzp_1791403236283_888', 'order_6ac6a4e5c5ee1', 269.95, 'Razorpay', 'Success', '2026-10-07 14:30:37', '2026-10-07 14:30:37'),
(11, 21, 2, 'pay_rzp_1791403250220_409', 'order_6ac6a4f3ac7a1', 542.95, 'Razorpay', 'Success', '2026-10-07 14:30:51', '2026-10-07 14:30:51'),
(12, 22, 5, 'pay_rzp_1791782605040_184', 'order_6acc6ecf2064a', 269.95, 'Razorpay', 'Success', '2026-10-11 23:53:27', '2026-10-11 23:53:27'),
(13, 24, 2, 'pay_TsHsJDMLjbNq1f', 'order_6ade72aaa8c8f', 196.45, 'Razorpay', 'Success', '2026-10-25 15:50:42', '2026-10-25 15:50:42');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('fOpMaQqsCJknV1SZ8jDSb1rZWqBCmPz2s0zva1a8', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36', 'eyJfdG9rZW4iOiJIZmZQb1RnWDFuQXF0RmM4U2t0cG1icXNYRzM4dWVRNUZpMTFkUElPIiwiX2ZsYXNoIjp7Im5ldyI6W10sIm9sZCI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvbG9jYWxob3N0OjgwMDBcL2FkbWluXC9mZWVkYmFjayIsInJvdXRlIjoiYWRtaW4uZmVlZGJhY2suaW5kZXgifSwibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiOjF9', 1792983006);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'cafe_name', 'Sip N Bite Café & Bistro', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(2, 'cafe_tagline', 'Good Food. Great Coffee. Unforgettable Moments.', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(3, 'cafe_phone', '+91 9876543210', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(4, 'cafe_email', 'contact@sipnbite.com', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(5, 'cafe_address', '109, First Floor, International Business Centre (IBC), Piplod, Surat', '2026-08-17 12:08:12', '2026-10-25 15:41:54'),
(6, 'opening_hours', '09:00 AM - 11:00 PM (Mon - Sun)', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(7, 'tax_percentage', '5', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(8, 'flat_delivery_fee', '40', '2026-08-17 12:08:12', '2026-08-17 12:08:12');

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

DROP TABLE IF EXISTS `tables`;
CREATE TABLE IF NOT EXISTS `tables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `table_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capacity` int NOT NULL,
  `location_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Indoor',
  `status` enum('available','reserved','maintenance') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tables_table_number_unique` (`table_number`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`id`, `table_number`, `capacity`, `location_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'T1 (Indoor)', 2, 'Indoor', 'available', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(2, 'T2 (Indoor)', 4, 'Indoor', 'available', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(3, 'T3 (Indoor VIP)', 6, 'Indoor', 'available', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(4, 'O1 (Patio Outdoor)', 4, 'Outdoor', 'available', '2026-08-17 12:08:12', '2026-08-17 12:08:12'),
(5, 'R1 (Rooftop View)', 2, 'Rooftop', 'available', '2026-08-17 12:08:12', '2026-08-17 12:08:12');

-- --------------------------------------------------------

--
-- Table structure for table `table_bookings`
--

DROP TABLE IF EXISTS `table_bookings`;
CREATE TABLE IF NOT EXISTS `table_bookings` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `table_id` bigint UNSIGNED DEFAULT NULL,
  `booking_date` date NOT NULL,
  `booking_time` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guests_count` int NOT NULL,
  `special_request` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','confirmed','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `table_bookings_user_id_foreign` (`user_id`),
  KEY `table_bookings_table_id_foreign` (`table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `table_bookings`
--

INSERT INTO `table_bookings` (`id`, `user_id`, `table_id`, `booking_date`, `booking_time`, `guests_count`, `special_request`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, NULL, '2026-09-17', '12:30', 5, 'party', 'confirmed', '2026-09-05 00:48:44', '2026-09-05 00:49:04'),
(2, 2, NULL, '2026-09-05', '19:30', 2, 'temp', 'pending', '2026-09-05 04:46:07', '2026-09-05 04:46:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `role` enum('admin','customer') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `role`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@sipnbite.com', NULL, 'admin123', '+91 9876543210', 'Sip N Bite , online cafe.', 'admin', 'active', NULL, '2026-08-17 12:08:12', '2026-10-17 09:06:38'),
(2, 'Yash', 'customer@sipnbite.com', NULL, 'customer123', '+91 9123456789', 'Turning Point, Ghod Dod Road, Surat', 'customer', 'active', NULL, '2026-08-17 12:08:12', '2026-10-25 21:06:37'),
(3, 'prathamesh', 'prathamesh@gmail.com', NULL, 'test123', '6958470124', 'katargam road, surat', 'customer', 'active', NULL, '2026-08-18 03:36:37', '2026-08-18 03:36:37'),
(4, 'temp', 'temp@gmail.com', NULL, 'temp123', '9876543210', 'athwa gate,surat', 'customer', 'active', NULL, '2026-09-05 00:46:02', '2026-09-05 00:46:02'),
(5, 'krish', 'krish@gmail.com', NULL, 'krish@123', '6352417496', 'Vesu,surat', 'customer', 'active', NULL, '2026-10-11 23:36:08', '2026-10-11 23:36:08'),
(6, 'user_1', 'user@gmail.com', NULL, 'user123', '9584758624', 'Vesu, Surat', 'customer', 'active', NULL, '2026-10-25 21:03:58', '2026-10-25 21:03:58');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
