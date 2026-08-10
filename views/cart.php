<?php
$cartItems = $cartItems ?? [];
$totalQuantity = $totalQuantity ?? 0;
$totalAmount = $totalAmount ?? 0;
$warnings = $warnings ?? [];

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .cart-page {
        display: flex;
        flex-direction: column;
        gap: 28px;
        margin-top: 16px;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 16px;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .alert-warning {
        background: #fef9c3;
        color: #854d0e;
        border: 1px solid #fef08a;
    }

    .cart-card {
        background: #ffffff;
        border-radius: 24px;
        padding: 28px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05);
    }

    .cart-header-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .cart-header-title h1 {
        margin: 0;
        font-size: 24px;
        color: #0f172a;
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
    }

    .cart-table th,
    .cart-table td {
        padding: 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    .cart-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .product-cell {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .cart-thumb {
        width: 64px;
        height: 64px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .quantity-control {
        display: inline-flex;
        align-items: center;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        background: #ffffff;
    }

    .quantity-btn {
        width: 32px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: none;
        color: #0f172a;
        font-weight: 700;
        font-size: 16px;
        cursor: pointer;
        text-decoration: none;
    }

    .quantity-btn:hover {
        background: #e2e8f0;
    }

    .quantity-input {
        width: 44px;
        height: 34px;
        border: none;
        text-align: center;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .size-badge {
        display: inline-block;
        padding: 3px 8px;
        border-radius: 6px;
        background: #ede9fe;
        color: #6d28d9;
        font-size: 12px;
        font-weight: 700;
    }

    .cart-summary-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
        margin-top: 24px;
    }

    .summary-card {
        background: #f8fafc;
        border-radius: 20px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 15px;
        color: #475569;
    }

    .summary-row.total {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        padding-top: 12px;
        border-top: 2px dashed #cbd5e1;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 12px 24px;
        min-height: 48px;
        border-radius: 14px;
        border: none;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-danger {
        background: #ef4444;
        color: #ffffff;
    }

    .btn-sm {
        padding: 6px 12px;
        min-height: 34px;
        font-size: 13px;
        border-radius: 8px;
    }

    @media (max-width: 850px) {
        .cart-summary-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container cart-page">
    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($warnings)): ?>
        <?php foreach ($warnings as $warn): ?>
            <div class="alert alert-warning">⚠️ <?= e($warn) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="cart-card">
        <div class="cart-header-title">
            <div>
                <h1>Giỏ hàng của bạn</h1>
                <p style="margin:4px 0 0; color:#64748b; font-size:14px;">
                    Tổng số lượng: <strong><?= e($totalQuantity) ?></strong> sản phẩm
                </p>
            </div>
            <?php if (!empty($cartItems)): ?>
                <a href="<?= BASE_URL ?>?action=cart-clear" 
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng không?');">
                    🗑️ Xóa toàn bộ giỏ hàng
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($cartItems)): ?>
            <div style="overflow-x: auto;">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th>Đơn giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th style="text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <?php if (!empty($item['product_image'])): ?>
                                            <img src="<?= BASE_ASSETS_UPLOADS . e($item['product_image']) ?>" alt="<?= e($item['product_name']) ?>" class="cart-thumb">
                                        <?php else: ?>
                                            <div class="cart-thumb" style="display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">No image</div>
                                        <?php endif; ?>
                                        <div>
                                            <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $item['san_pham_id'] ?>" style="text-decoration:none; color:#0f172a; font-weight:700;">
                                                <?= e($item['product_name']) ?>
                                            </a>
                                            <div style="font-size:12px; color:#64748b; margin-top:2px;">Kho còn: <?= e($item['stock']) ?> SP</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="size-badge">Size <?= e($item['size']) ?></span>
                                </td>
                                <td>
                                    <strong style="color:#475569;"><?= number_format($item['price'], 0, ',', '.') ?>đ</strong>
                                </td>
                                <td>
                                    <div class="quantity-control">
                                        <!-- Nút Giảm -->
                                        <a href="<?= BASE_URL ?>?action=cart-update&variant_id=<?= $item['variant_id'] ?>&quantity=<?= $item['quantity'] - 1 ?>" class="quantity-btn">-</a>
                                        
                                        <!-- Ô Nhập Số Lượng -->
                                        <input type="text" class="quantity-input" value="<?= e($item['quantity']) ?>" readonly>
                                        
                                        <!-- Nút Tăng -->
                                        <a href="<?= BASE_URL ?>?action=cart-update&variant_id=<?= $item['variant_id'] ?>&quantity=<?= $item['quantity'] + 1 ?>" class="quantity-btn">+</a>
                                    </div>
                                </td>
                                <td>
                                    <strong style="color:#db2777; font-size:16px;"><?= number_format($item['subtotal'], 0, ',', '.') ?>đ</strong>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= BASE_URL ?>?action=cart-remove&variant_id=<?= $item['variant_id'] ?>" 
                                       class="btn btn-secondary btn-sm"
                                       style="color:#ef4444;"
                                       onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?');">
                                        Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="cart-summary-grid">
                <div>
                    <a href="<?= BASE_URL ?>?action=products" class="btn btn-secondary">← Tiếp tục mua sắm</a>
                </div>

                <div class="summary-card">
                    <h3 style="margin:0 0 10px; font-size:18px; color:#0f172a;">Tóm tắt đơn hàng</h3>
                    <div class="summary-row">
                        <span>Tổng số lượng:</span>
                        <strong><?= e($totalQuantity) ?> món</strong>
                    </div>
                    <div class="summary-row total">
                        <span>Tổng tiền:</span>
                        <span style="color:#db2777;"><?= number_format($totalAmount, 0, ',', '.') ?>đ</span>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="alert('Đã sẵn sàng cho bước Thanh toán!');">
                        Tiến hành thanh toán →
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 48px 20px; color: #64748b;">
                <div style="font-size: 48px; margin-bottom: 12px;">🛒</div>
                <h3 style="margin: 0 0 8px; color: #0f172a;">Giỏ hàng của bạn đang trống</h3>
                <p style="margin: 0 0 20px;">Hãy khám phá các sản phẩm thời trang mới nhất và thêm vào giỏ hàng!</p>
                <a href="<?= BASE_URL ?>?action=products" class="btn btn-primary">Khám phá sản phẩm ngay</a>
            </div>
        <?php endif; ?>
    </div>
</div>
