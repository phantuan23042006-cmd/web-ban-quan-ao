-- ============================================================
-- DuAn1 — Fashion Store
-- Schema đồng bộ với PHP code
-- Tạo lại: 2026-08-12
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- XÓA CÁC BẢNG CŨ (thứ tự từ con → cha để tránh lỗi FK)
-- ============================================================

DROP TABLE IF EXISTS `danh_gia`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `chi_tiet_san_pham`;
DROP TABLE IF EXISTS `san_pham`;
DROP TABLE IF EXISTS `danh_muc`;
DROP TABLE IF EXISTS `noi_nhap_hang`;
DROP TABLE IF EXISTS `login_logs`;
DROP TABLE IF EXISTS `users`;

-- ============================================================
-- BẢNG: users (Tài khoản)
-- ============================================================

CREATE TABLE `users` (
  `id`            BIGINT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `full_name`     VARCHAR(100)        NOT NULL                    COMMENT 'Họ và tên',
  `email`         VARCHAR(150)        NOT NULL                    COMMENT 'Email (unique)',
  `phone`         VARCHAR(20)         DEFAULT NULL                COMMENT 'Số điện thoại',
  `address`       VARCHAR(255)        DEFAULT NULL                COMMENT 'Địa chỉ',
  `password`      VARCHAR(255)        NOT NULL                    COMMENT 'Mật khẩu đã hash',
  `role`          ENUM('user','admin') NOT NULL DEFAULT 'user'    COMMENT 'Vai trò',
  `status`        ENUM('active','blocked') NOT NULL DEFAULT 'active' COMMENT 'Trạng thái',
  `last_login_at` DATETIME            DEFAULT NULL                COMMENT 'Lần đăng nhập cuối',
  `created_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP           NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_users_email`  (`email`),
  UNIQUE KEY `uk_users_phone`  (`phone`),
  KEY `idx_users_role`         (`role`),
  KEY `idx_users_status`       (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu: admin / 123456, user / 123456
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `address`, `password`, `role`, `status`, `last_login_at`, `created_at`, `updated_at`) VALUES
(1, 'Quản trị viên', 'admin@gmail.com', '0900000000', 'Hà Nội',
    '$2y$12$9Hm1cuihDaouAArI/ldELeqfPPTE6PCA19.KKc3UtDTodV4LAW39a',
    'admin', 'active', NOW(), NOW(), NOW()),
(2, 'Phan Thế Tuân', 'phantuan23042006@gmail.com', '0352253220', 'Hà Nội',
    '$2y$10$m2T8QIByuecTpjrTHP.u6uhWhf3hxi8uL1TjraulGbFwrkapE5kqa',
    'user', 'active', NOW(), NOW(), NOW());

-- ============================================================
-- BẢNG: login_logs (Lịch sử đăng nhập)
-- ============================================================

CREATE TABLE `login_logs` (
  `id`           BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `user_id`      BIGINT UNSIGNED  DEFAULT NULL               COMMENT 'NULL nếu tài khoản không tồn tại',
  `email`        VARCHAR(150)     NOT NULL,
  `login_status` ENUM('success','failed') NOT NULL,
  `ip_address`   VARCHAR(45)      DEFAULT NULL,
  `user_agent`   VARCHAR(500)     DEFAULT NULL,
  `created_at`   TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_login_logs_user_id` (`user_id`),
  KEY `idx_login_logs_email`   (`email`),
  KEY `idx_login_logs_status`  (`login_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- BẢNG: danh_muc (Danh mục sản phẩm)
-- PHP model: Category → $table = 'danh_muc'
-- ============================================================

CREATE TABLE `danh_muc` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100)    NOT NULL            COMMENT 'Tên danh mục',
  `created_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_danh_muc_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `danh_muc` (`id`, `name`) VALUES
(1, 'Thời trang nam'),
(2, 'Thời trang nữ'),
(3, 'Áo khoác'),
(4, 'Áo thun'),
(5, 'Quần'),
(6, 'Phụ kiện');

-- ============================================================
-- BẢNG: noi_nhap_hang (Nhà cung cấp / Nơi nhập hàng)
-- PHP model: Supplier → $table = 'noi_nhap_hang'
-- ============================================================

CREATE TABLE `noi_nhap_hang` (
  `id`            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(150)    NOT NULL            COMMENT 'Tên nơi nhập hàng',
  `dia_chi`       VARCHAR(255)    DEFAULT NULL        COMMENT 'Địa chỉ',
  `so_dien_thoai` VARCHAR(25)     DEFAULT NULL        COMMENT 'Số điện thoại liên hệ',
  `ghi_chu`       TEXT            DEFAULT NULL        COMMENT 'Ghi chú',
  `created_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_noi_nhap_hang_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `noi_nhap_hang` (`id`, `name`, `dia_chi`, `so_dien_thoai`, `ghi_chu`) VALUES
(1, 'Xưởng May Hà Nội', '12 Nguyễn Trãi, Hà Nội', '024-3838-0000', 'Nhập sỉ áo khoác, sơ mi'),
(2, 'Công ty Thời Trang HCM', '88 Lê Lai, TP.HCM', '028-3838-1111', 'Nhập quần, váy, phụ kiện'),
(3, 'Xưởng Dệt Bình Dương', 'KCN Mỹ Phước, Bình Dương', '0274-3838-222', 'Nhập áo thun các loại');

-- ============================================================
-- BẢNG: san_pham (Sản phẩm)
-- PHP model: Product → $table = 'san_pham'
-- ============================================================

CREATE TABLE `san_pham` (
  `id`               BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`             VARCHAR(200)    NOT NULL              COMMENT 'Tên sản phẩm',
  `gioi_thieu`       TEXT            DEFAULT NULL          COMMENT 'Mô tả giới thiệu',
  `anh`              VARCHAR(255)    DEFAULT NULL          COMMENT 'Tên file ảnh (lưu trong assets/uploads/)',
  `danh_muc_id`      BIGINT UNSIGNED NOT NULL              COMMENT 'FK → danh_muc.id',
  `noi_nhap_hang_id` BIGINT UNSIGNED NOT NULL              COMMENT 'FK → noi_nhap_hang.id',
  `created_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_san_pham_danh_muc`      (`danh_muc_id`),
  KEY `idx_san_pham_noi_nhap_hang` (`noi_nhap_hang_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `san_pham` (`id`, `name`, `gioi_thieu`, `anh`, `danh_muc_id`, `noi_nhap_hang_id`) VALUES
(1, 'Áo khoác denim oversize', 'Áo khoác denim phong cách Hàn Quốc, chất vải dày dặn cá tính.', 'prod_denim_jacket.svg', 3, 1),
(2, 'Áo thun basic cổ tròn',  'Áo thun cotton 100% thoáng mát, thấm hút mồ hôi tốt.',          'prod_basic_tshirt.svg', 4, 3),
(3, 'Váy nữ dáng dài',         'Váy nữ tôn dáng thanh lịch, phù hợp đi chơi và dự tiệc.',       'prod_long_dress.svg',    2, 2),
(4, 'Quần jean ống rộng',      'Quần jean ống rộng hack dáng chuẩn trend.',                       'prod_wide_jeans.svg',    5, 2),
(5, 'Áo sơ mi tay dài cao cấp','Áo sơ mi công sở nam form slim-fit sang trọng.',                 'prod_shirt.svg',         1, 1),
(6, 'Túi xách nữ thời trang',  'Túi xách da tổng hợp cao cấp, kiểu dáng hiện đại.',              'prod_handbag.svg',       6, 2);

-- ============================================================
-- BẢNG: chi_tiet_san_pham (Biến thể size sản phẩm)
-- PHP model: ProductDetail → $table = 'chi_tiet_san_pham'
-- ============================================================

CREATE TABLE `chi_tiet_san_pham` (
  `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
  `san_pham_id` BIGINT UNSIGNED  NOT NULL              COMMENT 'FK → san_pham.id',
  `size`        VARCHAR(20)      NOT NULL              COMMENT 'Size (S, M, L, XL, XXL...)',
  `gia_nhap`    DECIMAL(12,2)    NOT NULL DEFAULT 0    COMMENT 'Giá nhập vào',
  `gia_ban`     DECIMAL(12,2)    NOT NULL DEFAULT 0    COMMENT 'Giá bán ra',
  `so_luong`    INT              NOT NULL DEFAULT 0    COMMENT 'Tồn kho',
  `created_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ctsp_sp_size` (`san_pham_id`, `size`),
  KEY `idx_ctsp_san_pham_id` (`san_pham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Biến thể cho 6 sản phẩm mẫu
INSERT INTO `chi_tiet_san_pham` (`san_pham_id`, `size`, `gia_nhap`, `gia_ban`, `so_luong`) VALUES
-- Áo khoác denim oversize (id=1)
(1, 'M',   350000, 520000, 15),
(1, 'L',   350000, 520000, 20),
(1, 'XL',  350000, 540000, 10),
(1, 'XXL', 350000, 560000,  5),
-- Áo thun basic (id=2)
(2, 'S',   100000, 220000, 30),
(2, 'M',   100000, 229000, 40),
(2, 'L',   100000, 239000, 30),
(2, 'XL',  105000, 249000, 20),
-- Váy nữ dáng dài (id=3)
(3, 'S',   200000, 380000, 10),
(3, 'M',   200000, 399000, 15),
(3, 'L',   210000, 419000, 10),
-- Quần jean (id=4)
(4, 'S',   180000, 329000, 12),
(4, 'M',   180000, 349000, 18),
(4, 'L',   185000, 369000, 10),
(4, 'XL',  190000, 389000,  5),
-- Áo sơ mi (id=5)
(5, 'M',   150000, 289000, 20),
(5, 'L',   150000, 299000, 25),
(5, 'XL',  155000, 319000, 15),
-- Túi xách (id=6)
(6, 'ONE SIZE', 250000, 480000, 20);

-- ============================================================
-- BẢNG: danh_gia (Đánh giá sản phẩm)
-- PHP model: Review → $table = 'danh_gia'
-- ============================================================

CREATE TABLE `danh_gia` (
  `id`          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `san_pham_id` BIGINT UNSIGNED NOT NULL              COMMENT 'FK → san_pham.id',
  `user_id`     BIGINT UNSIGNED NOT NULL              COMMENT 'FK → users.id',
  `so_sao`      TINYINT UNSIGNED NOT NULL DEFAULT 5   COMMENT 'Số sao (1–5)',
  `noi_dung`    TEXT            DEFAULT NULL          COMMENT 'Nội dung đánh giá',
  `created_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_danh_gia_san_pham_id` (`san_pham_id`),
  KEY `idx_danh_gia_user_id`     (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `danh_gia` (`san_pham_id`, `user_id`, `so_sao`, `noi_dung`, `created_at`) VALUES
(1, 2, 5, 'Áo khoác rất đẹp, chất vải denim dày dặn tôn dáng!', '2026-08-02 10:00:00'),
(2, 2, 4, 'Áo thun mặc thoáng mát, đúng size mô tả.',           '2026-08-06 16:20:00');

-- ============================================================
-- BẢNG: orders (Đơn hàng)
-- PHP model: Order → INSERT dùng order_code, recipient_name...
-- ============================================================

CREATE TABLE `orders` (
  `id`               BIGINT UNSIGNED   NOT NULL AUTO_INCREMENT,
  `order_code`       VARCHAR(50)       NOT NULL              COMMENT 'Mã đơn hàng (unique)',
  `user_id`          BIGINT UNSIGNED   NOT NULL              COMMENT 'FK → users.id',
  `recipient_name`   VARCHAR(100)      NOT NULL              COMMENT 'Tên người nhận',
  `recipient_phone`  VARCHAR(20)       NOT NULL              COMMENT 'SĐT người nhận',
  `recipient_address`VARCHAR(255)      NOT NULL              COMMENT 'Địa chỉ nhận hàng',
  `note`             TEXT              DEFAULT NULL          COMMENT 'Ghi chú đơn hàng',
  `payment_method`   ENUM('cod','banking','vnpay','momo') NOT NULL DEFAULT 'cod',
  `status`           ENUM('pending','confirmed','shipping','completed','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount`     DECIMAL(14,2)     NOT NULL DEFAULT 0,
  `created_at`       TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_code`   (`order_code`),
  KEY `idx_orders_user_id`      (`user_id`),
  KEY `idx_orders_status`       (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `recipient_name`, `recipient_phone`, `recipient_address`, `note`, `payment_method`, `status`, `total_amount`, `created_at`) VALUES
(1, 'DH20260801ABCD', 2, 'Phan Thế Tuân', '0352253220', 'Hà Nội', 'Giao giờ hành chính', 'cod',     'completed', 520000.00, '2026-08-01 09:00:00'),
(2, 'DH20260805EFGH', 2, 'Phan Thế Tuân', '0352253220', 'Hà Nội', 'Gọi trước khi giao',   'cod',     'shipping',  229000.00, '2026-08-05 14:30:00'),
(3, 'DH20260810IJKL', 2, 'Phan Thế Tuân', '0352253220', 'Hà Nội', NULL,                    'banking', 'pending',   399000.00, '2026-08-10 10:15:00');

-- ============================================================
-- BẢNG: order_items (Chi tiết đơn hàng)
-- PHP model: Order → INSERT dùng san_pham_id, chi_tiet_san_pham_id, size, gia_ban, so_luong, thanh_tien
-- ============================================================

CREATE TABLE `order_items` (
  `id`                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`            BIGINT UNSIGNED NOT NULL              COMMENT 'FK → orders.id',
  `san_pham_id`         BIGINT UNSIGNED DEFAULT NULL          COMMENT 'FK → san_pham.id (SET NULL khi xóa SP)',
  `chi_tiet_san_pham_id`BIGINT UNSIGNED DEFAULT NULL          COMMENT 'FK → chi_tiet_san_pham.id',
  `product_name`        VARCHAR(200)    NOT NULL              COMMENT 'Tên sản phẩm tại thời điểm đặt',
  `size`                VARCHAR(20)     NOT NULL              COMMENT 'Size tại thời điểm đặt',
  `gia_ban`             DECIMAL(12,2)   NOT NULL              COMMENT 'Giá bán tại thời điểm đặt',
  `so_luong`            INT             NOT NULL DEFAULT 1    COMMENT 'Số lượng',
  `thanh_tien`          DECIMAL(14,2)   NOT NULL              COMMENT 'Thành tiền = gia_ban × so_luong',
  `created_at`          TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order_id`   (`order_id`),
  KEY `idx_order_items_sp_id`      (`san_pham_id`),
  KEY `idx_order_items_ctsp_id`    (`chi_tiet_san_pham_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Chi tiết cho 3 đơn hàng mẫu (variant_id ánh xạ theo INSERT chi_tiet_san_pham ở trên)
INSERT INTO `order_items` (`order_id`, `san_pham_id`, `chi_tiet_san_pham_id`, `product_name`, `size`, `gia_ban`, `so_luong`, `thanh_tien`) VALUES
(1, 1, 2, 'Áo khoác denim oversize', 'L',   520000.00, 1, 520000.00),
(2, 2, 6, 'Áo thun basic cổ tròn',   'M',   229000.00, 1, 229000.00),
(3, 3, 9, 'Váy nữ dáng dài',         'M',   399000.00, 1, 399000.00);

-- ============================================================
-- FOREIGN KEYS
-- ============================================================

ALTER TABLE `login_logs`
  ADD CONSTRAINT `fk_login_logs_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `san_pham`
  ADD CONSTRAINT `fk_san_pham_danh_muc`
    FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_san_pham_noi_nhap_hang`
    FOREIGN KEY (`noi_nhap_hang_id`) REFERENCES `noi_nhap_hang` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `chi_tiet_san_pham`
  ADD CONSTRAINT `fk_ctsp_san_pham`
    FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `danh_gia`
  ADD CONSTRAINT `fk_danh_gia_san_pham`
    FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_danh_gia_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_san_pham`
    FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_order_items_ctsp`
    FOREIGN KEY (`chi_tiet_san_pham_id`) REFERENCES `chi_tiet_san_pham` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
