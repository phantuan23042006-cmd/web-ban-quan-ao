<?php
/**
 * Trang đặt lại mật khẩu - reset_password.php
 */
$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);
$email = $_GET['email'] ?? '';
$token = $_GET['token'] ?? '';
?>

<div class="auth-card" style="max-width:440px;margin:40px auto;padding:32px;background:#fff;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.06);border:1px solid #e2e8f0">

    <div style="text-align:center;margin-bottom:24px">
        <h2 style="margin:0 0 8px;font-size:1.6rem;color:#0f172a">🔒 Đặt lại mật khẩu</h2>
        <p style="margin:0;color:#64748b;font-size:.92rem">
            Nhập mật khẩu mới cho tài khoản <strong style="color:#0f172a"><?= e($email) ?></strong>
        </p>
    </div>

    <?php if ($error): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:.9rem;font-weight:600;border:1px solid #fecaca">
            ✕ <?= e($error) ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>?action=reset-password-submit">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
        <input type="hidden" name="email" value="<?= e($email) ?>">
        <input type="hidden" name="token" value="<?= e($token) ?>">

        <div style="margin-bottom:16px">
            <label for="password" style="display:block;font-weight:700;font-size:.88rem;color:#334155;margin-bottom:8px">
                Mật khẩu mới <span style="color:red">*</span>
            </label>
            <input
                type="password"
                id="password"
                name="password"
                required
                minlength="6"
                placeholder="Ít nhất 6 ký tự"
                style="width:100%;padding:12px 14px;border:1.5px solid #cbd5e1;border-radius:12px;font-size:.95rem;box-sizing:border-box"
            >
        </div>

        <div style="margin-bottom:24px">
            <label for="confirm_password" style="display:block;font-weight:700;font-size:.88rem;color:#334155;margin-bottom:8px">
                Xác nhận mật khẩu mới <span style="color:red">*</span>
            </label>
            <input
                type="password"
                id="confirm_password"
                name="confirm_password"
                required
                minlength="6"
                placeholder="Nhập lại mật khẩu mới"
                style="width:100%;padding:12px 14px;border:1.5px solid #cbd5e1;border-radius:12px;font-size:.95rem;box-sizing:border-box"
            >
        </div>

        <button
            type="submit"
            style="width:100%;padding:13px;border-radius:12px;border:none;background:linear-gradient(135deg,#10b981,#059669);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;box-shadow:0 4px 14px rgba(16,185,129,0.3)"
        >
            ✓ Cập nhật mật khẩu mới
        </button>
    </form>

</div>
