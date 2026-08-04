<?php

class HomeController
{
    private function requireLogin(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    public function products()
    {
        $this->requireLogin();

        $title = 'Sản phẩm';
        $view = 'products';
        $layout = 'user';
        $products = $this->sampleProducts();

        require PATH_VIEW_MAIN;
    }

    public function categories()
    {
        $this->requireLogin();

        $title = 'Danh mục';
        $view = 'categories';
        $layout = 'user';
        $categories = $this->sampleCategories();

        require PATH_VIEW_MAIN;
    }

    public function orders()
    {
        $this->requireLogin();

        $title = 'Đơn hàng của tôi';
        $view = 'orders';
        $layout = 'user';
        $orders = $this->sampleOrders();

        require PATH_VIEW_MAIN;
    }

    public function profile()
    {
        $this->requireLogin();

        $title = 'Thông tin tài khoản';
        $view = 'profile';
        $layout = 'user';
        $user = $_SESSION['user'] ?? [];

        require PATH_VIEW_MAIN;
    }

    public function index()
    {
        $this->requireLogin();

        /*
        |--------------------------------------------------------------------------
        | ADMIN KHÔNG DÙNG GIAO DIỆN USER
        |--------------------------------------------------------------------------
        */

        if (($_SESSION['user']['role'] ?? '') === 'admin') {
            header(
                'Location: ' .
                BASE_URL .
                '?action=admin-dashboard'
            );
            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | GIAO DIỆN TRANG CHỦ USER
        |--------------------------------------------------------------------------
        */

        $title = 'Trang chủ';
        $view = 'home';
        $layout = 'user';

        $currentUser = $_SESSION['user'];

        $successMessage =
            $_SESSION['success_message'] ?? null;

        unset($_SESSION['success_message']);

        require PATH_VIEW_MAIN;
    }

    private function sampleProducts(): array
    {
        return [
            [
                'name' => 'Áo khoác denim oversize',
                'category' => 'Áo khoác',
                'price' => '590.000đ',
                'badge' => 'Bán chạy',
                'emoji' => '🧥',
            ],
            [
                'name' => 'Áo thun basic cổ tròn',
                'category' => 'Áo thun',
                'price' => '249.000đ',
                'badge' => 'Mới',
                'emoji' => '👕',
            ],
            [
                'name' => 'Váy nữ dáng dài',
                'category' => 'Váy',
                'price' => '459.000đ',
                'badge' => 'Hot',
                'emoji' => '👗',
            ],
            [
                'name' => 'Quần jean ống rộng',
                'category' => 'Quần',
                'price' => '389.000đ',
                'badge' => 'Giảm giá',
                'emoji' => '👖',
            ],
        ];
    }

    private function sampleCategories(): array
    {
        return [
            ['name' => 'Thời trang nam', 'description' => 'Áo, quần và phụ kiện cho nam', 'emoji' => '👔'],
            ['name' => 'Thời trang nữ', 'description' => 'Váy, áo và các item nữ tinh tế', 'emoji' => '👗'],
            ['name' => 'Phụ kiện', 'description' => 'Túi, mũ và phụ kiện thời trang', 'emoji' => '👜'],
            ['name' => 'Hàng mới về', 'description' => 'Bộ sưu tập cập nhật mỗi tuần', 'emoji' => '✨'],
        ];
    }

    private function sampleOrders(): array
    {
        return [
            [
                'id' => 'DH-1001',
                'date' => '01/08/2026',
                'status' => 'Đã giao',
                'total' => '598.000đ',
            ],
            [
                'id' => 'DH-1002',
                'date' => '05/08/2026',
                'status' => 'Đang giao',
                'total' => '249.000đ',
            ],
        ];
    }
}