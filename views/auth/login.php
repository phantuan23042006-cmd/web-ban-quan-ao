<?php

$errors = $errors ?? [];
$old = $old ?? [];
$csrfToken = $csrfToken ?? '';
$successMessage = $successMessage ?? null;

if (!function_exists('loginEscape')) {
    function loginEscape($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('loginOld')) {
    function loginOld($key, $old)
    {
        return loginEscape($old[$key] ?? '');
    }
}

if (!function_exists('loginError')) {
    function loginError($key, $errors)
    {
        return loginEscape($errors[$key] ?? '');
    }
}

?>

<style>
    .login-page {
        min-height: calc(100vh - 80px);
        padding: 50px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(
                circle at top left,
                rgba(236, 72, 153, 0.18),
                transparent 35%
            ),
            radial-gradient(
                circle at bottom right,
                rgba(124, 58, 237, 0.16),
                transparent 35%
            ),
            #f8fafc;
    }

    .login-container {
        width: 100%;
        max-width: 1000px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        overflow: hidden;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 24px 65px rgba(15, 23, 42, 0.14);
    }

    .login-banner {
        position: relative;
        min-height: 620px;
        padding: 55px 45px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        color: #ffffff;
        background:
            linear-gradient(
                145deg,
                rgba(15, 23, 42, 0.93),
                rgba(124, 58, 237, 0.84)
            ),
            url("<?= BASE_URL ?>assets/uploads/login-fashion.jpg")
                center / cover no-repeat;
    }

    .login-banner::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(
                180deg,
                rgba(255, 255, 255, 0.05),
                rgba(15, 23, 42, 0.26)
            );
        pointer-events: none;
    }

    .login-banner-content,
    .login-banner-footer {
        position: relative;
        z-index: 1;
    }

    .login-brand {
        display: inline-flex;
        align-items: center;
        gap: 11px;
        margin-bottom: 60px;
        font-size: 25px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .login-logo {
        width: 46px;
        height: 46px;
        display: grid;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, 0.25);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.16);
        backdrop-filter: blur(12px);
    }

    .login-banner h2 {
        margin: 0 0 18px;
        max-width: 430px;
        font-size: clamp(32px, 4vw, 48px);
        line-height: 1.15;
    }

    .login-banner p {
        margin: 0;
        max-width: 390px;
        color: rgba(255, 255, 255, 0.84);
        font-size: 16px;
        line-height: 1.8;
    }

    .login-benefits {
        margin: 32px 0 0;
        padding: 0;
        display: grid;
        gap: 15px;
        list-style: none;
    }

    .login-benefits li {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.94);
        font-size: 15px;
    }

    .login-benefit-icon {
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.16);
        font-weight: 700;
    }

    .login-banner-footer {
        margin-top: 45px;
        color: rgba(255, 255, 255, 0.72);
        font-size: 13px;
    }

    .login-form-panel {
        padding: 55px 52px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .login-form-heading {
        margin-bottom: 32px;
    }

    .login-form-heading h1 {
        margin: 0 0 10px;
        color: #0f172a;
        font-size: 32px;
        font-weight: 800;
    }

    .login-form-heading p {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }

    .login-alert {
        margin-bottom: 20px;
        padding: 14px 16px;
        border-radius: 13px;
        font-size: 14px;
        line-height: 1.6;
    }

    .login-alert-error {
        border: 1px solid #fecdd3;
        background: #fff1f2;
        color: #be123c;
    }

    .login-alert-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .login-form {
        display: grid;
        gap: 21px;
    }

    .login-field {
        display: grid;
        gap: 8px;
    }

    .login-field label {
        color: #334155;
        font-size: 14px;
        font-weight: 700;
    }

    .login-required {
        color: #e11d48;
    }

    .login-input-wrapper {
        position: relative;
    }

    .login-input {
        width: 100%;
        height: 52px;
        box-sizing: border-box;
        padding: 0 15px;
        border: 1px solid #dbe3ee;
        border-radius: 13px;
        outline: none;
        background: #f8fafc;
        color: #0f172a;
        font-size: 15px;
        transition: 0.2s ease;
    }

    .login-input.has-toggle {
        padding-right: 55px;
    }

    .login-input:focus {
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.10);
    }

    .login-input.is-invalid {
        border-color: #e11d48;
        background: #fff7f9;
    }

    .login-input::placeholder {
        color: #94a3b8;
    }

    .login-password-toggle {
        position: absolute;
        top: 50%;
        right: 9px;
        width: 38px;
        height: 36px;
        border: 0;
        border-radius: 9px;
        transform: translateY(-50%);
        background: transparent;
        color: #64748b;
        cursor: pointer;
        font-size: 17px;
    }

    .login-password-toggle:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    .login-error {
        color: #e11d48;
        font-size: 13px;
        line-height: 1.45;
    }

    .login-form-options {
        margin-top: -4px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .login-remember {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        cursor: pointer;
        font-size: 14px;
    }

    .login-remember input {
        width: 17px;
        height: 17px;
        accent-color: #7c3aed;
    }

    .login-forgot {
        color: #7c3aed;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
    }

    .login-forgot:hover {
        text-decoration: underline;
    }

    .login-submit {
        width: 100%;
        height: 52px;
        margin-top: 2px;
        border: 0;
        border-radius: 13px;
        color: #ffffff;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        box-shadow: 0 12px 24px rgba(124, 58, 237, 0.22);
        cursor: pointer;
        font-size: 16px;
        font-weight: 800;
        transition: 0.2s ease;
    }

    .login-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 17px 30px rgba(124, 58, 237, 0.28);
    }

    .login-submit:active {
        transform: translateY(0);
    }

    .login-register-link {
        margin: 25px 0 0;
        text-align: center;
        color: #64748b;
        font-size: 14px;
    }

    .login-register-link a {
        color: #7c3aed;
        font-weight: 800;
        text-decoration: none;
    }

    .login-register-link a:hover {
        text-decoration: underline;
    }

    .login-home-link {
        margin-top: 16px;
        text-align: center;
    }

    .login-home-link a {
        color: #64748b;
        font-size: 13px;
        text-decoration: none;
    }

    .login-home-link a:hover {
        color: #7c3aed;
    }

    @media (max-width: 880px) {
        .login-container {
            max-width: 620px;
            grid-template-columns: 1fr;
        }

        .login-banner {
            min-height: auto;
            padding: 38px 32px;
        }

        .login-brand {
            margin-bottom: 35px;
        }

        .login-banner-footer {
            margin-top: 32px;
        }

        .login-form-panel {
            padding: 44px 34px;
        }
    }

    @media (max-width: 560px) {
        .login-page {
            padding: 20px 12px;
            align-items: flex-start;
        }

        .login-container {
            border-radius: 18px;
        }

        .login-banner {
            padding: 30px 23px;
        }

        .login-banner h2 {
            font-size: 30px;
        }

        .login-form-panel {
            padding: 34px 22px;
        }

        .login-form-heading h1 {
            font-size: 28px;
        }

        .login-form-options {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<section class="login-page">
    <div class="login-container">
        <aside class="login-banner">
            <div class="login-banner-content">
                <a href="<?= BASE_URL ?>" class="login-brand" style="text-decoration:none;color:inherit;display:inline-flex;align-items:center;gap:12px">
                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo.jpg" alt="TAN & TUAN" style="height:44px;object-fit:contain;background:#fff;padding:3px 6px;border-radius:10px;vertical-align:middle">
                    <span style="font-family:'Space Grotesk',sans-serif;font-weight:900;letter-spacing:1px;text-transform:uppercase">TAN & TUAN</span>
                </a>

                <h2>
                    Chào mừng bạn quay trở lại
                </h2>

                <p>
                    Đăng nhập để tiếp tục mua sắm, quản lý giỏ hàng
                    và theo dõi những đơn hàng của bạn.
                </p>

                <ul class="login-benefits">
                    <li>
                        <span class="login-benefit-icon">✓</span>
                        Xem và quản lý lịch sử mua hàng
                    </li>

                    <li>
                        <span class="login-benefit-icon">✓</span>
                        Theo dõi trạng thái giao hàng
                    </li>

                    <li>
                        <span class="login-benefit-icon">✓</span>
                        Truy cập đúng chức năng theo quyền tài khoản
                    </li>
                </ul>
            </div>

            <div class="login-banner-footer">
                Fashion Store · Phong cách của bạn, lựa chọn của bạn
            </div>
        </aside>

        <div class="login-form-panel">
            <div class="login-form-heading">
                <h1>Đăng nhập</h1>

                <p>
                    Nhập email và mật khẩu để truy cập tài khoản.
                </p>
            </div>

            <?php if (!empty($successMessage)): ?>
                <div class="login-alert login-alert-success">
                    <?= loginEscape($successMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors['general'])): ?>
                <div class="login-alert login-alert-error">
                    <?= loginError('general', $errors) ?>
                </div>
            <?php endif; ?>

            <form
                class="login-form"
                action="<?= BASE_URL ?>?action=login-submit"
                method="POST"
                autocomplete="on"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= loginEscape($csrfToken) ?>"
                >

                <div class="login-field">
                    <label for="email">
                        Email
                        <span class="login-required">*</span>
                    </label>

                    <input
                        class="login-input
                            <?= !empty($errors['email'])
                                ? 'is-invalid'
                                : '' ?>"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= loginOld('email', $old) ?>"
                        maxlength="150"
                        placeholder="Nhập địa chỉ email"
                        autocomplete="email"
                        required
                        autofocus
                    >

                    <?php if (!empty($errors['email'])): ?>
                        <span class="login-error">
                            <?= loginError('email', $errors) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="login-field">
                    <label for="password">
                        Mật khẩu
                        <span class="login-required">*</span>
                    </label>

                    <div class="login-input-wrapper">
                        <input
                            class="login-input has-toggle
                                <?= !empty($errors['password'])
                                    ? 'is-invalid'
                                    : '' ?>"
                            type="password"
                            id="password"
                            name="password"
                            maxlength="72"
                            placeholder="Nhập mật khẩu"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            class="login-password-toggle"
                            type="button"
                            id="toggleLoginPassword"
                            aria-label="Hiện hoặc ẩn mật khẩu"
                        >
                            👁
                        </button>
                    </div>

                    <?php if (!empty($errors['password'])): ?>
                        <span class="login-error">
                            <?= loginError('password', $errors) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="login-form-options">
                    <label class="login-remember">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                        >
                        <span>Ghi nhớ đăng nhập</span>
                    </label>

                    <a
                        class="login-forgot"
                        href="<?= BASE_URL ?>?action=forgot-password"
                    >
                        Quên mật khẩu?
                    </a>
                </div>

                <button
                    class="login-submit"
                    type="submit"
                >
                    Đăng nhập
                </button>
            </form>

            <p class="login-register-link">
                Chưa có tài khoản?

                <a href="<?= BASE_URL ?>?action=register">
                    Đăng ký ngay
                </a>
            </p>

            <div class="login-home-link">
                <a href="<?= BASE_URL ?>">
                    ← Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>
</section>

<script>
    const loginPasswordInput =
        document.getElementById('password');

    const loginPasswordButton =
        document.getElementById('toggleLoginPassword');

    if (loginPasswordInput && loginPasswordButton) {
        loginPasswordButton.addEventListener(
            'click',
            function () {
                const isHidden =
                    loginPasswordInput.type === 'password';

                loginPasswordInput.type =
                    isHidden ? 'text' : 'password';

                loginPasswordButton.textContent =
                    isHidden ? '🙈' : '👁';
            }
        );
    }
</script>