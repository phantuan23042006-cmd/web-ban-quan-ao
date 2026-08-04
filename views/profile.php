<?php
$user = $user ?? [];
?>

<style>
    .profile-page {
        display: grid;
        gap: 18px;
    }

    .profile-card {
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.05);
    }

    .profile-card h2 {
        margin: 0 0 12px;
        color: #0f172a;
    }

    .profile-card p {
        margin: 0 0 10px;
        color: #64748b;
        line-height: 1.7;
    }
</style>

<div class="profile-page">
    <div class="profile-card">
        <h2>Thông tin tài khoản</h2>
        <p><strong>Họ tên:</strong> <?= e($user['full_name'] ?? 'Khách hàng') ?></p>
        <p><strong>Email:</strong> <?= e($user['email'] ?? '') ?></p>
        <p><strong>Điện thoại:</strong> <?= e($user['phone'] ?? '') ?></p>
        <p><strong>Địa chỉ:</strong> <?= e($user['address'] ?? '') ?></p>
    </div>
</div>
