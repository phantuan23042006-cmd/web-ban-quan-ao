<?php
$categories = $categories ?? [];
?>

<style>
    .categories-page {
        display: grid;
        gap: 18px;
    }

    .category-card {
        padding: 22px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .category-card h3 {
        margin: 0 0 8px;
        color: #0f172a;
        font-size: 20px;
    }

    .category-card p {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }
</style>

<div class="categories-page">
    <?php foreach ($categories as $category): ?>
        <article class="category-card">
            <h3><?= e($category['emoji']) ?> <?= e($category['name']) ?></h3>
            <p><?= e($category['description']) ?></p>
        </article>
    <?php endforeach; ?>
</div>
