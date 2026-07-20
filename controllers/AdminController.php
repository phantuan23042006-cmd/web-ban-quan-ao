<?php

class AdminController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->requireAdmin();
    }

    /**
     * Tạm thời chuyển dashboard sang trang quản lý tài khoản.
     */
    public function index()
    {
        $this->redirect('admin-users');
    }

    /**
     * Danh sách và tìm kiếm tài khoản.
     */
    public function users()
    {
        $keyword = trim(
            $_GET['keyword'] ?? ''
        );

        $role = trim(
            $_GET['filter_role'] ?? ''
        );

        $status = trim(
            $_GET['filter_status'] ?? ''
        );

        if (
            !in_array(
                $role,
                ['', 'user', 'admin'],
                true
            )
        ) {
            $role = '';
        }

        if (
            !in_array(
                $status,
                ['', 'active', 'blocked'],
                true
            )
        ) {
            $status = '';
        }

        $page = max(
            1,
            (int) ($_GET['page'] ?? 1)
        );

        $perPage = 10;

        $filters = [
            'keyword' => $keyword,
            'role' => $role,
            'status' => $status,
        ];

        $totalItems =
            $this->userModel
                ->countUsersForAdmin($filters);

        $totalPages = max(
            1,
            (int) ceil(
                $totalItems / $perPage
            )
        );

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $users =
            $this->userModel
                ->getUsersForAdmin(
                    $filters,
                    $page,
                    $perPage
                );

        $stats =
            $this->userModel
                ->getUserStatistics();

        $pagination = [
            'page' => $page,
            'per_page' => $perPage,
            'total_items' => $totalItems,
            'total_pages' => $totalPages,
            'from' => $totalItems > 0
                ? (($page - 1) * $perPage) + 1
                : 0,
            'to' => min(
                $page * $perPage,
                $totalItems
            ),
        ];

        $title = 'Quản lý tài khoản';
        $view = 'admin/users';
        $layout = 'admin';

        $csrfToken =
            $this->generateCsrfToken();

        $successMessage =
            $_SESSION['admin_success'] ?? null;

        $errorMessage =
            $_SESSION['admin_error'] ?? null;

        $resetPasswordInfo =
            $_SESSION['admin_reset_password']
            ?? null;

        unset(
            $_SESSION['admin_success'],
            $_SESSION['admin_error'],
            $_SESSION['admin_reset_password']
        );

        require PATH_VIEW_MAIN;
    }

    /**
     * Khóa hoặc mở khóa tài khoản.
     */
    public function updateUserStatus()
    {
        $this->requirePostRequest();
        $this->requireValidCsrf();

        $userId = (int) (
            $_POST['user_id'] ?? 0
        );

        $status = trim(
            $_POST['status'] ?? ''
        );

        if (
            $userId <= 0 ||
            !in_array(
                $status,
                ['active', 'blocked'],
                true
            )
        ) {
            $_SESSION['admin_error'] =
                'Thông tin cập nhật trạng thái không hợp lệ.';

            $this->redirectUsersWithFilters();
        }

        $currentAdminId = (int) (
            $_SESSION['user']['id'] ?? 0
        );

        if (
            $userId === $currentAdminId &&
            $status === 'blocked'
        ) {
            $_SESSION['admin_error'] =
                'Bạn không thể tự khóa tài khoản đang đăng nhập.';

            $this->redirectUsersWithFilters();
        }

        $targetUser =
            $this->userModel->findById($userId);

        if (!$targetUser) {
            $_SESSION['admin_error'] =
                'Không tìm thấy tài khoản cần cập nhật.';

            $this->redirectUsersWithFilters();
        }

        if (
            ($targetUser['status'] ?? '') ===
            $status
        ) {
            $_SESSION['admin_error'] =
                'Tài khoản đã ở trạng thái này.';

            $this->redirectUsersWithFilters();
        }

        try {
            $updated =
                $this->userModel
                    ->updateStatus(
                        $userId,
                        $status
                    );

            if (!$updated) {
                throw new Exception(
                    'Không thể cập nhật trạng thái.'
                );
            }

            $_SESSION['admin_success'] =
                $status === 'active'
                    ? 'Đã mở khóa tài khoản thành công.'
                    : 'Đã khóa tài khoản thành công.';
        } catch (Throwable $exception) {
            $_SESSION['admin_error'] =
                'Không thể cập nhật trạng thái tài khoản.';
        }

        $this->redirectUsersWithFilters();
    }

    /**
     * Thay đổi quyền tài khoản.
     */
    public function updateUserRole()
    {
        $this->requirePostRequest();
        $this->requireValidCsrf();

        $userId = (int) (
            $_POST['user_id'] ?? 0
        );

        $role = trim(
            $_POST['role'] ?? ''
        );

        if (
            $userId <= 0 ||
            !in_array(
                $role,
                ['user', 'admin'],
                true
            )
        ) {
            $_SESSION['admin_error'] =
                'Thông tin phân quyền không hợp lệ.';

            $this->redirectUsersWithFilters();
        }

        $currentAdminId = (int) (
            $_SESSION['user']['id'] ?? 0
        );

        if (
            $userId === $currentAdminId &&
            $role === 'user'
        ) {
            $_SESSION['admin_error'] =
                'Bạn không thể tự hạ quyền tài khoản đang đăng nhập.';

            $this->redirectUsersWithFilters();
        }

        $targetUser =
            $this->userModel->findById($userId);

        if (!$targetUser) {
            $_SESSION['admin_error'] =
                'Không tìm thấy tài khoản cần phân quyền.';

            $this->redirectUsersWithFilters();
        }

        if (
            ($targetUser['role'] ?? '') ===
            $role
        ) {
            $_SESSION['admin_error'] =
                'Tài khoản đã có quyền này.';

            $this->redirectUsersWithFilters();
        }

        try {
            $updated =
                $this->userModel
                    ->updateRole(
                        $userId,
                        $role
                    );

            if (!$updated) {
                throw new Exception(
                    'Không thể cập nhật quyền.'
                );
            }

            $_SESSION['admin_success'] =
                $role === 'admin'
                    ? 'Đã cấp quyền quản trị viên.'
                    : 'Đã chuyển tài khoản về quyền khách hàng.';
        } catch (Throwable $exception) {
            $_SESSION['admin_error'] =
                'Không thể cập nhật quyền tài khoản.';
        }

        $this->redirectUsersWithFilters();
    }

    /**
     * Đặt lại mật khẩu tài khoản.
     */
    public function resetUserPassword()
    {
        $this->requirePostRequest();
        $this->requireValidCsrf();

        $userId = (int) (
            $_POST['user_id'] ?? 0
        );

        if ($userId <= 0) {
            $_SESSION['admin_error'] =
                'Tài khoản cần đặt lại mật khẩu không hợp lệ.';

            $this->redirectUsersWithFilters();
        }

        $targetUser =
            $this->userModel->findById($userId);

        if (!$targetUser) {
            $_SESSION['admin_error'] =
                'Không tìm thấy tài khoản.';

            $this->redirectUsersWithFilters();
        }

        try {
            $newPassword =
                'Fs@' .
                strtoupper(
                    bin2hex(
                        random_bytes(4)
                    )
                );

            $updated =
                $this->userModel
                    ->updatePassword(
                        $userId,
                        $newPassword
                    );

            if (!$updated) {
                throw new Exception(
                    'Không thể đặt lại mật khẩu.'
                );
            }

            $_SESSION['admin_success'] =
                'Đã đặt lại mật khẩu thành công.';

            $_SESSION['admin_reset_password'] = [
                'full_name' =>
                    $targetUser['full_name'],
                'email' =>
                    $targetUser['email'],
                'password' =>
                    $newPassword,
            ];
        } catch (Throwable $exception) {
            $_SESSION['admin_error'] =
                'Không thể đặt lại mật khẩu tài khoản.';
        }

        $this->redirectUsersWithFilters();
    }

    /**
     * Bắt buộc tài khoản admin.
     */
    private function requireAdmin()
    {
        if (empty($_SESSION['user'])) {
            $_SESSION['login_errors'] = [
                'general' =>
                    'Vui lòng đăng nhập để tiếp tục.',
            ];

            $this->redirect('login');
        }

        if (
            ($_SESSION['user']['role'] ?? '') !==
            'admin'
        ) {
            http_response_code(403);

            $title = 'Không có quyền truy cập';
            $view = 'errors/403';
            $layout = 'user';

            require PATH_VIEW_MAIN;
            exit;
        }

        if (
            ($_SESSION['user']['status'] ?? '') !==
            'active'
        ) {
            $_SESSION = [];
            session_destroy();

            session_start();

            $_SESSION['login_errors'] = [
                'general' =>
                    'Tài khoản của bạn đã bị khóa.',
            ];

            $this->redirect('login');
        }
    }

    /**
     * Chỉ chấp nhận POST.
     */
    private function requirePostRequest()
    {
        if (
            ($_SERVER['REQUEST_METHOD'] ?? '') !==
            'POST'
        ) {
            $this->redirect('admin-users');
        }
    }

    /**
     * Kiểm tra CSRF.
     */
    private function requireValidCsrf()
    {
        $token =
            $_POST['csrf_token'] ?? '';

        if (!$this->validateCsrfToken($token)) {
            $_SESSION['admin_error'] =
                'Phiên thao tác không hợp lệ. Vui lòng thử lại.';

            $this->redirectUsersWithFilters();
        }
    }

    private function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] =
                bin2hex(
                    random_bytes(32)
                );
        }

        return $_SESSION['csrf_token'];
    }

    private function validateCsrfToken($token)
    {
        if (
            empty($token) ||
            empty($_SESSION['csrf_token'])
        ) {
            return false;
        }

        return hash_equals(
            $_SESSION['csrf_token'],
            $token
        );
    }

    /**
     * Quay lại danh sách và giữ bộ lọc.
     */
    private function redirectUsersWithFilters()
    {
        $params = [
            'action' => 'admin-users',
        ];

        $keyword = trim(
            $_POST['return_keyword'] ?? ''
        );

        $role = trim(
            $_POST['return_role'] ?? ''
        );

        $status = trim(
            $_POST['return_status'] ?? ''
        );

        $page = max(
            1,
            (int) (
                $_POST['return_page'] ?? 1
            )
        );

        if ($keyword !== '') {
            $params['keyword'] = $keyword;
        }

        if (
            in_array(
                $role,
                ['user', 'admin'],
                true
            )
        ) {
            $params['filter_role'] = $role;
        }

        if (
            in_array(
                $status,
                ['active', 'blocked'],
                true
            )
        ) {
            $params['filter_status'] = $status;
        }

        if ($page > 1) {
            $params['page'] = $page;
        }

        header(
            'Location: ' .
            BASE_URL .
            '?' .
            http_build_query($params)
        );

        exit;
    }

    private function redirect($action)
    {
        header(
            'Location: ' .
            BASE_URL .
            '?action=' .
            urlencode($action)
        );

        exit;
    }
}
