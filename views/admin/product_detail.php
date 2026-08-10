<?php
$product = $product ?? null;
$variants = $product['variants'] ?? [];
$reviews = $reviews ?? [];
$ratingInfo = $ratingInfo ?? ['avg_stars' => 0, 'total_reviews' => 0];

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .product-detail-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1050px;
        margin: 0 auto;
    }

    .admin-card {
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .product-main-grid {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 24px;
    }

    .product-image-box {
        width: 100%;
        height: 320px;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-image-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-info-box {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .product-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .product-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .meta-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
    }

    .meta-tag.rating {
        background: #fef9c3;
        color: #854d0e;
    }

    .section-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 16px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }

    .variant-table {
        width: 100%;
        border-collapse: collapse;
    }

    .variant-table th,
    .variant-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .variant-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .review-item {
        padding: 16px 0;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .reviewer-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 14px;
    }

    .review-stars {
        color: #eab308;
        font-size: 14px;
    }

    .review-content {
        color: #334155;
        font-size: 14px;
        line-height: 1.5;
    }

    .review-date {
        font-size: 12px;
        color: #94a3b8;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        min-height: 44px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
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

    @media (max-width: 768px) {
        .product-main-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="product-detail-page">
    <div class="admin-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0;">Chi tiết sản phẩm #<?= e($product['id']) ?></h3>
            <div style="display:flex; gap:10px;">
                <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary btn-sm">← Quay lại danh sách</a>
                <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">Chỉnh sửa</a>
            </div>
        </div>

        <div class="product-main-grid">
            <div class="product-image-box">
                <?php if (!empty($product['anh'])): ?>
                    <img src="<?= BASE_ASSETS_UPLOADS . e($product['anh']) ?>" alt="<?= e($product['name']) ?>">
                <?php else: ?>
                    <span style="color:#94a3b8;">Chưa có ảnh</span>
                <?php endif; ?>
            </div>

            <div class="product-info-box">
                <h1 class="product-title"><?= e($product['name']) ?></h1>

                <div class="product-meta">
                    <span class="meta-tag">🏷️ Danh mục: <strong><?= e($product['category_name'] ?? 'Chưa phân loại') ?></strong></span>
                    <span class="meta-tag">🏬 Nơi nhập: <strong><?= e($product['supplier_name'] ?? 'Chưa rõ') ?></strong></span>
                    <span class="meta-tag rating">
                        ⭐ <strong><?= e($ratingInfo['avg_stars']) ?>/5</strong> (<?= e($ratingInfo['total_reviews']) ?> đánh giá)
                    </span>
                </div>

                <div>
                    <h4 style="margin:0 0 6px; color:#475569; font-size:14px;">Giới thiệu sản phẩm:</h4>
                    <p style="margin:0; color:#334155; line-height:1.6; white-space:pre-line;">
                        <?= e($product['gioi_thieu'] ?? 'Chưa có thông tin giới thiệu.') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Danh sách Size & Giá -->
    <div class="admin-card">
        <h3 class="section-title">Danh sách biến thể Size sản phẩm</h3>

        <div style="overflow-x: auto;">
            <table class="variant-table">
                <thead>
                    <tr>
                        <th>Size</th>
                        <th>Giá nhập (VNĐ)</th>
                        <th>Giá bán (VNĐ)</th>
                        <th>Số lượng còn</th>
                        <th>Trạng thái tồn kho</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($variants)): ?>
                        <?php foreach ($variants as $v): ?>
                            <tr>
                                <td><strong style="font-size:15px;"><?= e($v['size']) ?></strong></td>
                                <td><?= number_format($v['gia_nhap'], 0, ',', '.') ?> đ</td>
                                <td style="color:#16a34a; font-weight:700;"><?= number_format($v['gia_ban'], 0, ',', '.') ?> đ</td>
                                <td><strong><?= e($v['so_luong']) ?></strong></td>
                                <td>
                                    <?php if ($v['so_luong'] > 0): ?>
                                        <span style="color:#166534; background:#dcfce7; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:700;">Còn <?= e($v['so_luong']) ?> sp</span>
                                    <?php else: ?>
                                        <span style="color:#b91c1c; background:#fee2e2; padding:4px 8px; border-radius:6px; font-size:12px; font-weight:700;">Hết hàng</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">Sản phẩm chưa có biến thể size nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Đánh giá khách hàng -->
    <div class="admin-card">
        <h3 class="section-title">Đánh giá từ khách hàng (<?= e($ratingInfo['total_reviews']) ?>)</h3>

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $rev): ?>
                <div class="review-item">
                    <div class="review-header">
                        <div>
                            <span class="reviewer-name"><?= e($rev['user_name']) ?></span>
                            <span style="color:#64748b; font-size:12px;">(<?= e($rev['user_email']) ?>)</span>
                        </div>
                        <div class="review-stars">
                            <?= str_repeat('★', (int)$rev['so_sao']) ?><?= str_repeat('☆', 5 - (int)$rev['so_sao']) ?>
                        </div>
                    </div>
                    <div class="review-content"><?= e($rev['noi_dung'] ?? 'Không có nội dung.') ?></div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <span class="review-date"><?= e($rev['created_at']) ?></span>
                        <a href="<?= BASE_URL ?>?action=admin-review-delete&id=<?= $rev['id'] ?>&redirect_product=<?= $product['id'] ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Xóa đánh giá vi phạm này?');">Xóa đánh giá</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#94a3b8; text-align:center; padding:20px 0; margin:0;">Chưa có đánh giá nào cho sản phẩm này.</p>
        <?php endif; ?>
    </div>
</div>
