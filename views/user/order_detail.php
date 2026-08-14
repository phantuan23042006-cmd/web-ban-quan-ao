<?php
/**
 * Chi tiết đơn hàng — order_detail.php
 * Biến nhận từ controller: $order (array, gồm $order['items'])
 */

$statusLabels = [
    'pending'   => ['label' => 'Chờ xác nhận', 'class' => 'status-pending'],
    'confirmed' => ['label' => 'Đã xác nhận',  'class' => 'status-confirmed'],
    'completed' => ['label' => 'Đã giao',      'class' => 'status-completed'],
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
                <div style="display:flex;align-items:center;gap:12px">
                    <span class="order-status <?= e($st['class']) ?>">
                        <?= e($st['label']) ?>
                    </span>
                    <?php if ($order['status'] !== 'completed' && $order['status'] !== 'cancelled'): ?>
                        <form method="post" action="<?= BASE_URL ?>?action=order-cancel" style="display:inline-block;margin:0" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn hàng này không?');">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                            <input type="hidden" name="return_url" value="<?= e(BASE_URL . '?action=order-detail&id=' . $order['id']) ?>">
                            <button type="submit" style="padding:6px 14px;border-radius:10px;border:none;background:#ef4444;color:#fff;font-weight:700;font-size:.85rem;cursor:pointer">
                                🚫 Hủy đơn
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
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
                <?php
                    $payMethodMap = [
                        'cod'            => ['label' => 'Thanh toán khi nhận hàng (COD)', 'icon' => '💵'],
                        'qr_techcombank' => ['label' => 'Chuyển khoản QR Code (Techcombank)', 'icon' => '🔴'],
                    ];
                    $pmKey = $order['payment_method'] ?? 'cod';
                    $pmInfo = $payMethodMap[$pmKey] ?? ['label' => strtoupper($pmKey), 'icon' => '💳'];
                    $isPaid = (($order['payment_status'] ?? 'unpaid') === 'paid');
                ?>

                <dl style="margin-bottom:16px">
                    <dt>Phương thức</dt>
                    <dd style="font-weight:700;color:#0f172a">
                        <?= $pmInfo['icon'] ?> <?= e($pmInfo['label']) ?>
                    </dd>

                    <dt>Trạng thái thanh toán</dt>
                    <dd>
                        <?php if ($isPaid): ?>
                            <span style="background:#dcfce7;color:#166534;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:800">
                                ✓ ĐÃ THANH TOÁN
                            </span>
                        <?php else: ?>
                            <span style="background:#fef3c7;color:#92400e;padding:4px 12px;border-radius:999px;font-size:12px;font-weight:800">
                                ⏳ CHƯA THANH TOÁN
                            </span>
                        <?php endif; ?>
                    </dd>

                    <dt>Ngày đặt</dt>
                    <dd style="color:#64748b;font-size:.9rem">
                        <?= e(date('d/m/Y H:i', strtotime($order['created_at']))) ?>
                    </dd>
                </dl>

                <?php if (!$isPaid && $pmKey !== 'cod'): ?>
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:16px;padding:20px;margin-top:16px;text-align:center">
                        <h4 style="margin:0 0 12px;color:#0f172a;font-size:15px">
                            📲 Vui lòng quét mã QR bên dưới để thanh toán
                        </h4>

                        <div style="display:inline-block;margin-bottom:14px">
                            <img 
                                src="https://img.vietqr.io/image/TCB-66333388889999-compact2.png?amount=<?= (int)$order['total_amount'] ?>&addInfo=<?= urlencode($order['order_code']) ?>&accountName=TAN%20AND%20TUAN%20CLOTHING" 
                                alt="Mã thanh toán QR" 
                                style="width:220px;height:220px;object-fit:contain;border-radius:14px;border:1px solid #cbd5e1;padding:8px;background:#fff;box-shadow:0 8px 20px rgba(0,0,0,0.06)"
                            >
                        </div>

                        <div style="background:#fff;border:1px solid #cbd5e1;border-radius:12px;padding:12px 16px;text-align:left;font-size:13px;display:grid;gap:6px;margin-bottom:16px">
                            <div><strong>Ngân hàng:</strong> Techcombank (TCB)</div>
                            <div><strong>Số tài khoản:</strong> <span style="font-family:monospace;font-size:15px;color:#e11d48;font-weight:800">66333388889999</span></div>
                            <div><strong>Chủ tài khoản:</strong> TAN & TUAN CLOTHING</div>
                            <div><strong>Số tiền:</strong> <strong style="color:#16a34a"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</strong></div>
                            <div><strong>Nội dung chuyển khoản:</strong> <span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-family:monospace;font-weight:800;color:#6366f1"><?= e($order['order_code']) ?></span></div>
                        </div>

                        <form method="post" action="<?= BASE_URL ?>?action=confirm-payment">
                            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                            <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                            <button type="submit" style="width:100%;padding:12px;border-radius:12px;border:none;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-weight:700;font-size:14px;cursor:pointer;box-shadow:0 4px 14px rgba(16,185,129,0.3)">
                                ✓ Tôi đã chuyển khoản / thanh toán xong
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </section>

        </div>

    </div>

</div>

<style>
    @media (max-width: 820px) {
        .container.storefront > div { grid-template-columns: 1fr !important; }
    }
</style>
