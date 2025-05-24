<?php

namespace App\Models;

use Core\Database\ModelAbstract;

class CouponsModel extends ModelAbstract
{
    public function getValidCoupon(string $code, float $subtotal): ?array
    {
        $sql = "SELECT * FROM coupons 
                WHERE code = ? 
                AND status = 'active'
                AND valid_until > NOW() 
                AND minimum_value <= ? 
                LIMIT 1";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$code, $subtotal]);
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO coupons (code, discount_type, discount_value, minimum_value, valid_until, status) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            $data['code'],
            $data['discount_type'],
            $data['discount_value'],
            $data['minimum_value'],
            $data['valid_until'],
            $data['status'] ?? 'active'
        ]);

        return $this->pdo()->lastInsertId();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $sql = "UPDATE coupons SET status = ? WHERE id = ?";
        $stmt = $this->pdo()->prepare($sql);
        return $stmt->execute([$status, $id]);
    }
}
