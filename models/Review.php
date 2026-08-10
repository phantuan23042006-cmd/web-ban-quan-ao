<?php

class Review extends BaseModel
{
    protected $table = 'danh_gia';

    /**
     * Lấy danh sách đánh giá của một sản phẩm.
     */
    public function getByProductId($productId)
    {
        $sql = "
            SELECT 
                r.*, 
                u.full_name AS user_name,
                u.email AS user_email
            FROM {$this->table} r
            JOIN users u ON r.user_id = u.id
            WHERE r.san_pham_id = :san_pham_id
            ORDER BY r.created_at DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':san_pham_id' => (int) $productId]);

        return $stmt->fetchAll();
    }

    /**
     * Lấy điểm đánh giá trung bình và tổng số lượng đánh giá của sản phẩm.
     */
    public function getAverageRating($productId)
    {
        $sql = "
            SELECT 
                COALESCE(AVG(so_sao), 0) AS avg_stars,
                COUNT(*) AS total_reviews
            FROM {$this->table}
            WHERE san_pham_id = :san_pham_id
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':san_pham_id' => (int) $productId]);

        $result = $stmt->fetch();

        return [
            'avg_stars'     => round((float) ($result['avg_stars'] ?? 0), 1),
            'total_reviews' => (int) ($result['total_reviews'] ?? 0),
        ];
    }

    /**
     * Lấy danh sách đánh giá phục vụ trang quản trị (Admin) có phân trang và bộ lọc.
     */
    public function getAllAdmin($filters = [], $page = 1, $perPage = 10)
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;

        $conditions = [];
        $params = [];

        if (!empty($filters['san_pham_id'])) {
            $conditions[] = "r.san_pham_id = :san_pham_id";
            $params[':san_pham_id'] = (int) $filters['san_pham_id'];
        }

        if (!empty($filters['so_sao'])) {
            $conditions[] = "r.so_sao = :so_sao";
            $params[':so_sao'] = (int) $filters['so_sao'];
        }

        if (!empty($filters['keyword'])) {
            $conditions[] = "(p.name LIKE :keyword OR u.full_name LIKE :keyword OR r.noi_dung LIKE :keyword)";
            $params[':keyword'] = '%' . trim($filters['keyword']) . '%';
        }

        $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Đếm tổng số bản ghi
        $countSql = "
            SELECT COUNT(*) 
            FROM {$this->table} r
            JOIN san_pham p ON r.san_pham_id = p.id
            JOIN users u ON r.user_id = u.id
            {$whereClause}
        ";
        $countStmt = $this->pdo->prepare($countSql);
        $countStmt->execute($params);
        $totalItems = (int) $countStmt->fetchColumn();

        // Query lấy dữ liệu
        $sql = "
            SELECT 
                r.*,
                p.name AS product_name,
                p.anh AS product_image,
                u.full_name AS user_name,
                u.email AS user_email
            FROM {$this->table} r
            JOIN san_pham p ON r.san_pham_id = p.id
            JOIN users u ON r.user_id = u.id
            {$whereClause}
            ORDER BY r.created_at DESC
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
     * Tìm đánh giá theo ID.
     */
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => (int) $id]);

        return $stmt->fetch() ?: null;
    }

    public function findByUserProduct(int $userId, int $productId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM ' . $this->table . ' WHERE user_id = :user_id AND san_pham_id = :product_id ORDER BY id DESC LIMIT 1');
        $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);
        return $stmt->fetch() ?: null;
    }

    /**
     * Thêm đánh giá mới (dành cho khách hàng).
     */
    public function create($data)
    {
        $soSao = max(1, min(5, (int) $data['so_sao']));
        $sql = "
            INSERT INTO {$this->table} (san_pham_id, user_id, so_sao, noi_dung)
            VALUES (:san_pham_id, :user_id, :so_sao, :noi_dung)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':san_pham_id' => (int) $data['san_pham_id'],
            ':user_id'     => (int) $data['user_id'],
            ':so_sao'      => $soSao,
            ':noi_dung'    => !empty($data['noi_dung']) ? trim($data['noi_dung']) : null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật đánh giá (chính chủ).
     */
    public function update($id, $userId, $data)
    {
        $soSao = max(1, min(5, (int) $data['so_sao']));
        $sql = "
            UPDATE {$this->table}
            SET so_sao = :so_sao,
                noi_dung = :noi_dung,
                updated_at = NOW()
            WHERE id = :id AND user_id = :user_id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id'       => (int) $id,
            ':user_id'  => (int) $userId,
            ':so_sao'   => $soSao,
            ':noi_dung' => !empty($data['noi_dung']) ? trim($data['noi_dung']) : null,
        ]);
    }

    /**
     * Xóa đánh giá (cho phép truyền userId nếu là khách hàng xóa bài chính mình, hoặc null nếu admin xóa).
     */
    public function delete($id, $userId = null)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $params = [':id' => (int) $id];

        if ($userId !== null) {
            $sql .= " AND user_id = :user_id";
            $params[':user_id'] = (int) $userId;
        }

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute($params);
    }
}
