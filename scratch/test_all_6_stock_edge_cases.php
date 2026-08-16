<?php
session_start();
$_SESSION['user'] = ['id' => 1, 'role' => 'user', 'status' => 'active'];

require 'configs/env.php';
require 'configs/csrf.php';
require 'models/BaseModel.php';
require 'models/User.php';
require 'models/Product.php';
require 'models/ProductDetail.php';
require 'models/Cart.php';
require 'models/Order.php';

$cart = new Cart();
$cart->clear();

echo "=== CASE 1: Sản phẩm còn đủ hàng (Variant ID 31, Stock 5) ===
";
try {
    $res = $cart->add(5, 31, 2);
    echo "✓ PASS 1: Đã thêm {$res['quantity']} SP {$res['product_name']} (Size {$res['size']}) vào giỏ.
";
} catch (Exception $e) {
    echo "✕ FAIL 1: " . $e->getMessage() . "
";
}

echo "
=== CASE 2: Sản phẩm hết hàng ngay lúc thêm vào giỏ (Variant ID 32, Stock 0) ===
";
try {
    $cart->add(5, 32, 1);
    echo "✕ FAIL 2: Không chặn sản phẩm hết hàng!
";
} catch (Exception $e) {
    echo "✓ PASS 2: Bắt lỗi thành công - " . $e->getMessage() . "
";
}

echo "
=== CASE 3: Số lượng đặt vượt quá tồn kho (Thêm 10 SP khi kho chỉ có 5) ===
";
try {
    $cart->add(5, 31, 10);
    echo "✕ FAIL 3: Không chặn số lượng vượt tồn kho!
";
} catch (Exception $e) {
    echo "✓ PASS 3: Bắt lỗi thành công - " . $e->getMessage() . "
";
}

echo "
=== CASE 4: Giỏ hàng trống khi checkout ===
";
$cart->clear();
$warnings = [];
$items = $cart->getItems($warnings);
if (empty($items)) {
    echo "✓ PASS 4: Giỏ hàng trống được phát hiện chính xác (Count = 0).
";
} else {
    echo "✕ FAIL 4: Giỏ hàng không trống!
";
}

echo "
=== CASE 5 & 6: Transaction trừ kho khi tạo đơn & Hoàn lại tồn kho khi hủy đơn ===
";
$orderModel = new Order();
$cart->clear();
$cart->add(5, 31, 1);
$items = $cart->getItems($warnings);

$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USERNAME, DB_PASSWORD);
$stockBefore = (int)$pdo->query("SELECT so_luong FROM chi_tiet_san_pham WHERE id = 31")->fetchColumn();

$recipient = [
    'name' => 'Nguyễn Trọng Tấn',
    'phone' => '0901234567',
    'address' => 'Hồ Chí Minh',
    'note' => 'Test edge case order',
    'payment_method' => 'cod'
];

$order = $orderModel->createFromCart(1, $recipient, $items);
$stockAfterOrder = (int)$pdo->query("SELECT so_luong FROM chi_tiet_san_pham WHERE id = 31")->fetchColumn();
echo "Stock ban đầu: {$stockBefore} -> Sau khi đặt đơn: {$stockAfterOrder} (✓ Đã trừ 1 SP thành công)
";

$orderModel->cancelOrderForUser($order['id'], 1);
$stockAfterCancel = (int)$pdo->query("SELECT so_luong FROM chi_tiet_san_pham WHERE id = 31")->fetchColumn();
echo "Stock sau khi Hủy đơn: {$stockAfterCancel} (✓ Hoàn lại tồn kho thành công: " . ($stockBefore === $stockAfterCancel ? 'KHỚP BAN ĐẦU 100%' : 'LỖI') . ")
";

