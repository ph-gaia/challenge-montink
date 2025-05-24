<?php

namespace App\Models;

use Core\Database\ModelAbstract;

class OrdersModel extends ModelAbstract
{
    public function create(array $data): int
    {
        $sql = "INSERT INTO orders (
                    customer_name, 
                    customer_email, 
                    subtotal, 
                    shipping, 
                    discount, 
                    total, 
                    coupon_id
                ) VALUES (:customer_name, :customer_email, :subtotal, :shipping, :discount, :total, :coupon_id)";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($data);

        return $this->pdo()->lastInsertId();
    }

    public function addOrderItem(int $orderId, array $data): int
    {
        $sql = "INSERT INTO order_items (
                    order_id, 
                    product_id, 
                    quantity, 
                    unit_price,
                    total_price
                ) VALUES (:order_id, :product_id, :quantity, :unit_price, :total_price)";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            'order_id' => $orderId,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'unit_price' => $data['unit_price'],
            'total_price' => $data['total_price']
        ]);

        return $this->pdo()->lastInsertId();
    }

    public function getOrderById(int $id): ?array
    {
        $sql = "SELECT o.*, c.code as coupon_code 
                FROM orders o 
                LEFT JOIN coupons c ON o.coupon_id = c.id 
                WHERE o.id = ? 
                LIMIT 1";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function getOrderItems(int $orderId): array
    {
        $sql = "SELECT oi.*, p.name as product_name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.id 
                WHERE oi.order_id = ?";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}
