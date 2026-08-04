<section class="admin-section">
    <header class="admin-section-header">
        <h2>Thống kê bán hàng</h2>
        <p>Xem báo cáo tổng quan: tổng sản phẩm, đơn hàng, doanh thu và trạng thái đơn.</p>
    </header>

    <div class="admin-card admin-card--stats">
        <div>
            <strong>Tổng sản phẩm</strong>
            <p><?= number_format($totalProducts) ?></p>
        </div>
        <div>
            <strong>Tổng đơn hàng</strong>
            <p><?= number_format($totalOrders) ?></p>
        </div>
        <div>
            <strong>Doanh thu</strong>
            <p><?= number_format($totalRevenue, 0, ',', '.') ?>đ</p>
        </div>
        <div>
            <strong>Đơn hàng đang xử lý</strong>
            <p><?= number_format($pendingOrders) ?></p>
        </div>
        <div>
            <strong>Đơn hàng hoàn thành</strong>
            <p><?= number_format($completedOrders) ?></p>
        </div>
    </div>

    <?php if (!empty($stats)): ?>
        <div class="admin-card admin-card--info">
            <strong>Thống kê tài khoản</strong>
            <ul>
                <li>Tổng người dùng: <?= number_format($stats['total_users'] ?? 0) ?></li>
                <li>Người dùng hoạt động: <?= number_format($stats['active_users'] ?? 0) ?></li>
                <li>Người dùng bị khóa: <?= number_format($stats['blocked_users'] ?? 0) ?></li>
                <li>Quản trị viên: <?= number_format($stats['total_admins'] ?? 0) ?></li>
                <li>Thành viên: <?= number_format($stats['total_members'] ?? 0) ?></li>
            </ul>
        </div>
    <?php endif; ?>
</section>
