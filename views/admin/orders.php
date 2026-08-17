<?php
/**
 * View Quản lý Đơn hàng Admin với Lọc theo Ngày & Doanh thu theo Ngày
 */
$fromDate         = $fromDate ?? '';
$toDate           = $toDate ?? '';
$totalRevenue     = (float) ($totalRevenue ?? 0);
$completedRevenue = (float) ($completedRevenue ?? 0);
$totalOrders      = (int) ($totalOrders ?? count($orders ?? []));
?>
<style>
    .admin-orders-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .admin-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .stat-info h4 {
        margin: 0 0 4px;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-info .stat-value {
        margin: 0;
        font-size: 22px;
        font-weight: 800;
        color: #0f172a;
    }

    .admin-orders-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.03);
    }

    .admin-orders-toolbar {
        margin-bottom: 20px;
    }

    .admin-orders-toolbar h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
    }

    .admin-orders-form {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        align-items: end;
    }

    .admin-orders-form .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .admin-orders-form label {
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .admin-orders-form input[type="text"],
    .admin-orders-form input[type="date"],
    .admin-orders-form select {
        padding: 10px 14px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        transition: all .15s ease;
        background: #f8fafc;
    }

    .admin-orders-form input:focus,
    .admin-orders-form select:focus {
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .btn-group {
        display: flex;
        gap: 8px;
    }

    .btn-submit {
        background: #7c3aed;
        color: #ffffff;
        border: none;
        padding: 11px 18px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: background .15s ease;
        flex: 1;
        text-align: center;
        text-decoration: none;
    }

    .btn-submit:hover {
        background: #6d28d9;
    }

    .btn-reset {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        padding: 11px 14px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-reset:hover {
        background: #e2e8f0;
        color: #1e293b;
    }

    .admin-orders-table-wrap {
        overflow-x: auto;
        padding: 0;
    }

    .admin-orders-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .admin-orders-table th {
        background: #f8fafc;
        padding: 14px 20px;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        border-bottom: 1px solid #e2e8f0;
    }

    .admin-orders-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 14px;
        vertical-align: middle;
    }

    .admin-orders-table tr:hover {
        background: #faf5ff;
    }

    .order-status-badge {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-update-form {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 4px;
    }

    .status-update-form select {
        padding: 4px 8px;
        font-size: 12px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
    }

    .status-update-form button {
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 700;
        background: #7c3aed;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-detail {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid #cbd5e1;
        transition: all .15s ease;
    }

    .btn-detail:hover {
        background: #7c3aed;
        color: #ffffff;
        border-color: #7c3aed;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    .admin-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 14px;
        color: #64748b;
        font-size: 14px;
    }

    .admin-pagination a {
        margin-left: 6px;
        padding: 8px 12px;
        border-radius: 10px;
        text-decoration: none;
        color: #4c1d95;
        font-weight: 700;
    }

    .admin-pagination a.active,
    .admin-pagination a:hover {
        background: #ede9fe;
    }
</style>

<div class="admin-orders-page">

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <!-- THỐNG KÊ DOANH THU KHI LỌC NGHỆ NGHỆ THUẬT -->
    <div class="admin-stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background:#ede9fe;color:#7c3aed">📦</div>
            <div class="stat-info">
                <h4>Đơn hàng đã lọc</h4>
                <p class="stat-value"><?= number_format($totalOrders) ?> đơn</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#fce7f3;color:#db2777">💰</div>
            <div class="stat-info">
                <h4>Tổng doanh thu theo lọc</h4>
                <p class="stat-value" style="color:#db2777"><?= number_format($totalRevenue, 0, ',', '.') ?>đ</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon" style="background:#dcfce7;color:#166534">🏆</div>
            <div class="stat-info">
                <h4>Doanh thu đã giao (Thực thu)</h4>
                <p class="stat-value" style="color:#166534"><?= number_format($completedRevenue, 0, ',', '.') ?>đ</p>
            </div>
        </div>
    </div>

    <!-- BỘ LỌC ĐƠN HÀNG VÀ DOANH THU THEO NGÀY -->
    <div class="admin-orders-card">
        <div class="admin-orders-toolbar">
            <h3>Quản lý & Lọc doanh thu đơn hàng</h3>
            <p style="margin:4px 0 0;color:#64748b;font-size:13px">Lọc đơn hàng theo Từ ngày - Đến ngày, từ khóa hoặc trạng thái xử lý.</p>
        </div>

        <form class="admin-orders-form" method="get">
            <input type="hidden" name="action" value="admin-orders">
            
            <div class="field">
                <label for="keyword">Mã đơn / Tên khách</label>
                <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Nhập mã đơn, tên khách...">
            </div>

            <div class="field">
                <label for="filter_status">Trạng thái đơn hàng</label>
                <select id="filter_status" name="filter_status">
                    <option value="">-- Tất cả trạng thái --</option>
                    <?php foreach ($statusLabels as $key => $st): ?>
                        <option value="<?= e($key) ?>" <?= $status === $key ? 'selected' : '' ?>>
                            <?= e($st['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field">
                <label for="from_date">Từ ngày</label>
                <input id="from_date" type="date" name="from_date" value="<?= e($fromDate) ?>">
            </div>

            <div class="field">
                <label for="to_date">Đến ngày</label>
                <input id="to_date" type="date" name="to_date" value="<?= e($toDate) ?>">
            </div>

            <div class="field btn-group">
                <button class="btn-submit" type="submit">🔍 Lọc dữ liệu</button>
                <a class="btn-reset" href="<?= BASE_URL ?>?action=admin-orders" title="Xóa tất cả bộ lọc">🔄 Bỏ lọc</a>
            </div>
        </form>
    </div>

    <!-- BẢNG DANH SÁCH ĐƠN HÀNG -->
    <?php if (!empty($orders)): ?>
        <div class="admin-orders-card admin-orders-table-wrap">
            <table class="admin-orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th>Trạng thái</th>
                        <th>Tổng tiền</th>
                        <th style="text-align:center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                            $stKey = $order['status'] ?? 'pending';
                            $stInfo = $statusLabels[$stKey] ?? ['label' => e($stKey), 'bg' => '#f1f5f9', 'color' => '#475569'];
                        ?>
                        <tr>
                            <td style="font-weight:700;font-family:monospace;color:#0f172a">
                                <?= e($order['order_code'] ?? ('#' . $order['id'])) ?>
                            </td>
                            <td style="font-weight:600">
                                <?= e($order['customer']) ?>
                            </td>
                            <td style="color:#64748b;font-size:13px">
                                <?= e(date('d/m/Y H:i', strtotime($order['date']))) ?>
                            </td>
                            <td>
                                <span class="order-status-badge" style="background:<?= e($stInfo['bg']) ?>;color:<?= e($stInfo['color']) ?>">
                                    <?= e($stInfo['label']) ?>
                                </span>

                                <?php $nextOpts = $allowedNextStatuses[$stKey] ?? []; ?>
                                <?php if (!empty($nextOpts) && in_array($stKey, ['pending', 'confirmed'], true)): ?>
                                    <form class="status-update-form" method="post" action="<?= BASE_URL ?>?action=admin-order-status">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="order_id" value="<?= e($order['id']) ?>">
                                        <select name="status">
                                            <?php foreach ($nextOpts as $sKey => $sLabel): ?>
                                                <option value="<?= e($sKey) ?>" <?= $stKey === $sKey ? 'selected' : '' ?>>
                                                    <?= e($sLabel) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit">Lưu</button>
                                    </form>
                                <?php else: ?>
                                    <small style="display:block;margin-top:4px;color:#94a3b8;font-size:11px;font-weight:600">(Đã khóa)</small>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight:800;color:#db2777">
                                <?= e($order['total']) ?>
                            </td>
                            <td style="text-align:center">
                                <a class="btn-detail" href="<?= BASE_URL ?>?action=admin-order-detail&id=<?= (int)$order['id'] ?>" title="Xem chi tiết đơn hàng">
                                    👁️ Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <span>Hiển thị <?= count($orders) ?> / <?= $totalOrders ?> đơn hàng</span>
            <div>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                        $query = array_filter([
                            'action'        => 'admin-orders',
                            'keyword'       => $keyword,
                            'filter_status' => $status,
                            'from_date'     => $fromDate,
                            'to_date'       => $toDate,
                            'page'          => $i,
                        ], fn($v) => $v !== '' && $v !== null);
                    ?>
                    <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= BASE_URL ?>?<?= http_build_query($query) ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="admin-orders-card" style="text-align:center;padding:48px 24px;color:#64748b">
            <div style="font-size:3rem;margin-bottom:12px">📦</div>
            <p style="font-size:16px;font-weight:600;margin:0 0 4px">Không tìm thấy đơn hàng phù hợp</p>
            <p style="font-size:13px;margin:0">Hãy thử chọn khoảng thời gian khác hoặc bỏ lọc trạng thái.</p>
        </div>
    <?php endif; ?>

</div>
