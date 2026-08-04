<?php
$products = $products ?? [];
$categories = $categories ?? [];
$keyword = $keyword ?? '';
$category = $category ?? '';
$status = $status ?? '';
$page = $page ?? 1;
$totalPages = max(1, $totalPages ?? 1);
?>

<style>
    .admin-products-page {
        display: grid;
        gap: 24px;
    }

    .admin-products-card {
        padding: 24px;
        border-radius: 20px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.06);
    }

    .admin-products-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
    }

    .admin-products-toolbar h3 {
        margin: 0;
        font-size: 20px;
        color: #0f172a;
    }

    .admin-products-form {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
        align-items: end;
    }

    .admin-products-form .field {
        display: grid;
        gap: 8px;
    }

    .admin-products-form input,
    .admin-products-form select,
    .admin-products-form button {
        min-height: 44px;
        padding: 0 12px;
        border-radius: 12px;
        border: 1px solid #d7e1ef;
        background: #f8fafc;
        color: #0f172a;
        font-size: 14px;
    }

    .admin-products-form button {
        border: 0;
        background: linear-gradient(135deg, #7c3aed, #db2777);
        color: #ffffff;
        cursor: pointer;
    }

    .admin-products-table-wrap {
        overflow-x: auto;
    }

    .admin-products-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    .admin-products-table th,
    .admin-products-table td {
        padding: 16px 14px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        vertical-align: middle;
    }

    .admin-products-table th {
        color: #64748b;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .admin-products-table td {
        color: #334155;
        font-size: 14px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 7px 11px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
    }

    .status-badge.active {
        background: #dcfce7;
        color: #166534;
    }

    .status-badge.inactive {
        background: #fee2e2;
        color: #b91c1c;
    }

    .admin-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 14px;
        color: #64748b;
        font-size: 14px;
    }

    .admin-pagination a {
        margin-left: 6px;
        padding: 8px 12px;
        border-radius: 10px;
        text-decoration: none;
        color: #4c1d95;
        font-weight: 700;
    }

    .admin-pagination a.active,
    .admin-pagination a:hover {
        background: #ede9fe;
    }

    .admin-empty-state {
        padding: 30px;
        border-radius: 18px;
        border: 1px dashed #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        text-align: center;
    }

    @media (max-width: 900px) {
        .admin-products-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-products-page">
    <div class="admin-products-card">
        <div class="admin-products-toolbar">
            <div>
                <h3>Quản lý sản phẩm</h3>
                <p>Quản lý danh sách sản phẩm và bộ lọc theo danh mục, trạng thái.</p>
            </div>
        </div>

        <form class="admin-products-form" method="get">
            <input type="hidden" name="action" value="admin-products">
            <div class="field">
                <label for="keyword">Từ khóa</label>
                <input id="keyword" type="text" name="keyword" value="<?= e($keyword) ?>" placeholder="Tên sản phẩm hoặc danh mục">
            </div>
            <div class="field">
                <label for="filter_category">Danh mục</label>
                <select id="filter_category" name="filter_category">
                    <option value="">Tất cả</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="filter_status">Trạng thái</label>
                <select id="filter_status" name="filter_status">
                    <option value="">Tất cả</option>
                    <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Hoạt động</option>
                    <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Ngừng bán</option>
                </select>
            </div>
            <div class="field">
                <button type="submit">Lọc</button>
            </div>
        </form>
    </div>

    <?php if (!empty($products)): ?>
        <div class="admin-products-card admin-products-table-wrap">
            <table class="admin-products-table">
                <thead>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Giá</th>
                        <th>Kho</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= e($product['name']) ?></td>
                            <td><?= e($product['category']) ?></td>
                            <td><?= e($product['price']) ?></td>
                            <td><?= e($product['stock']) ?></td>
                            <td>
                                <span class="status-badge <?= e($product['status']) ?>">
                                    <?= e($product['status'] === 'active' ? 'Hoạt động' : 'Ngừng bán') ?>
                                </span>
                            </td>
                            <td><?= e($product['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="admin-pagination">
            <span>Hiển thị <?= e(count($products)) ?> trên tổng <?= e(count($products)) ?> sản phẩm</span>
            <div>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                        $query = [
                            'action' => 'admin-products',
                            'keyword' => $keyword,
                            'filter_category' => $category,
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
        <div class="admin-empty-state">
            Không tìm thấy sản phẩm phù hợp với bộ lọc.
        </div>
    <?php endif; ?>
</div>
