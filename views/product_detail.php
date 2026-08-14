<?php
$product = $product ?? null;
$variants = $product['variants'] ?? [];
$reviews = $reviews ?? [];
$ratingInfo = $ratingInfo ?? ['avg_stars' => 0, 'total_reviews' => 0];
$currentUser = $_SESSION['user'] ?? null;
$myReview = $myReview ?? null;

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .customer-product-detail {
        display: flex;
        flex-direction: column;
        gap: 32px;
        margin-top: 16px;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 500;
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

    .card {
        background: #ffffff;
        border-radius: 24px;
        padding: 32px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.05);
    }

    .product-hero {
        display: grid;
        grid-template-columns: 400px 1fr;
        gap: 36px;
    }

    .product-img-box {
        width: 100%;
        height: 400px;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-main-info {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .product-name {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .product-meta-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
    }

    .tag.rating-tag {
        background: #fef9c3;
        color: #854d0e;
        font-size: 15px;
    }

    /* Add to Cart Section Styling */
    .add-to-cart-box {
        background: #f8fafc;
        border-radius: 18px;
        padding: 22px;
        border: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .variant-selector-title {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 8px;
    }

    .variant-options {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .variant-radio-label {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        padding: 10px 16px;
        border-radius: 12px;
        border: 2px solid #cbd5e1;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.2s ease;
        user-select: none;
    }

    .variant-radio-label input[type="radio"] {
        display: none;
    }

    .variant-radio-label:hover {
        border-color: #7c3aed;
        background: #faf5ff;
    }

    .variant-radio-label.selected {
        border-color: #7c3aed;
        background: #f3e8ff;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15);
    }

    .variant-radio-label.out-of-stock {
        opacity: 0.5;
        border-color: #e2e8f0;
        background: #f1f5f9;
        cursor: not-allowed;
    }

    .variant-size {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .variant-price {
        font-size: 13px;
        font-weight: 700;
        color: #db2777;
        margin-top: 2px;
    }

    .variant-stock {
        font-size: 11px;
        color: #64748b;
        margin-top: 2px;
    }

    .cart-quantity-row {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 8px;
    }

    .quantity-input-group {
        display: flex;
        align-items: center;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        overflow: hidden;
        background: #ffffff;
    }

    .qty-btn {
        width: 36px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8fafc;
        border: none;
        font-size: 18px;
        font-weight: 700;
        cursor: pointer;
        color: #0f172a;
    }

    .qty-input {
        width: 48px;
        height: 42px;
        border: none;
        text-align: center;
        font-weight: 700;
        font-size: 15px;
    }

    .description-box {
        line-height: 1.7;
        color: #334155;
        font-size: 15px;
    }

    /* Review section */
    .review-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 16px;
    }

    .review-form-card {
        background: #f8fafc;
        border-radius: 18px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        margin-bottom: 32px;
    }

    .star-select {
        display: flex;
        gap: 8px;
        font-size: 24px;
        cursor: pointer;
        color: #cbd5e1;
        margin-bottom: 12px;
    }

    .star-select span {
        transition: color 0.15s ease;
    }

    .star-select span.active {
        color: #eab308;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        min-height: 44px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 14px;
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

    .review-card-item {
        padding: 20px 0;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    @media (max-width: 850px) {
        .product-hero {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container customer-product-detail">
    <a href="<?= BASE_URL ?>?action=products" class="btn btn-secondary btn-sm" style="align-self: flex-start;">← Quay lại danh sách sản phẩm</a>

    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="product-hero">
            <div class="product-img-box">
                <?php if (!empty($product['anh'])): ?>
                    <img src="<?= BASE_ASSETS_UPLOADS . e($product['anh']) ?>" alt="<?= e($product['name']) ?>">
                <?php else: ?>
                    <span style="color:#94a3b8;">Chưa có ảnh</span>
                <?php endif; ?>
            </div>

            <div class="product-main-info">
                <h1 class="product-name"><?= e($product['name']) ?></h1>

                <div class="product-meta-tags">
                    <span class="tag">🏷️ Danh mục: <strong><?= e($product['category_name'] ?? 'Khác') ?></strong></span>
                    <span class="tag">🏬 Nơi nhập: <strong><?= e($product['supplier_name'] ?? 'Khác') ?></strong></span>
                    <span class="tag rating-tag">
                        ⭐ <strong><?= e($ratingInfo['avg_stars']) ?> / 5</strong> (<?= e($ratingInfo['total_reviews']) ?> đánh giá)
                    </span>
                </div>

                <!-- Form Thêm Vào Giỏ Hàng kèm chọn Size -->
                <form action="<?= BASE_URL ?>?action=cart-add" method="post" class="add-to-cart-box" id="addToCartForm">
                    <input type="hidden" name="san_pham_id" value="<?= e($product['id']) ?>">

                    <div>
                        <h4 class="variant-selector-title">Chọn Size (Kích thước) <span style="color:red;">*</span>:</h4>
                        
                        <?php if (!empty($variants)): ?>
                            <div class="variant-options">
                                <?php foreach ($variants as $idx => $v): ?>
                                    <?php $isOutOfStock = (int)$v['so_luong'] <= 0; ?>
                                    <label class="variant-radio-label <?= $isOutOfStock ? 'out-of-stock' : '' ?>" onclick="selectVariant(this)">
                                        <input type="radio" name="variant_id" value="<?= e($v['id']) ?>" 
                                               data-price="<?= e($v['gia_ban']) ?>" 
                                               data-stock="<?= e($v['so_luong']) ?>"
                                               <?= $isOutOfStock ? 'disabled' : '' ?> 
                                               required>
                                        <span class="variant-size">Size <?= e($v['size']) ?></span>
                                        <span class="variant-price"><?= number_format($v['gia_ban'], 0, ',', '.') ?>đ</span>
                                        <span class="variant-stock"><?= $isOutOfStock ? 'Hết hàng' : 'Còn ' . e($v['so_luong']) . ' SP' ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color:#ef4444; font-size:14px; margin:0;">Sản phẩm chưa có biến thể size khả dụng.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($variants)): ?>
                        <div class="cart-quantity-row">
                            <span style="font-size:14px; font-weight:700; color:#0f172a;">Số lượng:</span>
                            <div class="quantity-input-group">
                                <button type="button" class="qty-btn" onclick="adjustQty(-1)">-</button>
                                <input type="number" id="detail_quantity" name="quantity" value="1" min="1" class="qty-input" oninput="validateDirectQty(this)" onblur="checkMinQty(this)">
                                <button type="button" class="qty-btn" onclick="adjustQty(1)">+</button>
                            </div>
                            <button type="submit" class="btn btn-primary" style="flex:1;">🛒 Thêm vào giỏ hàng</button>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- Description -->
                <div>
                    <h3 style="font-size:16px; margin:0 0 8px; color:#0f172a;">Giới thiệu sản phẩm:</h3>
                    <div class="description-box">
                        <?= e($product['gioi_thieu'] ?? 'Sản phẩm cao cấp phong cách thời trang hiện đại.') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đánh giá sản phẩm -->
    <div class="card">
        <div class="review-section-header">
            <h2 style="margin:0; font-size:22px; color:#0f172a;">
                Đánh giá sản phẩm (<?= e($ratingInfo['avg_stars']) ?>/5 ⭐)
            </h2>
        </div>

        <!-- Gợi ý viết đánh giá -->
        <div style="background:#f8fafc;border-radius:16px;padding:18px 20px;border:1px dashed #cbd5e1;margin-bottom:28px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div style="display:flex;align-items:center;gap:14px">
                <div style="font-size:28px">⭐</div>
                <div>
                    <h4 style="margin:0 0 4px;font-size:15px;color:#0f172a">Bạn đã mua sản phẩm này?</h4>
                    <p style="margin:0;font-size:13px;color:#64748b">Vào mục <strong>"Đơn hàng của tôi"</strong> ➔ chọn <strong>"Chi tiết đơn hàng"</strong> đã hoàn thành để viết và quản lý nhận xét của bạn.</p>
                </div>
            </div>
            <a href="<?= BASE_URL ?>?action=orders" class="btn btn-secondary btn-sm" style="font-weight:700">📦 Đơn hàng của tôi</a>
        </div>

        <!-- Danh sách tất cả đánh giá -->
        <div>
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $rev): ?>
                    <div class="review-card-item">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <strong style="color:#0f172a; font-size:15px;"><?= e($rev['user_name']) ?></strong>
                                <?php if ($currentUser && (string)$rev['user_id'] === (string)$currentUser['id']): ?>
                                    <span style="background:#ede9fe; color:#6d28d9; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700; margin-left:6px;">Bạn</span>
                                <?php endif; ?>
                            </div>
                            <div style="color:#eab308; font-size:16px;">
                                <?= str_repeat('★', (int)$rev['so_sao']) ?><?= str_repeat('☆', 5 - (int)$rev['so_sao']) ?>
                            </div>
                        </div>
                        <div style="color:#334155; font-size:14px; line-height:1.5;">
                            <?= e($rev['noi_dung'] ?? 'Không có nội dung.') ?>
                        </div>
                        <span style="font-size:12px; color:#94a3b8;"><?= e($rev['created_at']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="text-align:center; color:#94a3b8; padding:30px 0; margin:0;">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên đánh giá!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
let selectedMaxStock = 999;

function selectVariant(label) {
    const radio = label.querySelector('input[type="radio"]');
    if (radio.disabled) return;

    document.querySelectorAll('.variant-radio-label').forEach(l => l.classList.remove('selected'));
    label.classList.add('selected');
    radio.checked = true;

    selectedMaxStock = parseInt(radio.getAttribute('data-stock')) || 1;
    const qtyInput = document.getElementById('detail_quantity');
    if (parseInt(qtyInput.value) > selectedMaxStock) {
        qtyInput.value = selectedMaxStock;
    }
}

function adjustQty(amount) {
    const qtyInput = document.getElementById('detail_quantity');
    let current = parseInt(qtyInput.value) || 1;
    current += amount;

    if (current < 1) current = 1;
    if (current > selectedMaxStock) {
        alert('Kho chỉ còn tối đa ' + selectedMaxStock + ' sản phẩm cho size này.');
        current = selectedMaxStock;
    }
    qtyInput.value = current;
}

function validateDirectQty(input) {
    let val = parseInt(input.value);
    if (isNaN(val)) return;
    if (val > selectedMaxStock) {
        alert('Kho chỉ còn tối đa ' + selectedMaxStock + ' sản phẩm cho size này.');
        input.value = selectedMaxStock;
    }
}

function checkMinQty(input) {
    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) {
        input.value = 1;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Tự động chọn size đầu tiên còn hàng
    const firstAvailable = document.querySelector('.variant-radio-label:not(.out-of-stock)');
    if (firstAvailable) {
        selectVariant(firstAvailable);
    }

    // Star selector JS
    const starSelector = document.getElementById('starSelector');
    const starInput = document.getElementById('so_sao_input');
    
    if (starSelector && starInput) {
        const stars = starSelector.querySelectorAll('span');
        const initialValue = parseInt(starInput.value) || 5;

        function updateStars(val) {
            stars.forEach((s, idx) => {
                if (idx < val) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            starInput.value = val;
        }

        updateStars(initialValue);

        stars.forEach(s => {
            s.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-star'));
                updateStars(val);
            });
        });
    }
});
</script>
