<?php

class Cart extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Lấy danh sách sản phẩm trong giỏ hàng đồng thời kiểm tra và đồng bộ giá/tồn kho từ DB.
     */
    public function getItems(&$warnings = [])
    {
        $cart = $_SESSION['cart'] ?? [];
        $validatedCart = [];

        foreach ($cart as $variantId => $item) {
            $variantId = (int) $variantId;

            // Truy vấn thông tin biến thể mới nhất từ DB
            $sql = "
                SELECT 
                    v.id AS variant_id,
                    v.san_pham_id,
                    v.size,
                    v.gia_ban,
                    v.so_luong AS ton_kho,
                    p.name AS product_name,
                    p.anh AS product_image
                FROM chi_tiet_san_pham v
                JOIN san_pham p ON v.san_pham_id = p.id
                WHERE v.id = :variant_id
                LIMIT 1
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':variant_id' => $variantId]);
            $dbVariant = $stmt->fetch();

            // Nếu biến thể hoặc sản phẩm đã bị xóa hoặc hết hàng hoàn toàn trong DB
            if (!$dbVariant || (int)$dbVariant['ton_kho'] <= 0) {
                $warnings[] = "Sản phẩm '{$item['product_name']} (Size {$item['size']})' đã hết hàng và được xóa khỏi giỏ hàng.";
                unset($_SESSION['cart'][$variantId]);
                continue;
            }

            $currentStock = (int) $dbVariant['ton_kho'];
            $cartQty = (int) $item['quantity'];

            // Nếu số lượng trong giỏ vượt quá tồn kho thực tế
            if ($cartQty > $currentStock) {
                $warnings[] = "Sản phẩm '{$dbVariant['product_name']} (Size {$dbVariant['size']})' chỉ còn {$currentStock} sản phẩm. Số lượng trong giỏ đã được điều chỉnh.";
                $cartQty = $currentStock;
            }

            $unitPrice = (float) $dbVariant['gia_ban'];
            $subtotal = $unitPrice * $cartQty;

            // Cập nhật lại session
            $_SESSION['cart'][$variantId]['quantity'] = $cartQty;
            $_SESSION['cart'][$variantId]['price']    = $unitPrice;
            $_SESSION['cart'][$variantId]['stock']    = $currentStock;

            $validatedCart[$variantId] = [
                'variant_id'    => $variantId,
                'san_pham_id'   => (int) $dbVariant['san_pham_id'],
                'product_name'  => $dbVariant['product_name'],
                'product_image' => $dbVariant['product_image'],
                'size'          => $dbVariant['size'],
                'price'         => $unitPrice,
                'quantity'      => $cartQty,
                'stock'         => $currentStock,
                'subtotal'      => $subtotal,
            ];
        }

        return $validatedCart;
    }

    /**
     * Thêm sản phẩm vào giỏ hàng dựa trên SanPhamID và VariantID (Size).
     */
    public function add($productId, $variantId, $quantity = 1)
    {
        $productId = (int) $productId;
        $variantId = (int) $variantId;
        $quantity  = max(1, (int) $quantity);

        if ($productId <= 0 || $variantId <= 0) {
            throw new Exception("Vui lòng chọn Size sản phẩm hợp lệ trước khi thêm vào giỏ hàng.");
        }

        // Kiểm tra biến thể trong DB
        $sql = "
            SELECT 
                v.id AS variant_id,
                v.san_pham_id,
                v.size,
                v.gia_ban,
                v.so_luong AS ton_kho,
                p.name AS product_name,
                p.anh AS product_image
            FROM chi_tiet_san_pham v
            JOIN san_pham p ON v.san_pham_id = p.id
            WHERE v.id = :variant_id AND v.san_pham_id = :san_pham_id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':variant_id'  => $variantId,
            ':san_pham_id' => $productId,
        ]);

        $dbVariant = $stmt->fetch();
        if (!$dbVariant) {
            throw new Exception("Biến thể size sản phẩm được chọn không tồn tại.");
        }

        $stock = (int) $dbVariant['ton_kho'];
        if ($stock <= 0) {
            throw new Exception("Rất tiếc, sản phẩm '{$dbVariant['product_name']} (Size {$dbVariant['size']})' đã hết hàng.");
        }

        $currentInCart = isset($_SESSION['cart'][$variantId]) ? (int) $_SESSION['cart'][$variantId]['quantity'] : 0;
        $newQty = $currentInCart + $quantity;

        if ($newQty > $stock) {
            $availableToAdd = $stock - $currentInCart;
            if ($availableToAdd <= 0) {
                throw new Exception("Bạn đã có {$currentInCart} sản phẩm size '{$dbVariant['size']}' trong giỏ hàng. Không thể thêm quá số lượng tồn kho (Còn {$stock} SP).");
            } else {
                throw new Exception("Số lượng yêu cầu vượt quá kho. Bạn chỉ có thể thêm tối đa {$availableToAdd} sản phẩm nữa.");
            }
        }

        $_SESSION['cart'][$variantId] = [
            'variant_id'    => $variantId,
            'san_pham_id'   => $productId,
            'product_name'  => $dbVariant['product_name'],
            'product_image' => $dbVariant['product_image'],
            'size'          => $dbVariant['size'],
            'price'         => (float) $dbVariant['gia_ban'],
            'quantity'      => $newQty,
            'stock'         => $stock,
        ];

        return [
            'product_name' => $dbVariant['product_name'],
            'size'         => $dbVariant['size'],
            'quantity'     => $newQty,
        ];
    }

    /**
     * Cập nhật số lượng của 1 sản phẩm trong giỏ.
     */
    public function updateQuantity($variantId, $quantity)
    {
        $variantId = (int) $variantId;
        $quantity  = (int) $quantity;

        if (!isset($_SESSION['cart'][$variantId])) {
            return false;
        }

        if ($quantity <= 0) {
            return $this->remove($variantId);
        }

        // Kiểm tra kho thực tế
        $sql = "SELECT so_luong FROM chi_tiet_san_pham WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $variantId]);
        $stock = (int) $stmt->fetchColumn();

        if ($quantity > $stock) {
            $_SESSION['cart'][$variantId]['quantity'] = $stock;
            throw new Exception("Số lượng đã được điều chỉnh về tối đa tồn kho khả dụng ({$stock} sản phẩm).");
        }

        $_SESSION['cart'][$variantId]['quantity'] = $quantity;
        return true;
    }

    /**
     * Xóa 1 biến thể sản phẩm khỏi giỏ hàng.
     */
    public function remove($variantId)
    {
        $variantId = (int) $variantId;
        if (isset($_SESSION['cart'][$variantId])) {
            unset($_SESSION['cart'][$variantId]);
            return true;
        }
        return false;
    }

    /**
     * Xóa toàn bộ giỏ hàng.
     */
    public function clear()
    {
        $_SESSION['cart'] = [];
        return true;
    }

    /**
     * Lấy tổng số lượng sản phẩm trong giỏ hàng.
     */
    public function getTotalQuantity()
    {
        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach ($cart as $item) {
            $total += (int) ($item['quantity'] ?? 0);
        }
        return $total;
    }

    /**
     * Lấy tổng thành tiền của giỏ hàng.
     */
    public function getTotalAmount()
    {
        $items = $this->getItems();
        $total = 0;
        foreach ($items as $item) {
            $total += (float) ($item['subtotal'] ?? 0);
        }
        return $total;
    }
}
