<?php
require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../models/BaseModel.php';

class DatabaseSetup extends BaseModel
{
    public function run()
    {
        try {
            echo "--- DANG KHOT TAO DATABASE DU AN 1 ---\n";

            // 1. BẢNG DANH_MUC
            $sql1 = "
                CREATE TABLE IF NOT EXISTS `danh_muc` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(100) NOT NULL UNIQUE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->pdo->exec($sql1);
            echo "[OK] Bang danh_muc đã sẵn sàng.\n";

            // 2. BẢNG NOI_NHAP_HANG
            $sql2 = "
                CREATE TABLE IF NOT EXISTS `noi_nhap_hang` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `dia_chi` VARCHAR(255) NULL,
                    `so_dien_thoai` VARCHAR(20) NULL,
                    `ghi_chu` TEXT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->pdo->exec($sql2);
            echo "[OK] Bang noi_nhap_hang đã sẵn sàng.\n";

            // 3. BẢNG SAN_PHAM
            $sql3 = "
                CREATE TABLE IF NOT EXISTS `san_pham` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `name` VARCHAR(255) NOT NULL,
                    `gioi_thieu` TEXT NULL,
                    `anh` VARCHAR(255) NULL,
                    `danh_muc_id` INT NOT NULL,
                    `noi_nhap_hang_id` INT NOT NULL,
                    CONSTRAINT `fk_san_pham_danh_muc` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
                    CONSTRAINT `fk_san_pham_noi_nhap` FOREIGN KEY (`noi_nhap_hang_id`) REFERENCES `noi_nhap_hang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->pdo->exec($sql3);
            echo "[OK] Bang san_pham đã sẵn sàng.\n";

            // 4. BẢNG CHI_TIET_SAN_PHAM
            $sql4 = "
                CREATE TABLE IF NOT EXISTS `chi_tiet_san_pham` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `san_pham_id` INT NOT NULL,
                    `size` VARCHAR(20) NOT NULL,
                    `gia_nhap` DECIMAL(12,2) NOT NULL,
                    `gia_ban` DECIMAL(12,2) NOT NULL,
                    `so_luong` INT NOT NULL DEFAULT 0,
                    UNIQUE KEY `unique_san_pham_size` (`san_pham_id`, `size`),
                    CONSTRAINT `fk_chi_tiet_san_pham` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->pdo->exec($sql4);
            echo "[OK] Bang chi_tiet_san_pham đã sẵn sàng.\n";

            // 5. BẢNG DANH_GIA
            $sql5 = "
                CREATE TABLE IF NOT EXISTS `danh_gia` (
                    `id` INT AUTO_INCREMENT PRIMARY KEY,
                    `san_pham_id` INT NOT NULL,
                    `user_id` BIGINT UNSIGNED NOT NULL,
                    `so_sao` TINYINT NOT NULL CHECK (`so_sao` BETWEEN 1 AND 5),
                    `noi_dung` TEXT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    CONSTRAINT `fk_danh_gia_san_pham` FOREIGN KEY (`san_pham_id`) REFERENCES `san_pham` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `fk_danh_gia_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            $this->pdo->exec($sql5);
            echo "[OK] Bang danh_gia đã sẵn sàng.\n";

            // SEED DỮ LIỆU MẪU NẾU TRỐNG
            $countCat = (int) $this->pdo->query("SELECT COUNT(*) FROM danh_muc")->fetchColumn();
            if ($countCat === 0) {
                $this->pdo->exec("INSERT INTO danh_muc (name) VALUES ('Áo Nam'), ('Quần Nam'), ('Áo Khoác'), ('Phụ Kiện');");
                echo "[SEED] Đã thêm danh mục mẫu.\n";
            }

            $countSupplier = (int) $this->pdo->query("SELECT COUNT(*) FROM noi_nhap_hang")->fetchColumn();
            if ($countSupplier === 0) {
                $this->pdo->exec("INSERT INTO noi_nhap_hang (name, dia_chi, so_dien_thoai, ghi_chu) VALUES 
                    ('Kho May Hà Nội', '123 Đường Giải Phóng, Hà Nội', '0987654321', 'Nhà cung cấp áo sơ mi và áo polo cao cấp'),
                    ('Tổng Kho Sài Gòn', '456 Đường Lê Văn Sỹ, TP.HCM', '0912345678', 'Chuyên cung cấp quần jeans và kaki');");
                echo "[SEED] Đã thêm nơi nhập hàng mẫu.\n";
            }

            $countProd = (int) $this->pdo->query("SELECT COUNT(*) FROM san_pham")->fetchColumn();
            if ($countProd === 0) {
                $this->pdo->exec("INSERT INTO san_pham (name, gioi_thieu, anh, danh_muc_id, noi_nhap_hang_id) VALUES 
                    ('Áo Polo Basic Premium', 'Áo Polo chất liệu cotton 100% thoáng mát, thấm hút mồ hôi tốt, kiểu dáng trẻ trung năng động.', 'polo_basic.jpg', 1, 1),
                    ('Quần Jeans Slimfit Co Giãn', 'Quần Jeans phong cách hiện đại, tôn dáng, chất vải mềm mịn có độ co giãn nhẹ.', 'jeans_slimfit.jpg', 2, 2);");
                echo "[SEED] Đã thêm sản phẩm mẫu.\n";

                // Seed biến thể chi tiết sản phẩm
                $this->pdo->exec("INSERT INTO chi_tiet_san_pham (san_pham_id, size, gia_nhap, gia_ban, so_luong) VALUES
                    (1, 'S', 150000.00, 250000.00, 20),
                    (1, 'M', 160000.00, 270000.00, 30),
                    (1, 'L', 170000.00, 290000.00, 25),
                    (1, 'XL', 180000.00, 310000.00, 15),
                    (2, '29', 220000.00, 390000.00, 12),
                    (2, '30', 220000.00, 390000.00, 18),
                    (2, '31', 230000.00, 410000.00, 10);");
                echo "[SEED] Đã thêm chi tiết size mẫu.\n";
            }

            // Check admin user in `users`
            $adminUser = $this->pdo->query("SELECT * FROM users WHERE role = 'admin' LIMIT 1")->fetch();
            if (!$adminUser) {
                $password = password_hash('123456', PASSWORD_DEFAULT);
                $this->pdo->exec("INSERT INTO users (full_name, email, phone, password, role, status) VALUES ('Quản Trị Viên', 'admin@gmail.com', '0999888777', '$password', 'admin', 'active');");
                echo "[SEED] Đã tạo tài khoản admin mẫu (admin@gmail.com / 123456).\n";
            }

            // Check test customer user in `users`
            $testUser = $this->pdo->query("SELECT * FROM users WHERE role = 'user' LIMIT 1")->fetch();
            if (!$testUser) {
                $password = password_hash('123456', PASSWORD_DEFAULT);
                $this->pdo->exec("INSERT INTO users (full_name, email, phone, password, role, status) VALUES ('Khách Hàng Mẫu', 'user@gmail.com', '0911222333', '$password', 'user', 'active');");
                $testUserId = (int) $this->pdo->lastInsertId();
                echo "[SEED] Đã tạo tài khoản user mẫu (user@gmail.com / 123456).\n";
            } else {
                $testUserId = (int) $testUser['id'];
            }

            // Seed review if empty
            $countReview = (int) $this->pdo->query("SELECT COUNT(*) FROM danh_gia")->fetchColumn();
            if ($countReview === 0 && $testUserId > 0) {
                $this->pdo->exec("INSERT INTO danh_gia (san_pham_id, user_id, so_sao, noi_dung) VALUES
                    (1, $testUserId, 5, 'Chất áo polo cực kỳ đẹp, đường may chắc chắn, rất vừa vặn!'),
                    (1, $testUserId, 4, 'Vải mặc mát, giao hàng nhanh chóng.');");
                echo "[SEED] Đã thêm đánh giá mẫu.\n";
            }

            echo "\n=== HOÀN THÀNH KHỞI TẠO CƠ SỞ DỮ LIỆU ===\n";

        } catch (Exception $e) {
            echo "[LỖI] " . $e->getMessage() . "\n";
        }
    }
}

(new DatabaseSetup())->run();
