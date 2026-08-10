<?php

class Supplier extends BaseModel
{
    protected $table = 'noi_nhap_hang';

    /**
     * Lấy danh sách nơi nhập hàng (tìm kiếm theo tên hoặc số điện thoại).
     */
    public function getAll($keyword = '')
    {
        $keyword = trim($keyword);
        $sql = "
            SELECT 
                s.*, 
                COUNT(p.id) AS product_count
            FROM {$this->table} s
            LEFT JOIN san_pham p ON s.id = p.noi_nhap_hang_id
        ";

        $params = [];
        if ($keyword !== '') {
            $sql .= " WHERE s.name LIKE :keyword OR s.so_dien_thoai LIKE :keyword OR s.dia_chi LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql .= " GROUP BY s.id ORDER BY s.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Tìm nơi nhập hàng theo ID.
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Kiểm tra tên nơi nhập hàng bị trùng lặp hay không.
     */
    public function nameExists($name, $exceptId = null)
    {
        $name = trim($name);
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE LOWER(name) = LOWER(:name)";
        $params = [':name' => $name];

        if ($exceptId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = (int) $exceptId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách sản phẩm thuộc nơi nhập hàng này.
     */
    public function getProducts($id)
    {
        $sql = "
            SELECT 
                p.*, 
                c.name AS category_name,
                COALESCE(MIN(v.gia_ban), 0) AS gia_tu,
                COALESCE(MAX(v.gia_ban), 0) AS gia_den,
                COALESCE(SUM(v.so_luong), 0) AS ton_kho
            FROM san_pham p
            LEFT JOIN danh_muc c ON p.danh_muc_id = c.id
            LEFT JOIN chi_tiet_san_pham v ON p.id = v.san_pham_id
            WHERE p.noi_nhap_hang_id = :id
            GROUP BY p.id
            ORDER BY p.id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        return $stmt->fetchAll();
    }

    /**
     * Thêm nơi nhập hàng.
     */
    public function create($data)
    {
        $sql = "
            INSERT INTO {$this->table} (name, dia_chi, so_dien_thoai, ghi_chu)
            VALUES (:name, :dia_chi, :so_dien_thoai, :ghi_chu)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':name'          => trim($data['name']),
            ':dia_chi'       => !empty($data['dia_chi']) ? trim($data['dia_chi']) : null,
            ':so_dien_thoai' => !empty($data['so_dien_thoai']) ? trim($data['so_dien_thoai']) : null,
            ':ghi_chu'       => !empty($data['ghi_chu']) ? trim($data['ghi_chu']) : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật nơi nhập hàng.
     */
    public function update($id, $data)
    {
        $sql = "
            UPDATE {$this->table}
            SET name = :name,
                dia_chi = :dia_chi,
                so_dien_thoai = :so_dien_thoai,
                ghi_chu = :ghi_chu
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id'            => (int) $id,
            ':name'          => trim($data['name']),
            ':dia_chi'       => !empty($data['dia_chi']) ? trim($data['dia_chi']) : null,
            ':so_dien_thoai' => !empty($data['so_dien_thoai']) ? trim($data['so_dien_thoai']) : null,
            ':ghi_chu'       => !empty($data['ghi_chu']) ? trim($data['ghi_chu']) : null,
        ]);
    }

    /**
     * Kiểm tra xem nơi nhập hàng có sản phẩm đang sử dụng hay không.
     */
    public function hasProducts($id)
    {
        $sql = "SELECT COUNT(*) FROM san_pham WHERE noi_nhap_hang_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Xóa nơi nhập hàng nếu không có sản phẩm nào sử dụng.
     */
    public function delete($id)
    {
        if ($this->hasProducts($id)) {
            return false;
        }

        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => (int) $id]);
    }
}
