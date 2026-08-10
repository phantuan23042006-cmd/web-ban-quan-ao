<?php
$categories = $categories ?? [];
$keyword = $keyword ?? '';
$editCategory = $editCategory ?? null;

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .category-page {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 24px;
    }

    .admin-card {
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .admin-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .admin-card-header h3 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .search-form {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .search-form input {
        flex: 1;
        min-height: 44px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        font-size: 14px;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 18px;
        min-height: 44px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-danger {
        background: #ef4444;
        color: #ffffff;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    .btn-sm {
        padding: 6px 12px;
        min-height: 34px;
        font-size: 13px;
        border-radius: 8px;
    }

    .category-table {
        width: 100%;
        border-collapse: collapse;
    }

    .category-table th,
    .category-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .category-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .category-form {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }

    .form-group input {
        min-height: 44px;
        padding: 0 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .badge-count {
        background: #ede9fe;
        color: #6d28d9;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }

    @media (max-width: 900px) {
        .category-page {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php if ($successMessage): ?>
    <div class="alert alert-success">
        ✓ <?= e($successMessage) ?>
    </div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="alert alert-danger">
        ✕ <?= e($errorMessage) ?>
    </div>
<?php endif; ?>

<div class="category-page">
    <!-- Danh sách danh mục -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h3>Quản lý danh mục</h3>
                <p style="margin:4px 0 0; color:#64748b; font-size:13px;">Quản lý phân loại sản phẩm cho cửa hàng</p>
            </div>
        </div>

        <form class="search-form" action="<?= BASE_URL ?>" method="get">
            <input type="hidden" name="action" value="admin-categories">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tìm kiếm tên danh mục...">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= BASE_URL ?>?action=admin-categories" class="btn btn-secondary">Đặt lại</a>
            <?php endif; ?>
        </form>

        <div style="overflow-x: auto;">
            <table class="category-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Mã</th>
                        <th>Tên danh mục</th>
                        <th>Sản phẩm liên quan</th>
                        <th style="width: 140px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <tr>
                                <td>#<?= e($cat['id']) ?></td>
                                <td><strong><?= e($cat['name']) ?></strong></td>
                                <td>
                                    <span class="badge-count"><?= e($cat['product_count']) ?> sản phẩm</span>
                                </td>
                                <td style="text-align: right;">
                                    <a href="<?= BASE_URL ?>?action=admin-categories&edit=<?= $cat['id'] ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                    <a href="<?= BASE_URL ?>?action=admin-category-delete&id=<?= $cat['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục \'<?= e($cat['name']) ?>\' không?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align: center; color: #64748b; padding: 24px;">Chưa có danh mục nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Thêm / Sửa -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><?= $editCategory ? 'Sửa danh mục' : 'Thêm danh mục mới' ?></h3>
        </div>

        <form class="category-form" action="<?= BASE_URL ?>?action=<?= $editCategory ? 'admin-category-update' : 'admin-category-store' ?>" method="post">
            <?php if ($editCategory): ?>
                <input type="hidden" name="id" value="<?= e($editCategory['id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="category_name">Tên danh mục <span style="color:red;">*</span></label>
                <input type="text" id="category_name" name="name" required placeholder="Nhập tên danh mục..." value="<?= e($editCategory['name'] ?? '') ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <?= $editCategory ? 'Lưu thay đổi' : '+ Thêm mới' ?>
                </button>
                <?php if ($editCategory): ?>
                    <a href="<?= BASE_URL ?>?action=admin-categories" class="btn btn-secondary">Hủy</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
