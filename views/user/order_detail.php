<?php
/**
 * Chi tiết đơn hàng — order_detail.php
 * Biến nhận từ controller: $order (array, gồm $order['items'])
 */

$statusLabels = [
    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'status-pending'],
    'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'status-confirmed'],
    'completed' => ['label' => 'Hoàn thành',   'class' => 'status-completed'],
    'cancelled' => ['label' => 'Đã hủy',       'class' => 'status-cancelled'],
];
$st = $statusLabels[$order['status']] ?? ['label' => e($order['status']), 'class' => ''];
$isCompleted = ($order['status'] === 'completed');

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage   = $_SESSION['error_message']   ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<div class="container storefront">

    <!-- Notifications -->
    <?php if ($successMessage): ?>
        <div style="background:#dcfce7;color:#166534;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-weight:600;border:1px solid #bbf7d0">
            ✓ <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:14px 18px;border-radius:12px;margin-bottom:20px;font-weight:600;border:1px solid #fecaca">
            ✕ <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

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
                <h1 style="margin:0;font-size:1.4rem">Chi tiết đơn hàng</h1>
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
                                <td style="font-weight:600">
                                    <a href="<?= BASE_URL ?>?action=product-detail&id=<?= (int)$item['san_pham_id'] ?>" style="color:#0f172a;text-decoration:none">
                                        <?= e($item['product_name']) ?>
                                    </a>
                                </td>
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

            <!-- Khu vực đánh giá sản phẩm (chỉ khi đơn đã hoàn thành) -->
            <?php if ($isCompleted): ?>
                <div style="margin-top:32px;padding-top:24px;border-top:2px solid #f1f5f9">
                    <h3 style="margin:0 0 16px;font-size:1.1rem;color:#0f172a;display:flex;align-items:center;gap:8px">
                        ⭐ Đánh giá sản phẩm đã mua
                    </h3>

                    <?php foreach ($order['items'] as $item): ?>
                        <?php $rev = $item['review'] ?? null; ?>
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:18px;margin-bottom:14px">
                            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
                                <div>
                                    <strong style="color:#0f172a;font-size:.95rem"><?= e($item['product_name']) ?></strong>
                                    <span style="font-size:.8rem;color:#64748b;margin-left:6px">(Size <?= e($item['size']) ?>)</span>
                                </div>
                                <?php if ($rev): ?>
                                    <span style="background:#dcfce7;color:#166534;padding:3px 10px;border-radius:999px;font-size:.8rem;font-weight:700">
                                        ✓ Đã đánh giá <?= e($rev['so_sao']) ?>/5 ⭐
                                    </span>
                                <?php endif; ?>
                            </div>

                            <form action="<?= BASE_URL ?>?action=<?= $rev ? 'review-update' : 'review-store' ?>" method="post">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="san_pham_id" value="<?= (int)$item['san_pham_id'] ?>">
                                <?php if ($rev): ?>
                                    <input type="hidden" name="id" value="<?= (int)$rev['id'] ?>">
                                <?php endif; ?>

                                <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                                    <span style="font-size:.85rem;font-weight:600;color:#475569">Số sao:</span>
                                    <select name="so_sao" style="padding:6px 12px;border-radius:8px;border:1px solid #cbd5e1;font-weight:700;background:#fff;color:#eab308">
                                        <option value="5" <?= ($rev['so_sao'] ?? 5) == 5 ? 'selected' : '' ?>>⭐⭐⭐⭐⭐ (5/5)</option>
                                        <option value="4" <?= ($rev['so_sao'] ?? 5) == 4 ? 'selected' : '' ?>>⭐⭐⭐⭐ (4/5)</option>
                                        <option value="3" <?= ($rev['so_sao'] ?? 5) == 3 ? 'selected' : '' ?>>⭐⭐⭐ (3/5)</option>
                                        <option value="2" <?= ($rev['so_sao'] ?? 5) == 2 ? 'selected' : '' ?>>⭐⭐ (2/5)</option>
                                        <option value="1" <?= ($rev['so_sao'] ?? 5) == 1 ? 'selected' : '' ?>>⭐ (1/5)</option>
                                    </select>
                                </div>

                                <div style="margin-bottom:10px">
                                    <textarea name="noi_dung" rows="2" style="width:100%;padding:10px 12px;border-radius:10px;border:1px solid #cbd5e1;font-size:.88rem;box-sizing:border-box" placeholder="Nhận xét chất lượng sản phẩm..."><?= e($rev['noi_dung'] ?? '') ?></textarea>
                                </div>

                                <div style="display:flex;gap:8px">
                                    <button type="submit" style="padding:8px 16px;border-radius:8px;border:none;background:#7c3aed;color:#fff;font-weight:700;font-size:.85rem;cursor:pointer">
                                        <?= $rev ? 'Cập nhật đánh giá' : 'Gửi đánh giá' ?>
                                    </button>
                                    <?php if ($rev): ?>
                                        <a href="<?= BASE_URL ?>?action=review-delete&id=<?= $rev['id'] ?>&san_pham_id=<?= $item['san_pham_id'] ?>" 
                                           style="padding:8px 14px;border-radius:8px;border:1px solid #fecaca;background:#fee2e2;color:#991b1b;font-weight:700;font-size:.85rem;text-decoration:none"
                                           onclick="return confirm('Bạn có chắc muốn xóa đánh giá này không?');">
                                            Xóa
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

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
