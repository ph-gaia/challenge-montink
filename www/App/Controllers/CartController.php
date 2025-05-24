<?php

namespace App\Controllers;

use Core\Controller\AbstractController;
use Core\Session\Session;
use App\Models\ProductsModel;
use App\Models\CouponsModel;
use Core\Interfaces\ControllerInterface;

class CartController extends AbstractController implements ControllerInterface
{
    private Session $session;
    private ProductsModel $model;
    private CouponsModel $couponsModel;

    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
        $this->session = new Session();
        $this->model = new ProductsModel();
        $this->couponsModel = new CouponsModel();
    }

    public function index(): void
    {
        $cart = $this->session->getCart();
        $items = array_values($cart);
        
        $subtotal = array_reduce($items, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $shipping = $this->calculateShipping($subtotal);
        $total = $subtotal + $shipping;

        $this->view->title = "Carrinho de Compras";
        $this->view->cart = $cart;
        $this->view->subtotal = $subtotal;
        $this->view->shipping = $shipping;
        $this->view->total = $total;

        $this->render("Index");
    }

    public function add(): void
    {
        $productId = $this->getParam('id');
        $product = $this->model->getProductById($productId);
        
        if (!$product) {
            $this->jsonResponse(['error' => 'Product not found'], 404);
            return;
        }

        $cart = $this->session->getCart();
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity']++;
        } else {
            $cart[$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'quantity' => 1
            ];
        }

        $this->session->set('cart', $cart);
        $this->jsonResponse(['success' => true]);
    }

    public function update(): void
    {
        $productId = $this->getParam('id');
        $data = $this->getJsonData();
        $quantity = $data['quantity'] ?? 0;

        if ($quantity < 1) {
            $this->jsonResponse(['error' => 'Invalid quantity'], 400);
            return;
        }

        $cart = $this->session->getCart();
        
        if (!isset($cart[$productId])) {
            $this->jsonResponse(['error' => 'Product not in cart'], 404);
            return;
        }

        if ($quantity > $cart[$productId]['stock']) {
            $this->jsonResponse(['error' => 'Insufficient stock'], 400);
            return;
        }

        $this->session->updateCartQuantity($productId, $quantity);
        $this->jsonResponse(['success' => true]);
    }

    public function remove(): void
    {
        $productId = $this->getParam('id');
        $this->session->removeFromCart($productId);
        $this->jsonResponse(['success' => true]);
    }

    public function applyCoupon(): void
    {
        $data = $this->getJsonData();
        $code = $data['code'] ?? '';

        if (empty($code)) {
            $this->jsonResponse(['error' => 'Coupon code is required'], 400);
            return;
        }

        $cart = $this->session->getCart();
        $subtotal = array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $coupon = $this->couponsModel->getValidCoupon($code, $subtotal);
        
        if (!$coupon) {
            $this->jsonResponse(['error' => 'Invalid or expired coupon'], 400);
            return;
        }

        $this->jsonResponse([
            'success' => true,
            'discount' => (float) $coupon['discount_value']
        ]);
    }

    public function getCartData()
    {
        $cart = $this->session->getCart();
        $items = array_values($cart);
        
        $subtotal = array_reduce($items, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $shipping = $this->calculateShipping($subtotal);
        $total = $subtotal + $shipping;

        $this->view->title = "Carrinho de Compras";
        $this->view->cart = $cart;
        $this->view->subtotal = $subtotal;
        $this->view->shipping = $shipping;
        $this->view->total = $total;

        $this->jsonResponse([
            'items' => $items,
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'total' => $total
        ]);
    }

    private function calculateShipping(float $subtotal): float
    {
        if ($subtotal >= 200) {
            return 0;
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15;
        }
        return 20;
    }

    private function getJsonData(): array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }
} 