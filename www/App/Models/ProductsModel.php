<?php

namespace App\Models;

use Core\Database\ModelAbstract;
use App\Helpers\Paginator;
use Core\Http\Header;

class ProductsModel extends ModelAbstract
{

    private $header;

    private $entity = 'products';

    private $paginator;

    public function __construct()
    {
        parent::__construct();

        $this->header = new Header();
    }

    public function paginator($pagina)
    {
        $data = [
            'entidade' => $this->entity,
            'select' => '*',
            'pagina' => $pagina,
            'maxResult' => 5,
            'orderBy' => ''
        ];

        $this->paginator = new Paginator($data);
    }

    public function getResultPaginator()
    {
        return $this->paginator->getResultado();
    }

    public function getNavePaginator()
    {
        return $this->paginator->getNaveBtn();
    }

    public function getAllProducts(): array
    {
        $sql = "SELECT p.*, 
                (SELECT SUM(stock) FROM product_variations WHERE product_id = p.id) as total_stock 
                FROM products p";

        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProductById(int $id): ?array
    {
        $sql = "SELECT p.*, 
                (SELECT SUM(stock) FROM product_variations WHERE product_id = p.id) as total_stock 
                FROM products p 
                WHERE p.id = ?";
        
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([$id]);
        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($product) {
            $product['variations'] = $this->getProductVariations($id);
        }
        
        return $product;
    }

    public function createProduct(array $data): int
    {
        $sql = "INSERT INTO products (name, price, stock) VALUES (:name, :price, :stock)";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($data);
        return $this->pdo()->lastInsertId();
    }

    public function updateProduct(array $data): void
    {
        $sql = "UPDATE products SET name = :name, price = :price, stock = :stock WHERE id = :id";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($data);
    }

    public function getProductVariations(int $productId): array
    {
        $sql = "SELECT * FROM product_variations WHERE product_id = :product_id";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute(['product_id' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function createVariation(array $data): int
    {
        $sql = "INSERT INTO product_variations (product_id, name, stock, price_override) VALUES (:product_id, :name, :stock, :price_override)";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($data);
        return $this->pdo()->lastInsertId();
    }

    public function updateVariation(array $data): void
    {
        $sql = "UPDATE product_variations SET name = :name, stock = :stock, price_override = :price_override WHERE id = :id AND product_id = :product_id";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($data);
    }

    public function updateStock(int $productId, int $quantity): bool
    {
        $product = $this->getProductById($productId);
        
        if (!$product) {
            return false;
        }

        $totalStock = $product['total_stock'] ?? $product['stock'];
        
        if ($totalStock < $quantity) {
            return false;
        }

        // Update main product stock
        $sql = "UPDATE products SET stock = stock - :quantity WHERE id = :id";
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute([
            'quantity' => $quantity,
            'id' => $productId
        ]);

        if (!empty($product['variations'])) {
            $remainingQuantity = $quantity;
            foreach ($product['variations'] as $variation) {
                if ($remainingQuantity <= 0) {
                    break;
                }

                $deductQuantity = min($remainingQuantity, $variation['stock']);
                if ($deductQuantity > 0) {
                    $sql = "UPDATE product_variations SET stock = stock - :quantity WHERE id = :id";
                    $stmt = $this->pdo()->prepare($sql);
                    $stmt->execute([
                        'quantity' => $deductQuantity,
                        'id' => $variation['id']
                    ]);
                    $remainingQuantity -= $deductQuantity;
                }
            }
        }

        return true;
    }
}
