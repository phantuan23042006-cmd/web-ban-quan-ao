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
        <?= e($title) ?> | TAN & TUAN CLOTHING
    </title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
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
        | ADMIN - ULTRA MODERN DESIGN SYSTEM
        |--------------------------------------------------------------------------
        */

        .admin-layout {
            min-height: 100vh;
            background: #f4f6fa;
            color: #1e293b;
        }

        .admin-overlay {
            position: fixed;
            inset: 0;
            z-index: 1040;
            display: none;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(4px);
        }

        .admin-sidebar {
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 1050;
            width: var(--sidebar-width);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            background: #0b0f19;
            border-right: 1px solid rgba(255, 255, 255, 0.06);
            color: #94a3b8;
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .admin-brand {
            min-height: 80px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            color: #ffffff;
            font-size: 20px;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: -0.4px;
        }

        .admin-brand-logo {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            display: grid;
            place-items: center;
            color: #ffffff;
            font-size: 18px;
            font-weight: 900;
            box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
        }

        .admin-label {
            padding: 24px 24px 10px;
            color: #475569;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .admin-menu {
            margin: 0;
            padding: 0 16px;
            display: grid;
            gap: 5px;
            list-style: none;
        }

        .admin-menu a {
            min-height: 48px;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-radius: 14px;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 650;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .admin-menu a:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #f8fafc;
            transform: translateX(3px);
        }

        .admin-menu a.active {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #ffffff;
            font-weight: 700;
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.32);
            transform: translateX(0);
        }

        .admin-menu-icon {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }

        .admin-sidebar-bottom {
            margin-top: auto;
            padding: 20px 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.07);
        }

        .admin-user-box {
            padding: 12px 14px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .admin-user-info {
            min-width: 0;
            flex: 1;
        }

        .admin-user-info strong,
        .admin-user-info span {
            display: block;
        }

        .admin-user-info strong {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
        }

        .admin-user-info span {
            margin-top: 2px;
            color: #64748b;
            font-size: 11px;
            font-weight: 600;
        }

        .admin-wrapper {
            min-height: 100vh;
            margin-left: var(--sidebar-width);
            display: flex;
            flex-direction: column;
        }

        .admin-header {
            position: sticky;
            top: 0;
            z-index: 800;
            min-height: 80px;
            padding: 0 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
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
            color: #0f172a;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.4px;
        }

        .admin-heading p {
            margin: 3px 0 0;
            color: #64748b;
            font-size: 13px;
        }

        .admin-header-link {
            padding: 10px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .admin-header-link:hover {
            border-color: #cbd5e1;
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .admin-logout {
            border-color: #fecdd3;
            background: #fff1f2;
            color: #e11d48;
        }

        .admin-logout:hover {
            background: #ffe4e6;
            border-color: #fda4af;
        }

        .admin-content {
            padding: 32px 36px;
            flex: 1;
        }

        .admin-content-inner {
            width: 100%;
            max-width: 1550px;
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
        /* ─── Storefront tổng quát ─── */
        .storefront { padding-top: 36px; padding-bottom: 56px; }
        .storefront h1, .storefront h2, .storefront h3 { color: #0f172a; }

        /* ─── Hero banner ─── */
        .storefront-hero { min-height: 390px; display: flex; align-items: center; padding: 56px; border-radius: 28px; color: white; background: linear-gradient(90deg, rgba(15,23,42,.94), rgba(124,58,237,.55)) no-repeat center/cover; }
        .storefront-hero > div { max-width: 580px; }
        .storefront-hero h1 { color: white; font-size: clamp(2.3rem,5vw,4rem); margin: 10px 0; }
        .storefront-hero p  { font-size: 1.1rem; line-height: 1.7; }

        /* ─── Typography helpers ─── */
        .eyebrow { color: #7c3aed; font-weight: 800; letter-spacing: .12em; font-size: .76rem; }
        .storefront-hero .eyebrow { color: #e9d5ff; }

        /* ─── Section titles ─── */
        .section-title { display: flex; align-items: flex-end; justify-content: space-between; margin: 48px 0 20px; }
        .section-title h1, .section-title h2 { margin: 6px 0 0; }
        .section-title a { color: #7c3aed; font-weight: 700; text-decoration: none; }

        /* ─── Grids ─── */
        .category-grid, .product-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 18px; }

        /* ─── Cards chung ─── */
        .category-tile, .product-card, .profile-panel {
            border: 1px solid #e2e8f0; border-radius: 18px;
            background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.05);
        }

        /* ─── Category tile ─── */
        .category-tile { min-height: 128px; padding: 22px; display: flex; flex-direction: column; gap: 8px; text-decoration: none; color: #0f172a; border: 1px solid #e2e8f0; border-radius: 18px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.05); transition: transform .2s ease; }
        .category-tile:hover { transform: translateY(-3px); border-color: #cbd5e1; }
        .category-tile strong { font-size: 1.05rem; color: #0f172a; }
        .category-tile small { color: #64748b; font-size: .88rem; }
        .category-tile span  { color: #7c3aed; font-size: .85rem; font-weight: 700; margin-top: auto; }

        /* ─── Product card ─── */
        .product-card { border: 1px solid #e2e8f0; border-radius: 18px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.05); overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; display: flex; flex-direction: column; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(15,23,42,.1); }
        .product-card a     { text-decoration: none; color: inherit; display: flex; flex-direction: column; height: 100%; width: 100%; }
        .product-image      { height: 220px; width: 100%; display: flex; align-items: center; justify-content: center; background: #f8fafc; color: #64748b; overflow: hidden; position: relative; border-bottom: 1px solid #f1f5f9; }
        .product-image img  { width: 100%; height: 100%; object-fit: cover; display: block; }
        .product-info       { padding: 16px; background: #ffffff; flex: 1; display: flex; flex-direction: column; }
        .product-info small { color: #7c3aed; font-weight: 700; font-size: .8rem; text-transform: uppercase; letter-spacing: .03em; }
        .product-info h3    { font-size: .98rem; font-weight: 700; min-height: 42px; margin: 6px 0 10px; color: #0f172a; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .product-info-footer { margin-top: auto; display: flex; align-items: center; justify-content: space-between; }
        .product-info strong{ color: #db2777; font-size: 1.05rem; font-weight: 800; }
        .rating             { float: right; color: #a16207; font-size: .85rem; font-weight: 600; }

        /* ─── Catalog / Sản phẩm ─── */
        .catalog-heading { padding: 32px; border-radius: 20px; color: #fff; background: linear-gradient(135deg,#0f172a,#7c3aed); }
        .catalog-heading h1 { color: #fff; margin: 8px 0; }
        .catalog-heading .eyebrow { color: #e9d5ff; }
        .catalog-filter { margin: 24px 0; padding: 18px; display: flex; gap: 10px; flex-wrap: wrap; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; }
        .catalog-filter input,
        .catalog-filter select { min-height: 42px; border: 1px solid #cbd5e1; border-radius: 10px; padding: 0 12px; }

        /* ─── Buttons ─── */
        .button  { display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; padding: 11px 16px; border: 0; text-decoration: none; font-weight: 700; cursor: pointer; transition: opacity .15s; }
        .button:hover { opacity: .88; }
        .primary { background: linear-gradient(135deg,#7c3aed,#db2777); color: #fff; }
        .btn-danger { background: #be123c; color: #fff; }

        /* ─── Empty state ─── */
        .empty { grid-column: 1/-1; padding: 36px; text-align: center; background: #fff; border-radius: 16px; }

        /* ─── Profile panel ─── */
        .profile-panel { padding: 28px; }
        .profile-panel h1, .profile-panel h2 { margin: 0 0 20px; font-size: 1.35rem; }
        .profile-panel dl { display: grid; grid-template-columns: 140px 1fr; gap: 14px; margin: 0 0 24px; }
        .profile-panel dt  { color: #64748b; padding-top: 2px; }
        .profile-panel dd  { margin: 0; font-weight: 600; }

        /* ─── Form controls bên trong profile-panel ─── */
        .profile-panel .form-group        { margin-bottom: 18px; }
        .profile-panel label              { display: block; font-size: .88rem; font-weight: 600; color: #475569; margin-bottom: 6px; }
        .profile-panel input[type=text],
        .profile-panel input[type=email],
        .profile-panel input[type=tel],
        .profile-panel input[type=password] { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font: inherit; font-size: .95rem; transition: border-color .15s; }
        .profile-panel input:focus         { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
        .profile-panel textarea            { width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 10px; font: inherit; min-height: 80px; resize: vertical; }
        .profile-panel textarea:focus      { outline: none; border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
        .profile-tabs                      { display: flex; gap: 6px; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; }
        .profile-tabs a                    { padding: 10px 18px; text-decoration: none; font-weight: 700; font-size: .9rem; color: #64748b; border-radius: 10px 10px 0 0; border-bottom: 3px solid transparent; margin-bottom: -2px; }
        .profile-tabs a.active, .profile-tabs a:hover { color: #7c3aed; border-bottom-color: #7c3aed; }

        /* ─── Order table ─── */
        .order-table         { width: 100%; border-collapse: collapse; }
        .order-table th      { padding: 12px 14px; background: #f8fafc; color: #64748b; font-size: .82rem; text-align: left; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
        .order-table td      { padding: 14px; border-bottom: 1px solid #f1f5f9; }
        .order-table tr:last-child td { border-bottom: none; }
        .order-table a       { color: #7c3aed; font-weight: 700; text-decoration: none; }
        .order-table a:hover { text-decoration: underline; }
        .order-status        { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: .78rem; font-weight: 700; }
        .status-pending      { background: #fef3c7; color: #92400e; }
        .status-confirmed    { background: #dbeafe; color: #1e40af; }
        .status-shipping     { background: #e0e7ff; color: #4338ca; }
        .status-completed    { background: #dcfce7; color: #166534; }
        .status-cancelled    { background: #fee2e2; color: #991b1b; }

        /* ─── Checkout ─── */
        .checkout-grid { display: grid; grid-template-columns: 1.5fr 1fr; gap: 24px; }
        .checkout-grid .profile-panel:last-child h2 { margin-bottom: 12px; }
        .checkout-summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: .93rem; }
        .checkout-summary-row:last-child { border-bottom: none; }
        .checkout-total { display: flex; justify-content: space-between; padding: 14px 0 0; font-size: 1.1rem; font-weight: 800; color: #0f172a; }

        /* ─── Flash notices ─── */
        .flash          { padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: .93rem; font-weight: 600; }
        .flash-success  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .flash-error    { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .notice         { color: #92400e; background: #fffbeb; padding: 12px 16px; border-radius: 10px; border: 1px solid #fde68a; }

        /* ─── Cart badge ─── */
        .cart-icon-wrap { position: relative; display: inline-flex; }
        .cart-badge      { position: absolute; top: -6px; right: -6px; min-width: 18px; height: 18px; padding: 0 4px; display: flex; align-items: center; justify-content: center; border-radius: 99px; background: #db2777; color: #fff; font-size: 10px; font-weight: 800; line-height: 1; }

        /* ─── Responsive ─── */
        @media (max-width: 900px) {
            .category-grid, .product-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .checkout-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .storefront-hero  { padding: 32px 24px; }
            .category-grid, .product-grid { grid-template-columns: 1fr; }
            .catalog-filter > * { width: 100%; }
            .profile-panel dl  { grid-template-columns: 1fr; gap: 4px; }
            .profile-tabs a    { padding: 8px 12px; font-size: .82rem; }
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
                <img src="<?= BASE_ASSETS_UPLOADS ?>logo.jpg" alt="TAN & TUAN CLOTHING" style="height: 42px; object-fit: contain; border-radius: 8px;">
                <span style="font-weight: 800; font-size: 17px; letter-spacing: -0.4px;">
                    TAN & TUAN
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
                        <span>
                            Tài khoản
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        class="<?= menuActive(['admin-products'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=admin-products"
                    >
                        <span>
                            Sản phẩm
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        class="<?= menuActive(['admin-categories'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=admin-categories"
                    >
                        <span>
                            Danh mục
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        class="<?= menuActive(['admin-suppliers', 'admin-supplier-detail'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=admin-suppliers"
                    >
                        <span>
                            Nơi nhập hàng
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        class="<?= menuActive(['admin-orders'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=admin-orders"
                    >
                        <span>
                            Đơn hàng
                        </span>
                    </a>
                </li>

                <li>
                    <a
                        class="<?= menuActive(['admin-reviews'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=admin-reviews"
                    >
                        <span>
                            Đánh giá
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
                    <img src="<?= BASE_ASSETS_UPLOADS ?>logo.jpg" alt="TAN & TUAN CLOTHING" style="height: 48px; object-fit: contain; vertical-align: middle; border-radius: 6px;">
                    <span class="brand-text" style="font-weight: 800; color: #0f172a; font-size: 20px; letter-spacing: -0.5px;">
                        TAN & TUAN
                    </span>
                </a>

                <nav
                    class="user-nav"
                    id="userNav"
                >
                    <a
                        class="<?= menuActive(['/', 'home', 'user-dashboard'], $currentAction) ?>"
                        href="<?= BASE_URL ?>"
                    >
                        Trang chủ
                    </a>

                    <a
                        class="<?= menuActive(['products', 'product-detail'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=products"
                    >
                        Sản phẩm
                    </a>

                    <a
                        class="<?= menuActive(['categories'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=categories"
                    >
                        Danh mục
                    </a>

                    <a
                        class="<?= menuActive(['orders', 'order-detail'], $currentAction) ?>"
                        href="<?= BASE_URL ?>?action=orders"
                    >
                        Đơn hàng của tôi
                    </a>
                </nav>

                <div class="user-actions">

                    <?php
                        $cartCount = 0;
                        if (!empty($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $ci) {
                                $cartCount += (int)($ci['quantity'] ?? 0);
                            }
                        }
                    ?>
                    <a
                        class="icon-button cart-icon-wrap"
                        href="<?= BASE_URL ?>?action=cart"
                        title="Giỏ hàng"
                        aria-label="Giỏ hàng"
                        id="cartNavBtn"
                    >
                        🛒
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-badge" id="cartBadge">
                                <?= $cartCount > 99 ? '99+' : $cartCount ?>
                            </span>
                        <?php endif; ?>
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

                            <a href="<?= BASE_URL ?>?action=profile">
                                <span>👤</span>
                                Thông tin cá nhân
                            </a>

                            <a href="<?= BASE_URL ?>?action=change-password">
                                <span>🔑</span>
                                Đổi mật khẩu
                            </a>

                            <a href="<?= BASE_URL ?>?action=orders">
                                <span>📦</span>
                                Đơn hàng của tôi
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
                    <div class="footer-brand" style="display:flex;align-items:center;gap:12px">
                        <img src="<?= BASE_ASSETS_UPLOADS ?>logo.jpg" alt="TAN & TUAN CLOTHING" style="height: 52px; object-fit: contain; background: #fff; padding: 4px 8px; border-radius: 10px; vertical-align: middle;">
                        <span style="font-weight: 800; font-size: 20px; color: #ffffff; letter-spacing: -0.5px;">
                            TAN & TUAN
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
    /* ── Mobile menu ── */
    const userNav          = document.getElementById('userNav');
    const mobileMenuButton = document.getElementById('mobileMenuButton');
    if (userNav && mobileMenuButton) {
        mobileMenuButton.addEventListener('click', () => userNav.classList.toggle('open'));
    }

    /* ── Account dropdown ── */
    const userAccount  = document.getElementById('userAccount');
    const accountButton= document.getElementById('accountButton');
    if (userAccount && accountButton) {
        accountButton.addEventListener('click', (e) => {
            e.stopPropagation();
            userAccount.classList.toggle('open');
        });
        document.addEventListener('click', () => userAccount.classList.remove('open'));
    }

    /* ── Footer links ── */
    const footerLinks = document.querySelectorAll('.footer-column a[href="javascript:void(0)"]');
    const footerDests = [
        '<?= BASE_URL ?>?action=products',
        '<?= BASE_URL ?>?action=categories',
        '<?= BASE_URL ?>?action=cart',
        '<?= BASE_URL ?>?action=profile',
        '<?= BASE_URL ?>?action=orders'
    ];
    footerLinks.forEach((link, i) => { if (footerDests[i]) link.href = footerDests[i]; });

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
