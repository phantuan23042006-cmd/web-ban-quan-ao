<?php
$products = $products ?? [];
$categories = $categories ?? [];
$keyword = $keyword ?? '';
$danhMucId = $danhMucId ?? '';
?>

<style>
    .store-page {
        display: flex;
        flex-direction: column;
        gap: 28px;
        margin-top: 16px;
    }

    .store-hero {
        padding: 32px;
        border-radius: 24px;
        background: linear-gradient(135deg, #0f172a, #7c3aed);
        color: #ffffff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .store-hero h1 {
        margin: 0 0 8px;
        font-size: 32px;
        font-weight: 800;
    }

    .store-hero p {
        margin: 0;
        color: rgba(255, 255, 255, 0.84);
        line-height: 1.6;
        font-size: 15px;
    }

    .filter-bar {
        background: #ffffff;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.04);
    }

    .filter-form {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .filter-form input,
    .filter-form select {
        min-height: 44px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        background: #f8fafc;
    }

    .filter-form input {
        flex: 1;
        min-width: 200px;
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

    .store-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
    }

    .product-card {
        background: #ffffff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .product-card-img {
        width: 100%;
        height: 240px;
        object-fit: cover;
        background: #f8fafc;
    }

    .product-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
        justify-content: space-between;
        gap: 12px;
    }

    .product-card-category {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        color: #7c3aed;
        letter-spacing: 0.05em;
    }

    .product-card-title {
        font-size: 17px;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        line-height: 1.4;
    }

    .product-card-price {
        font-size: 18px;
        font-weight: 800;
        color: #db2777;
    }

    .product-card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
    }

    .rating-score {
        color: #eab308;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .store-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container store-page">
    <section class="store-hero">
        <div>
            <h1>Bộ Sưu Tập Thời Trang Nam</h1>
            <p>Sản phẩm chất lượng cao, kiểu dáng hiện đại, màu sắc bắt mắt.</p>
        </div>
    </section>

    <!-- Thanh Lọc -->
    <div class="filter-bar">
        <form class="filter-form" action="<?= BASE_URL ?>" method="get">
            <input type="hidden" name="action" value="products">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tìm kiếm tên sản phẩm...">
            <select name="danh_muc_id">
                <option value="">-- Tất cả danh mục --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat['id']) ?>" <?= (string)$danhMucId === (string)$cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <?php if ($keyword !== '' || $danhMucId !== ''): ?>
                <a href="<?= BASE_URL ?>?action=products" class="btn btn-secondary">Đặt lại</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Danh sách Sản Phẩm -->
    <div class="store-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $p): ?>
                <a href="<?= BASE_URL ?>?action=product-detail&id=<?= $p['id'] ?>" style="text-decoration: none; color: inherit;">
                    <article class="product-card">
                        <?php if (!empty($p['anh'])): ?>
                            <img src="<?= BASE_ASSETS_UPLOADS . e($p['anh']) ?>" alt="<?= e($p['name']) ?>" class="product-card-img">
                        <?php else: ?>
                            <div class="product-card-img" style="display:flex; align-items:center; justify-content:center; color:#94a3b8;">Chưa có ảnh</div>
                        <?php endif; ?>

                        <div class="product-card-body">
                            <div>
                                <span class="product-card-category"><?= e($p['category_name'] ?? 'Thời trang') ?></span>
                                <h3 class="product-card-title" style="margin-top:4px;"><?= e($p['name']) ?></h3>
                            </div>

                            <div class="product-card-price">
                                <?php if ($p['gia_tu'] == $p['gia_den'] && $p['gia_tu'] > 0): ?>
                                    <?= number_format($p['gia_tu'], 0, ',', '.') ?>đ
                                <?php elseif ($p['gia_tu'] > 0): ?>
                                    <?= number_format($p['gia_tu'], 0, ',', '.') ?>đ - <?= number_format($p['gia_den'], 0, ',', '.') ?>đ
                                <?php else: ?>
                                    Chưa cập nhật giá
                                <?php endif; ?>
                            </div>

                            <div class="product-card-footer">
                                <span class="rating-score">
                                    ⭐ <?= e($p['danh_gia_tb'] > 0 ? $p['danh_gia_tb'] : 'Mới') ?>
                                </span>
                                <span style="color:#7c3aed; font-weight:700;">Xem chi tiết →</span>
                            </div>
                        </div>
                    </article>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 48px; background: #ffffff; border-radius: 20px; border: 1px solid #e2e8f0; color: #64748b;">
                Không tìm thấy sản phẩm nào phù hợp.
            </div>
        <?php endif; ?>
    </div>
</div>
