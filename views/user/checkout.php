<?php
/**
 * Trang checkout - checkout.php
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

                <h2>📦 Thông tin nhận hàng</h2>

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

                    <div class="form-group" style="margin-bottom:24px">
                        <label style="display:block;font-weight:700;font-size:.95rem;color:#0f172a;margin-bottom:12px">
                            💳 Chọn phương thức thanh toán <span style="color:red">*</span>
                        </label>

                        <div class="payment-methods-grid" style="display:grid;gap:12px">

                            <!-- COD -->
                            <label class="payment-option-card" style="display:flex;align-items:center;gap:14px;padding:14px 16px;border:2px solid #e2e8f0;border-radius:14px;cursor:pointer;transition:all 0.2s ease;background:#fff">
                                <input type="radio" name="payment_method" value="cod" checked style="width:18px;height:18px;accent-color:#6366f1">
                                <div style="flex:1">
                                    <div style="font-weight:700;color:#0f172a;font-size:14px">💵 Thanh toán khi nhận hàng (COD)</div>
                                    <div style="font-size:12px;color:#64748b;margin-top:2px">Trả tiền mặt trực tiếp khi nhận hàng từ nhân viên giao hàng</div>
                                </div>
                            </label>

                            <!-- VietQR Techcombank -->
                            <label class="payment-option-card" style="display:flex;align-items:center;gap:14px;padding:14px 16px;border:2px solid #e2e8f0;border-radius:14px;cursor:pointer;transition:all 0.2s ease;background:#fff">
                                <input type="radio" name="payment_method" value="qr_techcombank" style="width:18px;height:18px;accent-color:#6366f1">
                                <div style="flex:1">
                                    <div style="font-weight:700;color:#0f172a;font-size:14px;display:flex;align-items:center;gap:8px">
                                        <span style="background:#e11d48;color:#fff;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:800">VietQR</span>
                                        Chuyển khoản QR Code (Techcombank)
                                    </div>
                                    <div style="font-size:12px;color:#64748b;margin-top:2px">STK: <strong>66333388889999</strong> (Ngân hàng Techcombank TCB)</div>
                                </div>
                            </label>

                        </div>
                    </div>

                    <button
                        type="submit"
                        class="button primary"
                        style="width:100%;justify-content:center;font-size:1rem;padding:14px"
                    >
                        🛍️ Đặt hàng ngay (<?= number_format($totalAmount, 0, ',', '.') ?>đ)
                    </button>

                </form>

            </section>

            <!-- Tóm tắt đơn hàng -->
            <aside class="profile-panel">

                <h2>🛒 Đơn hàng (<?= count($cartItems) ?> sản phẩm)</h2>

                <?php foreach ($cartItems as $item): ?>
                    <div class="checkout-summary-row">
                        <div>
                            <div style="font-weight:600;font-size:.9rem"><?= e($item['name'] ?? $item['product_name']) ?></div>
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
