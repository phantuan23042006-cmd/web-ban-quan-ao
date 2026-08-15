<div class="container storefront">

    <section class="catalog-heading">
        <span class="eyebrow" style="color:#e11d48;font-weight:800;letter-spacing:1.5px">TAN & TUAN STREETWEAR</span><h1 style="font-family:'Space Grotesk',sans-serif;font-size:2.4rem;font-weight:900;text-transform:uppercase;margin:4px 0 8px">TẤT CẢ SẢN PHẨM</h1>
        <p>Tìm kiếm, lọc và sắp xếp để chọn món đồ phù hợp nhất.</p>
    </section>

    <form class="catalog-filter" action="<?= BASE_URL ?>" method="get">
        <input type="hidden" name="action" value="products">
        <input name="keyword" value="<?= e($filters['keyword']) ?>" placeholder="Tìm tên sản phẩm…">
        <select name="category">
            <option value="">Tất cả danh mục</option>
            <?php foreach ($categories as $category): ?>
                <option value="<?= e($category['id']) ?>" <?= (int) $filters['danh_muc_id'] === (int) $category['id'] ? 'selected' : '' ?>>
                    <?= e($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <input name="min_price" type="number" min="0" value="<?= e($filters['min_price']) ?>" placeholder="Giá từ">
        <input name="max_price" type="number" min="0" value="<?= e($filters['max_price']) ?>" placeholder="Giá đến">
        <select name="sort">
            <option value="">Mới nhất</option>
            <option value="rating" <?= $filters['sort'] === 'rating' ? 'selected' : '' ?>>Đánh giá cao</option>
            <option value="price_asc" <?= $filters['sort'] === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
            <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
        </select>
        <button class="button primary">Lọc sản phẩm</button>
    </form>

    <div class="product-grid">
        <?php if ($products): ?>
            <?php foreach ($products as $product) { require __DIR__ . '/partials/product_card.php'; } ?>
        <?php else: ?>
            <p class="empty">Không tìm thấy sản phẩm phù hợp.</p>
        <?php endif; ?>
    </div>

    <?php if (!empty($pagination) && $pagination['total_pages'] > 1): ?>
        <?php
            $currentPage = $pagination['current_page'];
            $totalPages  = $pagination['total_pages'];
            $queryParams = $_GET;
        ?>
        <nav class="pagination-wrapper" style="margin-top:36px;display:flex;flex-direction:column;align-items:center;gap:12px">
            <div style="font-size:13px;color:#64748b">
                Trang <strong><?= $currentPage ?></strong> / <strong><?= $totalPages ?></strong> (Tổng <strong><?= $pagination['total_items'] ?></strong> sản phẩm)
            </div>
            <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:center">
                <?php if ($currentPage > 1): ?>
                    <?php $queryParams['page'] = $currentPage - 1; ?>
                    <a href="<?= BASE_URL ?>?<?= http_build_query($queryParams) ?>" class="button secondary compact" style="padding:8px 14px;border-radius:10px;text-decoration:none;font-weight:600">
                        ← Trang trước
                    </a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php $queryParams['page'] = $p; ?>
                    <?php if ($p == $currentPage): ?>
                        <span style="padding:8px 14px;border-radius:10px;background:#6366f1;color:#fff;font-weight:700;font-size:14px">
                            <?= $p ?>
                        </span>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>?<?= http_build_query($queryParams) ?>" style="padding:8px 14px;border-radius:10px;background:#f1f5f9;color:#334155;font-weight:600;font-size:14px;text-decoration:none">
                            <?= $p ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <?php $queryParams['page'] = $currentPage + 1; ?>
                    <a href="<?= BASE_URL ?>?<?= http_build_query($queryParams) ?>" class="button secondary compact" style="padding:8px 14px;border-radius:10px;text-decoration:none;font-weight:600">
                        Trang sau →
                    </a>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>

</div>
