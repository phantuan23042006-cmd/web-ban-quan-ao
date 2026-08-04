<?php
$orders = $orders ?? [];
$keyword = $keyword ?? '';
$status = $status ?? '';
$page = $page ?? 1;
$totalPages = max(1, $totalPages ?? 1);
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
    }

    .admin-orders-form .field {
        display: grid;
        gap: 8px;
    }

    .admin-orders-form input,
    .admin-orders-form select,
    .admin-orders-form button {
        min-height: 44px;
        padding: 0 12px;
        border-radius: 12px;
        border: 1px solid #d7e1ef;
        background: #f8fafc;
        color: #0f172a;
        font-size: 14px;
    }

    .admin-orders-form button {
        border: 0;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        cursor: pointer;
    }

    .admin-orders-table-wrap {
        overflow-x: auto;
    }

    .admin-orders-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .admin-orders-table th,
    .admin-orders-table td {
        padding: 16px 14px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    .admin-orders-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .admin-orders-table td {
        color: #334155;
        font-size: 14px;
    }

    .order-status {
        display: inline-flex;
        align-items: center;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .order-status.delivered {
        background: #dcfce7;
        color: #166534;
    }

    .order-status.shipping {
        background: #fef3c7;
        color: #92400e;
    }

    .order-status.pending {
        background: #e0f2fe;
        color: #0369a1;
    }

    .order-status.cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

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

    .admin-empty-state {
        padding: 30px;
        border-radius: 18px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        text-align: center;
    }

    @media (max-width: 900px) {
        .admin-orders-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-orders-page">
    <div class="admin-orders-card">
        <div class="admin-orders-toolbar">
            <div>
                <h3>Quản lý đơn hàng</h3>
                <p>Lọc đơn theo mã hoặc trạng thái để xử lý nhanh.</p>
            </div>
        </div>

        <form class="admin-orders-form" method="get">
            <input type="hidden" name="action" value="admin-orders">
            <div class="field">
                <label for="keyword">Từ khóa</label>
                <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Mã đơn, khách hàng">
            </div>
            <div class="field">
                <label for="filter_status">Trạng thái</label>
                <select id="filter_status" name="filter_status">
                    <option value="">Tất cả</option>
                    <option value="Đã giao" <?= $status === 'Đã giao' ? 'selected' : '' ?>>Đã giao</option>
                    <option value="Đang giao" <?= $status === 'Đang giao' ? 'selected' : '' ?>>Đang giao</option>
                    <option value="Chờ xử lý" <?= $status === 'Chờ xử lý' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="Bị hủy" <?= $status === 'Bị hủy' ? 'selected' : '' ?>>Bị hủy</option>
                </select>
            </div>
            <div class="field">
                <button type="submit">Lọc</button>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php
                            $statusClass = '';
                            switch ($order['status'] ?? '') {
                                case 'Đã giao':
                                    $statusClass = 'delivered';
                                    break;
                                case 'Đang giao':
                                    $statusClass = 'shipping';
                                    break;
                                case 'Chờ xử lý':
                                    $statusClass = 'pending';
                                    break;
                                case 'Bị hủy':
                                    $statusClass = 'cancelled';
                                    break;
                            }
                        ?>
                        <tr>
                            <td><?= e($order['id']) ?></td>
                            <td><?= e($order['customer']) ?></td>
                            <td><?= e($order['date']) ?></td>
                            <td>
                                <span class="order-status <?= e($statusClass) ?>">
                                    <?= e($order['status']) ?>
                                </span>
                            </td>
                            <td><?= e($order['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <span>Hiển thị <?= e(count($orders)) ?> trên tổng <?= e(count($orders)) ?> đơn hàng</span>
            <div>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                        $query = [
                            'action' => 'admin-orders',
                            'keyword' => $keyword,
                            'filter_status' => $status,
                            'page' => $i,
                        ];
                        $query = array_filter($query, static function ($value) {
                            return $value !== '' && $value !== null;
                        });
                    ?>
                    <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= BASE_URL ?>?<?= http_build_query($query) ?>">
                        <?= e($i) ?>
                    </a>
                <?php endfor; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            Không tìm thấy đơn hàng phù hợp với bộ lọc.
        </div>
    <?php endif; ?>
</div>

<?php
function getOrderStatusColor($status)
{
    $map = [
        'Đã giao' => ['#dcfce7', '#166534'],
        'Đang giao' => ['#fef3c7', '#92400e'],
        'Chờ xử lý' => ['#e0f2fe', '#0369a1'],
        'Bị hủy' => ['#fee2e2', '#991b1b'],
    ];

    return $map[$status] ?? ['#e2e8f0', '#334155'];
}
?>
