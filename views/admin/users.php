<?php

$users = $users ?? [];
$stats = $stats ?? [];
$pagination = $pagination ?? [];
$keyword = $keyword ?? '';
$role = $role ?? '';
$status = $status ?? '';
$page = (int) ($pagination['page'] ?? 1);
$totalPages = max(1, (int) ($pagination['total_pages'] ?? 1));
$successMessage = $successMessage ?? null;
$errorMessage = $errorMessage ?? null;
$resetPasswordInfo = $resetPasswordInfo ?? null;
$csrfToken = $csrfToken ?? '';

$totalUsers = (int) ($stats['total_users'] ?? 0);
$activeUsers = (int) ($stats['active_users'] ?? 0);
$blockedUsers = (int) ($stats['blocked_users'] ?? 0);
$adminUsers = (int) ($stats['admin_users'] ?? 0);
?>

<style>
    .admin-users-page {
        display: grid;
        gap: 24px;
    }

    .admin-users-shell {
        display: grid;
        gap: 20px;
    }

    .admin-users-alert {
        padding: 14px 16px;
        border-radius: 14px;
        font-size: 14px;
        line-height: 1.6;
    }

    .admin-users-alert.success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .admin-users-alert.error {
        border: 1px solid #fecdd3;
        background: #fff1f2;
        color: #be123c;
    }

    .admin-users-stats {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }

    .admin-users-stat {
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .admin-users-stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
    }

    .admin-users-stat-value {
        margin-top: 10px;
        color: #0f172a;
        font-size: 28px;
        font-weight: 800;
    }

    .admin-users-card {
        padding: 24px;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background: #ffffff;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
    }

    .admin-users-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .admin-users-toolbar h3 {
        margin: 0;
        color: #0f172a;
        font-size: 20px;
    }

    .admin-users-toolbar p {
        margin: 5px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .admin-users-form {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: end;
    }

    .admin-users-form .field {
        display: grid;
        gap: 6px;
    }

    .admin-users-form label {
        color: #334155;
        font-size: 12px;
        font-weight: 700;
    }

    .admin-users-form input,
    .admin-users-form select {
        min-height: 42px;
        padding: 0 12px;
        border: 1px solid #dbe3ee;
        border-radius: 11px;
        background: #f8fafc;
        color: #0f172a;
        font-size: 14px;
    }

    .admin-users-form button {
        min-height: 42px;
        padding: 0 16px;
        border: 0;
        border-radius: 11px;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
    }

    .admin-users-table-wrap {
        overflow-x: auto;
    }

    .admin-users-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 960px;
    }

    .admin-users-table th,
    .admin-users-table td {
        padding: 14px 12px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: top;
    }

    .admin-users-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .admin-users-table td {
        color: #334155;
        font-size: 14px;
    }

    .admin-users-name {
        font-weight: 700;
        color: #0f172a;
    }

    .admin-users-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .admin-users-badge.user {
        background: #eff6ff;
        color: #2563eb;
    }

    .admin-users-badge.admin {
        background: #fef3c7;
        color: #b45309;
    }

    .admin-users-badge.active {
        background: #dcfce7;
        color: #15803d;
    }

    .admin-users-badge.blocked {
        background: #fee2e2;
        color: #dc2626;
    }

    .admin-users-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .admin-users-actions form {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .admin-users-actions select,
    .admin-users-actions button {
        min-height: 36px;
        padding: 0 10px;
        border-radius: 10px;
        border: 1px solid #dbe3ee;
        font-size: 13px;
    }

    .admin-users-actions button {
        border: 0;
        background: #111827;
        color: #ffffff;
        cursor: pointer;
    }

    .admin-users-actions .reset-btn {
        background: #7c3aed;
    }

    .admin-users-empty {
        padding: 28px;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        text-align: center;
        color: #64748b;
        background: #f8fafc;
    }

    .admin-users-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        color: #64748b;
        font-size: 14px;
    }

    .admin-users-pagination a {
        margin-left: 6px;
        padding: 8px 12px;
        border-radius: 10px;
        color: #4c1d95;
        text-decoration: none;
        font-weight: 700;
    }

    .admin-users-pagination a.active,
    .admin-users-pagination a:hover {
        background: #ede9fe;
    }

    @media (max-width: 920px) {
        .admin-users-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .admin-users-stats {
            grid-template-columns: 1fr;
        }

        .admin-users-card {
            padding: 18px;
        }

        .admin-users-toolbar {
            align-items: flex-start;
        }
    }
</style>

<div class="admin-users-page">
    <?php if (!empty($successMessage)): ?>
        <div class="admin-users-alert success">
            <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="admin-users-alert error">
            <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($resetPasswordInfo)): ?>
        <div class="admin-users-alert success">
            <strong>Mật khẩu mới cho <?= e($resetPasswordInfo['full_name'] ?? '') ?>:</strong>
            <div>Email: <?= e($resetPasswordInfo['email'] ?? '') ?></div>
            <div>Mật khẩu tạm thời: <strong><?= e($resetPasswordInfo['password'] ?? '') ?></strong></div>
        </div>
    <?php endif; ?>

    <div class="admin-users-stats">
        <div class="admin-users-stat">
            <div class="admin-users-stat-label">Tổng tài khoản</div>
            <div class="admin-users-stat-value"><?= e($totalUsers) ?></div>
        </div>
        <div class="admin-users-stat">
            <div class="admin-users-stat-label">Đang hoạt động</div>
            <div class="admin-users-stat-value"><?= e($activeUsers) ?></div>
        </div>
        <div class="admin-users-stat">
            <div class="admin-users-stat-label">Bị khóa</div>
            <div class="admin-users-stat-value"><?= e($blockedUsers) ?></div>
        </div>
        <div class="admin-users-stat">
            <div class="admin-users-stat-label">Quản trị</div>
            <div class="admin-users-stat-value"><?= e($adminUsers) ?></div>
        </div>
    </div>

    <div class="admin-users-card">
        <div class="admin-users-toolbar">
            <div>
                <h3>Danh sách tài khoản</h3>
                <p>Tìm kiếm nhanh, lọc theo vai trò và trạng thái, quản lý tài khoản từ đây.</p>
            </div>

            <form class="admin-users-form" method="get">
                <input type="hidden" name="action" value="admin-users">
                <div class="field">
                    <label for="keyword">Từ khóa</label>
                    <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tên, email, SĐT">
                </div>
                <div class="field">
                    <label for="filter_role">Vai trò</label>
                    <select id="filter_role" name="filter_role">
                        <option value="" <?= $role === '' ? 'selected' : '' ?>>Tất cả</option>
                        <option value="user" <?= $role === 'user' ? 'selected' : '' ?>>Khách hàng</option>
                        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="field">
                    <label for="filter_status">Trạng thái</label>
                    <select id="filter_status" name="filter_status">
                        <option value="" <?= $status === '' ? 'selected' : '' ?>>Tất cả</option>
                        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="blocked" <?= $status === 'blocked' ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>
                <button type="submit">Lọc</button>
            </form>
        </div>

        <?php if (!empty($users)): ?>
            <div class="admin-users-table-wrap">
                <table class="admin-users-table">
                    <thead>
                        <tr>
                            <th>Người dùng</th>
                            <th>Liên hệ</th>
                            <th>Vai trò</th>
                            <th>Trạng thái</th>
                            <th>Hoạt động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <?php
                                $userId = (int) ($user['id'] ?? 0);
                                $userName = trim((string) ($user['full_name'] ?? '')) ?: 'Không có tên';
                                $userEmail = (string) ($user['email'] ?? '');
                                $userPhone = (string) ($user['phone'] ?? '');
                                $userRole = (string) ($user['role'] ?? 'user');
                                $userStatus = (string) ($user['status'] ?? 'active');
                                $createdAt = (string) ($user['created_at'] ?? '-');
                            ?>
                            <tr>
                                <td>
                                    <div class="admin-users-name"><?= e($userName) ?></div>
                                    <div style="margin-top: 4px; color: #64748b; font-size: 12px;">
                                        Tạo lúc: <?= e($createdAt) ?>
                                    </div>
                                </td>
                                <td>
                                    <div><?= e($userEmail) ?></div>
                                    <div style="margin-top: 4px; color: #64748b; font-size: 12px;"><?= e($userPhone) ?></div>
                                </td>
                                <td>
                                    <span class="admin-users-badge <?= e($userRole === 'admin' ? 'admin' : 'user') ?>">
                                        <?= e($userRole === 'admin' ? 'Admin' : 'Khách hàng') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="admin-users-badge <?= e($userStatus === 'blocked' ? 'blocked' : 'active') ?>">
                                        <?= e($userStatus === 'blocked' ? 'Bị khóa' : 'Hoạt động') ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="admin-users-actions">
                                        <form method="post" action="<?= BASE_URL ?>?action=admin-user-status">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="user_id" value="<?= e($userId) ?>">
                                            <input type="hidden" name="status" value="<?= e($userStatus === 'active' ? 'blocked' : 'active') ?>">
                                            <input type="hidden" name="return_keyword" value="<?= e($keyword) ?>">
                                            <input type="hidden" name="return_role" value="<?= e($role) ?>">
                                            <input type="hidden" name="return_status" value="<?= e($status) ?>">
                                            <input type="hidden" name="return_page" value="<?= e($page) ?>">
                                            <button type="submit">
                                                <?= e($userStatus === 'active' ? 'Khóa' : 'Mở khóa') ?>
                                            </button>
                                        </form>

                                        <form method="post" action="<?= BASE_URL ?>?action=admin-user-role">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="user_id" value="<?= e($userId) ?>">
                                            <input type="hidden" name="role" value="<?= e($userRole === 'admin' ? 'user' : 'admin') ?>">
                                            <input type="hidden" name="return_keyword" value="<?= e($keyword) ?>">
                                            <input type="hidden" name="return_role" value="<?= e($role) ?>">
                                            <input type="hidden" name="return_status" value="<?= e($status) ?>">
                                            <input type="hidden" name="return_page" value="<?= e($page) ?>">
                                            <button type="submit">
                                                <?= e($userRole === 'admin' ? 'Hạ quyền' : 'Cấp admin') ?>
                                            </button>
                                        </form>

                                        <form method="post" action="<?= BASE_URL ?>?action=admin-user-reset-password">
                                            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                            <input type="hidden" name="user_id" value="<?= e($userId) ?>">
                                            <input type="hidden" name="return_keyword" value="<?= e($keyword) ?>">
                                            <input type="hidden" name="return_role" value="<?= e($role) ?>">
                                            <input type="hidden" name="return_status" value="<?= e($status) ?>">
                                            <input type="hidden" name="return_page" value="<?= e($page) ?>">
                                            <button type="submit" class="reset-btn">Reset mật khẩu</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="admin-users-pagination">
                <span>Hiển thị <?= e($pagination['from'] ?? 0) ?> - <?= e($pagination['to'] ?? 0) ?> / <?= e($pagination['total_items'] ?? 0) ?></span>
                <div>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                            $query = [
                                'action' => 'admin-users',
                                'keyword' => $keyword,
                                'filter_role' => $role,
                                'filter_status' => $status,
                                'page' => $i,
                            ];
                            $query = array_filter($query, static function ($value) {
                                return $value !== '' && $value !== null;
                            });
                        ?>
                        <a class="<?= $i === $page ? 'active' : '' ?>" href="<?= BASE_URL ?>?<?= http_build_query($query) ?>">
                            <?= e($i) ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="admin-users-empty">
                Không có tài khoản nào phù hợp với bộ lọc hiện tại.
            </div>
        <?php endif; ?>
    </div>
</div>
