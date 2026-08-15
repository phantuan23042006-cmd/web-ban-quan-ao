<?php

$errors = $errors ?? [];
$old = $old ?? [];
$csrfToken = $csrfToken ?? '';

function authEscape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

function authOld($key, $old)
{
    return authEscape($old[$key] ?? '');
}

function authError($key, $errors)
{
    return authEscape($errors[$key] ?? '');
}

?>

<style>
    .register-page {
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

    .register-container {
        width: 100%;
        max-width: 1050px;
        display: grid;
        grid-template-columns: 0.9fr 1.1fr;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 65px rgba(15, 23, 42, 0.14);
    }

    .register-banner {
        position: relative;
        padding: 55px 45px;
        color: #ffffff;
        background:
            linear-gradient(
                145deg,
                rgba(15, 23, 42, 0.92),
                rgba(124, 58, 237, 0.82)
            ),
            url("<?= BASE_URL ?>assets/uploads/register-fashion.jpg")
                center / cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 680px;
    }

    .register-banner::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(
                180deg,
                rgba(255, 255, 255, 0.05),
                rgba(15, 23, 42, 0.22)
            );
        pointer-events: none;
    }

    .register-banner-content,
    .register-banner-footer {
        position: relative;
        z-index: 1;
    }

    .register-brand {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 55px;
        font-size: 25px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }

    .register-logo {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(12px);
    }

    .register-banner h2 {
        margin: 0 0 18px;
        font-size: clamp(32px, 4vw, 48px);
        line-height: 1.13;
    }

    .register-banner p {
        margin: 0;
        max-width: 390px;
        color: rgba(255, 255, 255, 0.84);
        font-size: 16px;
        line-height: 1.8;
    }

    .register-benefits {
        margin: 32px 0 0;
        padding: 0;
        list-style: none;
        display: grid;
        gap: 16px;
    }

    .register-benefits li {
        display: flex;
        align-items: center;
        gap: 12px;
        color: rgba(255, 255, 255, 0.94);
        font-size: 15px;
    }

    .register-benefit-icon {
        width: 30px;
        height: 30px;
        flex-shrink: 0;
        border-radius: 50%;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.16);
        font-weight: 700;
    }

    .register-banner-footer {
        margin-top: 45px;
        font-size: 13px;
        color: rgba(255, 255, 255, 0.72);
    }

    .register-form-panel {
        padding: 48px 52px;
    }

    .register-form-heading {
        margin-bottom: 32px;
    }

    .register-form-heading h1 {
        margin: 0 0 10px;
        color: #0f172a;
        font-size: 31px;
        font-weight: 800;
    }

    .register-form-heading p {
        margin: 0;
        color: #64748b;
        line-height: 1.7;
    }

    .register-form {
        display: grid;
        gap: 20px;
    }

    .register-form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    .register-field {
        display: grid;
        gap: 8px;
    }

    .register-field label {
        color: #334155;
        font-size: 14px;
        font-weight: 700;
    }

    .register-required {
        color: #e11d48;
    }

    .register-input-wrapper {
        position: relative;
    }

    .register-input,
    .register-textarea {
        width: 100%;
        box-sizing: border-box;
        border: 1px solid #dbe3ee;
        border-radius: 13px;
        background: #f8fafc;
        color: #0f172a;
        font-size: 15px;
        outline: none;
        transition: 0.2s ease;
    }

    .register-input {
        height: 50px;
        padding: 0 15px;
    }

    .register-input.has-toggle {
        padding-right: 54px;
    }

    .register-textarea {
        min-height: 90px;
        padding: 14px 15px;
        resize: vertical;
        font-family: inherit;
    }

    .register-input:focus,
    .register-textarea:focus {
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.10);
    }

    .register-input.is-invalid,
    .register-textarea.is-invalid {
        border-color: #e11d48;
        background: #fff7f9;
    }

    .register-input::placeholder,
    .register-textarea::placeholder {
        color: #94a3b8;
    }

    .register-password-toggle {
        position: absolute;
        top: 50%;
        right: 9px;
        transform: translateY(-50%);
        width: 38px;
        height: 36px;
        border: 0;
        border-radius: 9px;
        background: transparent;
        color: #64748b;
        cursor: pointer;
        font-size: 17px;
    }

    .register-password-toggle:hover {
        background: #ede9fe;
        color: #6d28d9;
    }

    .register-error {
        color: #e11d48;
        font-size: 13px;
        line-height: 1.45;
    }

    .register-alert {
        padding: 14px 16px;
        border: 1px solid #fecdd3;
        border-radius: 13px;
        background: #fff1f2;
        color: #be123c;
        font-size: 14px;
        line-height: 1.6;
    }

    .register-submit {
        width: 100%;
        height: 52px;
        margin-top: 3px;
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

    .register-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 17px 30px rgba(124, 58, 237, 0.28);
    }

    .register-submit:active {
        transform: translateY(0);
    }

    .register-login-link {
        margin: 23px 0 0;
        text-align: center;
        color: #64748b;
        font-size: 14px;
    }

    .register-login-link a {
        color: #7c3aed;
        font-weight: 800;
        text-decoration: none;
    }

    .register-login-link a:hover {
        text-decoration: underline;
    }

    .register-policy {
        margin: 3px 0 0;
        color: #94a3b8;
        font-size: 12px;
        line-height: 1.6;
        text-align: center;
    }

    @media (max-width: 880px) {
        .register-container {
            max-width: 620px;
            grid-template-columns: 1fr;
        }

        .register-banner {
            min-height: auto;
            padding: 38px 32px;
        }

        .register-brand {
            margin-bottom: 35px;
        }

        .register-banner-footer {
            margin-top: 32px;
        }

        .register-form-panel {
            padding: 42px 34px;
        }
    }

    @media (max-width: 560px) {
        .register-page {
            padding: 20px 12px;
            align-items: flex-start;
        }

        .register-container {
            border-radius: 18px;
        }

        .register-banner {
            padding: 30px 23px;
        }

        .register-banner h2 {
            font-size: 30px;
        }

        .register-benefits {
            gap: 12px;
        }

        .register-form-panel {
            padding: 32px 22px;
        }

        .register-form-heading h1 {
            font-size: 27px;
        }

        .register-form-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<section class="register-page">
    <div class="register-container">
        <aside class="register-banner">
            <div class="register-banner-content">
                <a href="<?= BASE_URL ?>" class="register-brand" style="text-decoration:none;color:inherit;display:inline-flex;align-items:center;gap:12px">
                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo.jpg" alt="TAN & TUAN" style="height:44px;object-fit:contain;background:#fff;padding:3px 6px;border-radius:10px;vertical-align:middle">
                    <span style="font-family:'Space Grotesk',sans-serif;font-weight:900;letter-spacing:1px;text-transform:uppercase">TAN & TUAN</span>
                </a>

                <h2>
                    Khám phá phong cách dành riêng cho bạn
                </h2>

                <p>
                    Tạo tài khoản để mua sắm nhanh hơn, quản lý đơn hàng
                    và cập nhật những sản phẩm thời trang mới nhất.
                </p>

                <ul class="register-benefits">
                    <li>
                        <span class="register-benefit-icon">✓</span>
                        Theo dõi trạng thái đơn hàng dễ dàng
                    </li>

                    <li>
                        <span class="register-benefit-icon">✓</span>
                        Lưu thông tin nhận hàng thuận tiện
                    </li>

                    <li>
                        <span class="register-benefit-icon">✓</span>
                        Đánh giá sản phẩm sau khi mua hàng
                    </li>
                </ul>
            </div>

            <div class="register-banner-footer">
                Thời trang hiện đại · Mua sắm an toàn · Giao hàng thuận tiện
            </div>
        </aside>

        <div class="register-form-panel">
            <div class="register-form-heading">
                <h1>Tạo tài khoản</h1>

                <p>
                    Điền thông tin bên dưới để bắt đầu mua sắm.
                </p>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="register-alert">
                    <?= authError('general', $errors) ?>
                </div>
            <?php endif; ?>

            <form
                class="register-form"
                action="<?= BASE_URL ?>?action=register-submit"
                method="POST"
                autocomplete="on"
            >
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= authEscape($csrfToken) ?>"
                >

                <div class="register-field">
                    <label for="full_name">
                        Họ và tên
                        <span class="register-required">*</span>
                    </label>

                    <input
                        class="register-input
                            <?= !empty($errors['full_name'])
                                ? 'is-invalid'
                                : '' ?>"
                        type="text"
                        id="full_name"
                        name="full_name"
                        value="<?= authOld('full_name', $old) ?>"
                        maxlength="100"
                        placeholder="Ví dụ: Nguyễn Văn An"
                        autocomplete="name"
                        required
                    >

                    <?php if (!empty($errors['full_name'])): ?>
                        <span class="register-error">
                            <?= authError('full_name', $errors) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="register-form-row">
                    <div class="register-field">
                        <label for="email">
                            Email
                            <span class="register-required">*</span>
                        </label>

                        <input
                            class="register-input
                                <?= !empty($errors['email'])
                                    ? 'is-invalid'
                                    : '' ?>"
                            type="email"
                            id="email"
                            name="email"
                            value="<?= authOld('email', $old) ?>"
                            maxlength="150"
                            placeholder="vidu@gmail.com"
                            autocomplete="email"
                            required
                        >

                        <?php if (!empty($errors['email'])): ?>
                            <span class="register-error">
                                <?= authError('email', $errors) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="register-field">
                        <label for="phone">
                            Số điện thoại
                            <span class="register-required">*</span>
                        </label>

                        <input
                            class="register-input
                                <?= !empty($errors['phone'])
                                    ? 'is-invalid'
                                    : '' ?>"
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?= authOld('phone', $old) ?>"
                            maxlength="20"
                            placeholder="0901234567"
                            autocomplete="tel"
                            required
                        >

                        <?php if (!empty($errors['phone'])): ?>
                            <span class="register-error">
                                <?= authError('phone', $errors) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="register-field">
                    <label for="address">
                        Địa chỉ
                    </label>

                    <textarea
                        class="register-textarea
                            <?= !empty($errors['address'])
                                ? 'is-invalid'
                                : '' ?>"
                        id="address"
                        name="address"
                        maxlength="255"
                        placeholder="Nhập địa chỉ nhận hàng"
                        autocomplete="street-address"
                    ><?= authOld('address', $old) ?></textarea>

                    <?php if (!empty($errors['address'])): ?>
                        <span class="register-error">
                            <?= authError('address', $errors) ?>
                        </span>
                    <?php endif; ?>
                </div>

                <div class="register-form-row">
                    <div class="register-field">
                        <label for="password">
                            Mật khẩu
                            <span class="register-required">*</span>
                        </label>

                        <div class="register-input-wrapper">
                            <input
                                class="register-input has-toggle
                                    <?= !empty($errors['password'])
                                        ? 'is-invalid'
                                        : '' ?>"
                                type="password"
                                id="password"
                                name="password"
                                minlength="6"
                                maxlength="72"
                                placeholder="Tối thiểu 6 ký tự"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                class="register-password-toggle"
                                type="button"
                                data-toggle-password="password"
                                aria-label="Hiện hoặc ẩn mật khẩu"
                            >
                                👁
                            </button>
                        </div>

                        <?php if (!empty($errors['password'])): ?>
                            <span class="register-error">
                                <?= authError('password', $errors) ?>
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="register-field">
                        <label for="password_confirmation">
                            Xác nhận mật khẩu
                            <span class="register-required">*</span>
                        </label>

                        <div class="register-input-wrapper">
                            <input
                                class="register-input has-toggle
                                    <?= !empty($errors['password_confirmation'])
                                        ? 'is-invalid'
                                        : '' ?>"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                minlength="6"
                                maxlength="72"
                                placeholder="Nhập lại mật khẩu"
                                autocomplete="new-password"
                                required
                            >

                            <button
                                class="register-password-toggle"
                                type="button"
                                data-toggle-password="password_confirmation"
                                aria-label="Hiện hoặc ẩn mật khẩu"
                            >
                                👁
                            </button>
                        </div>

                        <?php if (
                            !empty($errors['password_confirmation'])
                        ): ?>
                            <span class="register-error">
                                <?= authError(
                                    'password_confirmation',
                                    $errors
                                ) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <button
                    class="register-submit"
                    type="submit"
                >
                    Đăng ký tài khoản
                </button>

                <p class="register-policy">
                    Khi đăng ký, bạn đồng ý cung cấp thông tin chính xác
                    để hệ thống xử lý tài khoản và đơn hàng.
                </p>
            </form>

            <p class="register-login-link">
                Đã có tài khoản?
                <a href="<?= BASE_URL ?>?action=login">
                    Đăng nhập ngay
                </a>
            </p>
        </div>
    </div>
</section>

<script>
    document
        .querySelectorAll('[data-toggle-password]')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const inputId =
                    button.getAttribute('data-toggle-password');

                const input =
                    document.getElementById(inputId);

                if (!input) {
                    return;
                }

                const isPassword =
                    input.type === 'password';

                input.type =
                    isPassword ? 'text' : 'password';

                button.textContent =
                    isPassword ? '🙈' : '👁';
            });
        });
</script>