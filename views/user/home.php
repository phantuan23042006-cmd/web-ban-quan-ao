<div class="container storefront">
    <section class="storefront-hero">
        <div><span class="eyebrow">FASHION STORE</span><h1>Phong cách mới, dành riêng cho bạn.</h1><p>Khám phá những sản phẩm được cập nhật từ kho hàng thực tế của Fashion Store.</p><a class="button primary" href="<?= BASE_URL ?>?action=products">Mua sắm ngay</a></div>
    </section>
    <section><div class="section-title"><div><span class="eyebrow">DANH MỤC</span><h2>Chọn phong cách của bạn</h2></div><a href="<?= BASE_URL ?>?action=categories">Xem tất cả</a></div><div class="category-grid">
        <?php foreach ($categories as $category): ?><a class="category-tile" href="<?= BASE_URL ?>?action=products&category=<?= e($category['id']) ?>"><strong><?= e($category['name']) ?></strong><small><?= e($category['product_count']) ?> sản phẩm</small></a><?php endforeach; ?>
    </div></section>
    <section><div class="section-title"><div><span class="eyebrow">MỚI VỀ</span><h2>Sản phẩm mới</h2></div><a href="<?= BASE_URL ?>?action=products">Xem sản phẩm</a></div><div class="product-grid"><?php foreach ($newProducts as $product) { require __DIR__ . '/partials/product_card.php'; } ?></div></section>
    <?php if (!empty($featuredProducts)): ?><section><div class="section-title"><div><span class="eyebrow">ĐƯỢC YÊU THÍCH</span><h2>Sản phẩm nổi bật</h2></div></div><div class="product-grid"><?php foreach ($featuredProducts as $product) { require __DIR__ . '/partials/product_card.php'; } ?></div></section><?php endif; ?>
</div>
