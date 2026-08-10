<?php
$reviews = $reviews ?? [];
$products = $products ?? [];
$selectedProduct = $selectedProduct ?? '';
$selectedStars = $selectedStars ?? '';
$keyword = $keyword ?? '';
$page = $page ?? 1;
$totalPages = max(1, $totalPages ?? 1);
$totalItems = $totalItems ?? 0;

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .admin-reviews-page {
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

    .admin-card-header h3 {
        margin: 0;
        font-size: 20px;
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
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
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

    .review-table {
        width: 100%;
        border-collapse: collapse;
    }

    .review-table th,
    .review-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    .review-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .star-rating {
        color: #eab308;
        font-weight: 700;
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px 24px;
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

<div class="admin-reviews-page">
    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Quản lý đánh giá sản phẩm</h3>
            <p style="margin:4px 0 0; color:#64748b; font-size:13px;">Duyệt và quản lý phản hồi, bình luận đánh giá từ khách hàng</p>
        </div>

        <form class="filter-form" action="<?= BASE_URL ?>" method="get">
            <input type="hidden" name="action" value="admin-reviews">
            <div class="field">
                <label for="keyword">Từ khóa tìm kiếm</label>
                <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tên sản phẩm, người đánh giá, nội dung...">
            </div>
            <div class="field">
                <label for="san_pham_id">Lọc theo sản phẩm</label>
                <select id="san_pham_id" name="san_pham_id">
                    <option value="">-- Tất cả sản phẩm --</option>
                    <?php foreach ($products as $p): ?>
                        <option value="<?= e($p['id']) ?>" <?= (string)$selectedProduct === (string)$p['id'] ? 'selected' : '' ?>>
                            <?= e($p['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="so_sao">Lọc theo số sao</label>
                <select id="so_sao" name="so_sao">
                    <option value="">-- Tất cả số sao --</option>
                    <option value="5" <?= (string)$selectedStars === '5' ? 'selected' : '' ?>>5 Sao (⭐⭐⭐⭐⭐)</option>
                    <option value="4" <?= (string)$selectedStars === '4' ? 'selected' : '' ?>>4 Sao (⭐⭐⭐⭐)</option>
                    <option value="3" <?= (string)$selectedStars === '3' ? 'selected' : '' ?>>3 Sao (⭐⭐⭐)</option>
                    <option value="2" <?= (string)$selectedStars === '2' ? 'selected' : '' ?>>2 Sao (⭐⭐)</option>
                    <option value="1" <?= (string)$selectedStars === '1' ? 'selected' : '' ?>>1 Sao (⭐)</option>
                </select>
            </div>
            <div style="display: flex; gap: 8px;">
                <button type="submit" class="btn btn-primary">Lọc</button>
                <?php if ($keyword !== '' || $selectedProduct !== '' || $selectedStars !== ''): ?>
                    <a href="<?= BASE_URL ?>?action=admin-reviews" class="btn btn-secondary">Đặt lại</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="admin-card" style="padding:0; overflow:hidden;">
        <div style="overflow-x: auto;">
            <table class="review-table">
                <thead>
                    <tr>
                        <th style="padding-left:24px; width:60px;">Mã</th>
                        <th>Sản phẩm</th>
                        <th>Người đánh giá</th>
                        <th>Số sao</th>
                        <th>Nội dung</th>
                        <th>Thời gian</th>
                        <th style="padding-right:24px; text-align:right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($reviews)): ?>
                        <?php foreach ($reviews as $rev): ?>
                            <tr>
                                <td style="padding-left:24px;">#<?= e($rev['id']) ?></td>
                                <td>
                                    <strong><?= e($rev['product_name']) ?></strong>
                                </td>
                                <td>
                                    <strong><?= e($rev['user_name']) ?></strong>
                                    <div style="font-size:12px; color:#64748b;"><?= e($rev['user_email']) ?></div>
                                </td>
                                <td>
                                    <span class="star-rating">
                                        <?= str_repeat('★', (int)$rev['so_sao']) ?><?= str_repeat('☆', 5 - (int)$rev['so_sao']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="max-width:320px; line-height:1.4;">
                                        <?= e($rev['noi_dung'] ?? 'Không có nội dung.') ?>
                                    </div>
                                </td>
                                <td style="font-size:13px; color:#64748b;"><?= e($rev['created_at']) ?></td>
                                <td style="padding-right:24px; text-align:right;">
                                    <a href="<?= BASE_URL ?>?action=admin-review-delete&id=<?= $rev['id'] ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa đánh giá vi phạm này?');">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 32px;">Không tìm thấy đánh giá nào phù hợp.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <span>Hiển thị <?= count($reviews) ?> trên tổng số <?= $totalItems ?> đánh giá</span>
                <div class="pagination-links">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php
                            $query = array_filter([
                                'action' => 'admin-reviews',
                                'keyword' => $keyword,
                                'san_pham_id' => $selectedProduct,
                                'so_sao' => $selectedStars,
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
