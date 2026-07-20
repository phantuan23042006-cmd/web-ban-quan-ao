<?php

$currentUser = $_SESSION['user'] ?? null;
$currentRole = $currentUser['role'] ?? null;
$currentAction = $_GET['action'] ?? '/';

$title = $title ?? 'Fashion Store';
$view = $view ?? null;

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars(
            (string) $value,
            ENT_QUOTES,
            'UTF-8'
        );
    }
}

if (!function_exists('menuActive')) {
    function menuActive($actions, $currentAction)
    {
        return in_array(
            $currentAction,
            (array) $actions,
            true
        ) ? 'active' : '';
    }
}

/*
|--------------------------------------------------------------------------
| TỰ XÁC ĐỊNH LOẠI LAYOUT
|--------------------------------------------------------------------------
|
| auth  : trang đăng nhập, đăng ký
| user  : header + menu + nội dung + footer
| admin : sidebar + topbar + nội dung
|
*/

if (!isset($layout)) {
    if (
        $view !== null &&
        strpos($view, 'auth/') === 0
    ) {
        $layout = 'auth';
    } elseif ($currentRole === 'admin') {
        $layout = 'admin';
    } elseif ($currentRole === 'user') {
        $layout = 'user';
    } else {
        $layout = 'auth';
    }
}

/*
|--------------------------------------------------------------------------
| XÁC ĐỊNH FILE VIEW CON
|--------------------------------------------------------------------------
|
| Không require view trong function.
| Require trực tiếp để giữ các biến controller truyền sang.
|
*/

$viewFile = null;

if (!empty($view)) {
    $candidateViewFile =
        PATH_VIEW . $view . '.php';

    if (is_readable($candidateViewFile)) {
        $viewFile = $candidateViewFile;
    }
}

$userName = trim(
    $currentUser['full_name'] ?? 'Khách hàng'
);

$userEmail = trim(
    $currentUser['email'] ?? ''
);

if ($userName === '') {
    $userName = 'Khách hàng';
}

if (function_exists('mb_substr')) {
    $userInitial = mb_strtoupper(
        mb_substr($userName, 0, 1)
    );
} else {
    $userInitial = strtoupper(
        substr($userName, 0, 1)
    );
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($title) ?> | Fashion Store
    </title>

    <style>
        :root {
            --primary: #7c3aed;
            --primary-dark: #6d28d9;
            --secondary: #db2777;
            --dark: #0f172a;
            --dark-soft: #1e293b;
            --text: #334155;
            --muted: #64748b;
            --border: #e2e8f0;
            --background: #f8fafc;
            --white: #ffffff;
            --danger: #be123c;
            --sidebar-width: 270px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            min-height: 100vh;
            background: var(--background);
            color: var(--text);
            font-family:
                Arial,
                Helvetica,
                sans-serif;
        }

        a {
            color: inherit;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        .view-error {
            padding: 28px;
            border: 1px dashed #cbd5e1;
            border-radius: 16px;
            background: #ffffff;
            color: #64748b;
            text-align: center;
        }

        /*
        |--------------------------------------------------------------------------
        | AUTH
        |--------------------------------------------------------------------------
        */

        .auth-layout {
            min-height: 100vh;
        }

        /*
        |--------------------------------------------------------------------------
        | USER
        |--------------------------------------------------------------------------
        */

        .user-layout {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .container {
            width: min(
                1200px,
                calc(100% - 32px)
            );
            margin: 0 auto;
        }

        .user-topbar {
            background: var(--dark);
            color: rgba(255, 255, 255, 0.82);
            font-size: 13px;
        }

        .user-topbar-inner {
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .user-topbar-links {
            display: flex;
            gap: 18px;
        }

        .user-topbar a {
            text-decoration: none;
        }

        .user-topbar a:hover {
            color: #ffffff;
        }

        .user-header {
            position: sticky;
            top: 0;
            z-index: 900;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.97);
            box-shadow:
                0 6px 20px
                rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(14px);
        }

        .user-header-inner {
            position: relative;
            min-height: 76px;
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .brand {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 11px;
            color: var(--dark);
            font-size: 21px;
            font-weight: 800;
            text-decoration: none;
        }

        .brand-logo {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--secondary)
                );
            color: #ffffff;
            box-shadow:
                0 8px 18px
                rgba(124, 58, 237, 0.24);
        }

        .user-nav {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-nav a {
            padding: 11px 14px;
            border-radius: 10px;
            color: #475569;
            font-size: 14px;
            font-weight: 700;
            text-decoration: none;
        }

        .user-nav a:hover,
        .user-nav a.active {
            background: #f3e8ff;
            color: var(--primary-dark);
        }

        .user-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .icon-button {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: #ffffff;
            color: #475569;
            text-decoration: none;
            cursor: pointer;
        }

        .icon-button:hover {
            border-color: #c4b5fd;
            background: #faf5ff;
            color: var(--primary-dark);
        }

        .account {
            position: relative;
        }

        .account-button {
            min-height: 46px;
            padding: 5px 10px 5px 6px;
            display: flex;
            align-items: center;
            gap: 9px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #ffffff;
            color: var(--dark);
            cursor: pointer;
        }

        .avatar {
            width: 34px;
            height: 34px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--secondary)
                );
            color: #ffffff;
            font-size: 14px;
            font-weight: 800;
        }

        .account-name {
            max-width: 140px;
            overflow: hidden;
            font-size: 13px;
            font-weight: 700;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1000;
            width: 240px;
            padding: 9px;
            display: none;
            border: 1px solid var(--border);
            border-radius: 15px;
            background: #ffffff;
            box-shadow:
                0 18px 45px
                rgba(15, 23, 42, 0.14);
        }

        .account.open .account-menu {
            display: block;
        }

        .account-summary {
            padding: 10px 10px 13px;
            border-bottom: 1px solid #eef2f7;
        }

        .account-summary strong,
        .account-summary span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-summary strong {
            color: var(--dark);
            font-size: 14px;
        }

        .account-summary span {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
        }

        .account-menu a {
            margin-top: 4px;
            padding: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 9px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
        }

        .account-menu a:hover {
            background: #f8fafc;
            color: var(--primary-dark);
        }

        .account-menu .logout-link {
            color: var(--danger);
        }

        .mobile-menu-button {
            display: none;
        }

        .user-content {
            flex: 1;
            width: min(
                1200px,
                calc(100% - 32px)
            );
            margin: 0 auto;
            padding: 32px 0 55px;
        }

        .user-footer {
            margin-top: auto;
            background: var(--dark);
            color: rgba(255, 255, 255, 0.7);
        }

        .user-footer-main {
            padding: 48px 0 34px;
            display: grid;
            grid-template-columns:
                1.4fr 1fr 1fr 1fr;
            gap: 38px;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 11px;
            color: #ffffff;
            font-size: 21px;
            font-weight: 800;
        }

        .footer-description {
            max-width: 330px;
            margin: 17px 0 0;
            font-size: 14px;
            line-height: 1.8;
        }

        .footer-column h3 {
            margin: 0 0 17px;
            color: #ffffff;
            font-size: 15px;
        }

        .footer-column a,
        .footer-column span {
            margin-top: 10px;
            display: block;
            color: rgba(255, 255, 255, 0.68);
            font-size: 14px;
            line-height: 1.55;
            text-decoration: none;
        }

        .footer-column a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            border-top:
                1px solid
                rgba(255, 255, 255, 0.1);
        }

        .footer-bottom-inner {
            min-height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            font-size: 13px;
        }

        /*
        |--------------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------------
        */

        .admin-layout {
            min-height: 100vh;
            background: #f1f5f9;
        }

        .admin-overlay {
            position: fixed;
            inset: 0;
            z-index: 1040;
            display: none;
            background: rgba(15, 23, 42, 0.55);
        }

        .admin-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1050;
            width: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background:
                linear-gradient(
                    180deg,
                    #111827 0%,
                    #0f172a 100%
                );
            color: rgba(255, 255, 255, 0.76);
            transition: transform 0.25s ease;
        }

        .admin-brand {
            min-height: 78px;
            padding: 0 22px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom:
                1px solid
                rgba(255, 255, 255, 0.09);
            color: #ffffff;
            font-size: 19px;
            font-weight: 800;
            text-decoration: none;
        }

        .admin-label {
            padding: 24px 24px 8px;
            color: rgba(255, 255, 255, 0.38);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .admin-menu {
            margin: 0;
            padding: 0 13px;
            display: grid;
            gap: 4px;
            list-style: none;
        }

        .admin-menu a {
            min-height: 45px;
            padding: 0 13px;
            display: flex;
            align-items: center;
            gap: 13px;
            border-radius: 11px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            font-weight: 650;
            text-decoration: none;
        }

        .admin-menu a:hover {
            background:
                rgba(255, 255, 255, 0.07);
            color: #ffffff;
        }

        .admin-menu a.active {
            background:
                linear-gradient(
                    135deg,
                    var(--primary),
                    var(--secondary)
                );
            color: #ffffff;
            box-shadow:
                0 8px 20px
                rgba(124, 58, 237, 0.2);
        }

        .admin-menu-icon {
            width: 23px;
            text-align: center;
            font-size: 17px;
        }

        .admin-sidebar-bottom {
            margin-top: auto;
            padding: 18px 13px;
            border-top:
                1px solid
                rgba(255, 255, 255, 0.09);
        }

        .admin-user-box {
            padding: 11px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 12px;
            background:
                rgba(255, 255, 255, 0.06);
        }

        .admin-user-info {
            min-width: 0;
            flex: 1;
        }

        .admin-user-info strong,
        .admin-user-info span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-user-info strong {
            color: #ffffff;
            font-size: 13px;
        }

        .admin-user-info span {
            margin-top: 3px;
            color: rgba(255, 255, 255, 0.45);
            font-size: 11px;
        }

        .admin-wrapper {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
        }

        .admin-header {
            position: sticky;
            top: 0;
            z-index: 800;
            min-height: 78px;
            padding: 0 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid var(--border);
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(14px);
        }

        .admin-header-left,
        .admin-header-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .admin-toggle {
            display: none;
        }

        .admin-heading h1 {
            margin: 0;
            color: var(--dark);
            font-size: 22px;
        }

        .admin-heading p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
        }

        .admin-header-link {
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #ffffff;
            color: #475569;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }

        .admin-logout {
            border-color: #fecdd3;
            background: #fff1f2;
            color: var(--danger);
        }

        .admin-content {
            padding: 30px;
        }

        .admin-content-inner {
            width: 100%;
            max-width: 1500px;
            margin: 0 auto;
        }

        /*
        |--------------------------------------------------------------------------
        | RESPONSIVE
        |--------------------------------------------------------------------------
        */

        @media (max-width: 980px) {
            .mobile-menu-button,
            .admin-toggle {
                display: grid;
            }

            .user-nav {
                position: absolute;
                top: 76px;
                right: 0;
                left: 0;
                padding: 13px 16px 18px;
                display: none;
                flex-direction: column;
                align-items: stretch;
                border-bottom:
                    1px solid
                    var(--border);
                background: #ffffff;
                box-shadow:
                    0 15px 25px
                    rgba(15, 23, 42, 0.08);
            }

            .user-nav.open {
                display: flex;
            }

            .user-nav a {
                width: 100%;
            }

            .user-actions {
                margin-left: auto;
            }

            .admin-sidebar {
                transform: translateX(-100%);
            }

            .admin-sidebar.open {
                transform: translateX(0);
            }

            .admin-wrapper {
                margin-left: 0;
            }

            .admin-overlay.open {
                display: block;
            }
        }

        @media (max-width: 820px) {
            .user-footer-main {
                grid-template-columns: 1fr 1fr;
            }

            .user-topbar-links {
                display: none;
            }

            .user-topbar-inner {
                justify-content: center;
            }
        }

        @media (max-width: 620px) {
            .brand-text,
            .account-name,
            .account-arrow,
            .admin-heading p,
            .admin-view-site {
                display: none;
            }

            .user-header-inner {
                min-height: 68px;
            }

            .user-nav {
                top: 68px;
            }

            .account-button {
                padding-right: 6px;
            }

            .user-footer-main {
                grid-template-columns: 1fr;
                gap: 28px;
            }

            .footer-bottom-inner {
                padding: 15px 0;
                align-items: flex-start;
                flex-direction: column;
                justify-content: center;
            }

            .admin-header {
                min-height: 70px;
                padding: 0 15px;
            }

            .admin-heading h1 {
                font-size: 18px;
            }

            .admin-content {
                padding: 18px 12px;
            }
        }
    </style>
</head>

<body>

<?php if ($layout === 'auth'): ?>

    <main class="auth-layout">

        <?php if ($viewFile !== null): ?>

            <?php require $viewFile; ?>

        <?php else: ?>

            <div class="view-error">
                Không tìm thấy giao diện:

                <strong>
                    <?= e($view ?? '') ?>
                </strong>
            </div>

        <?php endif; ?>

    </main>

<?php elseif ($layout === 'admin'): ?>

    <div class="admin-layout">

        <div
            class="admin-overlay"
            id="adminOverlay"
        ></div>

        <aside
            class="admin-sidebar"
            id="adminSidebar"
        >
            <a
                class="admin-brand"
                href="<?= BASE_URL ?>?action=admin-dashboard"
            >
                <span class="brand-logo">
                    F
                </span>

                <span>
                    Fashion Admin
                </span>
            </a>

            <div class="admin-label">
                Tổng quan
            </div>

            <ul class="admin-menu">
                <li>
                    <a
                        class="<?= menuActive(
                            ['admin-dashboard'],
                            $currentAction
                        ) ?>"
                        href="<?= BASE_URL ?>?action=admin-dashboard"
                    >
                        <span class="admin-menu-icon">
                            ⌂
                        </span>

                        <span>
                            Dashboard
                        </span>
                    </a>
                </li>
            </ul>

            <div class="admin-label">
                Quản lý
            </div>

            <ul class="admin-menu">
                <li>
                    <a
                        class="<?= menuActive(
                            ['admin-users'],
                            $currentAction
                        ) ?>"
                        href="<?= BASE_URL ?>?action=admin-users"
                    >
                        <span class="admin-menu-icon">
                            ♙
                        </span>

                        <span>
                            Tài khoản
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        <span class="admin-menu-icon">
                            ▣
                        </span>

                        <span>
                            Sản phẩm
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        <span class="admin-menu-icon">
                            ▤
                        </span>

                        <span>
                            Danh mục
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        <span class="admin-menu-icon">
                            ▧
                        </span>

                        <span>
                            Đơn hàng
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        <span class="admin-menu-icon">
                            ★
                        </span>

                        <span>
                            Đánh giá
                        </span>
                    </a>
                </li>
            </ul>

            <div class="admin-label">
                Báo cáo
            </div>

            <ul class="admin-menu">
                <li>
                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        <span class="admin-menu-icon">
                            ▥
                        </span>

                        <span>
                            Thống kê
                        </span>
                    </a>
                </li>
            </ul>

            <div class="admin-sidebar-bottom">
                <div class="admin-user-box">

                    <span class="avatar">
                        <?= e($userInitial) ?>
                    </span>

                    <div class="admin-user-info">
                        <strong>
                            <?= e($userName) ?>
                        </strong>

                        <span>
                            Quản trị viên
                        </span>
                    </div>

                </div>
            </div>
        </aside>

        <div class="admin-wrapper">

            <header class="admin-header">

                <div class="admin-header-left">

                    <button
                        class="icon-button admin-toggle"
                        id="adminToggle"
                        type="button"
                        aria-label="Mở sidebar"
                    >
                        ☰
                    </button>

                    <div class="admin-heading">
                        <h1>
                            <?= e($title) ?>
                        </h1>

                        <p>
                            Hệ thống quản trị Fashion Store
                        </p>
                    </div>

                </div>

                <div class="admin-header-actions">

                    <a
                        class="admin-header-link admin-view-site"
                        href="<?= BASE_URL ?>"
                    >
                        Xem website
                    </a>

                    <a
                        class="admin-header-link admin-logout"
                        href="<?= BASE_URL ?>?action=logout"
                        onclick="
                            return confirm(
                                'Bạn có chắc muốn đăng xuất?'
                            );
                        "
                    >
                        Đăng xuất
                    </a>

                </div>

            </header>

            <main class="admin-content">
                <div class="admin-content-inner">

                    <?php if ($viewFile !== null): ?>

                        <?php require $viewFile; ?>

                    <?php else: ?>

                        <div class="view-error">
                            Chưa tạo giao diện:

                            <strong>
                                <?= e($view ?? '') ?>
                            </strong>
                        </div>

                    <?php endif; ?>

                </div>
            </main>

        </div>
    </div>

<?php else: ?>

    <div class="user-layout">

        <div class="user-topbar">
            <div class="container user-topbar-inner">

                <span>
                    Miễn phí giao hàng cho đơn từ 500.000đ
                </span>

                <div class="user-topbar-links">
                    <a href="javascript:void(0)">
                        Chính sách đổi trả
                    </a>

                    <a href="javascript:void(0)">
                        Hỗ trợ khách hàng
                    </a>
                </div>

            </div>
        </div>

        <header class="user-header">
            <div class="container user-header-inner">

                <a
                    class="brand"
                    href="<?= BASE_URL ?>"
                >
                    <span class="brand-logo">
                        F
                    </span>

                    <span class="brand-text">
                        Fashion Store
                    </span>
                </a>

                <nav
                    class="user-nav"
                    id="userNav"
                >
                    <a
                        class="<?= menuActive(
                            [
                                '/',
                                'home',
                                'user-dashboard'
                            ],
                            $currentAction
                        ) ?>"
                        href="<?= BASE_URL ?>"
                    >
                        Trang chủ
                    </a>

                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        Sản phẩm
                    </a>

                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        Danh mục
                    </a>

                    <a
                        href="javascript:void(0)"
                        title="Sẽ phát triển sau"
                    >
                        Đơn hàng của tôi
                    </a>
                </nav>

                <div class="user-actions">

                    <a
                        class="icon-button"
                        href="javascript:void(0)"
                        title="Giỏ hàng sẽ phát triển sau"
                        aria-label="Giỏ hàng"
                    >
                        🛒
                    </a>

                    <div
                        class="account"
                        id="userAccount"
                    >
                        <button
                            class="account-button"
                            id="accountButton"
                            type="button"
                        >
                            <span class="avatar">
                                <?= e($userInitial) ?>
                            </span>

                            <span class="account-name">
                                <?= e($userName) ?>
                            </span>

                            <span class="account-arrow">
                                ▼
                            </span>
                        </button>

                        <div class="account-menu">

                            <div class="account-summary">
                                <strong>
                                    <?= e($userName) ?>
                                </strong>

                                <span>
                                    <?= e($userEmail) ?>
                                </span>
                            </div>

                            <a href="javascript:void(0)">
                                <span>👤</span>
                                Thông tin tài khoản
                            </a>

                            <a href="javascript:void(0)">
                                <span>📦</span>
                                Lịch sử mua hàng
                            </a>

                            <a
                                class="logout-link"
                                href="<?= BASE_URL ?>?action=logout"
                                onclick="
                                    return confirm(
                                        'Bạn có chắc muốn đăng xuất?'
                                    );
                                "
                            >
                                <span>↪</span>
                                Đăng xuất
                            </a>

                        </div>
                    </div>

                    <button
                        class="icon-button mobile-menu-button"
                        id="mobileMenuButton"
                        type="button"
                        aria-label="Mở menu"
                    >
                        ☰
                    </button>

                </div>
            </div>
        </header>

        <main class="user-content">

            <?php if ($viewFile !== null): ?>

                <?php require $viewFile; ?>

            <?php else: ?>

                <div class="view-error">
                    Chưa tạo giao diện:

                    <strong>
                        <?= e($view ?? '') ?>
                    </strong>
                </div>

            <?php endif; ?>

        </main>

        <footer class="user-footer">

            <div class="container user-footer-main">

                <div>
                    <div class="footer-brand">

                        <span class="brand-logo">
                            F
                        </span>

                        <span>
                            Fashion Store
                        </span>

                    </div>

                    <p class="footer-description">
                        Cửa hàng thời trang trực tuyến dành cho
                        khách hàng yêu thích phong cách trẻ trung,
                        hiện đại và tiện lợi.
                    </p>
                </div>

                <div class="footer-column">
                    <h3>Mua sắm</h3>

                    <a href="javascript:void(0)">
                        Tất cả sản phẩm
                    </a>

                    <a href="javascript:void(0)">
                        Danh mục sản phẩm
                    </a>

                    <a href="javascript:void(0)">
                        Giỏ hàng
                    </a>
                </div>

                <div class="footer-column">
                    <h3>Tài khoản</h3>

                    <a href="javascript:void(0)">
                        Thông tin cá nhân
                    </a>

                    <a href="javascript:void(0)">
                        Đơn hàng của tôi
                    </a>

                    <a href="<?= BASE_URL ?>?action=logout">
                        Đăng xuất
                    </a>
                </div>

                <div class="footer-column">
                    <h3>Liên hệ</h3>

                    <span>
                        Email: support@fashion.local
                    </span>

                    <span>
                        Điện thoại: 0900 000 000
                    </span>

                    <span>
                        Thời gian: 08:00 - 21:00
                    </span>
                </div>

            </div>

            <div class="footer-bottom">
                <div class="container footer-bottom-inner">

                    <span>
                        © <?= date('Y') ?> Fashion Store.
                    </span>

                    <span>
                        Thanh toán an toàn · Bảo mật thông tin
                    </span>

                </div>
            </div>

        </footer>
    </div>

<?php endif; ?>

<script>
    const userNav =
        document.getElementById('userNav');

    const mobileMenuButton =
        document.getElementById(
            'mobileMenuButton'
        );

    if (userNav && mobileMenuButton) {
        mobileMenuButton.addEventListener(
            'click',
            function () {
                userNav.classList.toggle('open');
            }
        );
    }

    const userAccount =
        document.getElementById('userAccount');

    const accountButton =
        document.getElementById('accountButton');

    if (userAccount && accountButton) {
        accountButton.addEventListener(
            'click',
            function (event) {
                event.stopPropagation();

                userAccount.classList.toggle(
                    'open'
                );
            }
        );

        document.addEventListener(
            'click',
            function () {
                userAccount.classList.remove(
                    'open'
                );
            }
        );
    }

    const adminSidebar =
        document.getElementById('adminSidebar');

    const adminToggle =
        document.getElementById('adminToggle');

    const adminOverlay =
        document.getElementById('adminOverlay');

    function closeAdminSidebar() {
        if (adminSidebar) {
            adminSidebar.classList.remove(
                'open'
            );
        }

        if (adminOverlay) {
            adminOverlay.classList.remove(
                'open'
            );
        }
    }

    if (
        adminSidebar &&
        adminToggle &&
        adminOverlay
    ) {
        adminToggle.addEventListener(
            'click',
            function () {
                adminSidebar.classList.toggle(
                    'open'
                );

                adminOverlay.classList.toggle(
                    'open'
                );
            }
        );

        adminOverlay.addEventListener(
            'click',
            closeAdminSidebar
        );
    }
</script>

</body>
</html>