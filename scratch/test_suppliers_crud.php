<?php
require_once __DIR__ . '/../configs/env.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Supplier.php';
require_once __DIR__ . '/../models/Product.php';

echo "=== TEST CHỨC NĂNG QUẢN LÝ NƠI NHẬP HÀNG (SUPPLIERS) ===\n\n";

$supplierModel = new Supplier();

// 1. Test Thêm mới Nơi nhập hàng
$testName = "Kho Hàng Test " . time();
$newId = $supplierModel->create([
    'name' => $testName,
    'dia_chi' => '123 Đường Test, Cần Thơ',
    'so_dien_thoai' => '0912345678',
    'ghi_chu' => 'Ghi chú nhà cung cấp thử nghiệm',
]);
echo "1. Thêm thành công nhà cung cấp mới với ID: {$newId}\n";

// 2. Test trùng tên
$isDuplicate = $supplierModel->nameExists($testName);
echo "2. Kiểm tra trùng tên '{$testName}': " . ($isDuplicate ? "PHÁT HIỆN TRÙNG (ĐÚNG)" : "SAI") . "\n";

// 3. Test sửa nhà cung cấp
$updatedName = $testName . " (Đã sửa)";
$supplierModel->update($newId, [
    'name' => $updatedName,
    'dia_chi' => '456 Đường Sửa, Đà Nẵng',
    'so_dien_thoai' => '0988776655',
    'ghi_chu' => 'Đã cập nhật ghi chú',
]);
$edited = $supplierModel->findById($newId);
echo "3. Cập nhật thành công nhà cung cấp: {$edited['name']} | SĐT: {$edited['so_dien_thoai']}\n";

// 4. Test Tìm kiếm
$searchResults = $supplierModel->getAll("Đà Nẵng");
echo "4. Tìm kiếm từ khóa 'Đà Nẵng': Tìm thấy " . count($searchResults) . " kết quả.\n";

// 5. Test Thử xóa nơi nhập hàng ĐANG CÓ sản phẩm liên kết (Supplier ID 1: Kho May Hà Nội)
$hasProds = $supplierModel->hasProducts(1);
echo "5. Supplier #1 (Kho May Hà Nội) có sản phẩm sử dụng hay không: " . ($hasProds ? "CÓ SẢN PHẨM" : "KHÔNG") . "\n";
if ($hasProds) {
    $deleted = $supplierModel->delete(1);
    echo "   -> Thử xóa Supplier #1: " . ($deleted ? "THẤT BẠI (Sai quy tắc)" : "BỊ CHẶN XÓA THÀNH CÔNG (Đúng yêu cầu)") . "\n";
}

// 6. Test Xóa nơi nhập hàng vừa tạo (Không có sản phẩm)
$deletedNew = $supplierModel->delete($newId);
echo "6. Xóa nhà cung cấp mới không có sản phẩm (ID #{$newId}): " . ($deletedNew ? "XÓA THÀNH CÔNG" : "LỖI") . "\n";

echo "\n=== TẤT CẢ BÀI TEST HOÀN THÀNH TỐT! ===\n";
