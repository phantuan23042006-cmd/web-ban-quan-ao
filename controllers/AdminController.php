<?php

class AdminController
{
    private $userModel;
    private $categoryModel;
    private $supplierModel;
    private $productModel;
    private $productDetailModel;
    private $reviewModel;

    public function __construct()
    {
        $this->userModel          = new User();
        $this->categoryModel      = new Category();
        $this->supplierModel      = new Supplier();
        $this->productModel       = new Product();
        $this->productDetailModel = new ProductDetail();
        $this->reviewModel        = new Review();

        $this->requireAdmin();
    }

    private function requireAdmin(): void
    {
        if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
            header('Location: ' . BASE_URL . '?action=login');
            exit;
        }
    }

    private function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD METRICS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $dashboardMetrics = $this->userModel->getDashboardMetrics();
        $stats            = $dashboardMetrics['stats'] ?? $this->userModel->getUserStatistics();
        $overview         = [
            'total_products'   => $dashboardMetrics['total_products'] ?? 0,
            'total_orders'     => $dashboardMetrics['total_orders'] ?? 0,
            'pending_orders'   => $dashboardMetrics['pending_orders'] ?? 0,
            'completed_orders' => $dashboardMetrics['completed_orders'] ?? 0,
            'total_revenue'    => $dashboardMetrics['total_revenue'] ?? 0,
        ];
        $categories       = $this->categoryModel->getAll();
        $reviewData       = $this->reviewModel->getAllAdmin([], 1, 5);
        $reviews          = $reviewData['items'] ?? [];
        $reviewCount      = $reviewData['total_items'] ?? count($reviews);

        // Recent orders activity
        $recentOrders     = (new Order())->getAllAdmin();
        $recentActivity   = [];
        foreach (array_slice($recentOrders, 0, 5) as $o) {
            $recentActivity[] = [
                'title' => 'Đơn hàng ' . ($o['order_code'] ?? ('#' . $o['id'])) . ' - ' . ($o['customer_name'] ?? 'Khách hàng'),
                'meta'  => number_format($o['total_amount'] ?? 0, 0, ',', '.') . 'đ',
                'time'  => date('d/m/Y H:i', strtotime($o['created_at'] ?? 'now')),
            ];
        }

        $title = 'Dashboard Admin';
        $view = 'admin/dashboard';
        $layout = 'admin';
        $csrfToken = $this->generateCsrfToken();

        require PATH_VIEW_MAIN;
    }

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ DANH MỤC (CATEGORY CRUD)
    |--------------------------------------------------------------------------
    */
    public function categories()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $categories = $this->categoryModel->getAll($keyword);

        $editId = (int) ($_GET['edit'] ?? 0);
        $editCategory = $editId > 0 ? $this->categoryModel->findById($editId) : null;

        $title = 'Quản lý danh mục';
        $view = 'admin/categories';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function storeCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $name = trim($_POST['name'] ?? '');

        if ($name === '') {
            $_SESSION['error_message'] = 'Tên danh mục không được để trống.';
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        if ($this->categoryModel->nameExists($name)) {
            $_SESSION['error_message'] = "Danh mục '{$name}' đã tồn tại.";
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->create($name);
            $_SESSION['success_message'] = "Thêm danh mục '{$name}' thành công.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Đã có lỗi xảy ra khi tạo danh mục: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function updateCategory()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');

        if ($id <= 0 || $name === '') {
            $_SESSION['error_message'] = 'Dữ liệu danh mục không hợp lệ.';
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        if ($this->categoryModel->nameExists($name, $id)) {
            $_SESSION['error_message'] = "Tên danh mục '{$name}' đã bị trùng.";
            header('Location: ' . BASE_URL . '?action=admin-categories&edit=' . $id);
            exit;
        }

        try {
            $this->categoryModel->update($id, $name);
            $_SESSION['success_message'] = "Cập nhật danh mục thành công.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Không thể cập nhật danh mục: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    public function deleteCategory()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_message'] = 'Danh mục không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        if ($this->categoryModel->hasProducts($id)) {
            $_SESSION['error_message'] = 'Không thể xóa danh mục này vì đang có sản phẩm thuộc danh mục!';
            header('Location: ' . BASE_URL . '?action=admin-categories');
            exit;
        }

        try {
            $this->categoryModel->delete($id);
            $_SESSION['success_message'] = 'Xóa danh mục thành công.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Xóa danh mục thất bại: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-categories');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ NƠI NHẬP HÀNG (SUPPLIER CRUD)
    |--------------------------------------------------------------------------
    */
    public function suppliers()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $suppliers = $this->supplierModel->getAll($keyword);

        $editId = (int) ($_GET['edit'] ?? 0);
        $editSupplier = $editId > 0 ? $this->supplierModel->findById($editId) : null;

        $title = 'Quản lý nơi nhập hàng';
        $view = 'admin/suppliers';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function supplierDetail()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $supplier = $this->supplierModel->findById($id);

        if (!$supplier) {
            $_SESSION['error_message'] = 'Nơi nhập hàng không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        $products = $this->supplierModel->getProducts($id);

        $title = 'Chi tiết nơi nhập hàng: ' . $supplier['name'];
        $view = 'admin/supplier_detail';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function storeSupplier()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['so_dien_thoai'] ?? '');

        if ($name === '') {
            $_SESSION['error_message'] = 'Tên nơi nhập hàng không được để trống.';
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        if ($this->supplierModel->nameExists($name)) {
            $_SESSION['error_message'] = "Nơi nhập hàng '{$name}' đã tồn tại.";
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        if ($phone !== '' && !preg_match('/^[0-9\+\-\s\.\(\)]{8,20}$/', $phone)) {
            $_SESSION['error_message'] = 'Số điện thoại không hợp lệ (phải từ 8-20 ký tự số/dấu).';
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        try {
            $this->supplierModel->create([
                'name'          => $name,
                'dia_chi'       => $_POST['dia_chi'] ?? '',
                'so_dien_thoai' => $phone,
                'ghi_chu'       => $_POST['ghi_chu'] ?? '',
            ]);
            $_SESSION['success_message'] = "Thêm nơi nhập hàng '{$name}' thành công.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Đã có lỗi xảy ra: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-suppliers');
        exit;
    }

    public function updateSupplier()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['so_dien_thoai'] ?? '');

        if ($id <= 0 || $name === '') {
            $_SESSION['error_message'] = 'Tên nơi nhập hàng không được để trống.';
            header('Location: ' . BASE_URL . '?action=admin-suppliers&edit=' . $id);
            exit;
        }

        if ($this->supplierModel->nameExists($name, $id)) {
            $_SESSION['error_message'] = "Tên nơi nhập hàng '{$name}' đã bị trùng.";
            header('Location: ' . BASE_URL . '?action=admin-suppliers&edit=' . $id);
            exit;
        }

        if ($phone !== '' && !preg_match('/^[0-9\+\-\s\.\(\)]{8,20}$/', $phone)) {
            $_SESSION['error_message'] = 'Số điện thoại không hợp lệ (phải từ 8-20 ký tự số/dấu).';
            header('Location: ' . BASE_URL . '?action=admin-suppliers&edit=' . $id);
            exit;
        }

        try {
            $this->supplierModel->update($id, [
                'name'          => $name,
                'dia_chi'       => $_POST['dia_chi'] ?? '',
                'so_dien_thoai' => $phone,
                'ghi_chu'       => $_POST['ghi_chu'] ?? '',
            ]);
            $_SESSION['success_message'] = 'Cập nhật nơi nhập hàng thành công.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi cập nhật: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-suppliers');
        exit;
    }

    public function deleteSupplier()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_message'] = 'Nơi nhập hàng không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        if ($this->supplierModel->hasProducts($id)) {
            $_SESSION['error_message'] = 'Không thể xóa nơi nhập hàng này vì đang có sản phẩm sử dụng.';
            header('Location: ' . BASE_URL . '?action=admin-suppliers');
            exit;
        }

        try {
            $this->supplierModel->delete($id);
            $_SESSION['success_message'] = 'Xóa nơi nhập hàng thành công.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi khi xóa: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-suppliers');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ SẢN PHẨM & BIẾN THỂ SIZE (PRODUCT CRUD)
    |--------------------------------------------------------------------------
    */
    public function products()
    {
        $keyword       = trim($_GET['keyword'] ?? '');
        $danhMucId     = trim($_GET['danh_muc_id'] ?? '');
        $noiNhapHangId = trim($_GET['noi_nhap_hang_id'] ?? '');
        $page          = max(1, (int) ($_GET['page'] ?? 1));
        $perPage       = 10;

        $filters = [
            'keyword'          => $keyword,
            'danh_muc_id'      => $danhMucId,
            'noi_nhap_hang_id' => $noiNhapHangId,
        ];

        $productData = $this->productModel->getAllAdmin($filters, $page, $perPage);

        $products   = $productData['items'];
        $totalItems = $productData['total_items'];
        $totalPages = $productData['total_pages'];

        $categories = $this->categoryModel->getAll();
        $suppliers  = $this->supplierModel->getAll();

        $title = 'Quản lý sản phẩm';
        $view = 'admin/products';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function createProduct()
    {
        $categories = $this->categoryModel->getAll();
        $suppliers  = $this->supplierModel->getAll();

        $title = 'Thêm sản phẩm mới';
        $view = 'admin/product_form';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function storeProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $name          = trim($_POST['name'] ?? '');
        $danhMucId     = (int) ($_POST['danh_muc_id'] ?? 0);
        $noiNhapHangId = (int) ($_POST['noi_nhap_hang_id'] ?? 0);
        $gioiThieu     = trim($_POST['gioi_thieu'] ?? '');
        $rawVariants   = $_POST['variants'] ?? [];

        if ($name === '' || $danhMucId <= 0 || $noiNhapHangId <= 0) {
            $_SESSION['error_message'] = 'Vui lòng điền đầy đủ Tên sản phẩm, Danh mục và Nơi nhập hàng.';
            header('Location: ' . BASE_URL . '?action=admin-product-create');
            exit;
        }

        // Upload ảnh nếu có
        try {
            $imageName = $this->handleImageUpload('anh');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Ảnh không hợp lệ: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '?action=admin-product-create');
            exit;
        }

        try {
            $productId = $this->productModel->createProductWithDetails([
                'name'             => $name,
                'gioi_thieu'       => $gioiThieu,
                'anh'              => $imageName,
                'danh_muc_id'      => $danhMucId,
                'noi_nhap_hang_id' => $noiNhapHangId,
            ], $rawVariants);

            $_SESSION['success_message'] = "Tạo sản phẩm '{$name}' thành công.";
            header('Location: ' . BASE_URL . '?action=admin-product-detail&id=' . $productId);
            exit;

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi tạo sản phẩm: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '?action=admin-product-create');
            exit;
        }
    }

    public function editProduct()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if (!$product) {
            $_SESSION['error_message'] = 'Sản phẩm không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $categories = $this->categoryModel->getAll();
        $suppliers  = $this->supplierModel->getAll();

        $title = 'Sửa sản phẩm: ' . $product['name'];
        $view = 'admin/product_form';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function updateProduct()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $id            = (int) ($_POST['id'] ?? 0);
        $name          = trim($_POST['name'] ?? '');
        $danhMucId     = (int) ($_POST['danh_muc_id'] ?? 0);
        $noiNhapHangId = (int) ($_POST['noi_nhap_hang_id'] ?? 0);
        $gioiThieu     = trim($_POST['gioi_thieu'] ?? '');
        $rawVariants   = $_POST['variants'] ?? null;

        $existingProduct = $this->productModel->findById($id);

        if (!$existingProduct || $name === '' || $danhMucId <= 0 || $noiNhapHangId <= 0) {
            $_SESSION['error_message'] = 'Dữ liệu sản phẩm không hợp lệ.';
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        // Upload ảnh mới nếu người dùng chọn
        try {
            $newImage = $this->handleImageUpload('anh');
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Ảnh không hợp lệ: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $id);
            exit;
        }

        // Nếu có ảnh mới: xóa ảnh cũ khỏi disk
        if ($newImage !== null && !empty($existingProduct['anh'])) {
            $this->deleteOldImage($existingProduct['anh']);
        }
        $imageName = $newImage !== null ? $newImage : $existingProduct['anh'];

        try {
            $this->productModel->updateProductWithDetails($id, [
                'name'             => $name,
                'gioi_thieu'       => $gioiThieu,
                'anh'              => $imageName,
                'danh_muc_id'      => $danhMucId,
                'noi_nhap_hang_id' => $noiNhapHangId,
            ], $rawVariants);

            $_SESSION['success_message'] = "Cập nhật sản phẩm '{$name}' thành công.";
            header('Location: ' . BASE_URL . '?action=admin-product-detail&id=' . $id);
            exit;

        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Lỗi cập nhật sản phẩm: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '?action=admin-product-edit&id=' . $id);
            exit;
        }
    }

    public function productDetail()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $product = $this->productModel->findById($id);

        if (!$product) {
            $_SESSION['error_message'] = 'Sản phẩm không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        $reviews = $this->reviewModel->getByProductId($id);
        $ratingInfo = $this->reviewModel->getAverageRating($id);

        $title = 'Chi tiết sản phẩm: ' . $product['name'];
        $view = 'admin/product_detail';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function deleteProduct()
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_message'] = 'Sản phẩm không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-products');
            exit;
        }

        try {
            $this->productModel->deleteProduct($id);
            $_SESSION['success_message'] = 'Xóa sản phẩm và dữ liệu liên quan thành công.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Không thể xóa sản phẩm: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '?action=admin-products');
        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | QUẢN LÝ ĐÁNH GIÁ (REVIEWS CRUD)
    |--------------------------------------------------------------------------
    */
    public function reviews()
    {
        $keyword         = trim($_GET['keyword'] ?? '');
        $selectedProduct = trim($_GET['san_pham_id'] ?? '');
        $selectedStars   = trim($_GET['so_sao'] ?? '');
        $page            = max(1, (int) ($_GET['page'] ?? 1));
        $perPage         = 10;

        $filters = [
            'keyword'     => $keyword,
            'san_pham_id' => $selectedProduct,
            'so_sao'      => $selectedStars,
        ];

        $reviewData = $this->reviewModel->getAllAdmin($filters, $page, $perPage);

        $reviews    = $reviewData['items'];
        $totalItems = $reviewData['total_items'];
        $totalPages = $reviewData['total_pages'];

        $products = $this->productModel->getAllAdmin([], 1, 1000)['items'] ?? [];

        $title = 'Quản lý đánh giá';
        $view = 'admin/reviews';
        $layout = 'admin';

        require PATH_VIEW_MAIN;
    }

    public function deleteReview()
    {
        $id = (int) ($_GET['id'] ?? 0);
        $redirectProduct = (int) ($_GET['redirect_product'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_message'] = 'Đánh giá không tồn tại.';
            header('Location: ' . BASE_URL . '?action=admin-reviews');
            exit;
        }

        try {
            $this->reviewModel->delete($id);
            $_SESSION['success_message'] = 'Đã xóa đánh giá thành công.';
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Không thể xóa đánh giá: ' . $e->getMessage();
        }

        if ($redirectProduct > 0) {
            header('Location: ' . BASE_URL . '?action=admin-product-detail&id=' . $redirectProduct);
        } else {
            header('Location: ' . BASE_URL . '?action=admin-reviews');
        }
        exit;
    }

    /**
     * Xử lý upload ảnh sản phẩm.
     *
     * Kiểm tra:
     *  - File được chọn và upload thành công
     *  - Đuôi file hợp lệ (jpg, png, webp, gif)
     *  - MIME type thật (finfo) — ngăn spoof file
     *  - Kích thước ≤ 5 MB
     *  - Lưu vào assets/uploads/ với tên ngẫu nhiên
     *
     * @param  string      $field  Tên field trong $_FILES
     * @return string|null         Tên file mới nếu upload thành công, null nếu không có file
     * @throws Exception           Nếu file không hợp lệ
     */
    private function handleImageUpload(string $field = 'anh'): ?string
    {
        // Không có file nào được chọn
        if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $file = $_FILES[$field];

        // Lỗi upload từ PHP
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadErrors = [
                UPLOAD_ERR_INI_SIZE   => 'File quá lớn (vượt giới hạn server).',
                UPLOAD_ERR_FORM_SIZE  => 'File quá lớn (vượt giới hạn form).',
                UPLOAD_ERR_PARTIAL    => 'Upload không hoàn chỉnh. Vui lòng thử lại.',
                UPLOAD_ERR_NO_TMP_DIR => 'Thư mục tạm không tồn tại.',
                UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file lên đĩa.',
                UPLOAD_ERR_EXTENSION  => 'Upload bị chặn bởi extension.',
            ];
            $msg = $uploadErrors[$file['error']] ?? 'Lỗi upload không xác định (mã ' . $file['error'] . ').';
            throw new Exception($msg);
        }

        // Giới hạn kích thước: 5 MB
        $maxBytes = 5 * 1024 * 1024;
        if ($file['size'] > $maxBytes) {
            throw new Exception('Ảnh quá lớn. Vui lòng chọn ảnh nhỏ hơn 5 MB.');
        }

        // Kiểm tra đuôi file
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $allowedExts, true)) {
            throw new Exception('Định dạng file không hợp lệ. Chỉ chấp nhận: JPG, PNG, WEBP, GIF.');
        }

        // Kiểm tra MIME type thật (tránh spoof file)
        $allowedMimes = [
            'image/jpeg',
            'image/png',
            'image/webp',
            'image/gif',
        ];
        if (function_exists('finfo_open')) {
            $finfo    = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mimeType, $allowedMimes, true)) {
                throw new Exception('Nội dung file không phải ảnh hợp lệ. Vui lòng chọn ảnh JPG/PNG/WEBP/GIF.');
            }
        }

        // Tạo thư mục nếu chưa có
        if (!is_dir(PATH_ASSETS_UPLOADS)) {
            mkdir(PATH_ASSETS_UPLOADS, 0755, true);
        }

        // Tên file ngẫu nhiên an toàn
        $newFileName = 'prod_' . date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $targetPath  = PATH_ASSETS_UPLOADS . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            throw new Exception('Không thể lưu ảnh. Kiểm tra quyền ghi thư mục assets/uploads/');
        }

        return $newFileName;
    }

    /**
     * Xóa file ảnh cũ khỏi disk an toàn (chỉ xóa file trong uploads/).
     */
    private function deleteOldImage(?string $fileName): void
    {
        if ($fileName === null || trim($fileName) === '') {
            return;
        }

        // Chỉ cho phép tên file đơn giản (không có path traversal)
        $baseName = basename($fileName);
        $fullPath = rtrim(PATH_ASSETS_UPLOADS, '/\\') . DIRECTORY_SEPARATOR . $baseName;

        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | MẸO GIỮ CÁC METHOD HIỆN CÓ CỦA ADMINCONTROLLER
    |--------------------------------------------------------------------------
    */
public function orders()
{
    $keyword = trim($_GET['keyword'] ?? '');
    $status = trim($_GET['filter_status'] ?? '');
    $orders = array_map(static fn($o) => ['id'=>$o['id'],'customer'=>$o['customer_name'],'date'=>$o['created_at'],'status'=>$o['status'],'total'=>number_format($o['total_amount'],0,',','.').'đ'], (new Order())->getAllAdmin());
    if ($keyword !== '') $orders = array_values(array_filter($orders, static fn($o) => stripos($o['customer'], $keyword) !== false || stripos((string)$o['id'], $keyword) !== false));
    if ($status !== '') $orders = array_values(array_filter($orders, static fn($o) => $o['status'] === $status));
    $page = 1; $totalPages = 1;
    $title = 'Quản lý đơn hàng';
    $view = 'admin/orders';
    $layout = 'admin';

    require PATH_VIEW_MAIN;
}

public function updateOrderStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_validate($_POST['csrf_token'] ?? null)) { header('Location: ' . BASE_URL . '?action=admin-orders'); exit; }
    (new Order())->updateStatus((int)($_POST['order_id'] ?? 0), (string)($_POST['status'] ?? 'pending'));
    header('Location: ' . BASE_URL . '?action=admin-orders'); exit;
}

/*
|--------------------------------------------------------------------------
| QUẢN LÝ TÀI KHOẢN
|--------------------------------------------------------------------------
*/
public function users()
{
    $filters = [
        'keyword' => trim($_GET['keyword'] ?? ''),
        'role'    => trim($_GET['filter_role'] ?? ''),
        'status'  => trim($_GET['filter_status'] ?? ''),
    ];

    $page = max(1, (int) ($_GET['page'] ?? 1));
    $perPage = 10;

    $users = $this->userModel->getUsersForAdmin(
        $filters,
        $page,
        $perPage
    );

    $totalUsers = $this->userModel->countUsersForAdmin($filters);
    $totalPages = max(1, (int) ceil($totalUsers / $perPage));

    $statistics = $this->userModel->getUserStatistics();

    // Thông báo từ các thao tác quản lý tài khoản
    $successMessage = $_SESSION['success_message'] ?? null;
    $errorMessage = $_SESSION['error_message'] ?? null;
    $resetPasswordInfo = $_SESSION['reset_password_info'] ?? null;

    // Xóa thông báo sau khi lấy ra
    unset($_SESSION['success_message']);
    unset($_SESSION['error_message']);
    unset($_SESSION['reset_password_info']);

    // Dữ liệu phân trang mà users.php đang sử dụng
    $pagination = [
        'page'        => $page,
        'per_page'    => $perPage,
        'total_items' => $totalUsers,
        'total_pages' => $totalPages,
        'from'        => $totalUsers > 0
            ? (($page - 1) * $perPage) + 1
            : 0,
        'to'          => min($page * $perPage, $totalUsers),
    ];

    // users.php đang dùng các tên này
    $stats = [
        'total_users'   => $statistics['total_users'] ?? 0,
        'active_users'  => $statistics['active_users'] ?? 0,
        'blocked_users' => $statistics['blocked_users'] ?? 0,
        'admin_users'   => $statistics['total_admins'] ?? 0,
    ];

    // Các biến filter để users.php sử dụng
    $keyword = $filters['keyword'];
    $role = $filters['role'];
    $status = $filters['status'];

    $csrfToken = $this->generateCsrfToken();

    $title = 'Quản lý tài khoản';
    $view = 'admin/users';
    $layout = 'admin';

    require PATH_VIEW_MAIN;
}

/*
|--------------------------------------------------------------------------
| RESET MẬT KHẨU TÀI KHOẢN
|--------------------------------------------------------------------------
*/
public function resetUserPassword()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    $userId = (int) ($_POST['user_id'] ?? 0);

    if ($userId <= 0) {
        $_SESSION['error_message'] = 'Tài khoản không hợp lệ.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    $user = $this->userModel->findById($userId);

    if (!$user) {
        $_SESSION['error_message'] = 'Không tìm thấy tài khoản cần reset mật khẩu.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    /*
     * Tạo mật khẩu tạm thời
     */
    $newPassword = 'User@' . rand(100000, 999999);

    try {
        $updated = $this->userModel->updatePassword(
            $userId,
            $newPassword
        );

        if (!$updated) {
            throw new Exception('Không thể cập nhật mật khẩu.');
        }

        /*
         * Lưu thông tin vào SESSION để sau khi redirect
         * sang trang admin-users vẫn có thể hiển thị.
         */
        $_SESSION['success_message'] =
            'Đã reset mật khẩu cho tài khoản "' .
            ($user['full_name'] ?? $user['email']) .
            '" thành công.';

        $_SESSION['reset_password_info'] = [
            'full_name' => $user['full_name'] ?? '',
            'email'     => $user['email'] ?? '',
            'password'  => $newPassword,
        ];

    } catch (Exception $e) {
        $_SESSION['error_message'] =
            'Reset mật khẩu thất bại: ' . $e->getMessage();
    }

    /*
     * Giữ lại bộ lọc và trang hiện tại
     */
    $query = [
        'action' => 'admin-users',
        'keyword' => $_POST['return_keyword'] ?? '',
        'filter_role' => $_POST['return_role'] ?? '',
        'filter_status' => $_POST['return_status'] ?? '',
        'page' => (int) ($_POST['return_page'] ?? 1),
    ];

    $query = array_filter($query, static function ($value) {
        return $value !== '' && $value !== null;
    });

    header(
        'Location: ' .
        BASE_URL .
        '?' .
        http_build_query($query)
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| CẬP NHẬT TRẠNG THÁI TÀI KHOẢN (khóa / mở khóa)
|--------------------------------------------------------------------------
*/
public function updateUserStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $_SESSION['error_message'] = 'Yêu cầu không hợp lệ (CSRF).';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    $userId = (int) ($_POST['user_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');
    $allowedStatuses = ['active', 'blocked'];

    if ($userId <= 0 || !in_array($status, $allowedStatuses, true)) {
        $_SESSION['error_message'] = 'Dữ liệu không hợp lệ.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    // Không cho phép admin tự khóa tài khoản của chính mình
    if ($userId === (int) ($_SESSION['user']['id'] ?? 0)) {
        $_SESSION['error_message'] = 'Bạn không thể thay đổi trạng thái tài khoản của chính mình.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    $user = $this->userModel->findById($userId);

    if (!$user) {
        $_SESSION['error_message'] = 'Không tìm thấy tài khoản.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    try {
        $updated = $this->userModel->updateStatus($userId, $status);

        if ($updated) {
            $statusLabel = $status === 'active' ? 'mở khóa' : 'khóa';
            $_SESSION['success_message'] =
                "Đã {$statusLabel} tài khoản \"" .
                ($user['full_name'] ?? $user['email']) .
                "\" thành công.";
        } else {
            $_SESSION['error_message'] = 'Không thể cập nhật trạng thái tài khoản.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Lỗi: ' . $e->getMessage();
    }

    // Giữ lại bộ lọc hiện tại sau khi redirect
    $query = array_filter([
        'action'        => 'admin-users',
        'keyword'       => $_POST['return_keyword']       ?? '',
        'filter_role'   => $_POST['return_role']          ?? '',
        'filter_status' => $_POST['return_status']        ?? '',
        'page'          => (int) ($_POST['return_page']   ?? 1),
    ], static fn($v) => $v !== '' && $v !== null);

    header('Location: ' . BASE_URL . '?' . http_build_query($query));
    exit;
}

/*
|--------------------------------------------------------------------------
| CẬP NHẬT VAI TRÒ TÀI KHOẢN (user ↔ admin)
|--------------------------------------------------------------------------
*/
public function updateUserRole()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    if (!csrf_validate($_POST['csrf_token'] ?? null)) {
        $_SESSION['error_message'] = 'Yêu cầu không hợp lệ (CSRF).';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    $userId = (int) ($_POST['user_id'] ?? 0);
    $role   = trim($_POST['role'] ?? '');
    $allowedRoles = ['user', 'admin'];

    if ($userId <= 0 || !in_array($role, $allowedRoles, true)) {
        $_SESSION['error_message'] = 'Dữ liệu không hợp lệ.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    // Không cho phép admin tự hạ quyền của chính mình
    if ($userId === (int) ($_SESSION['user']['id'] ?? 0)) {
        $_SESSION['error_message'] = 'Bạn không thể thay đổi vai trò của chính mình.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    $user = $this->userModel->findById($userId);

    if (!$user) {
        $_SESSION['error_message'] = 'Không tìm thấy tài khoản.';
        header('Location: ' . BASE_URL . '?action=admin-users');
        exit;
    }

    try {
        $updated = $this->userModel->updateRole($userId, $role);

        if ($updated) {
            $roleLabel = $role === 'admin' ? 'Quản trị viên' : 'Người dùng';
            $_SESSION['success_message'] =
                "Đã cập nhật vai trò tài khoản \"" .
                ($user['full_name'] ?? $user['email']) .
                "\" thành {$roleLabel}.";
        } else {
            $_SESSION['error_message'] = 'Không thể cập nhật vai trò tài khoản.';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Lỗi: ' . $e->getMessage();
    }

    // Giữ lại bộ lọc hiện tại sau khi redirect
    $query = array_filter([
        'action'        => 'admin-users',
        'keyword'       => $_POST['return_keyword']       ?? '',
        'filter_role'   => $_POST['return_role']          ?? '',
        'filter_status' => $_POST['return_status']        ?? '',
        'page'          => (int) ($_POST['return_page']   ?? 1),
    ], static fn($v) => $v !== '' && $v !== null);

    header('Location: ' . BASE_URL . '?' . http_build_query($query));
    exit;
}

}
