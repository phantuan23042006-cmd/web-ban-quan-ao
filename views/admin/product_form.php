<?php
$product = $product ?? null;
$categories = $categories ?? [];
$suppliers = $suppliers ?? [];
$variants = $product['variants'] ?? [];

$isEdit = !empty($product['id']);
$formAction = $isEdit ? BASE_URL . '?action=admin-product-update' : BASE_URL . '?action=admin-product-store';

$successMessage = $_SESSION['success_message'] ?? null;
$errorMessage = $_SESSION['error_message'] ?? null;
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<style>
    .product-form-page {
        display: flex;
        flex-direction: column;
        gap: 24px;
        max-width: 1000px;
        margin: 0 auto;
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
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 16px;
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

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: #1e293b;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        padding: 10px 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        font-size: 14px;
        background: #ffffff;
    }

    .form-group textarea {
        min-height: 100px;
        resize: vertical;
    }

    .image-preview-wrap {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-top: 6px;
    }

    .image-preview {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
    }

    /* Variant Table Styling */
    .variant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        margin-bottom: 14px;
    }

    .variant-header h4 {
        margin: 0;
        font-size: 16px;
        color: #0f172a;
    }

    .variant-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }

    .variant-table th,
    .variant-table td {
        padding: 12px 14px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }

    .variant-table th {
        background: #f8fafc;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .variant-input {
        width: 100%;
        padding: 6px 10px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
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

    .btn-success {
        background: #16a34a;
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

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
    }

    /* Modal styling */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .modal-card {
        background: #ffffff;
        width: min(480px, calc(100% - 32px));
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="product-form-page">
    <?php if ($successMessage): ?>
        <div class="alert alert-success">✓ <?= e($successMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
        <div class="alert alert-danger">✕ <?= e($errorMessage) ?></div>
    <?php endif; ?>

    <form action="<?= $formAction ?>" method="post" enctype="multipart/form-data">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= e($product['id']) ?>">
        <?php endif; ?>

        <div class="admin-card">
            <div class="admin-card-header">
                <h3><?= $isEdit ? 'Chỉnh sửa sản phẩm: #' . e($product['id']) : 'Thêm sản phẩm mới' ?></h3>
                <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary btn-sm">Quay lại danh sách</a>
            </div>

            <div class="form-grid">
                <!-- Tên sản phẩm -->
                <div class="form-group full-width">
                    <label for="name">Tên sản phẩm <span style="color:red;">*</span></label>
                    <input type="text" id="name" name="name" required placeholder="Nhập tên sản phẩm..." value="<?= e($product['name'] ?? '') ?>">
                </div>

                <!-- Danh mục -->
                <div class="form-group">
                    <label for="danh_muc_id">Danh mục <span style="color:red;">*</span></label>
                    <select id="danh_muc_id" name="danh_muc_id" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat['id']) ?>" <?= isset($product['danh_muc_id']) && (string)$product['danh_muc_id'] === (string)$cat['id'] ? 'selected' : '' ?>>
                                <?= e($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Nơi nhập hàng -->
                <div class="form-group">
                    <label for="noi_nhap_hang_id">Nơi nhập hàng <span style="color:red;">*</span></label>
                    <select id="noi_nhap_hang_id" name="noi_nhap_hang_id" required>
                        <option value="">-- Chọn nơi nhập hàng --</option>
                        <?php foreach ($suppliers as $sup): ?>
                            <option value="<?= e($sup['id']) ?>" <?= isset($product['noi_nhap_hang_id']) && (string)$product['noi_nhap_hang_id'] === (string)$sup['id'] ? 'selected' : '' ?>>
                                <?= e($sup['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Ảnh sản phẩm -->
                <div class="form-group full-width">
                    <label for="anh">Ảnh sản phẩm <small style="color:#64748b;font-weight:normal">(Chấp nhận JPG, PNG, WEBP, GIF — Tối đa 5 MB)</small></label>
                    <input type="file" id="anh" name="anh" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewSelectedImage(this)">

                    <div class="image-preview-wrap" id="imagePreviewContainer" style="<?= ($isEdit && !empty($product['anh'])) ? '' : 'display:none;' ?>">
                        <img id="imagePreviewImg" src="<?= ($isEdit && !empty($product['anh'])) ? BASE_ASSETS_UPLOADS . e($product['anh']) : '' ?>" alt="Product image preview" class="image-preview">
                        <span id="imagePreviewText" style="font-size:13px; color:#64748b;">
                            <?= ($isEdit && !empty($product['anh'])) ? 'Ảnh hiện tại: ' . e($product['anh']) : '' ?>
                        </span>
                    </div>
                </div>

                <!-- Giới thiệu -->
                <div class="form-group full-width">
                    <label for="gioi_thieu">Giới thiệu sản phẩm</label>
                    <textarea id="gioi_thieu" name="gioi_thieu" placeholder="Nhập mô tả giới thiệu chi tiết sản phẩm..."><?= e($product['gioi_thieu'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Khu vực Biến thể / Size sản phẩm -->
        <div class="admin-card" style="margin-top: 24px;">
            <div class="variant-header">
                <div>
                    <h4 style="font-size:18px; margin:0;">Biến thể / Size sản phẩm</h4>
                    <p style="margin:4px 0 0; color:#64748b; font-size:13px;">Quản lý từng kích thước, giá nhập, giá bán và số lượng tồn kho</p>
                </div>
                <button type="button" class="btn btn-success btn-sm" onclick="openAddVariantModal()">+ Thêm size</button>
            </div>

            <div style="overflow-x: auto;">
                <table class="variant-table" id="variantTable">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Giá nhập (VNĐ)</th>
                            <th>Giá bán (VNĐ)</th>
                            <th>Số lượng tồn</th>
                            <th style="width: 100px; text-align: right;">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody id="variantTbody">
                        <?php if (!empty($variants)): ?>
                            <?php foreach ($variants as $idx => $v): ?>
                                <tr id="variant-row-<?= $idx ?>">
                                    <td>
                                        <strong><?= e($v['size']) ?></strong>
                                        <input type="hidden" name="variants[<?= $idx ?>][size]" value="<?= e($v['size']) ?>">
                                    </td>
                                    <td>
                                        <?= number_format($v['gia_nhap'], 0, ',', '.') ?> đ
                                        <input type="hidden" name="variants[<?= $idx ?>][gia_nhap]" value="<?= e($v['gia_nhap']) ?>">
                                    </td>
                                    <td>
                                        <?= number_format($v['gia_ban'], 0, ',', '.') ?> đ
                                        <input type="hidden" name="variants[<?= $idx ?>][gia_ban]" value="<?= e($v['gia_ban']) ?>">
                                    </td>
                                    <td>
                                        <?= e($v['so_luong']) ?>
                                        <input type="hidden" name="variants[<?= $idx ?>][so_luong]" value="<?= e($v['so_luong']) ?>">
                                    </td>
                                    <td style="text-align: right;">
                                        <button type="button" class="btn btn-warning btn-sm" style="background:#f59e0b;color:#fff;border:none;margin-right:4px;" onclick="editVariantRow(this)">Sửa</button>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Xóa</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="empty-variant-row">
                                <td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">
                                    Chưa có biến thể size nào. Vui lòng bấm "+ Thêm size" để bổ sung.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="admin-card" style="margin-top: 24px;">
            <div class="form-actions" style="margin:0; padding:0; border:0;">
                <a href="<?= BASE_URL ?>?action=admin-products" class="btn btn-secondary">Hủy bỏ</a>
                <button type="submit" class="btn btn-primary" style="min-width: 160px;"><?= $isEdit ? 'Lưu thay đổi' : 'Tạo sản phẩm' ?></button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Thêm / Sửa Size -->
<div class="modal-overlay" id="variantModal">
    <div class="modal-card">
        <h3 style="margin-top:0; color:#0f172a;" id="modalTitle">+ Thêm size biến thể mới</h3>
        
        <div class="form-group" style="margin-bottom:12px;">
            <label for="modal_size">Size (Kích thước) <span style="color:red;">*</span></label>
            <input type="text" id="modal_size" placeholder="Ví dụ: S, M, L, XL, 29, 30...">
        </div>

        <div class="form-group" style="margin-bottom:12px;">
            <label for="modal_gia_nhap">Giá nhập (VNĐ) <span style="color:red;">*</span></label>
            <input type="number" id="modal_gia_nhap" min="1" step="1000" placeholder="Nhập giá nhập (> 0)...">
        </div>

        <div class="form-group" style="margin-bottom:12px;">
            <label for="modal_gia_ban">Giá bán (VNĐ) <span style="color:red;">*</span></label>
            <input type="number" id="modal_gia_ban" min="1" step="1000" placeholder="Nhập giá bán (> 0)...">
        </div>

        <div class="form-group" style="margin-bottom:20px;">
            <label for="modal_so_luong">Số lượng tồn kho <span style="color:red;">*</span></label>
            <input type="number" id="modal_so_luong" min="0" step="1" value="0" placeholder="Nhập số lượng (>= 0)...">
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end;">
            <button type="button" class="btn btn-secondary" onclick="closeAddVariantModal()">Hủy</button>
            <button type="button" class="btn btn-primary" id="modalSubmitBtn" onclick="submitAddVariantModal()">Thêm vào danh sách</button>
        </div>
    </div>
</div>

<script>
let variantCounter = <?= count($variants) ?>;
let editingRow = null;

function openAddVariantModal() {
    editingRow = null;
    document.getElementById('modalTitle').innerText = '+ Thêm size biến thể mới';
    document.getElementById('modalSubmitBtn').innerText = 'Thêm vào danh sách';
    document.getElementById('modal_size').value = '';
    document.getElementById('modal_gia_nhap').value = '';
    document.getElementById('modal_gia_ban').value = '';
    document.getElementById('modal_so_luong').value = '0';
    document.getElementById('variantModal').style.display = 'flex';
}

function editVariantRow(btn) {
    editingRow = btn.closest('tr');
    const sizeInput    = editingRow.querySelector('input[name$="[size]"]').value;
    const giaNhapInput = editingRow.querySelector('input[name$="[gia_nhap]"]').value;
    const giaBanInput  = editingRow.querySelector('input[name$="[gia_ban]"]').value;
    const soLuongInput = editingRow.querySelector('input[name$="[so_luong]"]').value;

    document.getElementById('modalTitle').innerText = '✏️ Chỉnh sửa biến thể size "' + sizeInput + '"';
    document.getElementById('modalSubmitBtn').innerText = 'Cập nhật biến thể';
    document.getElementById('modal_size').value = sizeInput;
    document.getElementById('modal_gia_nhap').value = giaNhapInput;
    document.getElementById('modal_gia_ban').value = giaBanInput;
    document.getElementById('modal_so_luong').value = soLuongInput;
    document.getElementById('variantModal').style.display = 'flex';
}

function closeAddVariantModal() {
    document.getElementById('variantModal').style.display = 'none';
    editingRow = null;
}

function submitAddVariantModal() {
    const sizeInput = document.getElementById('modal_size').value.trim().toUpperCase();
    const giaNhapInput = parseFloat(document.getElementById('modal_gia_nhap').value);
    const giaBanInput = parseFloat(document.getElementById('modal_gia_ban').value);
    const soLuongInput = parseInt(document.getElementById('modal_so_luong').value);

    if (!sizeInput) {
        alert('Vui lòng nhập Size sản phẩm.');
        return;
    }

    if (isNaN(giaNhapInput) || giaNhapInput <= 0) {
        alert('Giá nhập phải lớn hơn 0.');
        return;
    }

    if (isNaN(giaBanInput) || giaBanInput <= 0) {
        alert('Giá bán phải lớn hơn 0.');
        return;
    }

    if (isNaN(soLuongInput) || soLuongInput < 0) {
        alert('Số lượng tồn phải lớn hơn hoặc bằng 0.');
        return;
    }

    // Kiểm tra trùng size trong bảng ngoại trừ dòng đang sửa
    const existingInputs = Array.from(document.querySelectorAll('#variantTbody input[name$="[size]"]'));
    const isDuplicate = existingInputs.some(input => {
        if (editingRow && input.closest('tr') === editingRow) return false;
        return input.value.toUpperCase() === sizeInput;
    });

    if (isDuplicate) {
        alert('Size "' + sizeInput + '" đã tồn tại trong danh sách!');
        return;
    }

    const formattedGiaNhap = new Intl.NumberFormat('vi-VN').format(giaNhapInput) + ' đ';
    const formattedGiaBan = new Intl.NumberFormat('vi-VN').format(giaBanInput) + ' đ';

    if (editingRow) {
        // Cập nhật dòng đang chỉnh sửa
        const idxMatch = editingRow.id.match(/\d+/);
        const idx = idxMatch ? idxMatch[0] : 0;

        editingRow.innerHTML = `
            <td>
                <strong>${escapeHtml(sizeInput)}</strong>
                <input type="hidden" name="variants[${idx}][size]" value="${escapeHtml(sizeInput)}">
            </td>
            <td>
                ${formattedGiaNhap}
                <input type="hidden" name="variants[${idx}][gia_nhap]" value="${giaNhapInput}">
            </td>
            <td>
                ${formattedGiaBan}
                <input type="hidden" name="variants[${idx}][gia_ban]" value="${giaBanInput}">
            </td>
            <td>
                ${soLuongInput}
                <input type="hidden" name="variants[${idx}][so_luong]" value="${soLuongInput}">
            </td>
            <td style="text-align: right;">
                <button type="button" class="btn btn-warning btn-sm" style="background:#f59e0b;color:#fff;border:none;margin-right:4px;" onclick="editVariantRow(this)">Sửa</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Xóa</button>
            </td>
        `;
    } else {
        // Thêm dòng mới
        const emptyRow = document.getElementById('empty-variant-row');
        if (emptyRow) {
            emptyRow.remove();
        }

        const tbody = document.getElementById('variantTbody');
        const idx = variantCounter++;

        const tr = document.createElement('tr');
        tr.id = 'variant-row-' + idx;
        tr.innerHTML = `
            <td>
                <strong>${escapeHtml(sizeInput)}</strong>
                <input type="hidden" name="variants[${idx}][size]" value="${escapeHtml(sizeInput)}">
            </td>
            <td>
                ${formattedGiaNhap}
                <input type="hidden" name="variants[${idx}][gia_nhap]" value="${giaNhapInput}">
            </td>
            <td>
                ${formattedGiaBan}
                <input type="hidden" name="variants[${idx}][gia_ban]" value="${giaBanInput}">
            </td>
            <td>
                ${soLuongInput}
                <input type="hidden" name="variants[${idx}][so_luong]" value="${soLuongInput}">
            </td>
            <td style="text-align: right;">
                <button type="button" class="btn btn-warning btn-sm" style="background:#f59e0b;color:#fff;border:none;margin-right:4px;" onclick="editVariantRow(this)">Sửa</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Xóa</button>
            </td>
        `;

        tbody.appendChild(tr);
    }

    closeAddVariantModal();
}

function removeVariantRow(btn) {
    const row = btn.closest('tr');
    row.remove();

    const tbody = document.getElementById('variantTbody');
    if (tbody.children.length === 0) {
        tbody.innerHTML = `
            <tr id="empty-variant-row">
                <td colspan="5" style="text-align:center; color:#94a3b8; padding:20px;">
                    Chưa có biến thể size nào. Vui lòng bấm "+ Thêm size" để bổ sung.
                </td>
            </tr>
        `;
    }
}

function escapeHtml(text) {
    return text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function previewSelectedImage(input) {
    const container = document.getElementById('imagePreviewContainer');
    const img = document.getElementById('imagePreviewImg');
    const text = document.getElementById('imagePreviewText');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 5 * 1024 * 1024) {
            alert('File ảnh quá lớn! Vui lòng chọn file dưới 5 MB.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            img.src = e.target.result;
            text.textContent = 'Ảnh mới đã chọn: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
            container.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    }
}
</script>
