<?php
class Order extends BaseModel
{
    public function createFromCart(int $userId, array $recipient, array $cartItems): array
    {
        if (!$cartItems) throw new RuntimeException('Giỏ hàng đang trống.');
        $this->pdo->beginTransaction();
        try {
            $lock = $this->pdo->prepare('SELECT v.id, v.san_pham_id, v.size, v.gia_ban, v.so_luong, p.name FROM chi_tiet_san_pham v JOIN san_pham p ON p.id = v.san_pham_id WHERE v.id = :id FOR UPDATE');
            $lines=[]; $total=0.0;
            foreach($cartItems as $item){$quantity=(int)($item['quantity']??0);$variantId=(int)($item['variant_id']??0);if($quantity<1||$variantId<1)throw new RuntimeException('Số lượng sản phẩm không hợp lệ.');$lock->execute([':id'=>$variantId]);$variant=$lock->fetch();if(!$variant||(int)$variant['so_luong']<$quantity)throw new RuntimeException('Sản phẩm không đủ tồn kho.');$line=(float)$variant['gia_ban']*$quantity;$total+=$line;$lines[]=[$variant,$quantity,$line];}
            $code='DH'.date('Ymd').strtoupper(bin2hex(random_bytes(4)));

            $paymentMethod = $recipient['payment_method'] ?? 'cod';
            $validMethods = ['cod', 'qr_techcombank'];
            if (!in_array($paymentMethod, $validMethods, true)) {
                $paymentMethod = 'cod';
            }

            $insert=$this->pdo->prepare('INSERT INTO orders (order_code,user_id,recipient_name,recipient_phone,recipient_address,note,payment_method,payment_status,status,total_amount) VALUES (:code,:user_id,:name,:phone,:address,:note,:payment_method,\'unpaid\',\'pending\',:total)');
            $insert->execute([':code'=>$code,':user_id'=>$userId,':name'=>$recipient['name'],':phone'=>$recipient['phone'],':address'=>$recipient['address'],':note'=>$recipient['note']?:null,':payment_method'=>$paymentMethod,':total'=>$total]);$orderId=(int)$this->pdo->lastInsertId();
            $itemInsert=$this->pdo->prepare('INSERT INTO order_items (order_id,san_pham_id,chi_tiet_san_pham_id,product_name,size,gia_ban,so_luong,thanh_tien) VALUES (:order_id,:product_id,:variant_id,:name,:size,:price,:quantity,:line_total)');$stock=$this->pdo->prepare('UPDATE chi_tiet_san_pham SET so_luong=so_luong-:quantity WHERE id=:id AND so_luong>=:quantity');
            foreach($lines as [$v,$q,$line]){$itemInsert->execute([':order_id'=>$orderId,':product_id'=>$v['san_pham_id'],':variant_id'=>$v['id'],':name'=>$v['name'],':size'=>$v['size'],':price'=>$v['gia_ban'],':quantity'=>$q,':line_total'=>$line]);$stock->execute([':id'=>$v['id'],':quantity'=>$q]);if($stock->rowCount()!==1)throw new RuntimeException('Không thể cập nhật tồn kho.');}
            $this->pdo->commit(); return ['id'=>$orderId,'code'=>$code,'total'=>$total,'payment_method'=>$paymentMethod];
        } catch(Throwable $e) { if($this->pdo->inTransaction())$this->pdo->rollBack(); throw $e; }
    }

    public function updatePaymentStatus(int $id, string $paymentStatus): bool
    {
        $stmt = $this->pdo->prepare('UPDATE orders SET payment_status = :status WHERE id = :id');
        return $stmt->execute([':id' => $id, ':status' => $paymentStatus]);
    }
    public function getByUser(int $userId): array {$s=$this->pdo->prepare('SELECT * FROM orders WHERE user_id=:id ORDER BY id DESC');$s->execute([':id'=>$userId]);return $s->fetchAll();}
    public function findForUser(int $id,int $userId): ?array {$s=$this->pdo->prepare('SELECT * FROM orders WHERE id=:id AND user_id=:user_id');$s->execute([':id'=>$id,':user_id'=>$userId]);$o=$s->fetch();if(!$o)return null;$i=$this->pdo->prepare('SELECT * FROM order_items WHERE order_id=:id ORDER BY id');$i->execute([':id'=>$id]);$o['items']=$i->fetchAll();return $o;}
    public function getAllAdmin(): array {return $this->pdo->query('SELECT o.*,u.full_name AS customer_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();}
        public function updateStatus(int $id, string $status): bool
    {
        $checkStmt = $this->pdo->prepare('SELECT status FROM orders WHERE id = :id LIMIT 1');
        $checkStmt->execute([':id' => $id]);
        $currentStatus = $checkStmt->fetchColumn();

        if ($currentStatus === false) {
            throw new InvalidArgumentException('Đơn hàng không tồn tại.');
        }

        if ($currentStatus === $status) {
            return true;
        }

        if ($currentStatus === 'completed') {
            throw new InvalidArgumentException('Đơn hàng ở trạng thái "Đã giao" không thể thay đổi trạng thái.');
        }

        if ($currentStatus === 'cancelled') {
            throw new InvalidArgumentException('Đơn hàng ở trạng thái "Đã hủy" không thể thay đổi trạng thái.');
        }

        if ($currentStatus === 'pending' && !in_array($status, ['confirmed', 'cancelled'], true)) {
            throw new InvalidArgumentException('Đơn hàng "Chờ xác nhận" chỉ có thể chuyển sang "Xác nhận" hoặc "Đã hủy".');
        }

        if ($currentStatus === 'confirmed' && !in_array($status, ['completed', 'cancelled'], true)) {
            throw new InvalidArgumentException('Đơn hàng "Xác nhận" chỉ có thể chuyển sang "Đã giao" hoặc "Đã hủy".');
        }

        $this->pdo->beginTransaction();
        try {
            $s = $this->pdo->prepare('UPDATE orders SET status = :status WHERE id = :id');
            $s->execute([':id' => $id, ':status' => $status]);

            if ($status === 'cancelled') {
                $itemsStmt = $this->pdo->prepare('SELECT chi_tiet_san_pham_id, so_luong FROM order_items WHERE order_id = :id');
                $itemsStmt->execute([':id' => $id]);
                $items = $itemsStmt->fetchAll();

                $restoreStock = $this->pdo->prepare('UPDATE chi_tiet_san_pham SET so_luong = so_luong + :quantity WHERE id = :id');
                foreach ($items as $item) {
                    if (!empty($item['chi_tiet_san_pham_id'])) {
                        $restoreStock->execute([
                            ':quantity' => (int)$item['so_luong'],
                            ':id'       => (int)$item['chi_tiet_san_pham_id']
                        ]);
                    }
                }
            }

            $this->pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $e;
        }
    }

    public function cancelOrderForUser(int $orderId, int $userId): bool
    {
        $checkStmt = $this->pdo->prepare('SELECT status FROM orders WHERE id = :id AND user_id = :user_id LIMIT 1');
        $checkStmt->execute([':id' => $orderId, ':user_id' => $userId]);
        $currentStatus = $checkStmt->fetchColumn();

        if ($currentStatus === false) {
            throw new InvalidArgumentException('Đơn hàng không tồn tại hoặc không thuộc quyền sở hữu của bạn.');
        }

        return $this->updateStatus($orderId, 'cancelled');
    }

    public function getAllowedNextStatuses(string $currentStatus): array
    {
        switch ($currentStatus) {
            case 'pending':
                return [
                    'pending'   => 'Chờ xác nhận',
                    'confirmed' => 'Xác nhận',
                    'cancelled' => 'Đã hủy'
                ];
            case 'confirmed':
                return [
                    'confirmed' => 'Xác nhận',
                    'completed' => 'Đã giao',
                    'cancelled' => 'Đã hủy'
                ];
            case 'completed':
                return [
                    'completed' => 'Đã giao'
                ];
            case 'cancelled':
                return [
                    'cancelled' => 'Đã hủy'
                ];
            default:
                return [];
        }
    }
    public function canReview(int $userId, int $productId): bool
    {
        $s = $this->pdo->prepare("SELECT 1 FROM orders o JOIN order_items i ON i.order_id = o.id WHERE o.user_id = :user AND i.san_pham_id = :product AND o.status = 'completed' LIMIT 1");
        $s->execute([':user' => $userId, ':product' => $productId]);
        return (bool) $s->fetchColumn();
    }

    public function findForAdmin(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT o.*, u.full_name AS customer_name, u.email AS customer_email, u.phone AS customer_phone FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();
        if (!$order) return null;
        $items = $this->pdo->prepare('SELECT * FROM order_items WHERE order_id = :id ORDER BY id ASC');
        $items->execute([':id' => $id]);
        $order['items'] = $items->fetchAll();
        return $order;
    }
}
