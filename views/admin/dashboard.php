<?php
$stats          = $stats ?? [];
$overview       = $overview ?? [];
$categories     = $categories ?? [];
$reviews        = $reviews ?? [];
$recentActivity = $recentActivity ?? [];

$total_users      = (int) ($stats['total_users'] ?? 0);
$active_users     = (int) ($stats['active_users'] ?? 0);
$blocked_users    = (int) ($stats['blocked_users'] ?? 0);
$admin_users      = (int) ($stats['total_admins'] ?? 0);

$total_products   = (int) ($overview['total_products'] ?? 0);
$total_orders     = (int) ($overview['total_orders'] ?? 0);
$pending_orders   = (int) ($overview['pending_orders'] ?? 0);
$completed_orders = (int) ($overview['completed_orders'] ?? 0);
$total_revenue    = (int) ($overview['total_revenue'] ?? 0);
$category_count   = (int) ($categoryCount ?? count($categories));
$review_average   = (float) ($reviewAverage ?? 0);
$review_count     = (int) ($reviewCount ?? count($reviews));
?>

<style>
    .dash-container {
        display: grid;
        gap: 28px;
    }

    /* Metric Grid */
    .dash-metrics {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 20px;
    }

    .dash-card {
        position: relative;
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .dash-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        border-color: rgba(99, 102, 241, 0.3);
    }

    .dash-card h3 {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dash-card .value {
        font-size: 32px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 12px;
        letter-spacing: -0.5px;
        line-height: 1;
    }

    .dash-card .subtext {
        margin-top: 12px;
        font-size: 12px;
        color: #94a3b8;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Action Links */
    .dash-quick-links {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
    }

    .quick-link-card {
        padding: 22px 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .quick-link-card:hover {
        background: #faf5ff;
        border-color: #c084fc;
        transform: translateY(-2px);
    }

    .quick-link-title {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
    }

    .quick-link-sub {
        font-size: 13px;
        color: #64748b;
        margin-top: 4px;
    }

    .quick-link-arrow {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #6366f1;
        display: grid;
        place-items: center;
        font-weight: 800;
        font-size: 16px;
        transition: all 0.2s ease;
    }

    .quick-link-card:hover .quick-link-arrow {
        background: #6366f1;
        color: #ffffff;
        transform: translateX(3px);
    }

    /* Section Panels */
    .dash-grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }

    .dash-panel {
        padding: 26px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
    }

    .dash-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .dash-panel-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Category progress */
    .cat-progress-item {
        margin-bottom: 16px;
    }

    .cat-progress-item:last-child {
        margin-bottom: 0;
    }

    .cat-progress-info {
        display: flex;
        justify-content: space-between;
        font-size: 14px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 6px;
    }

    .cat-bar-wrap {
        height: 8px;
        border-radius: 999px;
        background: #f1f5f9;
        overflow: hidden;
    }

    .cat-bar-fill {
        height: 100%;
        border-radius: 999px;
        background: linear-gradient(90deg, #6366f1, #ec4899);
        transition: width 0.5s ease;
    }

    /* Reviews list */
    .rev-item {
        padding: 14px 16px;
        border-radius: 14px;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        margin-bottom: 12px;
    }

    .rev-item:last-child {
        margin-bottom: 0;
    }

    .btn-gradient {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 10px 18px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        font-weight: 700;
        font-size: 13px;
        text-decoration: none;
        transition: opacity 0.2s ease;
        margin-top: 16px;
        width: 100%;
    }

    .btn-gradient:hover {
        opacity: 0.92;
    }

    @media (max-width: 990px) {
        .dash-grid-2 { grid-template-columns: 1fr; }
    }
</style>

<div class="dash-container">

    <!-- Top Metrics -->
    <div class="dash-metrics">

        <div class="dash-card">
            <h3>Khách hàng</h3>
            <div class="value"><?= e($total_users) ?></div>
            <div class="subtext">
                <span style="color:#10b981;font-weight:800"><?= e($active_users) ?></span> đang hoạt động
            </div>
        </div>

        <div class="dash-card">
            <h3>Sản phẩm</h3>
            <div class="value"><?= e($total_products) ?></div>
            <div class="subtext">
                Thuộc <strong><?= e($category_count) ?></strong> danh mục
            </div>
        </div>

        <div class="dash-card">
            <h3>Tổng đơn hàng</h3>
            <div class="value"><?= e($total_orders) ?></div>
            <div class="subtext">
                <span style="color:#10b981;font-weight:800"><?= e($completed_orders) ?></span> hoàn thành
            </div>
        </div>

        <div class="dash-card">
            <h3>Đơn chờ xử lý</h3>
            <div class="value" style="color:#e11d48"><?= e($pending_orders) ?></div>
            <div class="subtext" style="color:#e11d48">
                Cần xác nhận ngay
            </div>
        </div>

        <div class="dash-card">
            <h3>Doanh thu</h3>
            <div class="value" style="font-size:26px;color:#059669"><?= number_format($total_revenue, 0, ',', '.') ?>đ</div>
            <div class="subtext" style="color:#059669">
                Từ đơn hoàn thành
            </div>
        </div>

        <div class="dash-card">
            <h3>Đánh giá TB</h3>
            <div class="value" style="color:#0891b2"><?= e($review_average) ?><span style="font-size:16px;color:#94a3b8">/5</span></div>
            <div class="subtext">
                Tổng <strong><?= e($review_count) ?></strong> nhận xét
            </div>
        </div>

    </div>

    <!-- Quick Navigation Links -->
    <div class="dash-quick-links">
        <a class="quick-link-card" href="<?= BASE_URL ?>?action=admin-users">
            <div>
                <div class="quick-link-title">Quản lý người dùng</div>
                <div class="quick-link-sub">Xem, phân quyền và khóa tài khoản</div>
            </div>
            <div class="quick-link-arrow">→</div>
        </a>

        <a class="quick-link-card" href="<?= BASE_URL ?>?action=admin-products">
            <div>
                <div class="quick-link-title">Quản lý sản phẩm</div>
                <div class="quick-link-sub">Thêm sản phẩm, cập nhật size & tồn kho</div>
            </div>
            <div class="quick-link-arrow">→</div>
        </a>

        <a class="quick-link-card" href="<?= BASE_URL ?>?action=admin-orders">
            <div>
                <div class="quick-link-title">Quản lý đơn hàng</div>
                <div class="quick-link-sub">Cập nhật trạng thái giao hàng</div>
            </div>
            <div class="quick-link-arrow">→</div>
        </a>
    </div>

    <!-- Detail Grid: Categories & Reviews -->
    <div class="dash-grid-2">

        <!-- Categories Distribution -->
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h3>Phân loại sản phẩm</h3>
                <span style="font-size:13px;font-weight:700;color:#6366f1"><?= count($categories) ?> Danh mục</span>
            </div>

            <?php if (!empty($categories)): ?>
                <?php
                    $sumProdCount = 0;
                    foreach ($categories as $c) {
                        $sumProdCount += (int) ($c['product_count'] ?? $c['count'] ?? 0);
                    }
                ?>
                <?php foreach ($categories as $category): ?>
                    <?php
                        $cCount   = (int) ($category['product_count'] ?? $category['count'] ?? 0);
                        $cPercent = ($sumProdCount > 0) ? round(($cCount / $sumProdCount) * 100) : 0;
                    ?>
                    <div class="cat-progress-item">
                        <div class="cat-progress-info">
                            <span><?= e($category['name']) ?></span>
                            <span style="color:#64748b"><?= $cCount ?> sản phẩm (<?= $cPercent ?>%)</span>
                        </div>
                        <div class="cat-bar-wrap">
                            <div class="cat-bar-fill" style="width: <?= $cPercent ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#94a3b8;font-size:14px;text-align:center">Chưa có danh mục nào.</p>
            <?php endif; ?>

            <a class="btn-gradient" href="<?= BASE_URL ?>?action=admin-categories">Quản lý danh mục →</a>
        </div>

        <!-- Recent Reviews -->
        <div class="dash-panel">
            <div class="dash-panel-header">
                <h3>Đánh giá gần đây</h3>
                <span style="font-size:13px;font-weight:700;color:#0891b2"><?= e($review_count) ?> nhận xét</span>
            </div>

            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $review): ?>
                    <?php
                        $rCustomer = $review['user_name'] ?? $review['customer'] ?? 'Khách hàng';
                        $rProduct  = $review['product_name'] ?? '';
                        $rRating   = (int) ($review['so_sao'] ?? $review['rating'] ?? 5);
                        $rComment  = $review['noi_dung'] ?? $review['comment'] ?? 'Không có nội dung.';
                        $rDate     = !empty($review['created_at']) ? date('d/m/Y H:i', strtotime($review['created_at'])) : '';
                    ?>
                    <div class="rev-item">
                        <div style="display:flex;justify-content:space-between;align-items:center">
                            <strong style="color:#0f172a;font-size:14px"><?= e($rCustomer) ?></strong>
                            <div style="color:#f59e0b;font-weight:800;font-size:13px">
                                <?= $rRating ?>/5 sao
                            </div>
                        </div>
                        <?php if ($rProduct !== ''): ?>
                            <div style="font-size:12px;color:#6366f1;font-weight:700;margin-top:2px">
                                <?= e($rProduct) ?>
                            </div>
                        <?php endif; ?>
                        <p style="margin:4px 0 0;font-size:13px;color:#475569;line-height:1.4">
                            "<?= e($rComment) ?>"
                        </p>
                        <?php if ($rDate !== ''): ?>
                            <small style="font-size:11px;color:#94a3b8;display:block;margin-top:4px"><?= e($rDate) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color:#94a3b8;font-size:14px;text-align:center">Chưa có đánh giá nào.</p>
            <?php endif; ?>

            <a class="btn-gradient" href="<?= BASE_URL ?>?action=admin-reviews">Tất cả đánh giá →</a>
        </div>

    </div>

    <!-- Recent Activity -->
    <div class="dash-panel">
        <div class="dash-panel-header">
            <h3>Hoạt động đơn hàng gần đây</h3>
            <a href="<?= BASE_URL ?>?action=admin-orders" style="color:#6366f1;font-weight:700;font-size:13px;text-decoration:none">Xem tất cả đơn hàng →</a>
        </div>

        <?php if (!empty($recentActivity)): ?>
            <div style="display:grid;gap:12px">
                <?php foreach ($recentActivity as $act): ?>
                    <div style="padding:14px 18px;border-radius:14px;background:#f8fafc;border:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
                        <div>
                            <strong style="color:#0f172a;font-size:14px;display:block"><?= e($act['title']) ?></strong>
                            <span style="font-size:12px;color:#64748b"><?= e($act['time']) ?></span>
                        </div>
                        <span style="font-weight:800;color:#db2777;font-size:15px"><?= e($act['meta']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="color:#94a3b8;font-size:14px;text-align:center">Chưa có hoạt động nào mới.</p>
        <?php endif; ?>
    </div>

</div>
