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