<?php
$orders     = $orders ?? [];
$keyword    = $keyword ?? '';
$status     = $status ?? '';
$page       = $page ?? 1;
$totalPages = max(1, $totalPages ?? 1);

$statusLabels = [
    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'status-pending',   'bg' => '#fef3c7', 'color' => '#92400e'],
    'confirmed' => ['label' => 'Xác nhận',     'class' => 'status-confirmed', 'bg' => '#dbeafe', 'color' => '#1e40af'],
    'completed' => ['label' => 'Đã giao',      'class' => 'status-completed', 'bg' => '#dcfce7', 'color' => '#166534'],
    'cancelled' => ['label' => 'Đã hủy',       'class' => 'status-cancelled', 'bg' => '#fee2e2', 'color' => '#991b1b'],
];

$allowedNextStatuses = [
    'pending'   => ['pending' => 'Chờ xác nhận', 'confirmed' => 'Xác nhận', 'cancelled' => 'Đã hủy'],
    'confirmed' => ['confirmed' => 'Xác nhận', 'completed' => 'Đã giao', 'cancelled' => 'Đã hủy'],
    'completed' => [],
    'cancelled' => [],
];

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage   = $_SESSION['error_message']   ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .admin-orders-page {
        display: grid;
        gap: 24px;
    }

    .admin-orders-card {
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .admin-orders-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .admin-orders-toolbar h3 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .admin-orders-form {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
        margin-top: 16px;
    }

    .admin-orders-form .field {
        display: grid;
        gap: 8px;
    }

    .admin-orders-form label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .admin-orders-form input,
    .admin-orders-form select,
    .admin-orders-form button {
        min-height: 44px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #0f172a;
        font-size: 14px;
    }

    .admin-orders-form button {
        border: 0;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        font-weight: 700;
        cursor: pointer;
        transition: opacity .15s;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
    }

    .admin-orders-form button:hover {
        opacity: 0.9;
    }

    .admin-orders-table-wrap {
        overflow-x: auto;
    }

    .admin-orders-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 950px;
    }

    .admin-orders-table th,
    .admin-orders-table td {
        padding: 16px 14px;
        border-bottom: 1px solid #f1f5f9;
        text-align: left;
        vertical-align: middle;
    }

    .admin-orders-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .admin-orders-table td {
        color: #334155;
        font-size: 14px;
    }

    .order-status-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .status-update-form {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 8px;
    }

    .status-update-form select {
        padding: 4px 8px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 12px;
        background: #fff;
    }

    .status-update-form button {
        padding: 5px 10px;
        border-radius: 8px;
        border: none;
        background: #7c3aed;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
    }

    .status-update-form button:hover {
        background: #6d28d9;
    }

    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 10px;
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

    @media (max-width: 900px) {
        .admin-orders-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-orders-page">

    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <div class="admin-orders-card">
        <div class="admin-orders-toolbar">
            <div>
                <h3>Quản lý đơn hàng</h3>
                <p style="margin:4px 0 0;color:#64748b;font-size:13px">Lọc đơn theo mã hoặc trạng thái để xử lý nhanh.</p>
            </div>
        </div>

        <form class="admin-orders-form" method="get">
            <input type="hidden" name="action" value="admin-orders">
            <div class="field">
                <label for="keyword">Từ khóa tìm kiếm</label>
                <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Mã đơn hàng, tên khách hàng...">
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
                <button type="submit">🔍 Lọc đơn hàng</button>
            </div>
        </form>
    </div>

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
            <span>Hiển thị <?= count($orders) ?> đơn hàng</span>
            <div>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                        $query = array_filter([
                            'action'        => 'admin-orders',
                            'keyword'       => $keyword,
                            'filter_status' => $status,
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
            <p style="font-size:13px;margin:0">Hãy thử tìm kiếm với từ khóa khác hoặc bỏ lọc trạng thái.</p>
        </div>
    <?php endif; ?>

</div>
