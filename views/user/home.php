<div class="container storefront">

    <!-- Bad Habits Hero Streetwear Banner -->
    <section class="storefront-hero" style="background:linear-gradient(135deg, #090d16 0%, #1e1b4b 50%, #000000 100%);color:#fff;border-radius:24px;padding:60px 48px;position:relative;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.25);border:1px solid #334155;margin-bottom:40px">
        <div style="max-width:680px;position:relative;z-index:2">
            <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(225,29,72,0.15);color:#f43f5e;border:1px solid rgba(225,29,72,0.3);padding:6px 14px;border-radius:999px;font-size:12px;font-weight:800;letter-spacing:1px;text-transform:uppercase;margin-bottom:18px">
                🔥 TAN & TUAN CLOTHING • NEW COLLECTION 2026
            </div>
            <h1 style="font-family:'Space Grotesk',sans-serif;font-size:3.2rem;font-weight:900;line-height:1.1;letter-spacing:-1px;margin:0 0 16px;text-transform:uppercase;color:#ffffff">
                BADDEST GEAR<br>YOU WANT <span style="background:linear-gradient(135deg,#e11d48,#f43f5e);-webkit-background-clip:text;-webkit-text-fill-color:transparent">SO BAD.</span>
            </h1>
            <p style="font-size:1.1rem;color:#cbd5e1;line-height:1.6;margin:0 0 28px;font-weight:400">
                Khám phá những mẫu sản phẩm Streetwear đậm chất văn hóa đường phố Sài Gòn — Áo T-Shirt, Hoodie, Jacket & Phụ kiện đỉnh cao.
            </p>
            <div style="display:flex;gap:14px;flex-wrap:wrap">
                <a class="button primary" href="<?= BASE_URL ?>?action=products" style="background:#e11d48;color:#fff;font-family:'Space Grotesk',sans-serif;font-weight:800;font-size:1rem;padding:14px 28px;border-radius:12px;text-transform:uppercase;letter-spacing:1px;text-decoration:none;box-shadow:0 6px 20px rgba(225,29,72,0.4)">
                    🛍️ MUA SẮM NGAY
                </a>
                <a class="button secondary" href="<?= BASE_URL ?>?action=categories" style="background:rgba(255,255,255,0.1);color:#fff;border:1px solid rgba(255,255,255,0.2);font-family:'Space Grotesk',sans-serif;font-weight:700;font-size:1rem;padding:14px 24px;border-radius:12px;text-transform:uppercase;letter-spacing:1px;text-decoration:none">
                    BỘ SƯU TẬP →
                </a>
            </div>
        </div>
        <div style="position:absolute;right:-40px;bottom:-40px;font-family:'Space Grotesk',sans-serif;font-size:15rem;font-weight:900;color:rgba(255,255,255,0.02);user-select:none;pointer-events:none;line-height:1;text-transform:uppercase">
            BAD
        </div>
    </section>

    <!-- Streetwear Ticker Marquee Strip -->
    <div style="background:#0f172a;color:#cbd5e1;padding:12px 20px;border-radius:14px;margin-bottom:48px;font-family:'Space Grotesk',sans-serif;font-weight:800;font-size:13px;letter-spacing:1.5px;text-transform:uppercase;display:flex;justify-content:space-around;flex-wrap:wrap;gap:12px;border:1px solid #1e293b">
        <span>⚡ 100% STREETWEAR AUTHENTIC</span>
        <span>⚡ DESIGNED IN SAIGON</span>
        <span>⚡ PREMIUM HEAVYWEIGHT COTTON</span>
        <span>⚡ FAST NATIONWIDE SHIPPING</span>
    </div>

    <!-- Categories Section -->
    <section style="margin-bottom:48px">
        <div class="section-title" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px">
            <div>
                <span class="eyebrow" style="color:#e11d48;font-weight:800;letter-spacing:1.5px">BỘ SƯU TẬP</span>
                <h2 style="font-family:'Space Grotesk',sans-serif;font-size:1.8rem;font-weight:800;text-transform:uppercase;margin:4px 0 0;color:#0f172a">DANH MỤC SẢN PHẨM</h2>
            </div>
            <a href="<?= BASE_URL ?>?action=categories" style="color:#6366f1;font-weight:700;text-decoration:none;font-size:14px">Xem tất cả →</a>
        </div>
        <div class="category-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:16px">
            <?php foreach ($categories as $category): ?>
                <a class="category-tile" href="<?= BASE_URL ?>?action=products&category=<?= e($category['id']) ?>" style="background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:16px;padding:24px;text-decoration:none;transition:all 0.25s ease;display:flex;flex-direction:column;justify-content:space-between;min-height:130px">
                    <div>
                        <strong style="font-family:'Space Grotesk',sans-serif;font-size:1.15rem;font-weight:800;color:#0f172a;display:block;text-transform:uppercase"><?= e($category['name']) ?></strong>
                        <small style="color:#64748b;font-size:12px;font-weight:600;margin-top:4px;display:block"><?= e($category['product_count']) ?> sản phẩm</small>
                    </div>
                    <span style="color:#e11d48;font-weight:800;font-size:13px;letter-spacing:0.5px;margin-top:16px">Khám phá →</span>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- New Arrivals Section -->
    <section style="margin-bottom:48px">
        <div class="section-title" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px">
            <div>
                <span class="eyebrow" style="color:#e11d48;font-weight:800;letter-spacing:1.5px">MỚI VỀ</span>
                <h2 style="font-family:'Space Grotesk',sans-serif;font-size:1.8rem;font-weight:800;text-transform:uppercase;margin:4px 0 0;color:#0f172a">NEW ARRIVALS 2026</h2>
            </div>
            <a href="<?= BASE_URL ?>?action=products" style="color:#6366f1;font-weight:700;text-decoration:none;font-size:14px">Xem tất cả sản phẩm →</a>
        </div>
        <div class="product-grid">
            <?php foreach ($newProducts as $product) { require __DIR__ . '/partials/product_card.php'; } ?>
        </div>
    </section>

    <!-- Featured Products Section -->
    <?php if (!empty($featuredProducts)): ?>
        <section style="margin-bottom:48px">
            <div class="section-title" style="display:flex;justify-content:space-between;align-items:flex-end;margin-bottom:24px">
                <div>
                    <span class="eyebrow" style="color:#e11d48;font-weight:800;letter-spacing:1.5px">BEST SELLERS</span>
                    <h2 style="font-family:'Space Grotesk',sans-serif;font-size:1.8rem;font-weight:800;text-transform:uppercase;margin:4px 0 0;color:#0f172a">SẢN PHẨM NỔI BẬT</h2>
                </div>
            </div>
            <div class="product-grid">
                <?php foreach ($featuredProducts as $product) { require __DIR__ . '/partials/product_card.php'; } ?>
            </div>
        </section>
    <?php endif; ?>

</div>
