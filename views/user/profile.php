<?php
/**
 * Trang hồ sơ cá nhân — profile.php
 * Biến nhận từ controller: $user (array từ $_SESSION['user'])
 */

$successMessage = $_SESSION['profile_success'] ?? null;
$errorMessage   = $_SESSION['profile_error']   ?? null;
$old            = $_SESSION['profile_old']      ?? [];
unset($_SESSION['profile_success'], $_SESSION['profile_error'], $_SESSION['profile_old']);
?>

<div class="container storefront">

    <!-- Tab navigation -->
    <nav class="profile-tabs">
        <a class="active" href="<?= BASE_URL ?>?action=profile">👤 Thông tin cá nhân</a>
        <a href="<?= BASE_URL ?>?action=change-password">🔑 Đổi mật khẩu</a>
        <a href="<?= BASE_URL ?>?action=orders">📦 Đơn hàng của tôi</a>
    </nav>

    <section class="profile-panel">

        <h1>Thông tin cá nhân</h1>

        <?php if ($successMessage): ?>
            <div class="flash flash-success"><?= e($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="flash flash-error"><?= e($errorMessage) ?></div>
        <?php endif; ?>

        <!-- Thông tin hiện tại -->
        <dl>
            <dt>Email</dt>
            <dd><?= e($user['email'] ?? '') ?></dd>

            <dt>Vai trò</dt>
            <dd><?= ($user['role'] ?? '') === 'admin' ? '🛡️ Quản trị viên' : '👤 Thành viên' ?></dd>

            <dt>Trạng thái</dt>
            <dd><?= ($user['status'] ?? '') === 'active' ? '✅ Hoạt động' : '🔒 Bị khóa' ?></dd>
        </dl>

        <hr style="border:0;border-top:1px solid #f1f5f9;margin:0 0 24px">

        <!-- Form cập nhật -->
        <form
            method="post"
            action="<?= BASE_URL ?>?action=profile-update"
            id="profileForm"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <div class="form-group">
                <label for="full_name">Họ và tên</label>
                <input
                    type="text"
                    id="full_name"
                    name="full_name"
                    value="<?= e($old['full_name'] ?? $user['full_name'] ?? '') ?>"
                    required
                    maxlength="100"
                    placeholder="Nguyễn Văn A"
                >
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    value="<?= e($old['phone'] ?? $user['phone'] ?? '') ?>"
                    placeholder="0xxxxxxxxx"
                    maxlength="20"
                >
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ</label>
                <textarea
                    id="address"
                    name="address"
                    maxlength="255"
                    placeholder="Số nhà, đường, quận/huyện, tỉnh/thành phố"
                ><?= e($old['address'] ?? $user['address'] ?? '') ?></textarea>
            </div>

            <button type="submit" class="button primary">
                💾 Lưu thay đổi
            </button>
        </form>

    </section>
</div>
