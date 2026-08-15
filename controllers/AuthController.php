<?php

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Hiển thị trang đăng ký.
     */
    public function showRegister()
    {
        $this->redirectIfLoggedIn();

        $title = 'Đăng ký tài khoản';
        $view = 'auth/register';

        $errors = $_SESSION['register_errors'] ?? [];
        $old = $_SESSION['register_old'] ?? [];

        unset(
            $_SESSION['register_errors'],
            $_SESSION['register_old']
        );

        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    /**
     * Xử lý đăng ký tài khoản.
     */
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('register');
        }

        $this->redirectIfLoggedIn();

        $fullName = trim($_POST['full_name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';

        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA CSRF TOKEN
        |--------------------------------------------------------------------------
        */

        if (!$this->validateCsrfToken($csrfToken)) {
            $errors['general'] =
                'Phiên gửi biểu mẫu không hợp lệ. Vui lòng thử lại.';
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA HỌ TÊN
        |--------------------------------------------------------------------------
        */

        if ($fullName === '') {
            $errors['full_name'] = 'Vui lòng nhập họ và tên.';
        } elseif (mb_strlen($fullName) < 2) {
            $errors['full_name'] =
                'Họ và tên phải có ít nhất 2 ký tự.';
        } elseif (mb_strlen($fullName) > 100) {
            $errors['full_name'] =
                'Họ và tên không được vượt quá 100 ký tự.';
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA EMAIL
        |--------------------------------------------------------------------------
        */

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        } elseif (mb_strlen($email) > 150) {
            $errors['email'] =
                'Email không được vượt quá 150 ký tự.';
        } elseif ($this->userModel->emailExists($email)) {
            $errors['email'] =
                'Email này đã được sử dụng.';
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA SỐ ĐIỆN THOẠI
        |--------------------------------------------------------------------------
        */

        if ($phone === '') {
            $errors['phone'] =
                'Vui lòng nhập số điện thoại.';
        } elseif (!preg_match('/^(0|\+84)[0-9]{9,10}$/', $phone)) {
            $errors['phone'] =
                'Số điện thoại không đúng định dạng.';
        } elseif ($this->userModel->phoneExists($phone)) {
            $errors['phone'] =
                'Số điện thoại này đã được sử dụng.';
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA ĐỊA CHỈ
        |--------------------------------------------------------------------------
        */

        if ($address !== '' && mb_strlen($address) > 255) {
            $errors['address'] =
                'Địa chỉ không được vượt quá 255 ký tự.';
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA MẬT KHẨU
        |--------------------------------------------------------------------------
        */

        if ($password === '') {
            $errors['password'] =
                'Vui lòng nhập mật khẩu.';
        } elseif (strlen($password) < 6) {
            $errors['password'] =
                'Mật khẩu phải có ít nhất 6 ký tự.';
        } elseif (strlen($password) > 72) {
            $errors['password'] =
                'Mật khẩu không được vượt quá 72 ký tự.';
        }

        if ($passwordConfirmation === '') {
            $errors['password_confirmation'] =
                'Vui lòng nhập lại mật khẩu.';
        } elseif ($password !== $passwordConfirmation) {
            $errors['password_confirmation'] =
                'Mật khẩu xác nhận không khớp.';
        }

        /*
        |--------------------------------------------------------------------------
        | CÓ LỖI: LƯU THÔNG TIN VÀ QUAY LẠI FORM
        |--------------------------------------------------------------------------
        */

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;

            $_SESSION['register_old'] = [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ];

            $this->redirect('register');
        }

        /*
        |--------------------------------------------------------------------------
        | TẠO TÀI KHOẢN
        |--------------------------------------------------------------------------
        */

        try {
            $userId = $this->userModel->createUser([
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'password' => $password,
            ]);

            if ($userId <= 0) {
                throw new Exception('Không thể tạo tài khoản.');
            }

            /*
             * Tạo token mới sau khi hoàn thành đăng ký.
             */
            $this->regenerateCsrfToken();

            $_SESSION['success_message'] =
                'Đăng ký thành công. Bạn có thể đăng nhập ngay.';

            $this->redirect('login');
        } catch (PDOException $exception) {
            $_SESSION['register_errors'] = [
                'general' =>
                    'Không thể đăng ký tài khoản. Email hoặc số điện thoại có thể đã tồn tại.',
            ];

            $_SESSION['register_old'] = [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ];

            $this->redirect('register');
        } catch (Throwable $exception) {
            $_SESSION['register_errors'] = [
                'general' =>
                    'Đã xảy ra lỗi khi đăng ký. Vui lòng thử lại.',
            ];

            $_SESSION['register_old'] = [
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
            ];

            $this->redirect('register');
        }
    }

    /**
     * Hiển thị trang đăng nhập.
     */
    public function showLogin()
    {
        $this->redirectIfLoggedIn();

        $title = 'Đăng nhập';
        $view = 'auth/login';

        $errors = $_SESSION['login_errors'] ?? [];
        $old = $_SESSION['login_old'] ?? [];

        $successMessage =
            $_SESSION['success_message'] ?? null;

        unset(
            $_SESSION['login_errors'],
            $_SESSION['login_old'],
            $_SESSION['success_message']
        );

        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    /**
     * Xử lý đăng nhập.
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        $this->redirectIfLoggedIn();

        $email = strtolower(trim($_POST['email'] ?? ''));
        $password = $_POST['password'] ?? '';
        $csrfToken = $_POST['csrf_token'] ?? '';

        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA TOKEN
        |--------------------------------------------------------------------------
        */

        if (!$this->validateCsrfToken($csrfToken)) {
            $errors['general'] =
                'Phiên đăng nhập không hợp lệ. Vui lòng thử lại.';
        }

        /*
        |--------------------------------------------------------------------------
        | KIỂM TRA DỮ LIỆU ĐĂNG NHẬP
        |--------------------------------------------------------------------------
        */

        if ($email === '') {
            $errors['email'] = 'Vui lòng nhập email.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Email không đúng định dạng.';
        }

        if ($password === '') {
            $errors['password'] =
                'Vui lòng nhập mật khẩu.';
        }

        if (!empty($errors)) {
            $_SESSION['login_errors'] = $errors;
            $_SESSION['login_old'] = [
                'email' => $email,
            ];

            $this->redirect('login');
        }

        /*
        |--------------------------------------------------------------------------
        | XÁC THỰC TÀI KHOẢN
        |--------------------------------------------------------------------------
        */

        try {
            $result = $this->userModel->authenticate(
                $email,
                $password
            );

            if (!$result['success']) {
                $_SESSION['login_errors'] = [
                    'general' => $result['message'],
                ];

                $_SESSION['login_old'] = [
                    'email' => $email,
                ];

                $this->redirect('login');
            }

            /*
             * Chống session fixation.
             */
            session_regenerate_id(true);

            $user = $result['user'];

            /*
             * Chỉ lưu các thông tin cần thiết vào session.
             */
            $_SESSION['user'] = [
                'id' => (int) $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'address' => $user['address'],
                'role' => $user['role'],
                'status' => $user['status'],
            ];

            $this->regenerateCsrfToken();

            $_SESSION['success_message'] =
                'Đăng nhập thành công.';

            /*
             * Điều hướng theo quyền.
             */
            if ($user['role'] === 'admin') {
                $this->redirect('admin-dashboard');
            }

            $this->redirect('/');
        } catch (Throwable $exception) {
            $_SESSION['login_errors'] = [
                'general' =>
                    'Không thể đăng nhập vào lúc này. Vui lòng thử lại.',
            ];

            $_SESSION['login_old'] = [
                'email' => $email,
            ];

            $this->redirect('login');
        }
    }

    /**
     * Đăng xuất tài khoản.
     */
    public function logout()
    {
        /*
         * Xóa toàn bộ dữ liệu session.
         */
        $_SESSION = [];

        /*
         * Xóa cookie session.
         */
        if (ini_get('session.use_cookies')) {
            $cookieParameters =
                session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookieParameters['path'],
                $cookieParameters['domain'],
                $cookieParameters['secure'],
                $cookieParameters['httponly']
            );
        }

        /*
         * Kết thúc session hiện tại.
         */
        session_destroy();

        /*
         * Khởi tạo session mới để hiển thị thông báo.
         */
        session_start();
        session_regenerate_id(true);

        $_SESSION['success_message'] =
            'Bạn đã đăng xuất thành công.';

        $this->redirect('login');
    }

    /**
     * Chuyển hướng người đã đăng nhập.
     */
    private function redirectIfLoggedIn()
    {
        if (empty($_SESSION['user'])) {
            return;
        }

        $role = $_SESSION['user']['role'] ?? 'user';

        if ($role === 'admin') {
            $this->redirect('admin-dashboard');
        }

        $this->redirect('/');
    }

    /**
     * Tạo CSRF token.
     */
    private function generateCsrfToken()
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] =
                bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Tạo lại CSRF token.
     */
    private function regenerateCsrfToken()
    {
        $_SESSION['csrf_token'] =
            bin2hex(random_bytes(32));

        return $_SESSION['csrf_token'];
    }

    /**
     * Kiểm tra CSRF token.
     */
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
     * Điều hướng theo action.
     */
    private function redirect($action)
    {
        if ($action === '/') {
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

    public function forgotPassword()
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('products');
        }

        $csrfToken = $this->generateCsrfToken();
        $title = 'Quên mật khẩu';
        $view  = 'auth/forgot_password';
        $layout = 'auth';

        require PATH_VIEW_MAIN;
    }

    public function submitForgotPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('forgot-password');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF).';
            $this->redirect('forgot-password');
        }

        $email = strtolower(trim($_POST['email'] ?? ''));
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Vui lòng nhập địa chỉ email hợp lệ.';
            $this->redirect('forgot-password');
        }

        try {
            $token = $this->userModel->createPasswordResetToken($email);
            $resetLink = BASE_URL . '?action=reset-password&email=' . urlencode($email) . '&token=' . $token;

            $subject = '=?UTF-8?B?' . base64_encode('Yêu cầu đặt lại mật khẩu - DuAn1') . '?=';
            $message = "Xin chào,\n\nBạn đã yêu cầu đặt lại mật khẩu tại website DuAn1.\n";
            $message .= "Vui lòng truy cập đường dẫn sau trong vòng 15 phút để tạo mật khẩu mới:\n";
            $message .= $resetLink . "\n\n";
            $message .= "Nếu bạn không gửi yêu cầu này, vui lòng bỏ qua email.\nTrân trọng!";
            $headers = "From: no-reply@duan1.local\r\nContent-Type: text/plain; charset=UTF-8\r\n";

            @mail($email, $subject, $message, $headers);

            $_SESSION['reset_link_debug'] = $resetLink;
            $_SESSION['success'] = 'Đã gửi liên kết khôi phục mật khẩu đến email của bạn! Vui lòng kiểm tra hộp thư.';
            $this->redirect('forgot-password');

        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('forgot-password');
        }
    }

    public function resetPassword()
    {
        if (isset($_SESSION['user'])) {
            $this->redirect('products');
        }

        $email = strtolower(trim($_GET['email'] ?? ''));
        $token = trim($_GET['token'] ?? '');

        if (!$email || !$token || !$this->userModel->verifyPasswordResetToken($email, $token)) {
            $_SESSION['error'] = 'Liên kết khôi phục mật khẩu không hợp lệ hoặc đã hết hạn.';
            $this->redirect('login');
        }

        $csrfToken = $this->generateCsrfToken();
        $title = 'Đặt lại mật khẩu';
        $view  = 'auth/reset_password';
        $layout = 'auth';

        require PATH_VIEW_MAIN;
    }

    public function submitResetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('login');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->validateCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF).';
            $this->redirect('login');
        }

        $email       = strtolower(trim($_POST['email'] ?? ''));
        $token       = trim($_POST['token'] ?? '');
        $password    = $_POST['password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        if (strlen($password) < 6) {
            $_SESSION['error'] = 'Mật khẩu mới phải có ít nhất 6 ký tự.';
            header('Location: ' . BASE_URL . '?action=reset-password&email=' . urlencode($email) . '&token=' . urlencode($token));
            exit;
        }

        if ($password !== $confirmPass) {
            $_SESSION['error'] = 'Mật khẩu nhập lại không khớp.';
            header('Location: ' . BASE_URL . '?action=reset-password&email=' . urlencode($email) . '&token=' . urlencode($token));
            exit;
        }

        try {
            $this->userModel->resetPasswordWithToken($email, $token, $password);
            $_SESSION['success'] = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.';
            $this->redirect('login');
        } catch (Throwable $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('login');
        }
    }
}
