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

    public function cart()
{
    $this->requireLogin();

    if (($_SESSION['user']['role'] ?? '') === 'admin') {
        header('Location: ' . BASE_URL . '?action=admin-dashboard');
        exit;
    }

    $warnings = [];
    $cartModel = new Cart();
    $items = $cartModel->getItems($warnings);

    $totalQuantity = $cartModel->getTotalQuantity();
    $totalAmount = 0;

    foreach ($items as $item) {
        $totalAmount += (float) $item['subtotal'];
    }

    $title = 'Giỏ hàng';
    $view = 'cart';
    $layout = 'user';

    $successMessage = $_SESSION['success_message'] ?? null;
    $errorMessage = $_SESSION['error_message'] ?? null;

    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);

    require PATH_VIEW_MAIN;
}

public function addToCart()
{
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?action=products');
        exit;
    }

    $productId = (int) ($_POST['san_pham_id'] ?? 0);
    $variantId = (int) ($_POST['variant_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 1);

    try {
        $cartModel = new Cart();

        $result = $cartModel->add(
            $productId,
            $variantId,
            $quantity
        );

        $_SESSION['success_message'] =
            "Đã thêm {$result['product_name']} - Size {$result['size']} vào giỏ hàng.";
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }

    header(
        'Location: ' .
        BASE_URL .
        '?action=product-detail&id=' .
        $productId
    );
    exit;
}

public function updateCart()
{
    $this->requireLogin();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?action=cart');
        exit;
    }

    $variantId = (int) ($_POST['variant_id'] ?? 0);
    $quantity = (int) ($_POST['quantity'] ?? 0);

    try {
        $cartModel = new Cart();
        $cartModel->updateQuantity($variantId, $quantity);

        $_SESSION['success_message'] =
            'Đã cập nhật số lượng sản phẩm.';
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
    }

    header('Location: ' . BASE_URL . '?action=cart');
    exit;
}

public function removeFromCart()
{
    $this->requireLogin();

    $variantId = (int) ($_POST['variant_id'] ?? $_GET['variant_id'] ?? 0);

    $cartModel = new Cart();

    if ($cartModel->remove($variantId)) {
        $_SESSION['success_message'] =
            'Đã xóa sản phẩm khỏi giỏ hàng.';
    }

    header('Location: ' . BASE_URL . '?action=cart');
    exit;
}

public function clearCart()
{
    $this->requireLogin();

    $cartModel = new Cart();
    $cartModel->clear();

    $_SESSION['success_message'] =
        'Đã xóa toàn bộ giỏ hàng.';

    header('Location: ' . BASE_URL . '?action=cart');
    exit;
}
}