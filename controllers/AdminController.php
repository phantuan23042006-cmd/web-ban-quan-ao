<?php

class AdminController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->requireAdmin();
    }

    public function index()
    {
        $dashboardMetrics = $this->userModel->getDashboardMetrics();
        $stats = $dashboardMetrics['stats'] ?? [];
        $products = $this->userModel->getDashboardProducts();
        $orders = $this->userModel->getDashboardOrders();

        if ($products === []) {
            $products = $this->getSampleProducts();
        }

        if ($orders === []) {
            $orders = $this->getSampleOrders();
        }

        $categories = $this->userModel->getDashboardCategories();

        if ($categories === []) {
            $categories = $this->getCategoryBreakdown($products);
        }

        $reviews = $this->getDashboardReviews();
        $recentActivity = $this->getRecentActivity($orders);

        $pendingOrders = (int) ($dashboardMetrics['pending_orders'] ?? 0);
        $completedOrders = (int) ($dashboardMetrics['completed_orders'] ?? 0);
        $totalRevenue = (float) ($dashboardMetrics['total_revenue'] ?? 0);

        $overview = [
            'total_products' => (int) ($dashboardMetrics['total_products'] ?? count($products)),
            'total_orders' => (int) ($dashboardMetrics['total_orders'] ?? count($orders)),
            'pending_orders' => $pendingOrders > 0 ? $pendingOrders : 0,
            'completed_orders' => $completedOrders > 0 ? $completedOrders : 0,
            'total_revenue' => $totalRevenue,
        ];

        $categoryCount = count($categories);
        $reviewAverage = !empty($reviews)
            ? round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1)
            : 0;
        $reviewCount = count($reviews);

        $title = 'Dashboard Admin';
        $view = 'admin/dashboard';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    public function products()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $category = trim($_GET['filter_category'] ?? '');
        $status = trim($_GET['filter_status'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $allProducts = $this->getSampleProducts();
        $products = [];

        foreach ($allProducts as $product) {
            if ($keyword !== '' &&
                stripos($product['name'], $keyword) === false &&
                stripos($product['category'], $keyword) === false
            ) {
                continue;
            }

            if ($category !== '' && $product['category'] !== $category) {
                continue;
            }

            if ($status !== '' && $product['status'] !== $status) {
                continue;
            }

            $products[] = $product;
        }

        $totalItems = count($products);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $products = array_slice(
            $products,
            ($page - 1) * $perPage,
            $perPage
        );

        $categories = array_unique(
            array_column($allProducts, 'category')
        );

        $title = 'Quản lý sản phẩm';
        $view = 'admin/products';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    public function orders()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $status = trim($_GET['filter_status'] ?? '');
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $allOrders = $this->getSampleOrders();
        $orders = [];

        foreach ($allOrders as $order) {
            if ($keyword !== '' &&
                stripos($order['id'], $keyword) === false &&
                stripos($order['customer'], $keyword) === false
            ) {
                continue;
            }

            if ($status !== '' && $order['status'] !== $status) {
                continue;
            }

            $orders[] = $order;
        }

        $totalItems = count($orders);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $orders = array_slice(
            $orders,
            ($page - 1) * $perPage,
            $perPage
        );

        $title = 'Quản lý đơn hàng';
        $view = 'admin/orders';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    public function categories()
    {
        $title = 'Quản lý danh mục';
        $view = 'admin/categories';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    public function reviews()
    {
        $title = 'Quản lý đánh giá';
        $view = 'admin/reviews';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    public function statistics()
    {
        $dashboardMetrics = $this->userModel->getDashboardMetrics();
        $stats = $dashboardMetrics['stats'] ?? [];
        $totalProducts = (int) ($dashboardMetrics['total_products'] ?? 0);
        $totalOrders = (int) ($dashboardMetrics['total_orders'] ?? 0);
        $totalRevenue = (float) ($dashboardMetrics['total_revenue'] ?? 0);
        $pendingOrders = (int) ($dashboardMetrics['pending_orders'] ?? 0);
        $completedOrders = (int) ($dashboardMetrics['completed_orders'] ?? 0);

        $title = 'Thống kê bán hàng';
        $view = 'admin/statistics';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    private function getDashboardStats(): array
    {
        try {
            $stats = $this->userModel->getUserStatistics();
        } catch (Throwable $exception) {
            $stats = [];
        }

        return [
            'total_users' => (int) ($stats['total_users'] ?? 0),
            'active_users' => (int) ($stats['active_users'] ?? 0),
            'blocked_users' => (int) ($stats['blocked_users'] ?? 0),
            'total_admins' => (int) ($stats['total_admins'] ?? 0),
            'total_members' => (int) ($stats['total_members'] ?? 0),
        ];
    }

    private function getCategoryBreakdown(array $products): array
    {
        $grouped = [];

        foreach ($products as $product) {
            $category = trim((string) ($product['category'] ?? 'Khác'));
            $category = $category !== '' ? $category : 'Khác';
            $grouped[$category] = ($grouped[$category] ?? 0) + 1;
        }

        if ($grouped === []) {
            return [];
        }

        $maxCount = max(array_values($grouped));
        $result = [];

        foreach ($grouped as $name => $count) {
            $result[] = [
                'name' => $name,
                'count' => $count,
                'percent' => $maxCount > 0 ? (int) round(($count / $maxCount) * 100) : 0,
            ];
        }

        usort($result, static function ($first, $second) {
            return $second['count'] <=> $first['count'];
        });

        return $result;
    }

    private function getDashboardReviews(): array
    {
        $rows = $this->userModel->getDashboardReviewRows();

        if (!empty($rows)) {
            $reviews = [];

            foreach ($rows as $row) {
                $reviews[] = [
                    'customer' => $this->userModel->getFirstValue($row, ['customer', 'customer_name', 'full_name', 'author', 'name']),
                    'rating' => (int) $this->userModel->getFirstValue($row, ['rating', 'rate', 'stars']),
                    'comment' => $this->userModel->getFirstValue($row, ['comment', 'content', 'review', 'message']),
                ];
            }

            $reviews = array_filter($reviews, static function ($review) {
                return !empty($review['comment']) || !empty($review['customer']);
            });

            if (!empty($reviews)) {
                return $reviews;
            }
        }

        return [
            [
                'customer' => 'Nguyễn Văn A',
                'rating' => 5,
                'comment' => 'Giao hàng nhanh và chất lượng sản phẩm rất tốt.',
            ],
            [
                'customer' => 'Trần Thị B',
                'rating' => 4,
                'comment' => 'Mẫu mã đẹp, đổi trả thuận tiện.',
            ],
            [
                'customer' => 'Lê Minh C',
                'rating' => 5,
                'comment' => 'Nhân viên hỗ trợ rất nhiệt tình.',
            ],
        ];
    }

    private function getRecentActivity(array $orders): array
    {
        $activity = [];

        foreach ($orders as $order) {
            $status = (string) ($order['status'] ?? 'Đang cập nhật');
            $activity[] = [
                'title' => 'Đơn hàng ' . ($order['id'] ?? 'N/A') . ' - ' . $status,
                'meta' => 'Khách hàng ' . ($order['customer'] ?? 'N/A'),
                'time' => (string) ($order['date'] ?? ''),
            ];
        }

        return array_slice($activity, 0, 4);
    }

    private function getSampleProducts()
    {
        return [
            [
                'name' => 'Áo khoác denim oversize',
                'category' => 'Áo khoác',
                'price' => '590.000đ',
                'stock' => '25',
                'status' => 'active',
                'created_at' => '01/08/2026',
            ],
            [
                'name' => 'Áo thun basic cổ tròn',
                'category' => 'Áo thun',
                'price' => '249.000đ',
                'stock' => '40',
                'status' => 'active',
                'created_at' => '03/08/2026',
            ],
            [
                'name' => 'Váy nữ dáng dài',
                'category' => 'Váy',
                'price' => '459.000đ',
                'stock' => '18',
                'status' => 'inactive',
                'created_at' => '28/07/2026',
            ],
            [
                'name' => 'Quần jean ống rộng',
                'category' => 'Quần',
                'price' => '389.000đ',
                'stock' => '32',
                'status' => 'active',
                'created_at' => '06/08/2026',
            ],
            [
                'name' => 'Túi tote canvas',
                'category' => 'Phụ kiện',
                'price' => '219.000đ',
                'stock' => '12',
                'status' => 'active',
                'created_at' => '10/08/2026',
            ],
        ];
    }

    private function getSampleOrders()
    {
        return [
            [
                'id' => 'DH-1001',
                'customer' => 'Nguyễn Văn A',
                'date' => '01/08/2026',
                'status' => 'Đã giao',
                'total' => '598.000đ',
            ],
            [
                'id' => 'DH-1002',
                'customer' => 'Trần Thị B',
                'date' => '05/08/2026',
                'status' => 'Đang giao',
                'total' => '249.000đ',
            ],
            [
                'id' => 'DH-1003',
                'customer' => 'Lê Minh C',
                'date' => '08/08/2026',
                'status' => 'Chờ xử lý',
                'total' => '1.290.000đ',
            ],
            [
                'id' => 'DH-1004',
                'customer' => 'Phạm Thị D',
                'date' => '10/08/2026',
                'status' => 'Bị hủy',
                'total' => '329.000đ',
            ],
        ];
    }

    public function users()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $role = trim($_GET['filter_role'] ?? '');
        $status = trim($_GET['filter_status'] ?? '');

        if (!in_array($role, ['', 'user', 'admin'], true)) {
            $role = '';
        }

        if (!in_array($status, ['', 'active', 'blocked'], true)) {
            $status = '';
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 10;

        $filters = [
            'keyword' => $keyword,
            'role' => $role,
            'status' => $status,
        ];

        $totalItems = $this->userModel->countUsersForAdmin($filters);
        $totalPages = max(1, (int) ceil($totalItems / $perPage));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $users = $this->userModel->getUsersForAdmin($filters, $page, $perPage);
        $stats = $this->userModel->getUserStatistics();

        $pagination = [
            'page' => $page,
            'per_page' => $perPage,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'from' => $totalItems > 0 ? (($page - 1) * $perPage) + 1 : 0,
            'to' => min($page * $perPage, $totalItems),
        ];

        $title = 'Quản lý tài khoản';
        $view = 'admin/users';
        $layout = 'admin';

        $csrfToken = $this->generateCsrfToken();
        $successMessage = $_SESSION['admin_success'] ?? null;
        $errorMessage = $_SESSION['admin_error'] ?? null;
        $resetPasswordInfo = $_SESSION['admin_reset_password'] ?? null;

        unset(
            $_SESSION['admin_success'],
            $_SESSION['admin_error'],
            $_SESSION['admin_reset_password']
        );

        require PATH_VIEW_MAIN;
    }

    public function updateUserStatus()
    {
        $this->requirePostRequest();
        $this->requireValidCsrf();

        $userId = (int) ($_POST['user_id'] ?? 0);
        $status = trim($_POST['status'] ?? '');

        if ($userId <= 0 || !in_array($status, ['active', 'blocked'], true)) {
            $_SESSION['admin_error'] = 'Thông tin cập nhật trạng thái không hợp lệ.';
            $this->redirectUsersWithFilters();
        }

        $currentAdminId = (int) ($_SESSION['user']['id'] ?? 0);

        if ($userId === $currentAdminId && $status === 'blocked') {
            $_SESSION['admin_error'] = 'Bạn không thể tự khóa tài khoản đang đăng nhập.';
            $this->redirectUsersWithFilters();
        }

        $targetUser = $this->userModel->findById($userId);

        if (!$targetUser) {
            $_SESSION['admin_error'] = 'Không tìm thấy tài khoản cần cập nhật.';
            $this->redirectUsersWithFilters();
        }

        if (($targetUser['status'] ?? '') === $status) {
            $_SESSION['admin_error'] = 'Tài khoản đã ở trạng thái này.';
            $this->redirectUsersWithFilters();
        }

        try {
            $updated = $this->userModel->updateStatus($userId, $status);

            if (!$updated) {
                throw new Exception('Không thể cập nhật trạng thái.');
            }

            $_SESSION['admin_success'] = $status === 'active'
                ? 'Đã mở khóa tài khoản thành công.'
                : 'Đã khóa tài khoản thành công.';
        } catch (Throwable $exception) {
            $_SESSION['admin_error'] = 'Không thể cập nhật trạng thái tài khoản.';
        }

        $this->redirectUsersWithFilters();
    }

    public function updateUserRole()
    {
        $this->requirePostRequest();
        $this->requireValidCsrf();

        $userId = (int) ($_POST['user_id'] ?? 0);
        $role = trim($_POST['role'] ?? '');

        if ($userId <= 0 || !in_array($role, ['user', 'admin'], true)) {
            $_SESSION['admin_error'] = 'Thông tin phân quyền không hợp lệ.';
            $this->redirectUsersWithFilters();
        }

        $currentAdminId = (int) ($_SESSION['user']['id'] ?? 0);

        if ($userId === $currentAdminId && $role === 'user') {
            $_SESSION['admin_error'] = 'Bạn không thể tự hạ quyền tài khoản đang đăng nhập.';
            $this->redirectUsersWithFilters();
        }

        $targetUser = $this->userModel->findById($userId);

        if (!$targetUser) {
            $_SESSION['admin_error'] = 'Không tìm thấy tài khoản cần phân quyền.';
            $this->redirectUsersWithFilters();
        }

        if (($targetUser['role'] ?? '') === $role) {
            $_SESSION['admin_error'] = 'Tài khoản đã có quyền này.';
            $this->redirectUsersWithFilters();
        }

        try {
            $updated = $this->userModel->updateRole($userId, $role);

            if (!$updated) {
                throw new Exception('Không thể cập nhật quyền.');
            }

            $_SESSION['admin_success'] = $role === 'admin'
                ? 'Đã cấp quyền quản trị viên.'
                : 'Đã chuyển tài khoản về quyền khách hàng.';
        } catch (Throwable $exception) {
            $_SESSION['admin_error'] = 'Không thể cập nhật quyền tài khoản.';
        }

        $this->redirectUsersWithFilters();
    }

    public function resetUserPassword()
    {
        $this->requirePostRequest();
        $this->requireValidCsrf();

        $userId = (int) ($_POST['user_id'] ?? 0);

        if ($userId <= 0) {
            $_SESSION['admin_error'] = 'Tài khoản cần đặt lại mật khẩu không hợp lệ.';
            $this->redirectUsersWithFilters();
        }

        $targetUser = $this->userModel->findById($userId);

        if (!$targetUser) {
            $_SESSION['admin_error'] = 'Không tìm thấy tài khoản.';
            $this->redirectUsersWithFilters();
        }

        try {
            $newPassword = 'Fs@' . strtoupper(bin2hex(random_bytes(4)));
            $updated = $this->userModel->updatePassword($userId, $newPassword);

            if (!$updated) {
                throw new Exception('Không thể đặt lại mật khẩu.');
            }

            $_SESSION['admin_success'] = 'Đã đặt lại mật khẩu thành công.';
            $_SESSION['admin_reset_password'] = [
                'full_name' => $targetUser['full_name'],
                'email' => $targetUser['email'],
                'password' => $newPassword,
            ];
        } catch (Throwable $exception) {
            $_SESSION['admin_error'] = 'Không thể đặt lại mật khẩu tài khoản.';
        }

        $this->redirectUsersWithFilters();
    }

    private function requireAdmin()
    {
        if (empty($_SESSION['user'])) {
            $_SESSION['login_errors'] = [
                'general' => 'Vui lòng đăng nhập để tiếp tục.',
            ];

            $this->redirect('login');
        }

        if (($_SESSION['user']['role'] ?? '') !== 'admin') {
            http_response_code(403);

            $title = 'Không có quyền truy cập';
            $view = 'errors/403';
            $layout = 'user';

            require PATH_VIEW_MAIN;
            exit;
        }

        if (($_SESSION['user']['status'] ?? '') !== 'active') {
            $_SESSION = [];
            session_destroy();
            session_start();

            $_SESSION['login_errors'] = [
                'general' => 'Tài khoản của bạn đã bị khóa.',
            ];

            $this->redirect('login');
        }
    }

    private function requirePostRequest()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            $this->redirect('admin-users');
        }
    }

    private function requireValidCsrf()
    {
        $token = $_POST['csrf_token'] ?? '';

        if (!$this->validateCsrfToken($token)) {
            $_SESSION['admin_error'] = 'Phiên thao tác không hợp lệ. Vui lòng thử lại.';
            $this->redirectUsersWithFilters();
        }
    }

    private function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken($token)
    {
        if (empty($token) || empty($_SESSION['csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    private function redirectUsersWithFilters()
    {
        $params = ['action' => 'admin-users'];

        $keyword = trim($_POST['return_keyword'] ?? '');
        $role = trim($_POST['return_role'] ?? '');
        $status = trim($_POST['return_status'] ?? '');
        $page = max(1, (int) ($_POST['return_page'] ?? 1));

        if ($keyword !== '') {
            $params['keyword'] = $keyword;
        }

        if (in_array($role, ['user', 'admin'], true)) {
            $params['filter_role'] = $role;
        }

        if (in_array($status, ['active', 'blocked'], true)) {
            $params['filter_status'] = $status;
        }

        if ($page > 1) {
            $params['page'] = $page;
        }

        header('Location: ' . BASE_URL . '?' . http_build_query($params));
        exit;
    }

    private function redirect($action)
    {
        header('Location: ' . BASE_URL . '?action=' . urlencode($action));
        exit;
    }
}
