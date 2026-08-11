-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost:3306
-- Thời gian đã tạo: Th8 11, 2026 lúc 05:55 PM
-- Phiên bản máy phục vụ: 8.4.3
-- Phiên bản PHP: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `duan1`
--

-- --------------------------------------------------------

--
-- Xóa bảng cũ nếu tồn tại (để dễ nạp lại)
--
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `login_logs`;
DROP TABLE IF EXISTS `users`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `status` enum('active','blocked') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email` (`email`),
  UNIQUE KEY `uk_users_phone` (`phone`),
  KEY `idx_users_role` (`role`),
  KEY `idx_users_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `address`, `password`, `role`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Quản trị viên', 'admin@gmail.com', '0900000000', 'Hà Nội', '$2y$12$9Hm1cuihDaouAArI/ldELeqfPPTE6PCA19.KKc3UtDTodV4LAW39a', 'admin', 'active', '2026-07-20 23:18:16', '2026-07-20 15:27:24', '2026-07-20 16:18:16'),
(2, 'Phan Thế Tuân', 'phantuan23042006@gmail.com', '0352253220', 'Hà Nội', '$2y$10$m2T8QIByuecTpjrTHP.u6uhWhf3hxi8uL1TjraulGbFwrkapE5kqa', 'user', 'active', '2026-07-20 23:06:21', '2026-07-20 16:06:15', '2026-07-20 16:06:21');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login_status` enum('success','failed') COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_logs_user_id` (`user_id`),
  KEY `idx_login_logs_email` (`email`),
  KEY `idx_login_logs_status` (`login_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `email`, `login_status`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'admin@gmail.com', 'failed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/150.0.0.0', '2026-07-20 16:04:57'),
(2, 1, 'admin@gmail.com', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/150.0.0.0', '2026-07-20 16:05:28'),
(3, 2, 'phantuan23042006@gmail.com', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/150.0.0.0', '2026-07-20 16:06:21'),
(4, 1, 'admin@gmail.com', 'failed', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/150.0.0.0', '2026-07-20 16:09:00'),
(5, 1, 'admin@gmail.com', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/150.0.0.0', '2026-07-20 16:09:37'),
(6, 1, 'admin@gmail.com', 'success', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/150.0.0.0', '2026-07-20 16:18:16');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories` (Danh mục sản phẩm)
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_categories_slug` (`slug`),
  KEY `idx_categories_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `status`, `created_at`) VALUES
(1, 'Thời trang nam', 'thoi-trang-nam', 'Áo, quần và phụ kiện phong cách cho nam giới', '👔', 'active', CURRENT_TIMESTAMP),
(2, 'Thời trang nữ', 'thoi-trang-nu', 'Váy, áo và các item nữ tính, tinh tế', '👗', 'active', CURRENT_TIMESTAMP),
(3, 'Áo khoác', 'ao-khoac', 'Bộ sưu tập áo khoác thời trang các mùa', '🧥', 'active', CURRENT_TIMESTAMP),
(4, 'Áo thun', 'ao-thun', 'Áo thun basic, unisex dễ phối đồ', '👕', 'active', CURRENT_TIMESTAMP),
(5, 'Quần', 'quan', 'Quần jean, quần tây và quần short năng động', '👖', 'active', CURRENT_TIMESTAMP),
(6, 'Phụ kiện', 'phu-kien', 'Túi xách, nón và phụ kiện thời trang sành điệu', '👜', 'active', CURRENT_TIMESTAMP);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products` (Sản phẩm)
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(220) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_price` decimal(12,2) DEFAULT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `badge` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','hidden') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_products_slug` (`slug`),
  KEY `idx_products_category_id` (`category_id`),
  KEY `idx_products_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `image`, `price`, `sale_price`, `quantity`, `description`, `badge`, `status`, `created_at`) VALUES
(1, 3, 'Áo khoác denim oversize', 'ao-khoac-denim-oversize', '🧥', 590000.00, 520000.00, 50, 'Áo khoác denim phong cách Hàn Quốc, chất vải dày dặn cá tính.', 'Bán chạy', 'active', CURRENT_TIMESTAMP),
(2, 4, 'Áo thun basic cổ tròn', 'ao-thun-basic-co-tron', '👕', 249000.00, NULL, 100, 'Áo thun cotton 100% thoáng mát, thấm hút mồ hôi tốt.', 'Mới', 'active', CURRENT_TIMESTAMP),
(3, 2, 'Váy nữ dáng dài', 'vay-nu-dang-dai', '👗', 459000.00, 399000.00, 35, 'Váy nữ tôn dáng thanh lịch, phù hợp đi chơi và dự tiệc.', 'Hot', 'active', CURRENT_TIMESTAMP),
(4, 5, 'Quần jean ống rộng', 'quan-jean-ong-rong', '👖', 389000.00, 349000.00, 45, 'Quần jean ống rộng hack dáng chuẩn trend.', 'Giảm giá', 'active', CURRENT_TIMESTAMP),
(5, 1, 'Áo sơ mi tay dài cao cấp', 'ao-so-mi-tay-dai-cao-cap', '👔', 320000.00, NULL, 60, 'Áo sơ mi công sở nam form slim-fit sang trọng.', 'Mới', 'active', CURRENT_TIMESTAMP),
(6, 6, 'Túi xách nữ thời trang', 'tui-xach-nu-thoi-trang', '👜', 520000.00, 480000.00, 20, 'Túi xách da tổng hợp cao cấp, kiểu dáng hiện đại.', 'Hot', 'active', CURRENT_TIMESTAMP);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders` (Đơn hàng)
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `payment_method` enum('cod','vnpay','momo','banking') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cod',
  `payment_status` enum('pending','paid','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `status` enum('pending','processing','shipping','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_code` (`code`),
  KEY `idx_orders_user_id` (`user_id`),
  KEY `idx_orders_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `orders`
--

INSERT INTO `orders` (`id`, `code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `note`, `total_amount`, `payment_method`, `payment_status`, `status`, `created_at`) VALUES
(1, 'DH-1001', 2, 'Phan Thế Tuân', 'phantuan23042006@gmail.com', '0352253220', 'Hà Nội', 'Giao giờ hành chính', 590000.00, 'cod', 'paid', 'completed', '2026-08-01 09:00:00'),
(2, 'DH-1002', 2, 'Phan Thế Tuân', 'phantuan23042006@gmail.com', '0352253220', 'Hà Nội', 'Gọi trước khi giao', 249000.00, 'cod', 'pending', 'shipping', '2026-08-05 14:30:00'),
(3, 'DH-1003', 2, 'Phan Thế Tuân', 'phantuan23042006@gmail.com', '0352253220', 'Hà Nội', NULL, 459000.00, 'banking', 'pending', 'pending', '2026-08-10 10:15:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items` (Chi tiết đơn hàng)
--

CREATE TABLE `order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `product_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id` (`order_id`),
  KEY `idx_order_items_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `total_price`, `created_at`) VALUES
(1, 1, 1, 'Áo khoác denim oversize', 590000.00, 1, 590000.00, '2026-08-01 09:00:00'),
(2, 2, 2, 'Áo thun basic cổ tròn', 249000.00, 1, 249000.00, '2026-08-05 14:30:00'),
(3, 3, 3, 'Váy nữ dáng dài', 459000.00, 1, 459000.00, '2026-08-10 10:15:00');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart` (Giỏ hàng)
--

CREATE TABLE `cart` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_cart_user_product` (`user_id`, `product_id`),
  KEY `idx_cart_user_id` (`user_id`),
  KEY `idx_cart_product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`, `created_at`) VALUES
(1, 2, 4, 1, CURRENT_TIMESTAMP),
(2, 2, 2, 2, CURRENT_TIMESTAMP);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reviews` (Đánh giá sản phẩm)
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL DEFAULT '5',
  `comment` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_reviews_product_id` (`product_id`),
  KEY `idx_reviews_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 2, 5, 'Áo khoác rất đẹp, chất vải denim dày dặn tôn dáng!', '2026-08-02 10:00:00'),
(2, 2, 2, 4, 'Áo thun mặc thoáng mát, đúng size description.', '2026-08-06 16:20:00');

-- --------------------------------------------------------

--
-- Ràng buộc (Foreign Keys)
--

ALTER TABLE `login_logs`
  ADD CONSTRAINT `fk_login_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
