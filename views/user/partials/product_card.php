<article class="product-card">
    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= e($product['id']) ?>">
        <div class="product-image">
            <?php if (!empty($product['anh'])): ?>
                <img src="<?= BASE_ASSETS_UPLOADS . e($product['anh']) ?>" alt="<?= e($product['name']) ?>" loading="lazy">
            <?php else: ?>
                <span>Chưa có ảnh</span>
            <?php endif; ?>
        </div>
        <div class="product-info">
            <small><?= e($product['category_name'] ?? 'Thời trang') ?></small>
            <h3><?= e($product['name']) ?></h3>
            <div class="product-info-footer">
                <strong><?= $product['gia_tu'] > 0 ? number_format($product['gia_tu'], 0, ',', '.') . 'đ' : 'Liên hệ' ?></strong>
                <span class="rating">★ <?= $product['danh_gia_tb'] > 0 ? e($product['danh_gia_tb']) : 'Mới' ?></span>
            </div>
        </div>
    </a>
</article>
