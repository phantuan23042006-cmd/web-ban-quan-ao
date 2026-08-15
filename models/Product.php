<?php

class Product extends BaseModel
{
    protected $table = 'san_pham';

        public function getCatalog(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $params = [];
        $keyword = preg_replace('/\s+/u', ' ', trim((string) ($filters['keyword'] ?? '')));
        if ($keyword !== '') {
            foreach (preg_split('/\s+/u', $keyword) ?: [] as $index => $word) {
                $key = ':keyword_' . $index;
                $conditions[] = "p.name LIKE {$key}";
                $params[$key] = '%' . $word . '%';
            }
        }
        if (!empty($filters['danh_muc_id'])) {
            $conditions[] = 'p.danh_muc_id = :danh_muc_id';
            $params[':danh_muc_id'] = (int) $filters['danh_muc_id'];
        }
        $having = [];
        if (($filters['min_price'] ?? '') !== '') { $having[] = 'gia_den >= :min_price'; $params[':min_price'] = (float) $filters['min_price']; }
        if (($filters['max_price'] ?? '') !== '') { $having[] = 'gia_tu <= :max_price'; $params[':max_price'] = (float) $filters['max_price']; }

        $sorts = [
            'price_asc'  => 'gia_tu ASC, p.id DESC',
            'price_desc' => 'gia_tu DESC, p.id DESC',
            'rating'     => 'danh_gia_tb DESC, tong_danh_gia DESC, p.id DESC'
        ];

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $havingSql = $having ? 'HAVING ' . implode(' AND ', $having) : '';
        $orderBy = $sorts[$filters['sort'] ?? ''] ?? 'p.id DESC';

        $countSql = "SELECT COUNT(*) FROM (
                        SELECT p.id,
                               COALESCE(MIN(v.gia_ban), 0) AS gia_tu,
                               COALESCE(MAX(v.gia_ban), 0) AS gia_den
                        FROM {$this->table} p
                        LEFT JOIN chi_tiet_san_pham v ON v.san_pham_id = p.id
                        {$where}
                        GROUP BY p.id
                        {$havingSql}
                    ) AS sub";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = (int) $countStmt->fetchColumn();

        $sql = "SELECT p.id, p.name, p.gioi_thieu, p.anh, p.danh_muc_id, c.name AS category_name,
                    COALESCE(MIN(v.gia_ban), 0) AS gia_tu, COALESCE(MAX(v.gia_ban), 0) AS gia_den,
                    COALESCE(SUM(v.so_luong), 0) AS ton_kho, ROUND(COALESCE(AVG(r.so_sao), 0), 1) AS danh_gia_tb,
                    COUNT(DISTINCT r.id) AS tong_danh_gia
                FROM {$this->table} p
                LEFT JOIN danh_muc c ON c.id = p.danh_muc_id
                LEFT JOIN chi_tiet_san_pham v ON v.san_pham_id = p.id
                LEFT JOIN danh_gia r ON r.san_pham_id = p.id
                {$where} GROUP BY p.id {$havingSql} ORDER BY {$orderBy} LIMIT {$perPage} OFFSET {$offset}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return [
            'items'        => $items,
            'total_items'  => $totalItems,
            'total_pages'  => max(1, (int) ceil($totalItems / $perPage)),
            'current_page' => $page,
            'per_page'     => $perPage,
        ];
    }

    /**
     * Lấy danh sách sản phẩm dành cho Admin với các trường tính toán:
     * - Giá từ (gia_tu): giá bán nhỏ nhất
     * - Giá đến (gia_den): giá bán lớn nhất
     * - Tồn kho (ton_kho): tổng số lượng biến thể
     * - Đánh giá (danh_gia_tb): điểm trung bình từ bảng danh_gia
     */
    public function getAllAdmin($filters = [], $page = 1, $perPage = 10)
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $params = [];

        if (!empty($filters['keyword'])) {
            $conditions[] = "p.name LIKE :keyword";
            $params[':keyword'] = '%' . trim($filters['keyword']) . '%';
        }

        if (!empty($filters['danh_muc_id'])) {
            $conditions[] = "p.danh_muc_id = :danh_muc_id";
            $params[':danh_muc_id'] = (int) $filters['danh_muc_id'];
        }

        if (!empty($filters['noi_nhap_hang_id'])) {
            $conditions[] = "p.noi_nhap_hang_id = :noi_nhap_hang_id";
            $params[':noi_nhap_hang_id'] = (int) $filters['noi_nhap_hang_id'];
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Đếm tổng sản phẩm
        $countSql = "SELECT COUNT(*) FROM {$this->table} p {$whereClause}";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = (int) $countStmt->fetchColumn();

        // Lấy danh sách sản phẩm kèm theo thông tin tổng hợp
        $sql = "
            SELECT 
                p.id,
                p.name,
                p.gioi_thieu,
                p.anh,
                p.danh_muc_id,
                p.noi_nhap_hang_id,
                c.name AS category_name,
                s.name AS supplier_name,
                COALESCE(MIN(v.gia_ban), 0) AS gia_tu,
                COALESCE(MAX(v.gia_ban), 0) AS gia_den,
                COALESCE(SUM(v.so_luong), 0) AS ton_kho,
                ROUND(COALESCE(AVG(r.so_sao), 0), 1) AS danh_gia_tb,
                COUNT(DISTINCT r.id) AS tong_danh_gia
            FROM {$this->table} p
            LEFT JOIN danh_muc c ON p.danh_muc_id = c.id
            LEFT JOIN noi_nhap_hang s ON p.noi_nhap_hang_id = s.id
            LEFT JOIN chi_tiet_san_pham v ON p.id = v.san_pham_id
            LEFT JOIN danh_gia r ON p.id = r.san_pham_id
            {$whereClause}
            GROUP BY p.id
            ORDER BY p.id DESC
            LIMIT {$perPage} OFFSET {$offset}
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $items = $stmt->fetchAll();

        return [
            'items'       => $items,
            'total_items' => $totalItems,
            'total_pages' => max(1, (int) ceil($totalItems / $perPage)),
            'current_page'=> $page,
        ];
    }

    /**
     * Tìm thông tin chi tiết của 1 sản phẩm theo ID.
     */
    public function findById($id)
    {
        $sql = "
            SELECT 
                p.*,
                c.name AS category_name,
                s.name AS supplier_name,
                COALESCE(MIN(v.gia_ban), 0) AS gia_tu,
                COALESCE(MAX(v.gia_ban), 0) AS gia_den,
                COALESCE(SUM(v.so_luong), 0) AS ton_kho,
                ROUND(COALESCE(AVG(r.so_sao), 0), 1) AS danh_gia_tb,
                COUNT(DISTINCT r.id) AS tong_danh_gia
            FROM {$this->table} p
            LEFT JOIN danh_muc c ON p.danh_muc_id = c.id
            LEFT JOIN noi_nhap_hang s ON p.noi_nhap_hang_id = s.id
            LEFT JOIN chi_tiet_san_pham v ON p.id = v.san_pham_id
            LEFT JOIN danh_gia r ON p.id = r.san_pham_id
            WHERE p.id = :id
            GROUP BY p.id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        $product = $stmt->fetch();
        if (!$product) {
            return null;
        }

        // Lấy danh sách các size biến thể
        $sqlVariants = "SELECT * FROM chi_tiet_san_pham WHERE san_pham_id = :san_pham_id ORDER BY id ASC";
        $stmtVar = $this->pdo->prepare($sqlVariants);
        $stmtVar->execute([':san_pham_id' => (int) $id]);
        $product['variants'] = $stmtVar->fetchAll();

        return $product;
    }

    /**
     * Thêm sản phẩm kèm danh sách biến thể size trong Transaction.
     */
    public function createProductWithDetails($productData, $detailsData = [])
    {
        try {
            $this->pdo->beginTransaction();

            $sqlProduct = "
                INSERT INTO {$this->table} (name, gioi_thieu, anh, danh_muc_id, noi_nhap_hang_id)
                VALUES (:name, :gioi_thieu, :anh, :danh_muc_id, :noi_nhap_hang_id)
            ";

            $stmtProduct = $this->pdo->prepare($sqlProduct);
            $stmtProduct->execute([
                ':name'             => trim($productData['name']),
                ':gioi_thieu'        => !empty($productData['gioi_thieu']) ? trim($productData['gioi_thieu']) : null,
                ':anh'               => !empty($productData['anh']) ? trim($productData['anh']) : null,
                ':danh_muc_id'      => (int) $productData['danh_muc_id'],
                ':noi_nhap_hang_id' => (int) $productData['noi_nhap_hang_id'],
            ]);

            $productId = (int) $this->pdo->lastInsertId();

            if (!empty($detailsData)) {
                $sqlDetail = "
                    INSERT INTO chi_tiet_san_pham (san_pham_id, size, gia_nhap, gia_ban, so_luong)
                    VALUES (:san_pham_id, :size, :gia_nhap, :gia_ban, :so_luong)
                ";
                $stmtDetail = $this->pdo->prepare($sqlDetail);

                $seenSizes = [];
                foreach ($detailsData as $detail) {
                    $size = mb_strtoupper(trim($detail['size']));
                    if (in_array($size, $seenSizes, true)) {
                        throw new Exception("Khu vực biến thể: trùng lặp size '{$size}' cho sản phẩm này.");
                    }
                    $seenSizes[] = $size;

                    $giaNhap = (float) $detail['gia_nhap'];
                    $giaBan  = (float) $detail['gia_ban'];
                    $soLuong = (int) $detail['so_luong'];

                    if ($giaNhap <= 0) {
                        throw new Exception("Giá nhập của size '{$size}' phải lớn hơn 0.");
                    }
                    if ($giaBan <= 0) {
                        throw new Exception("Giá bán của size '{$size}' phải lớn hơn 0.");
                    }
                    if ($soLuong < 0) {
                        throw new Exception("Số lượng của size '{$size}' phải lớn hơn hoặc bằng 0.");
                    }

                    $stmtDetail->execute([
                        ':san_pham_id' => $productId,
                        ':size'        => $size,
                        ':gia_nhap'    => $giaNhap,
                        ':gia_ban'     => $giaBan,
                        ':so_luong'    => $soLuong,
                    ]);
                }
            }

            $this->pdo->commit();
            return $productId;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Cập nhật thông tin sản phẩm và đồng bộ danh sách biến thể trong Transaction.
     */
    public function updateProductWithDetails($id, $productData, $detailsData = null)
    {
        try {
            $this->pdo->beginTransaction();

            $sqlProduct = "
                UPDATE {$this->table}
                SET name = :name,
                    gioi_thieu = :gioi_thieu,
                    anh = :anh,
                    danh_muc_id = :danh_muc_id,
                    noi_nhap_hang_id = :noi_nhap_hang_id
                WHERE id = :id
            ";

            $stmtProduct = $this->pdo->prepare($sqlProduct);
            $stmtProduct->execute([
                ':id'               => (int) $id,
                ':name'             => trim($productData['name']),
                ':gioi_thieu'        => !empty($productData['gioi_thieu']) ? trim($productData['gioi_thieu']) : null,
                ':anh'               => !empty($productData['anh']) ? trim($productData['anh']) : null,
                ':danh_muc_id'      => (int) $productData['danh_muc_id'],
                ':noi_nhap_hang_id' => (int) $productData['noi_nhap_hang_id'],
            ]);

            // Nếu có truyền danh sách biến thể mới để làm mới
            if ($detailsData !== null) {
                // Xóa các biến thể cũ và tạo biến thể mới
                $this->pdo->exec("DELETE FROM chi_tiet_san_pham WHERE san_pham_id = " . (int) $id);

                if (!empty($detailsData)) {
                    $sqlDetail = "
                        INSERT INTO chi_tiet_san_pham (san_pham_id, size, gia_nhap, gia_ban, so_luong)
                        VALUES (:san_pham_id, :size, :gia_nhap, :gia_ban, :so_luong)
                    ";
                    $stmtDetail = $this->pdo->prepare($sqlDetail);

                    $seenSizes = [];
                    foreach ($detailsData as $detail) {
                        $size = mb_strtoupper(trim($detail['size']));
                        if (in_array($size, $seenSizes, true)) {
                            throw new Exception("Khu vực biến thể: trùng lặp size '{$size}' cho sản phẩm.");
                        }
                        $seenSizes[] = $size;

                        $giaNhap = (float) $detail['gia_nhap'];
                        $giaBan  = (float) $detail['gia_ban'];
                        $soLuong = (int) $detail['so_luong'];

                        if ($giaNhap <= 0) {
                            throw new Exception("Giá nhập của size '{$size}' phải lớn hơn 0.");
                        }
                        if ($giaBan <= 0) {
                            throw new Exception("Giá bán của size '{$size}' phải lớn hơn 0.");
                        }
                        if ($soLuong < 0) {
                            throw new Exception("Số lượng của size '{$size}' phải lớn hơn hoặc bằng 0.");
                        }

                        $stmtDetail->execute([
                            ':san_pham_id' => (int) $id,
                            ':size'        => $size,
                            ':gia_nhap'    => $giaNhap,
                            ':gia_ban'     => $giaBan,
                            ':so_luong'    => $soLuong,
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Xóa sản phẩm và các dữ liệu liên quan an toàn trong Transaction.
     */
    public function deleteProduct($id)
    {
        try {
            $this->pdo->beginTransaction();

            $id = (int) $id;
            $product = $this->findById($id);

            // Xóa biến thể và đánh giá
            $this->pdo->exec("DELETE FROM chi_tiet_san_pham WHERE san_pham_id = {$id}");
            $this->pdo->exec("DELETE FROM danh_gia WHERE san_pham_id = {$id}");

            // Xóa sản phẩm
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            $stmt->execute([':id' => $id]);

            $this->pdo->commit();

            // Nếu có ảnh, tiến hành xóa file ảnh cũ
            if ($product && !empty($product['anh'])) {
                $imagePath = PATH_ASSETS_UPLOADS . $product['anh'];
                if (file_exists($imagePath) && is_file($imagePath)) {
                    @unlink($imagePath);
                }
            }

            return true;

        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }
}
