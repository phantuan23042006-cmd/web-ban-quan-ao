<?php

$backLink = BASE_URL;
if (!empty($_SESSION['user'])) {
    $backLink = BASE_URL . '?action=admin-dashboard';
}
?>

<style>
    .error-card {
        max-width: 760px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 24px;
        background: #ffffff;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.08);
        text-align: center;
    }

    .error-card h1 {
        margin: 0 0 12px;
        color: #0f172a;
        font-size: 32px;
    }

    .error-card p {
        margin: 0 0 22px;
        color: #64748b;
        font-size: 16px;
        line-height: 1.8;
    }

    .error-card a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        font-weight: 700;
        text-decoration: none;
    }
</style>

<div class="error-card">
    <h1>403 - Không có quyền truy cập</h1>
    <p>Bạn không đủ quyền để xem trang này. Nếu đây là lỗi, vui lòng quay lại trang chính hoặc đăng nhập bằng tài khoản phù hợp.</p>
    <a href="<?= e($backLink) ?>">Quay lại</a>
</div>
