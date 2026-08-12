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
            $insert=$this->pdo->prepare('INSERT INTO orders (order_code,user_id,recipient_name,recipient_phone,recipient_address,note,payment_method,status,total_amount) VALUES (:code,:user_id,:name,:phone,:address,:note,\'cod\',\'pending\',:total)');
            $insert->execute([':code'=>$code,':user_id'=>$userId,':name'=>$recipient['name'],':phone'=>$recipient['phone'],':address'=>$recipient['address'],':note'=>$recipient['note']?:null,':total'=>$total]);$orderId=(int)$this->pdo->lastInsertId();
            $itemInsert=$this->pdo->prepare('INSERT INTO order_items (order_id,san_pham_id,chi_tiet_san_pham_id,product_name,size,gia_ban,so_luong,thanh_tien) VALUES (:order_id,:product_id,:variant_id,:name,:size,:price,:quantity,:line_total)');$stock=$this->pdo->prepare('UPDATE chi_tiet_san_pham SET so_luong=so_luong-:quantity WHERE id=:id AND so_luong>=:quantity');
            foreach($lines as [$v,$q,$line]){$itemInsert->execute([':order_id'=>$orderId,':product_id'=>$v['san_pham_id'],':variant_id'=>$v['id'],':name'=>$v['name'],':size'=>$v['size'],':price'=>$v['gia_ban'],':quantity'=>$q,':line_total'=>$line]);$stock->execute([':id'=>$v['id'],':quantity'=>$q]);if($stock->rowCount()!==1)throw new RuntimeException('Không thể cập nhật tồn kho.');}
            $this->pdo->commit(); return ['id'=>$orderId,'code'=>$code,'total'=>$total];
        } catch(Throwable $e) { if($this->pdo->inTransaction())$this->pdo->rollBack(); throw $e; }
    }
    public function getByUser(int $userId): array {$s=$this->pdo->prepare('SELECT * FROM orders WHERE user_id=:id ORDER BY id DESC');$s->execute([':id'=>$userId]);return $s->fetchAll();}
    public function findForUser(int $id,int $userId): ?array {$s=$this->pdo->prepare('SELECT * FROM orders WHERE id=:id AND user_id=:user_id');$s->execute([':id'=>$id,':user_id'=>$userId]);$o=$s->fetch();if(!$o)return null;$i=$this->pdo->prepare('SELECT * FROM order_items WHERE order_id=:id ORDER BY id');$i->execute([':id'=>$id]);$o['items']=$i->fetchAll();return $o;}
    public function getAllAdmin(): array {return $this->pdo->query('SELECT o.*,u.full_name AS customer_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.id DESC')->fetchAll();}
    public function updateStatus(int $id,string $status): bool {if(!in_array($status,['pending','confirmed','completed','cancelled'],true))throw new InvalidArgumentException('Trạng thái không hợp lệ.');$s=$this->pdo->prepare('UPDATE orders SET status=:status WHERE id=:id');return $s->execute([':id'=>$id,':status'=>$status]);}
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
