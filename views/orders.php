<?php
$orders = $orders ?? [];
?>

<style>
    .orders-page {
        display: grid;
        gap: 16px;
    }

    .order-card {
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .order-card h3 {
        margin: 0 0 8px;
        color: #0f172a;
    }

    .order-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 10px;
        color: #64748b;
        font-size: 14px;
    }
</style>

<div class="orders-page">
    <?php foreach ($orders as $order): ?>
        <article class="order-card">
            <h3><?= e($order['id']) ?></h3>
            <p>Ngày đặt: <?= e($order['date']) ?></p>
            <div class="order-meta">
                <span>Trạng thái: <?= e($order['status']) ?></span>
                <span>Tổng tiền: <?= e($order['total']) ?></span>
            </div>
        </article>
    <?php endforeach; ?>
</div>
