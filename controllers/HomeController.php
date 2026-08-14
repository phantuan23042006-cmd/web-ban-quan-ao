<?php
class HomeController
{
    private Product $productModel;
    private Category $categoryModel;
    private Review $reviewModel;
    private Order $orderModel;
    private User $userModel;

    public function __construct()
    {
        $this->productModel  = new Product();
        $this->categoryModel = new Category();
        $this->reviewModel   = new Review();
        $this->orderModel    = new Order();
        $this->userModel     = new User();
    }
    private function requireLogin(): void
    {
        if (empty($_SESSION['user'])) {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }

        $userId = (int) ($_SESSION['user']['id'] ?? 0);
        $userExists = $this->userModel->findById($userId);
        if (!$userExists || ($userExists['status'] ?? '') !== 'active') {
            unset($_SESSION['user']);
            $_SESSION['login_errors'] = ['general' => 'Phiên đăng nhập không còn hiệu lực. Vui lòng đăng nhập lại.'];
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }
    private function rejectAdmin(): void { if (($_SESSION['user']['role'] ?? '') === 'admin') { header('Location: ' . BASE_URL . '?action=admin-dashboard'); exit; } }
    public function index(): void { $this->requireLogin(); $this->rejectAdmin(); $title='Trang chủ'; $view='user/home'; $layout='user'; $categories=$this->categoryModel->getAll(); $featuredProducts=$this->productModel->getCatalog(['sort'=>'rating'],4); $newProducts=$this->productModel->getCatalog([],8); require PATH_VIEW_MAIN; }
    public function products(): void { $this->requireLogin(); $this->rejectAdmin(); $filters=['keyword'=>trim($_GET['keyword']??''),'danh_muc_id'=>(int)($_GET['category']??$_GET['danh_muc_id']??0),'min_price'=>$_GET['min_price']??'','max_price'=>$_GET['max_price']??'','sort'=>$_GET['sort']??'']; $title='Sản phẩm'; $view='user/products'; $layout='user'; $products=$this->productModel->getCatalog($filters); $categories=$this->categoryModel->getAll(); require PATH_VIEW_MAIN; }
    public function categories(): void { $this->requireLogin(); $this->rejectAdmin(); $title='Danh mục'; $view='user/categories'; $layout='user'; $categories=$this->categoryModel->getAll(); require PATH_VIEW_MAIN; }
    public function productDetail(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        $productId = (int) ($_GET['id'] ?? 0);
        $product   = $this->productModel->findById($productId);

        if (!$product) {
            http_response_code(404);
            $title  = 'Không tìm thấy sản phẩm';
            $view   = 'errors/404';
            $layout = 'user';
            require PATH_VIEW_MAIN;
            return;
        }

        $userId     = (int) ($_SESSION['user']['id'] ?? 0);
        $reviews    = $this->reviewModel->getByProductId($productId);
        $ratingInfo = $this->reviewModel->getAverageRating($productId);

        $myReview = null;
        foreach ($reviews as $review) {
            if ((int) $review['user_id'] === $userId) {
                $myReview = $review;
                break;
            }
        }

        // Chỉ cho phép đánh giá nếu đã mua sản phẩm và đơn hàng có trạng thái 'completed'
        $canReview = $this->orderModel->canReview($userId, $productId);

        $title  = $product['name'];
        $view   = 'user/product_detail';
        $layout = 'user';

        require PATH_VIEW_MAIN;
    }

    public function storeReview(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        $productId = (int) ($_POST['san_pham_id'] ?? 0);
        $userId    = (int) ($_SESSION['user']['id'] ?? 0);

        try {
            if (!csrf_validate($_POST['csrf_token'] ?? null)) {
                throw new RuntimeException('Yêu cầu không hợp lệ (CSRF).');
            }

            if (!$this->orderModel->canReview($userId, $productId)) {
                throw new RuntimeException('Bạn chỉ có thể gửi đánh giá sau khi đã mua sản phẩm và đơn hàng đã ở trạng thái Hoàn thành.');
            }

            $old = $this->reviewModel->findByUserProduct($userId, $productId);
            if ($old) {
                $this->reviewModel->update($old['id'], $userId, $_POST);
            } else {
                $this->reviewModel->create([
                    'san_pham_id' => $productId,
                    'user_id'     => $userId,
                    'so_sao'      => $_POST['so_sao'] ?? 5,
                    'noi_dung'    => $_POST['noi_dung'] ?? ''
                ]);
            }
            $_SESSION['success_message'] = 'Đã lưu đánh giá sản phẩm thành công.';
        } catch (Throwable $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=product-detail&id=' . $productId);
        exit;
    }

    public function updateReview(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        $productId = (int) ($_POST['san_pham_id'] ?? 0);
        $userId    = (int) ($_SESSION['user']['id'] ?? 0);
        $reviewId  = (int) ($_POST['id'] ?? 0);

        try {
            if (!csrf_validate($_POST['csrf_token'] ?? null)) {
                throw new RuntimeException('Yêu cầu không hợp lệ.');
            }
            if (!$this->orderModel->canReview($userId, $productId)) {
                throw new RuntimeException('Bạn không có quyền sửa đánh giá này.');
            }
            $this->reviewModel->update($reviewId, $userId, $_POST);
            $_SESSION['success_message'] = 'Đã cập nhật đánh giá thành công.';
        } catch (Throwable $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=product-detail&id=' . $productId);
        exit;
    }

    public function deleteReview(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        $productId = (int) ($_GET['san_pham_id'] ?? 0);
        $userId    = (int) ($_SESSION['user']['id'] ?? 0);
        $reviewId  = (int) ($_GET['id'] ?? 0);

        try {
            $this->reviewModel->delete($reviewId, $userId);
            $_SESSION['success_message'] = 'Đã xóa đánh giá thành công.';
        } catch (Throwable $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=product-detail&id=' . $productId);
        exit;
    }
    public function cart(): void { $this->requireLogin();$this->rejectAdmin();$warnings=[];$cartModel=new Cart();$cartItems=$cartModel->getItems($warnings);$totalQuantity=$cartModel->getTotalQuantity();$totalAmount=array_sum(array_column($cartItems,'subtotal'));$title='Giỏ hàng';$view='user/cart';$layout='user';require PATH_VIEW_MAIN; }
    public function addToCart(): void { $this->requireLogin();$productId=(int)($_POST['san_pham_id']??0);try{$result=(new Cart())->add($productId,(int)($_POST['variant_id']??0),(int)($_POST['quantity']??1));$_SESSION['success_message']='Đã thêm '.$result['product_name'].' vào giỏ hàng.';}catch(Throwable $e){$_SESSION['error_message']=$e->getMessage();}header('Location: '.BASE_URL.'?action=product-detail&id='.$productId);exit; }
    public function updateCart(): void { $this->requireLogin();try{(new Cart())->updateQuantity((int)($_POST['variant_id']??$_GET['variant_id']??0),(int)($_POST['quantity']??$_GET['quantity']??0));}catch(Throwable $e){$_SESSION['error_message']=$e->getMessage();}header('Location: '.BASE_URL.'?action=cart');exit; }
    public function removeFromCart(): void { $this->requireLogin();(new Cart())->remove((int)($_POST['variant_id']??$_GET['variant_id']??0));header('Location: '.BASE_URL.'?action=cart');exit; }
    public function clearCart(): void { $this->requireLogin();(new Cart())->clear();header('Location: '.BASE_URL.'?action=cart');exit; }
    public function checkout(): void { $this->requireLogin();$this->rejectAdmin();$warnings=[];$cartItems=(new Cart())->getItems($warnings);$totalAmount=array_sum(array_column($cartItems,'subtotal'));$user=$_SESSION['user'];$title='Thanh toán';$view='user/checkout';$layout='user';require PATH_VIEW_MAIN; }
    public function placeOrder(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['error_message'] = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }

        $recipient = [
            'name'           => trim($_POST['recipient_name'] ?? ''),
            'phone'          => trim($_POST['recipient_phone'] ?? ''),
            'address'        => trim($_POST['recipient_address'] ?? ''),
            'note'           => trim($_POST['note'] ?? ''),
            'payment_method' => trim($_POST['payment_method'] ?? 'cod')
        ];

        if (!$recipient['name'] || !$recipient['phone'] || !$recipient['address']) {
            $_SESSION['error_message'] = 'Vui lòng nhập đủ thông tin nhận hàng.';
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }

        $userId = (int) ($_SESSION['user']['id'] ?? 0);

        try {
            $warnings = [];
            $cart = new Cart();
            $items = $cart->getItems($warnings);

            if (empty($items)) {
                $_SESSION['error_message'] = 'Giỏ hàng đang trống.';
                header('Location: ' . BASE_URL . '?action=cart');
                exit;
            }

            $order = $this->orderModel->createFromCart($userId, $recipient, $items);
            $cart->clear();

            if (($recipient['payment_method'] ?? 'cod') !== 'cod') {
                $_SESSION['success_message'] = 'Đặt hàng thành công! Vui lòng tiến hành thanh toán bên dưới.';
            } else {
                $_SESSION['success_message'] = 'Đặt hàng thành công!';
            }
            header('Location: ' . BASE_URL . '?action=order-detail&id=' . $order['id']);
            exit;

        } catch (Throwable $e) {
            $_SESSION['error_message'] = 'Đặt hàng thất bại: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '?action=checkout');
            exit;
        }
    }

    public function confirmPayment(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        $userId  = (int) ($_SESSION['user']['id'] ?? 0);
        $orderId = (int) ($_POST['order_id'] ?? 0);

        if (!csrf_validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['error_message'] = 'Yêu cầu không hợp lệ (CSRF).';
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $order = $this->orderModel->findForUser($orderId, $userId);
        if ($order) {
            $this->orderModel->updatePaymentStatus($orderId, 'paid');
            $_SESSION['success_message'] = 'Xác nhận thanh toán thành công! Hệ thống đang chờ Admin duyệt đơn.';
        }

        header('Location: ' . BASE_URL . '?action=order-detail&id=' . $orderId);
        exit;
    }
    public function orders(): void { $this->requireLogin();$this->rejectAdmin();$orders=$this->orderModel->getByUser((int)$_SESSION['user']['id']);$title='Đơn hàng của tôi';$view='user/orders';$layout='user';require PATH_VIEW_MAIN; }
    public function orderDetail(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        $userId  = (int) ($_SESSION['user']['id'] ?? 0);
        $orderId = (int) ($_GET['id'] ?? 0);
        $order   = $this->orderModel->findForUser($orderId, $userId);

        if (!$order) {
            http_response_code(403);
            $title  = 'Không có quyền';
            $view   = 'errors/403';
            $layout = 'user';
            require PATH_VIEW_MAIN;
            return;
        }

        // Nếu đơn hàng đã hoàn thành, gắn thông tin đánh giá sẵn có (nếu có) cho từng sản phẩm
        if (($order['status'] ?? '') === 'completed') {
            foreach ($order['items'] as &$item) {
                $item['review'] = $this->reviewModel->findByUserProduct($userId, (int) $item['san_pham_id']);
            }
            unset($item);
        }

        $title  = 'Chi tiết đơn hàng ' . ($order['order_code'] ?? ('#' . $order['id']));
        $view   = 'user/order_detail';
        $layout = 'user';

        require PATH_VIEW_MAIN;
    }
    public function profile(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();
        $user  = $_SESSION['user'];
        $title = 'Hồ sơ của tôi';
        $view  = 'user/profile';
        $layout= 'user';
        require PATH_VIEW_MAIN;
    }

    /**
     * Xử lý cập nhật hồ sơ cá nhân.
     */
    public function updateProfile(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        if (!csrf_validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['profile_error'] = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        $userId  = (int) $_SESSION['user']['id'];
        $fullName= trim($_POST['full_name'] ?? '');
        $phone   = trim($_POST['phone']     ?? '');
        $address = trim($_POST['address']   ?? '');

        // Validate
        if ($fullName === '') {
            $_SESSION['profile_error'] = 'Vui lòng nhập họ và tên.';
            $_SESSION['profile_old']   = compact('full_name', 'phone', 'address');
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }
        if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 100) {
            $_SESSION['profile_error'] = 'Họ và tên phải từ 2 đến 100 ký tự.';
            $_SESSION['profile_old']   = ['full_name' => $fullName, 'phone' => $phone, 'address' => $address];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }
        if ($phone !== '' && !preg_match('/^(0|\+84)[0-9]{9,10}$/', $phone)) {
            $_SESSION['profile_error'] = 'Số điện thoại không đúng định dạng.';
            $_SESSION['profile_old']   = ['full_name' => $fullName, 'phone' => $phone, 'address' => $address];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }
        if ($phone !== '' && $this->userModel->phoneExists($phone, $userId)) {
            $_SESSION['profile_error'] = 'Số điện thoại này đã được sử dụng bởi tài khoản khác.';
            $_SESSION['profile_old']   = ['full_name' => $fullName, 'phone' => $phone, 'address' => $address];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }
        if ($address !== '' && mb_strlen($address) > 255) {
            $_SESSION['profile_error'] = 'Địa chỉ không được vượt quá 255 ký tự.';
            $_SESSION['profile_old']   = ['full_name' => $fullName, 'phone' => $phone, 'address' => $address];
            header('Location: ' . BASE_URL . '?action=profile');
            exit;
        }

        try {
            $this->userModel->updateProfile($userId, [
                'full_name' => $fullName,
                'phone'     => $phone   !== '' ? $phone   : null,
                'address'   => $address !== '' ? $address : null,
            ]);

            // Cập nhật lại session
            $_SESSION['user']['full_name'] = $fullName;
            $_SESSION['user']['phone']     = $phone;
            $_SESSION['user']['address']   = $address;

            $_SESSION['profile_success'] = 'Cập nhật hồ sơ thành công.';
        } catch (Throwable $e) {
            $_SESSION['profile_error'] = 'Không thể cập nhật hồ sơ: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=profile');
        exit;
    }

    /**
     * Hiển thị trang đổi mật khẩu.
     */
    public function showChangePassword(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();
        $title  = 'Đổi mật khẩu';
        $view   = 'user/change_password';
        $layout = 'user';
        require PATH_VIEW_MAIN;
    }

    /**
     * Xử lý đổi mật khẩu.
     */
    public function changePassword(): void
    {
        $this->requireLogin();
        $this->rejectAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }

        if (!csrf_validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['pwd_error'] = 'Yêu cầu không hợp lệ. Vui lòng thử lại.';
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }

        $userId          = (int) $_SESSION['user']['id'];
        $currentPassword = $_POST['current_password']      ?? '';
        $newPassword     = $_POST['new_password']          ?? '';
        $confirmation    = $_POST['new_password_confirmation'] ?? '';

        // Lấy hash mật khẩu hiện tại từ DB
        $userDb = $this->userModel->findByEmail($_SESSION['user']['email']);
        if (!$userDb || !password_verify($currentPassword, $userDb['password'])) {
            $_SESSION['pwd_error'] = 'Mật khẩu hiện tại không chính xác.';
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }

        if (strlen($newPassword) < 6) {
            $_SESSION['pwd_error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }
        if (strlen($newPassword) > 72) {
            $_SESSION['pwd_error'] = 'Mật khẩu mới không được vượt quá 72 ký tự.';
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }
        if ($newPassword !== $confirmation) {
            $_SESSION['pwd_error'] = 'Mật khẩu xác nhận không khớp.';
            header('Location: ' . BASE_URL . '?action=change-password');
            exit;
        }

        try {
            $this->userModel->updatePassword($userId, $newPassword);
            $_SESSION['pwd_success'] = 'Đổi mật khẩu thành công. Vui lòng đăng nhập lại nếu cần.';
        } catch (Throwable $e) {
            $_SESSION['pwd_error'] = 'Không thể đổi mật khẩu: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=change-password');
        exit;
    }

    public function cancelOrder(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        if (!csrf_validate($_POST['csrf_token'] ?? null)) {
            $_SESSION['error_message'] = 'Yêu cầu không hợp lệ (CSRF token).';
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        $orderId = (int) ($_POST['order_id'] ?? 0);
        $userId  = (int) ($_SESSION['user']['id'] ?? 0);

        if ($orderId <= 0 || $userId <= 0) {
            $_SESSION['error_message'] = 'Đơn hàng không hợp lệ.';
            header('Location: ' . BASE_URL . '?action=orders');
            exit;
        }

        try {
            $this->orderModel->cancelOrderForUser($orderId, $userId);
            $_SESSION['success_message'] = 'Hủy đơn hàng thành công!';
        } catch (Throwable $e) {
            $_SESSION['error_message'] = $e->getMessage();
        }

        $returnUrl = $_POST['return_url'] ?? (BASE_URL . '?action=order-detail&id=' . $orderId);
        header('Location: ' . $returnUrl);
        exit;
    }
}
