<?php
/**
 * Trang đổi mật khẩu — change_password.php
 * Biến nhận từ controller: không có biến đặc biệt ngoài session
 */

$successMessage = $_SESSION['pwd_success'] ?? null;
$errorMessage   = $_SESSION['pwd_error']   ?? null;
unset($_SESSION['pwd_success'], $_SESSION['pwd_error']);
?>

<div class="container storefront">

    <!-- Tab navigation -->
    <nav class="profile-tabs">
        <a href="<?= BASE_URL ?>?action=profile">👤 Thông tin cá nhân</a>
        <a class="active" href="<?= BASE_URL ?>?action=change-password">🔑 Đổi mật khẩu</a>
        <a href="<?= BASE_URL ?>?action=orders">📦 Đơn hàng của tôi</a>
    </nav>

    <section class="profile-panel" style="max-width:520px">

        <h1>Đổi mật khẩu</h1>

        <?php if ($successMessage): ?>
            <div class="flash flash-success"><?= e($successMessage) ?></div>
        <?php endif; ?>

        <?php if ($errorMessage): ?>
            <div class="flash flash-error"><?= e($errorMessage) ?></div>
        <?php endif; ?>

        <form
            method="post"
            action="<?= BASE_URL ?>?action=change-password-submit"
            id="changePwdForm"
        >
            <input
                type="hidden"
                name="csrf_token"
                value="<?= e(csrf_token()) ?>"
            >

            <div class="form-group">
                <label for="current_password">Mật khẩu hiện tại</label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                >
            </div>

            <div class="form-group">
                <label for="new_password">Mật khẩu mới</label>
                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    required
                    minlength="6"
                    maxlength="72"
                    autocomplete="new-password"
                    placeholder="Ít nhất 6 ký tự"
                >
            </div>

            <div class="form-group">
                <label for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                <input
                    type="password"
                    id="new_password_confirmation"
                    name="new_password_confirmation"
                    required
                    minlength="6"
                    maxlength="72"
                    autocomplete="new-password"
                    placeholder="Nhập lại mật khẩu mới"
                >
            </div>

            <button type="submit" class="button primary">
                🔒 Đổi mật khẩu
            </button>
        </form>

    </section>
</div>
