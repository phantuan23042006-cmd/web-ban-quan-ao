<?php
require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/ProductDetail.php';
require_once __DIR__ . '/../models/Review.php';

echo "=== KIỂM TRA MODEL & DATABASE LOGIC ===\n";

$catModel = new Category();
$categories = $catModel->getAll();
echo "1. Danh mục count: " . count($categories) . "\n";

$supModel = new Supplier();
$suppliers = $supModel->getAll();
echo "2. Nơi nhập hàng count: " . count($suppliers) . "\n";

$prodModel = new Product();
$products = $prodModel->getAllAdmin();
echo "3. Sản phẩm count: " . $products['total_items'] . "\n";

if ($products['total_items'] > 0) {
    $first = $products['items'][0];
    echo "   - SP đầu tiên: {$first['name']} | Giá từ: {$first['gia_tu']}đ | Giá đến: {$first['gia_den']}đ | Tồn kho: {$first['ton_kho']} | Đánh giá TB: {$first['danh_gia_tb']} ⭐\n";
    
    $detail = $prodModel->findById($first['id']);
    echo "   - Số lượng biến thể size của SP #{$first['id']}: " . count($detail['variants']) . "\n";
}

$revModel = new Review();
$reviews = $revModel->getAllAdmin();
echo "4. Đánh giá count: " . $reviews['total_items'] . "\n";

echo "=== KIỂM TRA THÀNH CÔNG ===\n";
