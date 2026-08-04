<?php
$backLink = BASE_URL;
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
    <h1>404 - Không tìm thấy trang</h1>
    <p>Đường dẫn bạn truy cập hiện không tồn tại. Hãy quay lại trang chủ để tiếp tục khám phá.</p>
    <a href="<?= e($backLink) ?>">Về trang chủ</a>
</div>
