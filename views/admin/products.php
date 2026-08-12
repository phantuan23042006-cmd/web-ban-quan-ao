<?php
$products = $products ?? [];
$categories = $categories ?? [];
$suppliers = $suppliers ?? [];
$keyword = $keyword ?? '';
$danhMucId = $danhMucId ?? '';
$noiNhapHangId = $noiNhapHangId ?? '';
$page = $page ?? 1;
$totalPages = max(1, $totalPages ?? 1);
$totalItems = $totalItems ?? 0;

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .admin-products-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .admin-card {
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .toolbar h3 {
        margin: 0;
        font-size: 22px;
        color: #0f172a;
    }

    .alert {
        padding: 14px 18px;
        border-radius: 12px;
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

    .filter-form {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr auto;
        gap: 14px;
        align-items: end;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .field label {
        font-size: 13px;
        font-weight: 600;
        color: #475569;
    }

    .field input,
    .field select {
        min-height: 44px;
        padding: 0 12px;
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
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.25);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
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

    .product-table {
        width: 100%;
        border-collapse: collapse;
    }

    .product-table th,
    .product-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    .product-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .img-thumb {
        width: 54px;
        height: 54px;
        object-fit: cover;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }

    .star-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 8px;
        background: #fef9c3;
        color: #a16207;
        font-weight: 700;
        font-size: 13px;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        color: #64748b;
        font-size: 14px;
    }

    .pagination-links {
        display: flex;
        gap: 6px;
    }

    .pagination-links a {
        padding: 8px 14px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #475569;
        text-decoration: none;
        font-weight: 700;
    }

    .pagination-links a.active {
        background: #7c3aed;
        color: #ffffff;
    }

    @media (max-width: 900px) {
        .filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-products-page">
    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="toolbar">
            <div>
                <h3>Quản lý sản phẩm</h3>
                <p style="margin:4px 0 0; color:#64748b; font-size:13px;">Quản lý kho hàng, biến thể size và giá bán sản phẩm</p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?action=admin-product-create" class="btn btn-primary">+ Thêm sản phẩm</a>
            </div>
        </div>

        <form class="filter-form" action="<?= BASE_URL ?>" method="get">
            <input type="hidden" name="action" value="admin-products">
            <div class="field">
                <label for="keyword">Tìm kiếm tên sản phẩm</label>
                <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Nhập tên sản phẩm...">
            </div>
            <div class="field">
                <label for="danh_muc_id">Danh mục</label>
                <select id="danh_muc_id" name="danh_muc_id">
                    <option value="">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat['id']) ?>" <?= (string)$danhMucId === (string)$cat['id'] ? 'selected' : '' ?>>
                            <?= e($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="noi_nhap_hang_id">Nơi nhập hàng</label>
                <select id="noi_nhap_hang_id" name="noi_nhap_hang_id">
                    <option value="">-- Tất cả nơi nhập --</option>
                    <?php foreach ($suppliers as $sup): ?>
                        <option value="<?= e($sup['id']) ?>" <?= (string)$noiNhapHangId === (string)$sup['id'] ? 'selected' : '' ?>>
                            <?= e($sup['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <?php if ($keyword !== '' || $danhMucId !== '' || $noiNhapHangId !== ''): ?>
                    <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary">Đặt lại</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x: auto;">
            <table class="product-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px;">Mã</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Nơi nhập</th>
                        <th>Giá từ</th>
                        <th>Giá đến</th>
                        <th>Tồn kho</th>
                        <th>Đánh giá</th>
                        <th style="padding-right:24px; text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td style="padding-left:24px;">#<?= e($p['id']) ?></td>
                                <td>
                                    <?php if (!empty($p['anh'])): ?>
                                        <img src="<?= BASE_ASSETS_UPLOADS . e($p['anh']) ?>" alt="<?= e($p['name']) ?>" class="img-thumb">
                                    <?php else: ?>
                                        <div class="img-thumb" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">No image</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= e($p['name']) ?></strong>
                                </td>
                                <td><?= e($p['category_name'] ?? '---') ?></td>
                                <td><?= e($p['supplier_name'] ?? '---') ?></td>
                                <td style="color:#16a34a; font-weight:700;">
                                    <?= number_format($p['gia_tu'], 0, ',', '.') ?> đ
                                </td>
                                <td style="color:#16a34a; font-weight:700;">
                                    <?= number_format($p['gia_den'], 0, ',', '.') ?> đ
                                </td>
                                <td>
                                    <span style="font-weight:700; color:<?= $p['ton_kho'] > 0 ? '#0f172a' : '#ef4444' ?>;">
                                        <?= e($p['ton_kho']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($p['danh_gia_tb'] > 0): ?>
                                        <span class="star-badge">★ <?= e($p['danh_gia_tb']) ?> <small>(<?= e($p['tong_danh_gia']) ?>)</small></span>
                                    <?php else: ?>
                                        <span style="color:#94a3b8; font-size:12px;">Chưa có</span>
                                    <?php endif; ?>
                                </td>
                                <td style="padding-right:24px; text-align:right; white-space:nowrap;">
                                    <a href="<?= BASE_URL ?>?action=admin-product-detail&id=<?= $p['id'] ?>" class="btn btn-info btn-sm">Xem</a>
                                    <a href="<?= BASE_URL ?>?action=admin-product-edit&id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Sửa</a>
                                    <a href="<?= BASE_URL ?>?action=admin-product-delete&id=<?= $p['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm \'<?= e($p['name']) ?>\' và tất cả biến thể, đánh giá liên quan?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" style="text-align: center; color: #64748b; padding: 32px;">Không tìm thấy sản phẩm nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination" style="padding: 20px 24px;">
                <span>Hiển thị <?= count($products) ?> trên tổng số <?= $totalItems ?> sản phẩm</span>
                <div class="pagination-links">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                            $query = array_filter([
                                'action' => 'admin-products',
                                'keyword' => $keyword,
                                'danh_muc_id' => $danhMucId,
                                'noi_nhap_hang_id' => $noiNhapHangId,
                                'page' => $i,
                            ]);
                        ?>
                        <a href="<?= BASE_URL ?>?<?= http_build_query($query) ?>" class="<?= $i === $page ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
