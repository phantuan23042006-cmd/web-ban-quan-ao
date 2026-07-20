<?php

$users = $users ?? [];

$stats = $stats ?? [
    'total_users' => 0,
    'total_admins' => 0,
    'total_members' => 0,
    'active_users' => 0,
    'blocked_users' => 0,
];

$filters = $filters ?? [
    'keyword' => '',
    'role' => '',
    'status' => '',
];

$pagination = $pagination ?? [
    'page' => 1,
    'per_page' => 10,
    'total_items' => 0,
    'total_pages' => 1,
    'from' => 0,
    'to' => 0,
];

$currentAdminId = (int) (
    $_SESSION['user']['id'] ?? 0
);

if (!function_exists('adminFormatDate')) {
    function adminFormatDate($date)
    {
        if (empty($date)) {
            return 'Chưa có';
        }

        $timestamp = strtotime($date);

        if (!$timestamp) {
            return 'Chưa có';
        }

        return date(
            'd/m/Y H:i',
            $timestamp
        );
    }
}

if (!function_exists('adminUsersPageUrl')) {
    function adminUsersPageUrl(
        $page,
        $filters
    ) {
        $params = [
            'action' => 'admin-users',
            'page' => $page,
        ];

        if (
            !empty($filters['keyword'])
        ) {
            $params['keyword'] =
                $filters['keyword'];
        }

        if (
            !empty($filters['role'])
        ) {
            $params['filter_role'] =
                $filters['role'];
        }

        if (
            !empty($filters['status'])
        ) {
            $params['filter_status'] =
                $filters['status'];
        }

        return BASE_URL .
            '?' .
            http_build_query($params);
    }
}

?>

<style>
    .account-admin-page {
        display: grid;
        gap: 24px;
    }

    .account-admin-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
    }

    .account-admin-heading h2 {
        margin: 0;
        color: #0f172a;
        font-size: 27px;
    }

    .account-admin-heading p {
        margin: 7px 0 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }

    .account-admin-count {
        padding: 10px 14px;
        border: 1px solid #ddd6fe;
        border-radius: 999px;
        background: #f5f3ff;
        color: #6d28d9;
        font-size: 13px;
        font-weight: 800;
    }

    .account-alert {
        padding: 14px 16px;
        border-radius: 13px;
        font-size: 14px;
        line-height: 1.6;
    }

    .account-alert-success {
        border: 1px solid #bbf7d0;
        background: #f0fdf4;
        color: #15803d;
    }

    .account-alert-error {
        border: 1px solid #fecdd3;
        background: #fff1f2;
        color: #be123c;
    }

    .account-password-result {
        padding: 18px;
        border: 1px solid #c4b5fd;
        border-radius: 14px;
        background: #f5f3ff;
    }

    .account-password-result h3 {
        margin: 0 0 10px;
        color: #5b21b6;
        font-size: 16px;
    }

    .account-password-result p {
        margin: 5px 0;
        color: #475569;
        font-size: 14px;
    }

    .account-password-code {
        margin-top: 12px;
        padding: 12px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        border-radius: 10px;
        background: #ffffff;
        color: #0f172a;
        font-family: monospace;
        font-size: 17px;
        font-weight: 800;
    }

    .account-copy-button {
        padding: 7px 11px;
        border: 0;
        border-radius: 8px;
        background: #7c3aed;
        color: #ffffff;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
    }

    .account-stat-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 16px;
    }

    .account-stat-card {
        padding: 20px;
        border: 1px solid #e2e8f0;
        border-radius: 17px;
        background: #ffffff;
        box-shadow: 0 7px 22px rgba(15, 23, 42, 0.05);
    }

    .account-stat-label {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .account-stat-value {
        margin-top: 9px;
        color: #0f172a;
        font-size: 29px;
        font-weight: 850;
    }

    .account-filter-card,
    .account-table-card {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: #ffffff;
        box-shadow: 0 8px 25px rgba(15, 23, 42, 0.05);
    }

    .account-filter-card {
        padding: 20px;
    }

    .account-filter-form {
        display: grid;
        grid-template-columns:
            minmax(220px, 1fr)
            190px
            190px
            auto;
        gap: 13px;
    }

    .account-input,
    .account-select {
        width: 100%;
        height: 45px;
        padding: 0 13px;
        border: 1px solid #dbe3ee;
        border-radius: 11px;
        outline: none;
        background: #f8fafc;
        color: #334155;
    }

    .account-input:focus,
    .account-select:focus {
        border-color: #7c3aed;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.09);
    }

    .account-filter-actions {
        display: flex;
        gap: 9px;
    }

    .account-button {
        min-height: 45px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 0;
        border-radius: 11px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
    }

    .account-button-primary {
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
    }

    .account-button-secondary {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
    }

    .account-table-header {
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        border-bottom: 1px solid #eef2f7;
    }

    .account-table-header strong {
        color: #0f172a;
        font-size: 16px;
    }

    .account-table-header span {
        color: #64748b;
        font-size: 13px;
    }

    .account-table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .account-table {
        width: 100%;
        min-width: 1050px;
        border-collapse: collapse;
    }

    .account-table th,
    .account-table td {
        padding: 15px 14px;
        border-bottom: 1px solid #eef2f7;
        text-align: left;
        vertical-align: middle;
    }

    .account-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 11px;
        font-weight: 850;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .account-table td {
        color: #475569;
        font-size: 13px;
    }

    .account-user-cell {
        display: flex;
        align-items: center;
        gap: 11px;
    }

    .account-avatar {
        width: 40px;
        height: 40px;
        flex-shrink: 0;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        font-size: 15px;
        font-weight: 850;
    }

    .account-user-info {
        min-width: 0;
    }

    .account-user-info strong,
    .account-user-info span {
        display: block;
    }

    .account-user-info strong {
        color: #0f172a;
        font-size: 13px;
    }

    .account-user-info span {
        margin-top: 4px;
        color: #94a3b8;
        font-size: 11px;
    }

    .account-role-badge,
    .account-status-badge {
        padding: 6px 9px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .account-role-admin {
        background: #ede9fe;
        color: #6d28d9;
    }

    .account-role-user {
        background: #e0f2fe;
        color: #0369a1;
    }

    .account-status-active {
        background: #dcfce7;
        color: #15803d;
    }

    .account-status-blocked {
        background: #ffe4e6;
        color: #be123c;
    }

    .account-inline-form {
        margin: 0;
    }

    .account-role-select {
        height: 35px;
        padding: 0 8px;
        border: 1px solid #dbe3ee;
        border-radius: 8px;
        background: #ffffff;
        color: #475569;
        font-size: 12px;
        cursor: pointer;
    }

    .account-actions {
        display: flex;
        align-items: center;
        gap: 7px;
    }

    .account-small-button {
        min-height: 34px;
        padding: 0 10px;
        border: 0;
        border-radius: 8px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .account-detail-button {
        background: #e0f2fe;
        color: #0369a1;
    }

    .account-lock-button {
        background: #fff1f2;
        color: #be123c;
    }

    .account-unlock-button {
        background: #dcfce7;
        color: #15803d;
    }

    .account-reset-button {
        background: #fef3c7;
        color: #b45309;
    }

    .account-self-label {
        color: #7c3aed;
        font-size: 11px;
        font-weight: 800;
    }

    .account-empty {
        padding: 50px 20px;
        color: #64748b;
        text-align: center;
    }

    .account-pagination {
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
    }

    .account-pagination-info {
        color: #64748b;
        font-size: 13px;
    }

    .account-pagination-links {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .account-page-link {
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        display: grid;
        place-items: center;
        border: 1px solid #e2e8f0;
        border-radius: 9px;
        background: #ffffff;
        color: #475569;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .account-page-link:hover,
    .account-page-link.active {
        border-color: #7c3aed;
        background: #7c3aed;
        color: #ffffff;
    }

    .account-modal {
        position: fixed;
        inset: 0;
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
        background: rgba(15, 23, 42, 0.58);
    }

    .account-modal.open {
        display: flex;
    }

    .account-modal-box {
        width: min(560px, 100%);
        overflow: hidden;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 25px 70px rgba(15, 23, 42, 0.28);
    }

    .account-modal-header {
        padding: 20px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #eef2f7;
    }

    .account-modal-header h3 {
        margin: 0;
        color: #0f172a;
        font-size: 19px;
    }

    .account-modal-close {
        width: 36px;
        height: 36px;
        border: 0;
        border-radius: 9px;
        background: #f1f5f9;
        color: #475569;
        cursor: pointer;
        font-size: 19px;
    }

    .account-modal-body {
        padding: 22px;
    }

    .account-detail-grid {
        display: grid;
        grid-template-columns: 145px 1fr;
        gap: 15px;
    }

    .account-detail-label {
        color: #64748b;
        font-size: 13px;
        font-weight: 700;
    }

    .account-detail-value {
        color: #0f172a;
        font-size: 13px;
        word-break: break-word;
    }

    @media (max-width: 1150px) {
        .account-stat-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .account-filter-form {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 650px) {
        .account-admin-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .account-stat-grid {
            grid-template-columns: 1fr 1fr;
        }

        .account-filter-form {
            grid-template-columns: 1fr;
        }

        .account-filter-actions {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .account-pagination {
            align-items: flex-start;
            flex-direction: column;
        }

        .account-detail-grid {
            grid-template-columns: 1fr;
            gap: 5px;
        }

        .account-detail-value {
            margin-bottom: 10px;
        }
    }
</style>

<div class="account-admin-page">

    <div class="account-admin-heading">
        <div>
            <h2>Quản lý tài khoản</h2>

            <p>
                Xem thông tin, tìm kiếm, phân quyền, khóa tài khoản
                và đặt lại mật khẩu người dùng.
            </p>
        </div>

        <div class="account-admin-count">
            <?= e($pagination['total_items']) ?>
            tài khoản phù hợp
        </div>
    </div>

    <?php if (!empty($successMessage)): ?>
        <div class="account-alert account-alert-success">
            <?= e($successMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)): ?>
        <div class="account-alert account-alert-error">
            <?= e($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($resetPasswordInfo)): ?>
        <div class="account-password-result">
            <h3>Mật khẩu mới đã được tạo</h3>

            <p>
                Tài khoản:
                <strong>
                    <?= e(
                        $resetPasswordInfo['full_name']
                        ?? ''
                    ) ?>
                </strong>
            </p>

            <p>
                Email:
                <?= e(
                    $resetPasswordInfo['email']
                    ?? ''
                ) ?>
            </p>

            <div class="account-password-code">
                <span id="generatedPassword">
                    <?= e(
                        $resetPasswordInfo['password']
                        ?? ''
                    ) ?>
                </span>

                <button
                    class="account-copy-button"
                    type="button"
                    id="copyPasswordButton"
                >
                    Sao chép
                </button>
            </div>
        </div>
    <?php endif; ?>

    <section class="account-stat-grid">
        <article class="account-stat-card">
            <div class="account-stat-label">
                Tổng tài khoản
            </div>

            <div class="account-stat-value">
                <?= e($stats['total_users']) ?>
            </div>
        </article>

        <article class="account-stat-card">
            <div class="account-stat-label">
                Khách hàng
            </div>

            <div class="account-stat-value">
                <?= e($stats['total_members']) ?>
            </div>
        </article>

        <article class="account-stat-card">
            <div class="account-stat-label">
                Quản trị viên
            </div>

            <div class="account-stat-value">
                <?= e($stats['total_admins']) ?>
            </div>
        </article>

        <article class="account-stat-card">
            <div class="account-stat-label">
                Đang hoạt động
            </div>

            <div class="account-stat-value">
                <?= e($stats['active_users']) ?>
            </div>
        </article>

        <article class="account-stat-card">
            <div class="account-stat-label">
                Đang bị khóa
            </div>

            <div class="account-stat-value">
                <?= e($stats['blocked_users']) ?>
            </div>
        </article>
    </section>

    <section class="account-filter-card">
        <form
            class="account-filter-form"
            action="<?= BASE_URL ?>"
            method="GET"
        >
            <input
                type="hidden"
                name="action"
                value="admin-users"
            >

            <input
                class="account-input"
                type="search"
                name="keyword"
                value="<?= e($filters['keyword']) ?>"
                placeholder="Tìm theo tên, email, điện thoại..."
            >

            <select
                class="account-select"
                name="filter_role"
            >
                <option value="">
                    Tất cả quyền
                </option>

                <option
                    value="user"
                    <?= $filters['role'] === 'user'
                        ? 'selected'
                        : '' ?>
                >
                    Khách hàng
                </option>

                <option
                    value="admin"
                    <?= $filters['role'] === 'admin'
                        ? 'selected'
                        : '' ?>
                >
                    Quản trị viên
                </option>
            </select>

            <select
                class="account-select"
                name="filter_status"
            >
                <option value="">
                    Tất cả trạng thái
                </option>

                <option
                    value="active"
                    <?= $filters['status'] === 'active'
                        ? 'selected'
                        : '' ?>
                >
                    Đang hoạt động
                </option>

                <option
                    value="blocked"
                    <?= $filters['status'] === 'blocked'
                        ? 'selected'
                        : '' ?>
                >
                    Đang bị khóa
                </option>
            </select>

            <div class="account-filter-actions">
                <button
                    class="account-button account-button-primary"
                    type="submit"
                >
                    Tìm kiếm
                </button>

                <a
                    class="account-button account-button-secondary"
                    href="<?= BASE_URL ?>?action=admin-users"
                >
                    Đặt lại
                </a>
            </div>
        </form>
    </section>

    <section class="account-table-card">
        <div class="account-table-header">
            <strong>Danh sách tài khoản</strong>

            <span>
                Hiển thị
                <?= e($pagination['from']) ?>
                –
                <?= e($pagination['to']) ?>
                trong
                <?= e($pagination['total_items']) ?>
                tài khoản
            </span>
        </div>

        <?php if (empty($users)): ?>

            <div class="account-empty">
                Không tìm thấy tài khoản phù hợp.
            </div>

        <?php else: ?>

            <div class="account-table-wrapper">
                <table class="account-table">
                    <thead>
                        <tr>
                            <th>Tài khoản</th>
                            <th>Điện thoại</th>
                            <th>Quyền</th>
                            <th>Trạng thái</th>
                            <th>Lần đăng nhập cuối</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($users as $user): ?>

                            <?php
                            $userId = (int) $user['id'];
                            $isCurrentAdmin =
                                $userId === $currentAdminId;

                            $initial = function_exists(
                                'mb_substr'
                            )
                                ? mb_strtoupper(
                                    mb_substr(
                                        $user['full_name']
                                            ?: 'U',
                                        0,
                                        1
                                    )
                                )
                                : strtoupper(
                                    substr(
                                        $user['full_name']
                                            ?: 'U',
                                        0,
                                        1
                                    )
                                );

                            $detailJson = e(
                                json_encode(
                                    [
                                        'id' => $user['id'],
                                        'full_name' =>
                                            $user['full_name'],
                                        'email' =>
                                            $user['email'],
                                        'phone' =>
                                            $user['phone']
                                            ?: 'Chưa cập nhật',
                                        'address' =>
                                            $user['address']
                                            ?: 'Chưa cập nhật',
                                        'role' =>
                                            $user['role'] === 'admin'
                                                ? 'Quản trị viên'
                                                : 'Khách hàng',
                                        'status' =>
                                            $user['status'] === 'active'
                                                ? 'Đang hoạt động'
                                                : 'Đang bị khóa',
                                        'last_login' =>
                                            adminFormatDate(
                                                $user['last_login_at']
                                            ),
                                        'created_at' =>
                                            adminFormatDate(
                                                $user['created_at']
                                            ),
                                    ],
                                    JSON_UNESCAPED_UNICODE
                                )
                            );
                            ?>

                            <tr>
                                <td>
                                    <div class="account-user-cell">
                                        <div class="account-avatar">
                                            <?= e($initial) ?>
                                        </div>

                                        <div class="account-user-info">
                                            <strong>
                                                <?= e(
                                                    $user['full_name']
                                                ) ?>
                                            </strong>

                                            <span>
                                                <?= e(
                                                    $user['email']
                                                ) ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <?= e(
                                        $user['phone']
                                        ?: 'Chưa cập nhật'
                                    ) ?>
                                </td>

                                <td>
                                    <?php if ($isCurrentAdmin): ?>

                                        <span
                                            class="account-role-badge account-role-admin"
                                        >
                                            Quản trị viên
                                        </span>

                                        <div class="account-self-label">
                                            Tài khoản của bạn
                                        </div>

                                    <?php else: ?>

                                        <form
                                            class="account-inline-form"
                                            action="<?= BASE_URL ?>?action=admin-user-role"
                                            method="POST"
                                        >
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e($csrfToken) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?= e($userId) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_keyword"
                                                value="<?= e(
                                                    $filters['keyword']
                                                ) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_role"
                                                value="<?= e(
                                                    $filters['role']
                                                ) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_status"
                                                value="<?= e(
                                                    $filters['status']
                                                ) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_page"
                                                value="<?= e(
                                                    $pagination['page']
                                                ) ?>"
                                            >

                                            <select
                                                class="account-role-select"
                                                name="role"
                                                onchange="
                                                    if (
                                                        confirm(
                                                            'Bạn có chắc muốn thay đổi quyền tài khoản này?'
                                                        )
                                                    ) {
                                                        this.form.submit();
                                                    } else {
                                                        this.value =
                                                            '<?= e(
                                                                $user['role']
                                                            ) ?>';
                                                    }
                                                "
                                            >
                                                <option
                                                    value="user"
                                                    <?= $user['role'] === 'user'
                                                        ? 'selected'
                                                        : '' ?>
                                                >
                                                    Khách hàng
                                                </option>

                                                <option
                                                    value="admin"
                                                    <?= $user['role'] === 'admin'
                                                        ? 'selected'
                                                        : '' ?>
                                                >
                                                    Quản trị viên
                                                </option>
                                            </select>
                                        </form>

                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span
                                        class="account-status-badge
                                            <?= $user['status'] === 'active'
                                                ? 'account-status-active'
                                                : 'account-status-blocked' ?>"
                                    >
                                        <?= $user['status'] === 'active'
                                            ? 'Đang hoạt động'
                                            : 'Đang bị khóa' ?>
                                    </span>
                                </td>

                                <td>
                                    <?= e(
                                        adminFormatDate(
                                            $user['last_login_at']
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <?= e(
                                        adminFormatDate(
                                            $user['created_at']
                                        )
                                    ) ?>
                                </td>

                                <td>
                                    <div class="account-actions">
                                        <button
                                            class="account-small-button account-detail-button"
                                            type="button"
                                            data-user="<?= $detailJson ?>"
                                            onclick="openAccountDetail(this)"
                                        >
                                            Chi tiết
                                        </button>

                                        <?php if (!$isCurrentAdmin): ?>

                                            <form
                                                class="account-inline-form"
                                                action="<?= BASE_URL ?>?action=admin-user-status"
                                                method="POST"
                                                onsubmit="
                                                    return confirm(
                                                        'Bạn có chắc muốn thay đổi trạng thái tài khoản này?'
                                                    );
                                                "
                                            >
                                                <input
                                                    type="hidden"
                                                    name="csrf_token"
                                                    value="<?= e(
                                                        $csrfToken
                                                    ) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="user_id"
                                                    value="<?= e(
                                                        $userId
                                                    ) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="status"
                                                    value="<?= $user['status'] === 'active'
                                                        ? 'blocked'
                                                        : 'active' ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="return_keyword"
                                                    value="<?= e(
                                                        $filters['keyword']
                                                    ) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="return_role"
                                                    value="<?= e(
                                                        $filters['role']
                                                    ) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="return_status"
                                                    value="<?= e(
                                                        $filters['status']
                                                    ) ?>"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="return_page"
                                                    value="<?= e(
                                                        $pagination['page']
                                                    ) ?>"
                                                >

                                                <button
                                                    class="account-small-button
                                                        <?= $user['status'] === 'active'
                                                            ? 'account-lock-button'
                                                            : 'account-unlock-button' ?>"
                                                    type="submit"
                                                >
                                                    <?= $user['status'] === 'active'
                                                        ? 'Khóa'
                                                        : 'Mở khóa' ?>
                                                </button>
                                            </form>

                                        <?php endif; ?>

                                        <form
                                            class="account-inline-form"
                                            action="<?= BASE_URL ?>?action=admin-user-reset-password"
                                            method="POST"
                                            onsubmit="
                                                return confirm(
                                                    'Đặt lại mật khẩu cho tài khoản này?'
                                                );
                                            "
                                        >
                                            <input
                                                type="hidden"
                                                name="csrf_token"
                                                value="<?= e($csrfToken) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="user_id"
                                                value="<?= e($userId) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_keyword"
                                                value="<?= e(
                                                    $filters['keyword']
                                                ) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_role"
                                                value="<?= e(
                                                    $filters['role']
                                                ) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_status"
                                                value="<?= e(
                                                    $filters['status']
                                                ) ?>"
                                            >

                                            <input
                                                type="hidden"
                                                name="return_page"
                                                value="<?= e(
                                                    $pagination['page']
                                                ) ?>"
                                            >

                                            <button
                                                class="account-small-button account-reset-button"
                                                type="submit"
                                            >
                                                Đặt lại MK
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (
                $pagination['total_pages'] > 1
            ): ?>

                <div class="account-pagination">
                    <div class="account-pagination-info">
                        Trang
                        <?= e($pagination['page']) ?>
                        /
                        <?= e($pagination['total_pages']) ?>
                    </div>

                    <div class="account-pagination-links">
                        <?php if (
                            $pagination['page'] > 1
                        ): ?>
                            <a
                                class="account-page-link"
                                href="<?= e(
                                    adminUsersPageUrl(
                                        $pagination['page'] - 1,
                                        $filters
                                    )
                                ) ?>"
                            >
                                ‹
                            </a>
                        <?php endif; ?>

                        <?php
                        $startPage = max(
                            1,
                            $pagination['page'] - 2
                        );

                        $endPage = min(
                            $pagination['total_pages'],
                            $pagination['page'] + 2
                        );
                        ?>

                        <?php for (
                            $pageNumber = $startPage;
                            $pageNumber <= $endPage;
                            $pageNumber++
                        ): ?>

                            <a
                                class="account-page-link
                                    <?= $pageNumber ===
                                        $pagination['page']
                                        ? 'active'
                                        : '' ?>"
                                href="<?= e(
                                    adminUsersPageUrl(
                                        $pageNumber,
                                        $filters
                                    )
                                ) ?>"
                            >
                                <?= e($pageNumber) ?>
                            </a>

                        <?php endfor; ?>

                        <?php if (
                            $pagination['page'] <
                            $pagination['total_pages']
                        ): ?>
                            <a
                                class="account-page-link"
                                href="<?= e(
                                    adminUsersPageUrl(
                                        $pagination['page'] + 1,
                                        $filters
                                    )
                                ) ?>"
                            >
                                ›
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php endif; ?>

        <?php endif; ?>
    </section>
</div>

<div
    class="account-modal"
    id="accountDetailModal"
>
    <div class="account-modal-box">
        <div class="account-modal-header">
            <h3>Chi tiết tài khoản</h3>

            <button
                class="account-modal-close"
                type="button"
                onclick="closeAccountDetail()"
            >
                ×
            </button>
        </div>

        <div class="account-modal-body">
            <div class="account-detail-grid">
                <div class="account-detail-label">
                    Mã tài khoản
                </div>
                <div
                    class="account-detail-value"
                    id="detailId"
                ></div>

                <div class="account-detail-label">
                    Họ và tên
                </div>
                <div
                    class="account-detail-value"
                    id="detailFullName"
                ></div>

                <div class="account-detail-label">
                    Email
                </div>
                <div
                    class="account-detail-value"
                    id="detailEmail"
                ></div>

                <div class="account-detail-label">
                    Điện thoại
                </div>
                <div
                    class="account-detail-value"
                    id="detailPhone"
                ></div>

                <div class="account-detail-label">
                    Địa chỉ
                </div>
                <div
                    class="account-detail-value"
                    id="detailAddress"
                ></div>

                <div class="account-detail-label">
                    Quyền
                </div>
                <div
                    class="account-detail-value"
                    id="detailRole"
                ></div>

                <div class="account-detail-label">
                    Trạng thái
                </div>
                <div
                    class="account-detail-value"
                    id="detailStatus"
                ></div>

                <div class="account-detail-label">
                    Đăng nhập cuối
                </div>
                <div
                    class="account-detail-value"
                    id="detailLastLogin"
                ></div>

                <div class="account-detail-label">
                    Ngày tạo
                </div>
                <div
                    class="account-detail-value"
                    id="detailCreatedAt"
                ></div>
            </div>
        </div>
    </div>
</div>

<script>
    function openAccountDetail(button) {
        const rawData =
            button.getAttribute('data-user');

        if (!rawData) {
            return;
        }

        let user;

        try {
            user = JSON.parse(rawData);
        } catch (error) {
            alert(
                'Không thể đọc thông tin tài khoản.'
            );
            return;
        }

        document.getElementById(
            'detailId'
        ).textContent = user.id || '';

        document.getElementById(
            'detailFullName'
        ).textContent = user.full_name || '';

        document.getElementById(
            'detailEmail'
        ).textContent = user.email || '';

        document.getElementById(
            'detailPhone'
        ).textContent = user.phone || '';

        document.getElementById(
            'detailAddress'
        ).textContent = user.address || '';

        document.getElementById(
            'detailRole'
        ).textContent = user.role || '';

        document.getElementById(
            'detailStatus'
        ).textContent = user.status || '';

        document.getElementById(
            'detailLastLogin'
        ).textContent =
            user.last_login || '';

        document.getElementById(
            'detailCreatedAt'
        ).textContent =
            user.created_at || '';

        document.getElementById(
            'accountDetailModal'
        ).classList.add('open');
    }

    function closeAccountDetail() {
        document.getElementById(
            'accountDetailModal'
        ).classList.remove('open');
    }

    const accountDetailModal =
        document.getElementById(
            'accountDetailModal'
        );

    if (accountDetailModal) {
        accountDetailModal.addEventListener(
            'click',
            function (event) {
                if (
                    event.target ===
                    accountDetailModal
                ) {
                    closeAccountDetail();
                }
            }
        );
    }

    const copyPasswordButton =
        document.getElementById(
            'copyPasswordButton'
        );

    if (copyPasswordButton) {
        copyPasswordButton.addEventListener(
            'click',
            async function () {
                const passwordElement =
                    document.getElementById(
                        'generatedPassword'
                    );

                if (!passwordElement) {
                    return;
                }

                const password =
                    passwordElement.textContent.trim();

                try {
                    await navigator.clipboard.writeText(
                        password
                    );

                    copyPasswordButton.textContent =
                        'Đã sao chép';
                } catch (error) {
                    alert(
                        'Mật khẩu mới: ' + password
                    );
                }
            }
        );
    }
</script>