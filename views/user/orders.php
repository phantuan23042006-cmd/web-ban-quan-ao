<?php
/**
 * Trang danh sách đơn hàng của tôi — orders.php
 * Biến nhận từ controller: $orders (array)
 */

$statusLabels = [
    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'status-pending'],
    'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'status-confirmed'],
    'completed' => ['label' => 'Đã giao',      'class' => 'status-completed'],
    'cancelled' => ['label' => 'Đã hủy',       'class' => 'status-cancelled'],
];

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage   = $_SESSION['error_message']   ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<div class="container storefront">

    <!-- Tab navigation -->
    <nav class="profile-tabs">
        <a href="<?= BASE_URL ?>?action=profile">👤 Thông tin cá nhân</a>
        <a href="<?= BASE_URL ?>?action=change-password">🔑 Đổi mật khẩu</a>
        <a class="active" href="<?= BASE_URL ?>?action=orders">📦 Đơn hàng của tôi</a>
    </nav>

    <section class="profile-panel">

        <h1>Đơn hàng của tôi</h1>

        <?php if ($successMessage): ?>
            <div class="flash flash-success"><?= e($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="flash flash-error"><?= e($errorMessage) ?></div>
        <?php endif; ?>

        <?php if (empty($orders)): ?>

            <div style="text-align:center;padding:40px 0;color:#64748b">
                <div style="font-size:3rem;margin-bottom:12px">📦</div>
                <p style="font-size:1.05rem;font-weight:600;margin:0 0 8px">Bạn chưa có đơn hàng nào</p>
                <p style="margin:0 0 20px;font-size:.9rem">Hãy khám phá các sản phẩm và đặt hàng ngay!</p>
                <a class="button primary" href="<?= BASE_URL ?>?action=products">🛍️ Mua sắm ngay</a>
            </div>

        <?php else: ?>

            <div style="overflow-x:auto">
                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                            <?php
                                $st = $statusLabels[$o['status']] ?? ['label' => e($o['status']), 'class' => ''];
                            ?>
                            <tr>
                                <td style="font-weight:700;font-family:monospace">
                                    <?= e($o['order_code']) ?>
                                </td>
                                <td style="color:#64748b;font-size:.88rem">
                                    <?= e(date('d/m/Y H:i', strtotime($o['created_at']))) ?>
                                </td>
                                <td style="font-weight:700;color:#0f172a">
                                    <?= number_format($o['total_amount'], 0, ',', '.') ?>đ
                                </td>
                                <td style="text-transform:uppercase;font-size:.85rem;font-weight:600;color:#475569">
                                    <?= e($o['payment_method']) ?>
                                </td>
                                <td>
                                    <span class="order-status <?= e($st['class']) ?>">
                                        <?= e($st['label']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px">
                                        <a class="button secondary compact" href="<?= BASE_URL ?>?action=order-detail&id=<?= (int) $o['id'] ?>" style="padding:5px 12px;font-size:12px;text-decoration:none">
                                            Xem →
                                        </a>
                                        <?php if ($o['status'] !== 'completed' && $o['status'] !== 'cancelled'): ?>
                                            <form method="post" action="<?= BASE_URL ?>?action=order-cancel" style="display:inline-block;margin:0" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                                <input type="hidden" name="order_id" value="<?= (int) $o['id'] ?>">
                                                <input type="hidden" name="return_url" value="<?= e(BASE_URL . '?action=orders') ?>">
                                                <button type="submit" class="button danger compact" style="padding:5px 12px;font-size:12px;border-radius:8px;border:none;background:#ef4444;color:#fff;font-weight:700;cursor:pointer">
                                                    Hủy đơn
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>

    </section>
</div>
