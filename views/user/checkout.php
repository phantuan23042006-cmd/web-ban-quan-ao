<?php
/**
 * Trang checkout — checkout.php
 * Biến nhận từ controller: $cartItems, $totalAmount, $user
 */

$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);
?>

<div class="container storefront">

    <div style="margin-bottom:28px">
        <span class="eyebrow">THANH TOÁN</span>
        <h1 style="margin:6px 0 0;color:#0f172a">Xác nhận đơn hàng</h1>
    </div>

    <?php if ($errorMessage): ?>
        <div class="flash flash-error"><?= e($errorMessage) ?></div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>

        <div class="profile-panel" style="text-align:center;padding:48px">
            <div style="font-size:3rem;margin-bottom:12px">🛒</div>
            <p style="font-size:1.05rem;font-weight:600;margin:0 0 8px;color:#0f172a">Giỏ hàng trống</p>
            <p style="margin:0 0 20px;color:#64748b">Vui lòng thêm sản phẩm vào giỏ hàng trước khi thanh toán.</p>
            <a class="button primary" href="<?= BASE_URL ?>?action=products">🛍️ Tiếp tục mua sắm</a>
        </div>

    <?php else: ?>

        <div class="checkout-grid">

            <!-- Form thông tin nhận hàng -->
            <section class="profile-panel">

                <h2>📋 Thông tin nhận hàng</h2>

                <form
                    method="post"
                    action="<?= BASE_URL ?>?action=place-order"
                    id="checkoutForm"
                >
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>"
                    >

                    <div class="form-group">
                        <label for="recipient_name">Họ và tên người nhận</label>
                        <input
                            type="text"
                            id="recipient_name"
                            name="recipient_name"
                            value="<?= e($user['full_name'] ?? '') ?>"
                            required
                            maxlength="100"
                            placeholder="Nguyễn Văn A"
                        >
                    </div>

                    <div class="form-group">
                        <label for="recipient_phone">Số điện thoại</label>
                        <input
                            type="tel"
                            id="recipient_phone"
                            name="recipient_phone"
                            value="<?= e($user['phone'] ?? '') ?>"
                            required
                            maxlength="20"
                            placeholder="0xxxxxxxxx"
                        >
                    </div>

                    <div class="form-group">
                        <label for="recipient_address">Địa chỉ nhận hàng</label>
                        <textarea
                            id="recipient_address"
                            name="recipient_address"
                            required
                            maxlength="255"
                            placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố"
                        ><?= e($user['address'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="note">Ghi chú (tùy chọn)</label>
                        <textarea
                            id="note"
                            name="note"
                            maxlength="500"
                            placeholder="Ví dụ: giao giờ hành chính, gọi trước khi giao..."
                        ></textarea>
                    </div>

                    <div style="padding:14px 18px;background:#f8fafc;border-radius:12px;margin-bottom:20px">
                        <span style="color:#64748b;font-size:.9rem;font-weight:600">💳 Phương thức thanh toán:</span>
                        <strong style="margin-left:8px">COD — Thanh toán khi nhận hàng</strong>
                    </div>

                    <button
                        type="submit"
                        class="button primary"
                        style="width:100%;justify-content:center;font-size:1rem;padding:14px"
                    >
                        🚀 Đặt hàng ngay (<?= number_format($totalAmount, 0, ',', '.') ?>đ)
                    </button>

                </form>

            </section>

            <!-- Tóm tắt đơn hàng -->
            <aside class="profile-panel">

                <h2>🛒 Đơn hàng (<?= count($cartItems) ?> sản phẩm)</h2>

                <?php foreach ($cartItems as $item): ?>
                    <div class="checkout-summary-row">
                        <div>
                            <div style="font-weight:600;font-size:.9rem"><?= e($item['product_name']) ?></div>
                            <div style="color:#64748b;font-size:.82rem;margin-top:2px">
                                Size: <?= e($item['size']) ?> &times; <?= (int) $item['quantity'] ?>
                            </div>
                        </div>
                        <div style="font-weight:700;color:#0f172a;white-space:nowrap;padding-left:12px">
                            <?= number_format($item['subtotal'], 0, ',', '.') ?>đ
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="checkout-total">
                    <span>Tổng cộng</span>
                    <span style="color:#db2777"><?= number_format($totalAmount, 0, ',', '.') ?>đ</span>
                </div>

                <div style="margin-top:14px;padding:12px;background:#fdf4ff;border-radius:10px;font-size:.82rem;color:#7c3aed;font-weight:600">
                    🚚 Miễn phí vận chuyển cho đơn hàng từ 500.000đ
                </div>

            </aside>

        </div>

    <?php endif; ?>

</div>
