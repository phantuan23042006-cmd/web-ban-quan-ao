<?php
/**
 * Trang chi tiết đơn hàng dành cho Admin — admin/order_detail.php
 * Biến nhận từ controller: $order (array từ Order::findForAdmin())
 */

$product = $product ?? null;
$statusLabels = [
    'pending'   => ['label' => 'Chờ xác nhận', 'bg' => '#fef3c7', 'color' => '#92400e'],
    'confirmed' => ['label' => 'Xác nhận',     'bg' => '#dbeafe', 'color' => '#1e40af'],
    'completed' => ['label' => 'Đã giao',      'bg' => '#dcfce7', 'color' => '#166534'],
    'cancelled' => ['label' => 'Đã hủy',       'bg' => '#fee2e2', 'color' => '#991b1b'],
];

$allowedNextStatuses = [
    'pending'   => ['pending' => 'Chờ xác nhận', 'confirmed' => 'Xác nhận', 'cancelled' => 'Đã hủy'],
    'confirmed' => ['confirmed' => 'Xác nhận', 'completed' => 'Đã giao', 'cancelled' => 'Đã hủy'],
    'completed' => [],
    'cancelled' => [],
];

$stKey = $order['status'] ?? 'pending';
$stInfo = $statusLabels[$stKey] ?? ['label' => e($stKey), 'bg' => '#f1f5f9', 'color' => '#475569'];

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage   = $_SESSION['error_message']   ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .admin-detail-page {
        display: grid;
        gap: 24px;
        max-width: 1100px;
        margin: 0 auto;
    }

    .admin-card {
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .admin-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    .admin-card-header h3 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .grid-2 {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 24px;
    }

    .order-item-table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-item-table th,
    .order-item-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #f1f5f9;
        text-align: left;
    }

    .order-item-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .info-dl {
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 12px;
        margin: 0;
        font-size: 14px;
    }

    .info-dl dt {
        color: #64748b;
        font-weight: 600;
    }

    .info-dl dd {
        margin: 0;
        font-weight: 700;
        color: #0f172a;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        font-weight: 700;
        font-size: 14px;
        border: 1px solid #cbd5e1;
    }

    .btn-back:hover {
        background: #e2e8f0;
    }

    .status-badge {
        display: inline-block;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 800;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }

    .alert-success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
    .alert-danger { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

    @media (max-width: 820px) {
        .grid-2 { grid-template-columns: 1fr; }
    }
</style>

<div class="admin-detail-page">

    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <div class="admin-card-header" style="background:#fff;padding:20px 24px;border-radius:20px;border:1px solid #e2e8f0">
        <div>
            <a class="btn-back" href="<?= BASE_URL ?>?action=admin-orders">← Quay lại danh sách đơn hàng</a>
            <h3 style="margin-top:12px">Chi tiết đơn hàng: <span style="font-family:monospace;color:#7c3aed"><?= e($order['order_code'] ?? ('#' . $order['id'])) ?></span></h3>
        </div>
        <span class="status-badge" style="background:<?= e($stInfo['bg']) ?>;color:<?= e($stInfo['color']) ?>">
            <?= e($stInfo['label']) ?>
        </span>
    </div>

    <div class="grid-2">

        <!-- Danh sách sản phẩm trong đơn -->
        <div class="admin-card">
            <h4 style="margin:0 0 16px;font-size:16px;color:#0f172a">🛍️ Danh sách sản phẩm (<?= count($order['items'] ?? []) ?>)</h4>

            <div style="overflow-x:auto">
                <table class="order-item-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th>Đơn giá</th>
                            <th style="text-align:center">SL</th>
                            <th style="text-align:right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($order['items'] ?? []) as $item): ?>
                            <tr>
                                <td style="font-weight:600"><?= e($item['product_name']) ?></td>
                                <td>
                                    <span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-size:12px;font-weight:700">
                                        <?= e($item['size']) ?>
                                    </span>
                                </td>
                                <td><?= number_format($item['gia_ban'], 0, ',', '.') ?>đ</td>
                                <td style="text-align:center;font-weight:700"><?= (int)$item['so_luong'] ?></td>
                                <td style="text-align:right;font-weight:800;color:#0f172a"><?= number_format($item['thanh_tien'], 0, ',', '.') ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;font-weight:700;padding-top:16px;border-top:2px solid #e2e8f0">
                                Tổng thành tiền:
                            </td>
                            <td style="text-align:right;font-size:18px;font-weight:800;color:#db2777;padding-top:16px;border-top:2px solid #e2e8f0">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Thông tin người nhận & Cập nhật trạng thái -->
        <div style="display:flex;flex-direction:column;gap:20px">

            <!-- Cập nhật trạng thái -->
            <div class="admin-card">
                <h4 style="margin:0 0 16px;font-size:16px;color:#0f172a">🔄 Cập nhật trạng thái</h4>
                
                <?php $nextOpts = $allowedNextStatuses[$stKey] ?? []; ?>
                <?php if (!empty($nextOpts) && in_array($stKey, ['pending', 'confirmed'], true)): ?>
                    <form method="post" action="<?= BASE_URL ?>?action=admin-order-status">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                        <input type="hidden" name="return_url" value="<?= e(BASE_URL . '?action=admin-order-detail&id=' . $order['id']) ?>">

                        <div style="margin-bottom:16px">
                            <select name="status" style="width:100%;padding:10px 14px;border-radius:12px;border:1px solid #cbd5e1;font-size:14px;font-weight:600">
                                <?php foreach ($nextOpts as $sKey => $sLabel): ?>
                                    <option value="<?= e($sKey) ?>" <?= $stKey === $sKey ? 'selected' : '' ?>>
                                        <?= e($sLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <button type="submit" style="width:100%;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-weight:700;font-size:14px;cursor:pointer;box-shadow:0 4px 14px rgba(99,102,241,0.25)">
                            💾 Lưu thay đổi trạng thái
                        </button>
                    </form>
                <?php else: ?>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;color:#64748b;font-size:13px;font-weight:600;text-align:center">
                        🔒 Đơn hàng đã ở trạng thái <strong>"<?= e($stInfo['label']) ?>"</strong>. Không thể thay đổi trạng thái nữa.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Người nhận -->
            <div class="admin-card">
                <h4 style="margin:0 0 16px;font-size:16px;color:#0f172a">👤 Thông tin nhận hàng</h4>
                <dl class="info-dl">
                    <dt>Họ tên</dt>
                    <dd><?= e($order['recipient_name']) ?></dd>

                    <dt>Điện thoại</dt>
                    <dd><?= e($order['recipient_phone']) ?></dd>

                    <dt>Địa chỉ</dt>
                    <dd><?= e($order['recipient_address']) ?></dd>

                    <?php if (!empty($order['note'])): ?>
                        <dt>Ghi chú</dt>
                        <dd><?= e($order['note']) ?></dd>
                    <?php endif; ?>

                    <?php
                        $payMethodMap = [
                            'cod'            => '💵 COD — Thanh toán khi nhận hàng',
                            'qr_techcombank' => '🔴 Chuyển khoản QR Code (Techcombank 66333388889999)',
                        ];
                        $pmText = $payMethodMap[$order['payment_method'] ?? 'cod'] ?? strtoupper($order['payment_method']);
                        $isPaid = (($order['payment_status'] ?? 'unpaid') === 'paid');
                    ?>
                    <dt>Phương thức TT</dt>
                    <dd style="font-weight:700"><?= e($pmText) ?></dd>

                    <dt>Trạng thái TT</dt>
                    <dd>
                        <?php if ($isPaid): ?>
                            <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:800">✓ Đã thanh toán</span>
                        <?php else: ?>
                            <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:999px;font-size:12px;font-weight:800">⏳ Chưa thanh toán</span>
                        <?php endif; ?>
                    </dd>

                    <dt>Tài khoản đặt</dt>
                    <dd><?= e($order['customer_name'] ?? 'Khách hàng') ?> (<?= e($order['customer_email'] ?? '') ?>)</dd>
                </dl>
            </div>

        </div>

    </div>

</div>
