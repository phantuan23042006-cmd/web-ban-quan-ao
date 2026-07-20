<?php

$action = $_GET['action'] ?? '/';

$isLoggedIn = !empty($_SESSION['user']);
$currentRole = $_SESSION['user']['role'] ?? null;

/**
 * Chuyển hướng đến một action.
 */
function routeRedirect(string $action = ''): void
{
    if ($action === '' || $action === '/') {
        header('Location: ' . BASE_URL);
        exit;
    }

    header(
        'Location: ' .
        BASE_URL .
        '?action=' .
        urlencode($action)
    );

    exit;
}

/**
 * Kiểm tra đăng nhập.
 */
function routeRequireLogin(): void
{
    if (!empty($_SESSION['user'])) {
        return;
    }

    $_SESSION['login_errors'] = [
        'general' => 'Vui lòng đăng nhập để tiếp tục.',
    ];

    routeRedirect('login');
}

/**
 * Kiểm tra quyền quản trị viên.
 */
function routeRequireAdmin(): void
{
    routeRequireLogin();

    if (
        ($_SESSION['user']['role'] ?? '') === 'admin'
    ) {
        return;
    }

    http_response_code(403);

    $title = 'Không có quyền truy cập';
    $view = 'errors/403';
    $layout = 'user';

    require PATH_VIEW_MAIN;
    exit;
}

switch ($action) {
    /*
    |--------------------------------------------------------------------------
    | TRANG MẶC ĐỊNH
    |--------------------------------------------------------------------------
    | Chưa đăng nhập: hiển thị trang đăng nhập.
    | Admin: chuyển vào trang quản trị.
    | User: hiển thị trang chủ khách hàng.
    */
    case '/':
    case 'home':
        if (!$isLoggedIn) {
            (new AuthController())->showLogin();
            break;
        }

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->index();
        break;

    /*
    |--------------------------------------------------------------------------
    | ĐĂNG KÝ
    |--------------------------------------------------------------------------
    */
    case 'register':
        (new AuthController())->showRegister();
        break;

    case 'register-submit':
        (new AuthController())->register();
        break;

    /*
    |--------------------------------------------------------------------------
    | ĐĂNG NHẬP
    |--------------------------------------------------------------------------
    */
    case 'login':
        (new AuthController())->showLogin();
        break;

    case 'login-submit':
        (new AuthController())->login();
        break;

    /*
    |--------------------------------------------------------------------------
    | ĐĂNG XUẤT
    |--------------------------------------------------------------------------
    */
    case 'logout':
        routeRequireLogin();

        (new AuthController())->logout();
        break;

    /*
    |--------------------------------------------------------------------------
    | TRANG CHỦ KHÁCH HÀNG
    |--------------------------------------------------------------------------
    */
    case 'user-dashboard':
        routeRequireLogin();

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->index();
        break;

    /*
    |--------------------------------------------------------------------------
    | TRANG QUẢN TRỊ
    |--------------------------------------------------------------------------
    */
    case 'admin-dashboard':
        routeRequireAdmin();

        (new AdminController())->index();
        break;

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ TÀI KHOẢN
    |--------------------------------------------------------------------------
    */
    case 'admin-users':
        routeRequireAdmin();

        (new AdminController())->users();
        break;

    case 'admin-user-status':
        routeRequireAdmin();

        (new AdminController())->updateUserStatus();
        break;

    case 'admin-user-role':
        routeRequireAdmin();

        (new AdminController())->updateUserRole();
        break;

    case 'admin-user-reset-password':
        routeRequireAdmin();

        (new AdminController())->resetUserPassword();
        break;

    /*
    |--------------------------------------------------------------------------
    | TRANG KHÔNG TỒN TẠI
    |--------------------------------------------------------------------------
    */
    default:
        http_response_code(404);

        $title = 'Không tìm thấy trang';
        $view = 'errors/404';

        if (!$isLoggedIn) {
            $layout = 'auth';
        } elseif ($currentRole === 'admin') {
            $layout = 'admin';
        } else {
            $layout = 'user';
        }

        require PATH_VIEW_MAIN;
        break;
}