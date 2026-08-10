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

    case 'products':
        routeRequireLogin();

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->products();
        break;

    case 'product-detail':
        routeRequireLogin();

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->productDetail();
        break;

    case 'review-store':
        routeRequireLogin();

        (new HomeController())->storeReview();
        break;

    case 'review-update':
        routeRequireLogin();

        (new HomeController())->updateReview();
        break;

    case 'review-delete':
        routeRequireLogin();

        (new HomeController())->deleteReview();
        break;

    /*
    |--------------------------------------------------------------------------
    | GIỎ HÀNG (SHOPPING CART)
    |--------------------------------------------------------------------------
    */
    case 'cart':
        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->cart();
        break;

    case 'cart-add':
        (new HomeController())->addToCart();
        break;

    case 'cart-update':
        (new HomeController())->updateCart();
        break;

    case 'cart-remove':
        (new HomeController())->removeFromCart();
        break;

    case 'cart-clear':
        (new HomeController())->clearCart();
        break;

    case 'checkout':
        routeRequireLogin();
        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }
        (new HomeController())->checkout();
        break;

    case 'place-order':
        routeRequireLogin();
        (new HomeController())->placeOrder();
        break;

    case 'order-detail':
        routeRequireLogin();
        (new HomeController())->orderDetail();
        break;


    case 'categories':
        routeRequireLogin();

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->categories();
        break;

    case 'orders':
        routeRequireLogin();

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->orders();
        break;

    case 'profile':
        routeRequireLogin();

        if ($currentRole === 'admin') {
            routeRedirect('admin-dashboard');
        }

        (new HomeController())->profile();
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

    case 'admin-products':
        routeRequireAdmin();

        (new AdminController())->products();
        break;

    case 'admin-product-create':
        routeRequireAdmin();

        (new AdminController())->createProduct();
        break;

    case 'admin-product-store':
        routeRequireAdmin();

        (new AdminController())->storeProduct();
        break;

    case 'admin-product-detail':
        routeRequireAdmin();

        (new AdminController())->productDetail();
        break;

    case 'admin-product-edit':
        routeRequireAdmin();

        (new AdminController())->editProduct();
        break;

    case 'admin-product-update':
        routeRequireAdmin();

        (new AdminController())->updateProduct();
        break;

    case 'admin-product-delete':
        routeRequireAdmin();

        (new AdminController())->deleteProduct();
        break;

    case 'admin-variant-store':
        routeRequireAdmin();

        (new AdminController())->storeVariant();
        break;

    case 'admin-variant-update':
        routeRequireAdmin();

        (new AdminController())->updateVariant();
        break;

    case 'admin-variant-delete':
        routeRequireAdmin();

        (new AdminController())->deleteVariant();
        break;

    case 'admin-orders':
        routeRequireAdmin();

        (new AdminController())->orders();
        break;

    case 'admin-order-status':
        routeRequireAdmin();
        (new AdminController())->updateOrderStatus();
        break;


    case 'admin-categories':
        routeRequireAdmin();

        (new AdminController())->categories();
        break;

    case 'admin-category-store':
        routeRequireAdmin();

        (new AdminController())->storeCategory();
        break;

    case 'admin-category-update':
        routeRequireAdmin();

        (new AdminController())->updateCategory();
        break;

    case 'admin-category-delete':
        routeRequireAdmin();

        (new AdminController())->deleteCategory();
        break;

    case 'admin-suppliers':
        routeRequireAdmin();

        (new AdminController())->suppliers();
        break;

    case 'admin-supplier-detail':
        routeRequireAdmin();

        (new AdminController())->supplierDetail();
        break;

    case 'admin-supplier-store':
        routeRequireAdmin();

        (new AdminController())->storeSupplier();
        break;

    case 'admin-supplier-update':
        routeRequireAdmin();

        (new AdminController())->updateSupplier();
        break;

    case 'admin-supplier-delete':
        routeRequireAdmin();

        (new AdminController())->deleteSupplier();
        break;

    case 'admin-reviews':
        routeRequireAdmin();

        (new AdminController())->reviews();
        break;

    case 'admin-review-delete':
        routeRequireAdmin();

        (new AdminController())->deleteReview();
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
