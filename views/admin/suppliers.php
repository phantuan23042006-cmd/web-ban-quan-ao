<?php
$suppliers = $suppliers ?? [];
$keyword = $keyword ?? '';
$editSupplier = $editSupplier ?? null;

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .supplier-page {
        display: grid;
        grid-template-columns: 1fr 380px;
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

    .btn-info {
        background: #0284c7;
        color: #ffffff;
    }

    .btn-danger {
        background: #ef4444;
        color: #ffffff;
    }

    .btn-sm {
        padding: 6px 12px;
        min-height: 34px;
        font-size: 13px;
        border-radius: 8px;
    }

    .supplier-table {
        width: 100%;
        border-collapse: collapse;
    }

    .supplier-table th,
    .supplier-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .supplier-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .supplier-form {
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

    .form-group input,
    .form-group textarea {
        min-height: 44px;
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 80px;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 10px;
    }

    .badge-count {
        background: #e0f2fe;
        color: #0369a1;
        padding: 4px 10px;
        border-radius: 999px;
        font-weight: 700;
        font-size: 12px;
    }

    @media (max-width: 950px) {
        .supplier-page {
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

<div class="supplier-page">
    <!-- Danh sách Nơi nhập hàng -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <h3>Quản lý nơi nhập hàng</h3>
                <p style="margin:4px 0 0; color:#64748b; font-size:13px;">Quản lý nhà cung cấp và thông tin liên hệ nhập hàng</p>
            </div>
        </div>

        <form class="search-form" action="<?= BASE_URL ?>" method="get">
            <input type="hidden" name="action" value="admin-suppliers">
            <input type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tìm kiếm theo tên, SĐT hoặc địa chỉ...">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <?php if ($keyword !== ''): ?>
                <a href="<?= BASE_URL ?>?action=admin-suppliers" class="btn btn-secondary">Đặt lại</a>
            <?php endif; ?>
        </form>

        <div style="overflow-x: auto;">
            <table class="supplier-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Tên nơi nhập hàng</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Ghi chú</th>
                        <th>Sản phẩm</th>
                        <th style="width: 170px; text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($suppliers)): ?>
                        <?php foreach ($suppliers as $sup): ?>
                            <tr>
                                <td>#<?= e($sup['id']) ?></td>
                                <td><strong><?= e($sup['name']) ?></strong></td>
                                <td><?= e($sup['dia_chi'] ?? '---') ?></td>
                                <td><?= e($sup['so_dien_thoai'] ?? '---') ?></td>
                                <td>
                                    <?php if (!empty($sup['ghi_chu'])): ?>
                                        <span style="font-size:13px; color:#475569;"><?= e(mb_strimwidth($sup['ghi_chu'], 0, 40, '...')) ?></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:13px;">---</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge-count"><?= e($sup['product_count']) ?> SP</span>
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    <a href="<?= BASE_URL ?>?action=admin-supplier-detail&id=<?= $sup['id'] ?>" class="btn btn-info btn-sm">Xem</a>
                                    <a href="<?= BASE_URL ?>?action=admin-suppliers&edit=<?= $sup['id'] ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                    <a href="<?= BASE_URL ?>?action=admin-supplier-delete&id=<?= $sup['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa nơi nhập hàng \'<?= e($sup['name']) ?>\' không?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 24px;">Chưa có nơi nhập hàng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Form Thêm / Sửa Nơi Nhập Hàng -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3><?= $editSupplier ? 'Sửa nơi nhập hàng' : 'Thêm nơi nhập hàng mới' ?></h3>
        </div>

        <form class="supplier-form" action="<?= BASE_URL ?>?action=<?= $editSupplier ? 'admin-supplier-update' : 'admin-supplier-store' ?>" method="post">
            <?php if ($editSupplier): ?>
                <input type="hidden" name="id" value="<?= e($editSupplier['id']) ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="supplier_name">Tên nơi nhập hàng <span style="color:red;">*</span></label>
                <input type="text" id="supplier_name" name="name" required placeholder="Nhập tên nơi nhập hàng..." value="<?= e($editSupplier['name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="dia_chi">Địa chỉ</label>
                <input type="text" id="dia_chi" name="dia_chi" placeholder="Nhập địa chỉ..." value="<?= e($editSupplier['dia_chi'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="so_dien_thoai">Số điện thoại</label>
                <input type="text" id="so_dien_thoai" name="so_dien_thoai" placeholder="Nhập số điện thoại liên hệ..." value="<?= e($editSupplier['so_dien_thoai'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="ghi_chu">Ghi chú</label>
                <textarea id="ghi_chu" name="ghi_chu" placeholder="Nhập ghi chú..."><?= e($editSupplier['ghi_chu'] ?? '') ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" style="flex:1;">
                    <?= $editSupplier ? 'Lưu thay đổi' : '+ Thêm nơi nhập' ?>
                </button>
                <?php if ($editSupplier): ?>
                    <a href="<?= BASE_URL ?>?action=admin-suppliers" class="btn btn-secondary">Hủy</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>
