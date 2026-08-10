<?php

class Category extends BaseModel
{
    protected $table = 'danh_muc';

    /**
     * Lấy tất cả danh mục (có thể tìm kiếm theo tên) kèm số lượng sản phẩm.
     */
    public function getAll($keyword = '')
    {
        $keyword = trim($keyword);
        $sql = "
            SELECT 
                c.*, 
                COUNT(p.id) AS product_count
            FROM {$this->table} c
            LEFT JOIN san_pham p ON c.id = p.danh_muc_id
        ";

        $params = [];
        if ($keyword !== '') {
            $sql .= " WHERE c.name LIKE :keyword";
            $params[':keyword'] = '%' . $keyword . '%';
        }

        $sql .= " GROUP BY c.id ORDER BY c.id DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Tìm danh mục theo ID.
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        return $stmt->fetch() ?: null;
    }

    /**
     * Kiểm tra tên danh mục trùng lập.
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
     * Thêm danh mục mới.
     */
    public function create($name)
    {
        $name = trim($name);
        $sql = "INSERT INTO {$this->table} (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':name' => $name]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật danh mục.
     */
    public function update($id, $name)
    {
        $name = trim($name);
        $sql = "UPDATE {$this->table} SET name = :name WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $name,
            ':id'   => (int) $id,
        ]);
    }

    /**
     * Kiểm tra danh mục có chứa sản phẩm hay không.
     */
    public function hasProducts($id)
    {
        $sql = "SELECT COUNT(*) FROM san_pham WHERE danh_muc_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Xóa danh mục an toàn.
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
