<?php
/**
 * Trang quên mật khẩu - forgot_password.php
 */
$error   = $_SESSION['error']   ?? null;
$success = $_SESSION['success'] ?? null;
$resetLinkDebug = $_SESSION['reset_link_debug'] ?? null;
unset($_SESSION['error'], $_SESSION['success'], $_SESSION['reset_link_debug']);
?>

<div class="auth-card" style="max-width:440px;margin:40px auto;padding:32px;background:#fff;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.06);border:1px solid #e2e8f0">

    <div style="text-align:center;margin-bottom:24px">
        <h2 style="margin:0 0 8px;font-size:1.6rem;color:#0f172a">🔑 Quên mật khẩu</h2>
        <p style="margin:0;color:#64748b;font-size:.92rem">
            Nhập địa chỉ email của bạn để nhận liên kết tạo lại mật khẩu mới.
        </p>
    </div>

    <?php if ($error): ?>
        <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:.9rem;font-weight:600;border:1px solid #fecaca">
            ✕ <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:12px;margin-bottom:20px;font-size:.9rem;font-weight:600;border:1px solid #bbf7d0">
            ✓ <?= e($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($resetLinkDebug): ?>
        <div style="background:#f0f9ff;border:1px solid #bae6fd;padding:14px;border-radius:12px;margin-bottom:20px;font-size:13px;color:#0369a1">
            <strong>🔗 Liên kết khôi phục (Local Debug):</strong><br>
            <a href="<?= e($resetLinkDebug) ?>" style="color:#0284c7;font-weight:700;word-break:break-all">
                <?= e($resetLinkDebug) ?>
            </a>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= BASE_URL ?>?action=forgot-password-submit">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <div style="margin-bottom:20px">
            <label for="email" style="display:block;font-weight:700;font-size:.88rem;color:#334155;margin-bottom:8px">
                Địa chỉ Email <span style="color:red">*</span>
            </label>
            <input
                type="email"
                id="email"
                name="email"
                required
                placeholder="nguyenvana@gmail.com"
                style="width:100%;padding:12px 14px;border:1.5px solid #cbd5e1;border-radius:12px;font-size:.95rem;box-sizing:border-box"
            >
        </div>

        <button
            type="submit"
            style="width:100%;padding:13px;border-radius:12px;border:none;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;font-weight:700;font-size:1rem;cursor:pointer;box-shadow:0 4px 14px rgba(99,102,241,0.3)"
        >
            ✉️ Gửi liên kết khôi phục
        </button>
    </form>

    <div style="margin-top:24px;text-align:center;font-size:.9rem;color:#64748b">
        Nhớ lại mật khẩu?
        <a href="<?= BASE_URL ?>?action=login" style="color:#6366f1;font-weight:700;text-decoration:none;margin-left:4px">
            Quay lại đăng nhập
        </a>
    </div>

</div>
