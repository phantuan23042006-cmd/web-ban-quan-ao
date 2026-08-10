<?php

class ProductDetail extends BaseModel
{
    protected $table = 'chi_tiet_san_pham';

    /**
     * Lấy danh sách các size (biến thể) của sản phẩm.
     */
    public function getByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE san_pham_id = :san_pham_id ORDER BY id ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':san_pham_id' => (int) $productId]);

        return $stmt->fetchAll();
    }

    /**
     * Kiểm tra xem size đã tồn tại đối với sản phẩm này chưa.
     */
    public function sizeExists($productId, $size, $exceptDetailId = null)
    {
        $size = mb_strtoupper(trim($size));
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE san_pham_id = :san_pham_id AND UPPER(size) = :size";
        $params = [
            ':san_pham_id' => (int) $productId,
            ':size'        => $size,
        ];

        if ($exceptDetailId !== null) {
            $sql .= " AND id != :except_id";
            $params[':except_id'] = (int) $exceptDetailId;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    /**
     * Thêm size biến thể mới cho sản phẩm.
     */
    public function create($data)
    {
        $sql = "
            INSERT INTO {$this->table} (san_pham_id, size, gia_nhap, gia_ban, so_luong)
            VALUES (:san_pham_id, :size, :gia_nhap, :gia_ban, :so_luong)
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':san_pham_id' => (int) $data['san_pham_id'],
            ':size'        => mb_strtoupper(trim($data['size'])),
            ':gia_nhap'    => (float) $data['gia_nhap'],
            ':gia_ban'     => (float) $data['gia_ban'],
            ':so_luong'    => (int) $data['so_luong'],
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Cập nhật thông tin size biến thể.
     */
    public function update($id, $data)
    {
        $sql = "
            UPDATE {$this->table}
            SET size = :size,
                gia_nhap = :gia_nhap,
                gia_ban = :gia_ban,
                so_luong = :so_luong
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':id'       => (int) $id,
            ':size'     => mb_strtoupper(trim($data['size'])),
            ':gia_nhap' => (float) $data['gia_nhap'],
            ':gia_ban'  => (float) $data['gia_ban'],
            ':so_luong' => (int) $data['so_luong'],
        ]);
    }

    /**
     * Xóa 1 biến thể size.
     */
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':id' => (int) $id]);
    }

    /**
     * Xóa tất cả các size của sản phẩm.
     */
    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE san_pham_id = :san_pham_id";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([':san_pham_id' => (int) $productId]);
    }
}
