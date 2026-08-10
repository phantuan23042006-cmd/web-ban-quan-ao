<?php
$supplier = $supplier ?? null;
$products = $products ?? [];
?>

<style>
    .supplier-detail-page {
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

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 16px;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .detail-item .label {
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
        color: #64748b;
    }

    .detail-item .value {
        font-size: 15px;
        color: #0f172a;
        font-weight: 600;
    }

    .product-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 16px;
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
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
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
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }
</style>

<div class="supplier-detail-page">
    <div class="admin-card">
        <div class="detail-header">
            <div>
                <h2 style="margin: 0; color: #0f172a;">Nơi nhập hàng: <?= e($supplier['name']) ?></h2>
                <p style="margin: 6px 0 0; color: #64748b;">Mã nơi nhập hàng: #<?= e($supplier['id']) ?></p>
            </div>
            <div>
                <a href="<?= BASE_URL ?>?action=admin-suppliers" class="btn btn-secondary">← Quay lại danh sách</a>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-item">
                <span class="label">Số điện thoại</span>
                <span class="value"><?= e($supplier['so_dien_thoai'] ?? 'Chưa cập nhật') ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Địa chỉ</span>
                <span class="value"><?= e($supplier['dia_chi'] ?? 'Chưa cập nhật') ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Tổng sản phẩm đang nhập</span>
                <span class="value" style="color: #7c3aed;"><?= e(count($products)) ?> sản phẩm</span>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <span class="label">Ghi chú</span>
                <span class="value" style="font-weight: 400; font-style: italic; color: #475569;">
                    <?= e($supplier['ghi_chu'] ?? 'Không có ghi chú.') ?>
                </span>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <h3 style="margin-top:0; color:#0f172a;">Sản phẩm thuộc nơi nhập hàng này</h3>

        <div style="overflow-x: auto;">
            <table class="product-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Danh mục</th>
                        <th>Khoảng giá bán</th>
                        <th>Tồn kho</th>
                        <th style="text-align: right;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($products)): ?>
                        <?php foreach ($products as $prod): ?>
                            <tr>
                                <td>#<?= e($prod['id']) ?></td>
                                <td>
                                    <?php if (!empty($prod['anh'])): ?>
                                        <img src="<?= BASE_ASSETS_UPLOADS . e($prod['anh']) ?>" alt="<?= e($prod['name']) ?>" class="img-thumb">
                                    <?php else: ?>
                                        <div class="img-thumb" style="background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">No image</div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= e($prod['name']) ?></strong></td>
                                <td><?= e($prod['category_name'] ?? 'Chưa phân loại') ?></td>
                                <td style="color:#16a34a; font-weight:700;">
                                    <?php if ($prod['gia_tu'] == $prod['gia_den']): ?>
                                        <?= number_format($prod['gia_tu'], 0, ',', '.') ?> đ
                                    <?php else: ?>
                                        <?= number_format($prod['gia_tu'], 0, ',', '.') ?> đ - <?= number_format($prod['gia_den'], 0, ',', '.') ?> đ
                                    <?php endif; ?>
                                </td>
                                <td><strong><?= e($prod['ton_kho']) ?></strong></td>
                                <td style="text-align: right;">
                                    <a href="<?= BASE_URL ?>?action=admin-product-detail&id=<?= $prod['id'] ?>" class="btn btn-secondary" style="padding: 6px 12px; min-height:30px; font-size:12px;">Xem chi tiết</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; color: #64748b; padding: 24px;">Hiện chưa có sản phẩm nào nhập từ nơi này.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
