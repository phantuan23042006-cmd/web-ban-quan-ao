<?php
$stats = $stats ?? [];
$overview = $overview ?? [];
$categories = $categories ?? [];
$reviews = $reviews ?? [];
$recentActivity = $recentActivity ?? [];

$total_users = (int) ($stats['total_users'] ?? 0);
$active_users = (int) ($stats['active_users'] ?? 0);
$blocked_users = (int) ($stats['blocked_users'] ?? 0);
$admin_users = (int) ($stats['total_admins'] ?? 0);

$total_products = (int) ($overview['total_products'] ?? 0);
$total_orders = (int) ($overview['total_orders'] ?? 0);
$pending_orders = (int) ($overview['pending_orders'] ?? 0);
$completed_orders = (int) ($overview['completed_orders'] ?? 0);
$total_revenue = (int) ($overview['total_revenue'] ?? 0);
$category_count = (int) ($categoryCount ?? count($categories));
$review_average = (float) ($reviewAverage ?? 0);
$review_count = (int) ($reviewCount ?? count($reviews));
?>

<style>
    .admin-dashboard-page {
        display: grid;
        gap: 24px;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 18px;
    }

    .dashboard-card {
        padding: 28px 24px;
        border-radius: 28px;
        background: linear-gradient(180deg, #ffffff 0%, #f8f3ff 100%);
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        min-height: 150px;
    }

    .dashboard-card h3 {
        margin: 0;
        color: #334155;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.22em;
        font-weight: 700;
    }

    .dashboard-card strong {
        display: block;
        margin-top: 20px;
        color: #0f172a;
        font-size: 40px;
        line-height: 1;
    }

    .dashboard-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .dashboard-action {
        padding: 24px;
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid #e5e7f0;
        box-shadow: 0 18px 35px rgba(15, 23, 42, 0.05);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        text-decoration: none;
        color: #111827;
        display: block;
        cursor: pointer;
    }

    .dashboard-action:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.1);
    }

    .dashboard-action h4 {
        margin: 0 0 8px;
        font-size: 18px;
    }

    .dashboard-action p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .dashboard-summary-card {
        padding: 28px;
        border-radius: 24px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 20px 40px rgba(15, 23, 42, 0.06);
        position: relative;
        z-index: 1;
    }

    .dashboard-summary-card h4 {
        margin: 0 0 18px;
        color: #0f172a;
        font-size: 20px;
        letter-spacing: -0.02em;
    }

    .dashboard-link-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-top: 12px;
        padding: 10px 14px;
        border: 0;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        text-decoration: none;
        font-weight: 700;
        cursor: pointer;
    }

    .dashboard-summary-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .dashboard-summary-mini {
        padding: 18px;
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    .dashboard-summary-mini strong {
        display: block;
        margin-top: 8px;
        font-size: 22px;
        color: #0f172a;
    }

    .dashboard-summary-mini span {
        color: #64748b;
        font-size: 13px;
    }

    .dashboard-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 18px;
    }

    .dashboard-list {
        display: grid;
        gap: 12px;
    }

    .dashboard-category-item,
    .dashboard-review-item,
    .dashboard-activity-item {
        padding: 18px 20px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.05);
        pointer-events: auto;
    }

    .dashboard-category-item {
        display: grid;
        gap: 10px;
    }

    .dashboard-category-head {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        font-weight: 700;
        color: #0f172a;
    }

    .dashboard-category-meta {
        color: #475569;
        font-size: 14px;
    }

    .dashboard-progress {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .dashboard-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(135deg, #7c3aed, #f472b6);
        transition: width 0.45s ease;
    }

    .dashboard-progress > span {
        display: block;
        height: 100%;
        border-radius: inherit;
        background: linear-gradient(135deg, #7c3aed, #db2777);
    }

    .dashboard-review-item strong,
    .dashboard-activity-item strong {
        display: block;
        margin-bottom: 8px;
        color: #0f172a;
    }

    .dashboard-review-item span,
    .dashboard-activity-item span {
        color: #475569;
        font-size: 14px;
        line-height: 1.7;
    }

    .dashboard-rating {
        color: #f97316;
        font-weight: 700;
        letter-spacing: 0.04em;
        margin-bottom: 10px;
    }

    .dashboard-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: #eef2ff;
        color: #4338ca;
        font-size: 13px;
        font-weight: 700;
    }

    .dashboard-category-desc {
        color: #475569;
        font-size: 14px;
        line-height: 1.7;
        margin-bottom: 12px;
    }

    .dashboard-summary-card h4 {
        user-select: none;
    }

    @media (max-width: 980px) {
        .dashboard-cards,
        .dashboard-actions,
        .dashboard-summary {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-dashboard-page">
    <div class="dashboard-cards">
        <article class="dashboard-card">
            <h3>Tổng người dùng</h3>
            <strong><?= e($total_users) ?></strong>
        </article>
        <article class="dashboard-card">
            <h3>Sản phẩm</h3>
            <strong><?= e($total_products) ?></strong>
        </article>
        <article class="dashboard-card">
            <h3>Đơn hàng</h3>
            <strong><?= e($total_orders) ?></strong>
        </article>
        <article class="dashboard-card">
            <h3>Đơn chờ xử lý</h3>
            <strong><?= e($pending_orders) ?></strong>
        </article>
        <article class="dashboard-card">
            <h3>Doanh thu</h3>
            <strong><?= e(number_format($total_revenue, 0, ',', '.')) ?>đ</strong>
        </article>
        <article class="dashboard-card">
            <h3>Đánh giá</h3>
            <strong><?= e($review_average) ?>/5</strong>
        </article>
    </div>

    <div class="dashboard-actions">
        <a class="dashboard-action" href="<?= BASE_URL ?>?action=admin-users">
            <h4>Quản lý người dùng</h4>
            <p>Xem và quản lý tài khoản khách hàng và admin.</p>
        </a>
        <a class="dashboard-action" href="<?= BASE_URL ?>?action=admin-products">
            <h4>Quản lý sản phẩm</h4>
            <p>Thêm, chỉnh sửa và lọc sản phẩm theo danh mục.</p>
        </a>
        <a class="dashboard-action" href="<?= BASE_URL ?>?action=admin-orders">
            <h4>Quản lý đơn hàng</h4>
            <p>Xem trạng thái và chi tiết đơn hàng của khách.</p>
        </a>
    </div>

    <div class="dashboard-detail-grid">
        <article class="dashboard-summary-card">
            <h4>Danh mục sản phẩm</h4>
            <div class="dashboard-list">
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <div class="dashboard-category-item">
                            <div class="dashboard-category-head">
                                <span><?= e($category['name']) ?></span>
                                <span><?= e($category['count']) ?> sản phẩm</span>
                            </div>
                            <?php if (!empty($category['description'])): ?>
                                <div class="dashboard-category-desc">
                                    <?= e($category['description']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="dashboard-progress">
                                <span style="width: <?= e($category['percent']) ?>%"></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dashboard-category-item">
                        <span>Không tìm thấy danh mục sản phẩm. Vui lòng kiểm tra dữ liệu `categories` hoặc `products`.</span>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <article class="dashboard-summary-card">
            <h4>Đánh giá gần đây <span class="dashboard-badge"><?= e($review_count) ?> đánh giá</span></h4>
            <div class="dashboard-list">
                <?php if (!empty($reviews)): ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="dashboard-review-item">
                            <strong><?= e($review['customer'] ?? 'Khách hàng ẩn danh') ?></strong>
                            <div class="dashboard-rating">★ <?= e($review['rating'] ?? 0) ?>/5</div>
                            <span><?= e($review['comment'] ?? 'Không có nội dung review.') ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="dashboard-review-item">
                        <span>Chưa có đánh giá nào để hiển thị.</span>
                    </div>
                <?php endif; ?>
            </div>
            <a class="dashboard-link-btn" href="<?= BASE_URL ?>?action=admin-reviews">Đi tới quản lý đánh giá</a>
        </article>
    </div>

    <article class="dashboard-summary-card">
        <h4>Hoạt động gần đây</h4>
        <div class="dashboard-list">
            <?php if (!empty($recentActivity)): ?>
                <?php foreach ($recentActivity as $activity): ?>
                    <div class="dashboard-activity-item">
                        <strong><?= e($activity['title']) ?></strong>
                        <span><?= e($activity['meta']) ?> · <?= e($activity['time']) ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="dashboard-activity-item">
                    <span>Chưa có hoạt động mới.</span>
                </div>
            <?php endif; ?>
        </div>
        <a class="dashboard-link-btn" href="<?= BASE_URL ?>?action=admin-orders">Xem đơn hàng</a>
    </article>
</div>
