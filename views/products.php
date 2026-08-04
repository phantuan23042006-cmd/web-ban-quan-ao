<?php
$products = $products ?? [];
?>

<style>
    .store-page {
        display: grid;
        gap: 24px;
    }

    .store-hero {
        padding: 28px;
        border-radius: 24px;
        background: linear-gradient(135deg, #111827, #7c3aed);
        color: #ffffff;
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    }

    .store-hero h1 {
        margin: 0 0 8px;
        font-size: 32px;
    }

    .store-hero p {
        margin: 0;
        color: rgba(255, 255, 255, 0.84);
        line-height: 1.7;
    }

    .store-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .store-card {
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .store-card-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
    }

    .store-card h3 {
        margin: 0;
        color: #0f172a;
    }

    .store-card .badge {
        padding: 6px 10px;
        border-radius: 999px;
        background: #f3e8ff;
        color: #6d28d9;
        font-size: 12px;
        font-weight: 800;
    }

    .store-card p {
        margin: 10px 0 0;
        color: #64748b;
        line-height: 1.7;
    }

    .store-price {
        margin-top: 14px;
        color: #db2777;
        font-weight: 800;
        font-size: 18px;
    }

    @media (max-width: 760px) {
        .store-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="store-page">
    <section class="store-hero">
        <h1>Sản phẩm thời trang mới</h1>
        <p>Khám phá bộ sưu tập quần áo hiện đại, từ áo thun basic đến áo khoác và quần jean cá tính.</p>
    </section>

    <div class="store-grid">
        <?php foreach ($products as $product): ?>
            <article class="store-card">
                <div class="store-card-head">
                    <h3><?= e($product['name']) ?></h3>
                    <span class="badge"><?= e($product['badge']) ?></span>
                </div>
                <p><?= e($product['category']) ?></p>
                <div class="store-price"><?= e($product['price']) ?></div>
            </article>
        <?php endforeach; ?>
    </div>
</div>
