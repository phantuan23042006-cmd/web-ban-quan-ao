<?php

class User extends BaseModel
{
    protected $table = 'users';

    /**
     * Tìm tài khoản theo email.
     */
    public function findByEmail($email)
    {
        $email = strtolower(trim($email));

        $sql = "
            SELECT *
            FROM {$this->table}
            WHERE email = :email
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':email' => $email,
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Tìm tài khoản theo ID.
     */
    public function findById($id)
    {
        $sql = "
            SELECT
                id,
                full_name,
                email,
                phone,
                address,
                role,
                status,
                last_login_at,
                created_at,
                updated_at
            FROM {$this->table}
            WHERE id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':id' => (int) $id,
        ]);

        $user = $stmt->fetch();

        return $user ?: null;
    }

    /**
     * Kiểm tra email đã tồn tại hay chưa.
     */
    public function emailExists($email, $exceptId = null)
    {
        $email = strtolower(trim($email));

        $sql = "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE email = :email
        ";

        $params = [
            ':email' => $email,
        ];

        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = (int) $exceptId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Kiểm tra số điện thoại đã tồn tại hay chưa.
     */
    public function phoneExists($phone, $exceptId = null)
    {
        $phone = trim($phone);

        if ($phone === '') {
            return false;
        }

        $sql = "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE phone = :phone
        ";

        $params = [
            ':phone' => $phone,
        ];

        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = (int) $exceptId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Đăng ký tài khoản khách hàng.
     *
     * Phương thức này luôn gán quyền user để tránh người dùng
     * tự đăng ký tài khoản admin.
     */
    public function createUser($data)
    {
        $sql = "
            INSERT INTO {$this->table}
            (
                full_name,
                email,
                phone,
                address,
                password,
                role,
                status
            )
            VALUES
            (
                :full_name,
                :email,
                :phone,
                :address,
                :password,
                'user',
                'active'
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':full_name' => trim($data['full_name']),
            ':email'     => strtolower(trim($data['email'])),
            ':phone'     => !empty($data['phone'])
                ? trim($data['phone'])
                : null,
            ':address'   => !empty($data['address'])
                ? trim($data['address'])
                : null,
            ':password'  => password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            ),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Xác thực đăng nhập.
     *
     * Kết quả:
     * [
     *     'success' => true hoặc false,
     *     'message' => thông báo,
     *     'user'    => thông tin tài khoản hoặc null
     * ]
     */
    public function authenticate($email, $password)
    {
        $email = strtolower(trim($email));
        $user = $this->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->createLoginLog(
                $user['id'] ?? null,
                $email,
                'failed'
            );

            return [
                'success' => false,
                'message' => 'Email hoặc mật khẩu không chính xác.',
                'user'    => null,
            ];
        }

        if ($user['status'] !== 'active') {
            $this->createLoginLog(
                $user['id'],
                $email,
                'failed'
            );

            return [
                'success' => false,
                'message' => 'Tài khoản đang bị khóa.',
                'user'    => null,
            ];
        }

        $this->updateLastLogin($user['id']);

        $this->createLoginLog(
            $user['id'],
            $email,
            'success'
        );

        /*
         * Không đưa mật khẩu vào session hoặc trả về controller.
         */
        unset($user['password']);

        return [
            'success' => true,
            'message' => 'Đăng nhập thành công.',
            'user'    => $user,
        ];
    }

    /**
     * Cập nhật lần đăng nhập gần nhất.
     */
    public function updateLastLogin($userId)
    {
        $sql = "
            UPDATE {$this->table}
            SET last_login_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id' => (int) $userId,
        ]);
    }

    /**
     * Ghi lịch sử đăng nhập.
     */
    public function createLoginLog($userId, $email, $loginStatus)
    {
        $allowedStatuses = ['success', 'failed'];

        if (!in_array($loginStatus, $allowedStatuses, true)) {
            $loginStatus = 'failed';
        }

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

        if ($userAgent !== null) {
            $userAgent = substr($userAgent, 0, 500);
        }

        $sql = "
            INSERT INTO login_logs
            (
                user_id,
                email,
                login_status,
                ip_address,
                user_agent
            )
            VALUES
            (
                :user_id,
                :email,
                :login_status,
                :ip_address,
                :user_agent
            )
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':user_id'      => $userId !== null
                ? (int) $userId
                : null,
            ':email'        => strtolower(trim($email)),
            ':login_status' => $loginStatus,
            ':ip_address'   => $ipAddress,
            ':user_agent'   => $userAgent,
        ]);
    }

    /**
     * Lấy danh sách tài khoản cho trang quản trị.
     */
    public function getAllUsers()
    {
        $sql = "
            SELECT
                id,
                full_name,
                email,
                phone,
                address,
                role,
                status,
                last_login_at,
                created_at,
                updated_at
            FROM {$this->table}
            ORDER BY id DESC
        ";

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    /**
     * Khóa hoặc mở khóa tài khoản.
     */
    public function updateStatus($userId, $status)
    {
        $allowedStatuses = ['active', 'blocked'];

        if (!in_array($status, $allowedStatuses, true)) {
            return false;
        }

        $sql = "
            UPDATE {$this->table}
            SET status = :status
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':id'     => (int) $userId,
        ]);
    }

    /**
     * Cập nhật quyền user hoặc admin.
     */
    public function updateRole($userId, $role)
    {
        $allowedRoles = ['user', 'admin'];

        if (!in_array($role, $allowedRoles, true)) {
            return false;
        }

        $sql = "
            UPDATE {$this->table}
            SET role = :role
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':role' => $role,
            ':id'   => (int) $userId,
        ]);
    }

    /**
     * Cập nhật hồ sơ cá nhân.
     */
    public function updateProfile($userId, $data)
    {
        $sql = "
            UPDATE {$this->table}
            SET
                full_name = :full_name,
                phone = :phone,
                address = :address
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':full_name' => trim($data['full_name']),
            ':phone'     => !empty($data['phone'])
                ? trim($data['phone'])
                : null,
            ':address'   => !empty($data['address'])
                ? trim($data['address'])
                : null,
            ':id'        => (int) $userId,
        ]);
    }

    /**
     * Đổi mật khẩu.
     */
    public function updatePassword($userId, $newPassword)
    {
        $sql = "
            UPDATE {$this->table}
            SET password = :password
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':password' => password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            ),
            ':id' => (int) $userId,
        ]);
    }

    /**
 * Lấy danh sách tài khoản có tìm kiếm, lọc và phân trang.
 */
public function getUsersForAdmin(
    $filters = [],
    $page = 1,
    $perPage = 10
) {
    $page = max(1, (int) $page);
    $perPage = max(1, (int) $perPage);
    $offset = ($page - 1) * $perPage;

    $params = [];
    $whereSql = $this->buildAdminUserWhere(
        $filters,
        $params
    );

    $sql = "
        SELECT
            id,
            full_name,
            email,
            phone,
            address,
            role,
            status,
            last_login_at,
            created_at,
            updated_at
        FROM {$this->table}
        {$whereSql}
        ORDER BY id DESC
        LIMIT :limit OFFSET :offset
    ";

    $stmt = $this->pdo->prepare($sql);

    foreach ($params as $key => $value) {
        $stmt->bindValue(
            $key,
            $value,
            PDO::PARAM_STR
        );
    }

    $stmt->bindValue(
        ':limit',
        $perPage,
        PDO::PARAM_INT
    );

    $stmt->bindValue(
        ':offset',
        $offset,
        PDO::PARAM_INT
    );

    $stmt->execute();

    return $stmt->fetchAll();
}

/**
 * Đếm số tài khoản theo điều kiện lọc.
 */
public function countUsersForAdmin($filters = [])
{
    $params = [];
    $whereSql = $this->buildAdminUserWhere(
        $filters,
        $params
    );

    $sql = "
        SELECT COUNT(*)
        FROM {$this->table}
        {$whereSql}
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);

    return (int) $stmt->fetchColumn();
}

/**
 * Thống kê tài khoản.
 */
public function getUserStatistics()
{
    $sql = "
        SELECT
            COUNT(*) AS total_users,

            COALESCE(
                SUM(role = 'admin'),
                0
            ) AS total_admins,

            COALESCE(
                SUM(role = 'user'),
                0
            ) AS total_members,

            COALESCE(
                SUM(status = 'active'),
                0
            ) AS active_users,

            COALESCE(
                SUM(status = 'blocked'),
                0
            ) AS blocked_users
        FROM {$this->table}
    ";

    $stmt = $this->pdo->query($sql);
    $result = $stmt->fetch();

    return [
        'total_users' => (int) (
            $result['total_users'] ?? 0
        ),

        'total_admins' => (int) (
            $result['total_admins'] ?? 0
        ),

        'total_members' => (int) (
            $result['total_members'] ?? 0
        ),

        'active_users' => (int) (
            $result['active_users'] ?? 0
        ),

        'blocked_users' => (int) (
            $result['blocked_users'] ?? 0
        ),
    ];
}

/**
 * Lấy thống kê dashboard từ dữ liệu thật nếu bảng tồn tại.
 */
public function getDashboardMetrics(): array
{
    $stats = $this->getUserStatistics();

    return [
        'stats' => $stats,
        'total_products' => $this->countTableRows('products'),
        'total_orders' => $this->countTableRows('orders'),
        'pending_orders' => $this->countOrdersByStatus([
            'pending',
            'processing',
            'waiting',
            'chờ xử lý',
            'chờ xác nhận',
            'đang xử lý',
        ]),
        'completed_orders' => $this->countOrdersByStatus([
            'completed',
            'delivered',
            'success',
            'đã giao',
            'hoàn thành',
        ]),
        'total_revenue' => $this->sumOrderRevenue(),
    ];
}

/**
 * Lấy danh sách sản phẩm để hiển thị trên dashboard.
 */
public function getDashboardProducts(): array
{
    if (!$this->tableExists('products')) {
        return [];
    }

    try {
        $stmt = $this->pdo->query(
            'SELECT * FROM `products` ORDER BY `id` DESC LIMIT 10'
        );
        $rows = $stmt->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }

    $products = [];

    foreach ($rows as $row) {
        $products[] = [
            'name' => $this->getFirstValue($row, ['name', 'title', 'product_name', 'product_title']),
            'category' => $this->getFirstValue($row, ['category', 'category_name', 'product_category', 'type']),
            'price' => $this->getFirstValue($row, ['price', 'selling_price', 'unit_price', 'amount']),
            'stock' => $this->getFirstValue($row, ['stock', 'quantity', 'inventory', 'available_stock']),
            'status' => $this->normalizeStatusValue($this->getFirstValue($row, ['status', 'state'])),
            'created_at' => $this->getFirstValue($row, ['created_at', 'created', 'date_created', 'updated_at']),
        ];
    }

    return $products;
}

public function getDashboardCategories(): array
{
    $categories = [];

    if ($this->tableExists('categories')) {
        try {
            $stmt = $this->pdo->query(
                'SELECT * FROM `categories` ORDER BY `name` ASC'
            );
            $rows = $stmt->fetchAll();
        } catch (Throwable $exception) {
            $rows = [];
        }

        foreach ($rows as $row) {
            $categories[] = [
                'name' => $this->getFirstValue($row, ['name', 'title', 'category_name', 'category']),
                'description' => $this->getFirstValue($row, ['description', 'desc']),
                'count' => 0,
                'percent' => 0,
            ];
        }

        if (!empty($categories) && $this->tableExists('products')) {
                $categoryColumn = $this->getProductCategoryColumn();

                if ($categoryColumn !== null) {
                    try {
                        $stmt = $this->pdo->query(
                            "SELECT `{$categoryColumn}` AS category, COUNT(*) AS total FROM `products` GROUP BY `{$categoryColumn}`"
                        );
                        $counts = [];

                        foreach ($stmt->fetchAll() as $row) {
                            $name = trim((string) ($row['category'] ?? ''));

                            if ($name !== '') {
                                $counts[$name] = (int) $row['total'];
                            }
                        }

                        $total = max(1, array_sum($counts));

                        foreach ($categories as &$category) {
                            $categoryName = $category['name'];
                            $category['count'] = $counts[$categoryName] ?? 0;
                            $category['percent'] = $category['count'] > 0
                                ? (int) round(($category['count'] / $total) * 100)
                                : 0;
                        }
                        unset($category);
                    } catch (Throwable $exception) {
                        // keep categories without counts
                    }
                }
            }

            return array_values(array_filter($categories, static function ($category) {
                return $category['name'] !== '';
            }));
        }

        if (!$this->tableExists('products')) {
            return [];
        }

        $categoryColumn = $this->getProductCategoryColumn();

        if ($categoryColumn === null) {
            return [];
        }

        try {
            $stmt = $this->pdo->query(
                "SELECT `{$categoryColumn}` AS category, COUNT(*) AS total FROM `products` GROUP BY `{$categoryColumn}` ORDER BY total DESC LIMIT 10"
            );
            $rows = $stmt->fetchAll();
        } catch (Throwable $exception) {
            return [];
        }

        $total = max(1, array_sum(array_column($rows, 'total')));

    foreach ($rows as $row) {
        $name = $this->getFirstValue($row, ['category']);

        if ($name === '') {
            continue;
        }

        $count = (int) ($row['total'] ?? 0);
        $categories[] = [
            'name' => $name,
            'description' => '',
            'count' => $count,
            'percent' => $count > 0 ? (int) round(($count / $total) * 100) : 0,
        ];
    }

    return $categories;
}

/**
 * Lấy danh sách đơn hàng để hiển thị trên dashboard.
 */
public function getDashboardOrders(): array
{
    if (!$this->tableExists('orders')) {
        return [];
    }

    try {
        $stmt = $this->pdo->query(
            'SELECT * FROM `orders` ORDER BY `id` DESC LIMIT 10'
        );
        $rows = $stmt->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }

    $orders = [];

    foreach ($rows as $row) {
        $orders[] = [
            'id' => $this->getFirstValue($row, ['id', 'order_id', 'order_code']),
            'customer' => $this->getFirstValue($row, ['customer', 'customer_name', 'full_name', 'customer_email', 'email']),
            'date' => $this->getFirstValue($row, ['created_at', 'order_date', 'date', 'updated_at']),
            'status' => $this->normalizeStatusValue($this->getFirstValue($row, ['status', 'state'])),
            'total' => $this->getFirstValue($row, ['total', 'total_amount', 'total_price', 'grand_total', 'amount', 'subtotal']),
        ];
    }

    return $orders;
}

private function getFirstValue(array $row, array $keys)
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
            return $this->normalizeDashboardValue($row[$key]);
        }
    }

    return '';
}

private function normalizeDashboardValue($value)
{
    if (is_string($value)) {
        return trim($value);
    }

    if (is_int($value) || is_float($value)) {
        return (string) $value;
    }

    return (string) $value;
}

private function normalizeStatusValue($value): string
{
    $status = strtolower(trim((string) $value));

    $map = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'waiting' => 'Chờ xử lý',
        'chờ xử lý' => 'Chờ xử lý',
        'chờ xác nhận' => 'Chờ xử lý',
        'đang xử lý' => 'Đang xử lý',
        'completed' => 'Đã giao',
        'delivered' => 'Đã giao',
        'success' => 'Đã giao',
        'đã giao' => 'Đã giao',
        'hoàn thành' => 'Đã giao',
        'cancel' => 'Bị hủy',
        'canceled' => 'Bị hủy',
        'cancelled' => 'Bị hủy',
        'bị hủy' => 'Bị hủy',
    ];

    return $map[$status] ?? ucfirst($status);
}

private function getProductCategoryColumn(): ?string
{
    $candidates = [
        'category',
        'category_name',
        'product_category',
        'type',
    ];

    foreach ($candidates as $column) {
        if ($this->columnExists('products', $column)) {
            return $column;
        }
    }

    return null;
}

public function getDashboardReviewRows(): array
{
    if (!$this->tableExists('reviews')) {
        return [];
    }

    try {
        $stmt = $this->pdo->query(
            'SELECT * FROM `reviews` ORDER BY `id` DESC LIMIT 5'
        );

        return $stmt->fetchAll();
    } catch (Throwable $exception) {
        return [];
    }
}

private function countTableRows($tableName): int
{
    if (!$this->tableExists($tableName)) {
        return 0;
    }

    try {
        $sql = "SELECT COUNT(*) FROM `{$tableName}`";
        $stmt = $this->pdo->query($sql);

        return (int) $stmt->fetchColumn();
    } catch (Throwable $exception) {
        return 0;
    }
}

private function countOrdersByStatus(array $statuses): int
{
    if (!$this->tableExists('orders')) {
        return 0;
    }

    if (!$this->columnExists('orders', 'status')) {
        return 0;
    }

    try {
        $placeholders = [];

        foreach ($statuses as $index => $status) {
            $placeholders[] = ':status' . $index;
        }

        $sql = "
            SELECT COUNT(*)
            FROM `orders`
            WHERE `status` IN (" . implode(', ', $placeholders) . ")
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($statuses as $index => $status) {
            $stmt->bindValue(':status' . $index, $status, PDO::PARAM_STR);
        }

        $stmt->execute();

        return (int) $stmt->fetchColumn();
    } catch (Throwable $exception) {
        return 0;
    }
}

private function sumOrderRevenue(): float
{
    if (!$this->tableExists('orders')) {
        return 0.0;
    }

    $candidateColumns = [
        'total_amount',
        'total_price',
        'grand_total',
        'amount',
        'subtotal',
        'total',
    ];

    foreach ($candidateColumns as $columnName) {
        if (!$this->columnExists('orders', $columnName)) {
            continue;
        }

        try {
            $sql = "SELECT COALESCE(SUM(`{$columnName}`), 0) FROM `orders`";
            $stmt = $this->pdo->query($sql);

            return (float) $stmt->fetchColumn();
        } catch (Throwable $exception) {
            continue;
        }
    }

    return 0.0;
}

private function tableExists($tableName): bool
{
    try {
        $stmt = $this->pdo->query(
            "SHOW TABLES LIKE '" . str_replace("'", "''", $tableName) . "'"
        );

        return (bool) $stmt->fetch();
    } catch (Throwable $exception) {
        return false;
    }
}

private function columnExists($tableName, $columnName): bool
{
    try {
        $stmt = $this->pdo->query(
            "SHOW COLUMNS FROM `" . str_replace("'", "''", $tableName) . "` LIKE '" . str_replace("'", "''", $columnName) . "'"
        );

        return (bool) $stmt->fetch();
    } catch (Throwable $exception) {
        return false;
    }
}

/**
 * Tạo điều kiện tìm kiếm tài khoản.
 */
private function buildAdminUserWhere(
    $filters,
    &$params
) {
    $conditions = [];

    $keyword = trim(
        $filters['keyword'] ?? ''
    );

    $role = trim(
        $filters['role'] ?? ''
    );

    $status = trim(
        $filters['status'] ?? ''
    );

    if ($keyword !== '') {
        $conditions[] = "
            (
                full_name LIKE :keyword
                OR email LIKE :keyword
                OR phone LIKE :keyword
                OR address LIKE :keyword
            )
        ";

        $params[':keyword'] =
            '%' . $keyword . '%';
    }

    if (
        in_array(
            $role,
            ['user', 'admin'],
            true
        )
    ) {
        $conditions[] = 'role = :role';
        $params[':role'] = $role;
    }

    if (
        in_array(
            $status,
            ['active', 'blocked'],
            true
        )
    ) {
        $conditions[] = 'status = :status';
        $params[':status'] = $status;
    }

    if (empty($conditions)) {
        return '';
    }

    return 'WHERE ' . implode(
        ' AND ',
        $conditions
    );
}
}