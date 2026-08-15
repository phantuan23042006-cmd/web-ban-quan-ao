<?php if (!is_array($product)) return; ?>
<article class="product-card" style="background:#fff;border:1px solid #e2e8f0;border-radius:18px;overflow:hidden;transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);position:relative">
    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= e($product['id']) ?>" style="text-decoration:none;color:inherit;display:block">
        <div class="product-image" style="aspect-ratio:1/1;width:100%;overflow:hidden;position:relative;background:#f8fafc;display:flex;align-items:center;justify-content:center">
            <?php if (!empty($product['anh'])): ?>
                <img src="<?= BASE_ASSETS_UPLOADS . e($product['anh']) ?>" alt="<?= e($product['name']) ?>" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)">
            <?php else: ?>
                <span style="color:#94a3b8;font-size:13px;font-weight:600">TAN & TUAN</span>
            <?php endif; ?>

            <div style="position:absolute;top:10px;left:10px;display:flex;gap:6px;z-index:2">
                <span style="background:#0f172a;color:#fff;font-family:'Space Grotesk',sans-serif;font-size:10px;font-weight:800;letter-spacing:0.5px;padding:4px 8px;border-radius:6px;text-transform:uppercase">
                    TAN & TUAN
                </span>
                <?php if (!empty($product['danh_gia_tb']) && $product['danh_gia_tb'] >= 4.5): ?>
                    <span style="background:#e11d48;color:#fff;font-family:'Space Grotesk',sans-serif;font-size:10px;font-weight:800;letter-spacing:0.5px;padding:4px 8px;border-radius:6px;text-transform:uppercase">
                        HOT 🔥
                    </span>
                <?php endif; ?>
            </div>
        </div>

        <div class="product-info" style="padding:16px">
            <small style="color:#64748b;font-size:11px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;display:block;margin-bottom:4px">
                <?= e($product['category_name'] ?? 'STREETWEAR') ?>
            </small>

            <h3 style="font-family:'Space Grotesk',sans-serif;font-size:1rem;font-weight:800;color:#0f172a;margin:0 0 10px;line-height:1.3;text-transform:uppercase;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;min-height:2.6em">
                <?= e($product['name']) ?>
            </h3>

            <div class="product-info-footer" style="display:flex;justify-content:space-between;align-items:center;border-top:1px solid #f1f5f9;padding-top:10px">
                <strong style="font-family:'Space Grotesk',sans-serif;font-size:1.1rem;font-weight:900;color:#e11d48">
                    <?= $product['gia_tu'] > 0 ? number_format($product['gia_tu'], 0, ',', '.') . 'đ' : 'Liên hệ' ?>
                </strong>

                <span class="rating" style="font-size:12px;font-weight:700;color:#f59e0b;background:#fef3c7;padding:3px 8px;border-radius:6px">
                    ★ <?= $product['danh_gia_tb'] > 0 ? e($product['danh_gia_tb']) : 'Mới' ?>
                </span>
            </div>

            <!-- Size Tags Preview -->
            <div style="display:flex;gap:4px;margin-top:10px">
                <span style="font-size:10px;font-weight:700;color:#475569;border:1px solid #cbd5e1;padding:2px 6px;border-radius:4px">S</span>
                <span style="font-size:10px;font-weight:700;color:#475569;border:1px solid #cbd5e1;padding:2px 6px;border-radius:4px">M</span>
                <span style="font-size:10px;font-weight:700;color:#475569;border:1px solid #cbd5e1;padding:2px 6px;border-radius:4px">L</span>
                <span style="font-size:10px;font-weight:700;color:#475569;border:1px solid #cbd5e1;padding:2px 6px;border-radius:4px">XL</span>
            </div>
        </div>
    </a>
</article>
