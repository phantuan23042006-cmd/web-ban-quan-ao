<?php
/**
 * Chi tiết đơn hàng — order_detail.php
 * Biến nhận từ controller: $order (array, gồm $order['items'])
 */

$statusLabels = [
    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'status-pending'],
    'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'status-confirmed'],
    'shipping'  => ['label' => 'Đang giao',    'class' => 'status-shipping'],
    'completed' => ['label' => 'Hoàn thành',   'class' => 'status-completed'],
    'cancelled' => ['label' => 'Đã hủy',       'class' => 'status-cancelled'],
];
$st = $statusLabels[$order['status']] ?? ['label' => e($order['status']), 'class' => ''];
?>

<div class="container storefront">

    <!-- Breadcrumb -->
    <div style="margin-bottom:24px;font-size:.9rem;color:#64748b">
        <a href="<?= BASE_URL ?>?action=orders" style="color:#7c3aed;font-weight:700;text-decoration:none">
            ← Đơn hàng của tôi
        </a>
        &nbsp;/&nbsp;
        <span style="font-family:monospace"><?= e($order['order_code']) ?></span>
    </div>

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:24px">

        <!-- Danh sách sản phẩm -->
        <section class="profile-panel">

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                <h1 style="margin:0">Chi tiết đơn hàng</h1>
                <span class="order-status <?= e($st['class']) ?>">
                    <?= e($st['label']) ?>
                </span>
            </div>

            <div style="overflow-x:auto">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Size</th>
                            <th>Đơn giá</th>
                            <th>SL</th>
                            <th style="text-align:right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td style="font-weight:600"><?= e($item['product_name']) ?></td>
                                <td>
                                    <span style="background:#f1f5f9;border-radius:6px;padding:2px 8px;font-size:.82rem;font-weight:700">
                                        <?= e($item['size']) ?>
                                    </span>
                                </td>
                                <td style="color:#475569">
                                    <?= number_format($item['gia_ban'], 0, ',', '.') ?>đ
                                </td>
                                <td style="color:#475569;text-align:center">
                                    <?= (int) $item['so_luong'] ?>
                                </td>
                                <td style="text-align:right;font-weight:700">
                                    <?= number_format($item['thanh_tien'], 0, ',', '.') ?>đ
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" style="text-align:right;font-weight:700;padding:16px 14px;border-top:2px solid #e2e8f0">
                                Tổng cộng:
                            </td>
                            <td style="text-align:right;font-size:1.15rem;font-weight:800;color:#db2777;padding:16px 14px;border-top:2px solid #e2e8f0">
                                <?= number_format($order['total_amount'], 0, ',', '.') ?>đ
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </section>

        <!-- Thông tin đơn -->
        <div style="display:flex;flex-direction:column;gap:20px">

            <!-- Người nhận -->
            <section class="profile-panel">
                <h2>Thông tin nhận hàng</h2>
                <dl>
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
                </dl>
            </section>

            <!-- Thanh toán -->
            <section class="profile-panel">
                <h2>Thanh toán</h2>
                <dl>
                    <dt>Phương thức</dt>
                    <dd style="text-transform:uppercase;font-weight:800">
                        <?= e($order['payment_method']) ?>
                    </dd>

                    <dt>Ngày đặt</dt>
                    <dd style="color:#64748b;font-size:.9rem">
                        <?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?>
                    </dd>
                </dl>
            </section>

        </div>

    </div>

</div>

<style>
    @media (max-width: 820px) {
        .container.storefront > div { grid-template-columns: 1fr !important; }
    }
</style>
