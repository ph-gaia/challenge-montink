<?php

namespace App\Controllers;

use Core\Controller\AbstractController;
use Core\Session\Session;
use App\Models\OrdersModel;
use App\Models\CouponsModel;
use Core\Interfaces\ControllerInterface;

class OrdersController extends AbstractController implements ControllerInterface
{
    private Session $session;
    private OrdersModel $ordersModel;
    private CouponsModel $couponsModel;

    public function __construct(array $parameters)
    {
        parent::__construct($parameters);
        $this->session = new Session();
        $this->ordersModel = new OrdersModel();
        $this->couponsModel = new CouponsModel();
    }

    public function index(): void
    {
        $this->view->title = "Pedidos";
        $this->render("Index");
    }

    public function create(): void
    {
        $data = $this->getJsonData();
        
        if (empty($data['customer_name']) || empty($data['customer_email'])) {
            $this->jsonResponse(['error' => 'Customer name and email are required'], 400);
            return;
        }

        $cart = $this->session->getCart();
        if (empty($cart)) {
            $this->jsonResponse(['error' => 'Cart is empty'], 400);
            return;
        }

        $subtotal = array_reduce($cart, function($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $shipping = $this->calculateShipping($subtotal);
        $discount = 0;
        $couponId = null;

        if (!empty($data['coupon_code'])) {
            $coupon = $this->couponsModel->getValidCoupon($data['coupon_code'], $subtotal);
            if ($coupon) {
                $discount = $coupon['discount_value'];
                $couponId = $coupon['id'];
            }
        }

        $total = $subtotal + $shipping - $discount;

        $orderId = $this->ordersModel->create([
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'subtotal' => $subtotal,
            'shipping' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'coupon_id' => $couponId
        ]);

        if (!$orderId) {
            $this->jsonResponse(['error' => 'Failed to create order'], 500);
            return;
        }

        foreach ($cart as $productId => $item) {
            $this->ordersModel->addOrderItem($orderId, [
                'product_id' => $productId,
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['price'] * $item['quantity']
            ]);
        }

        $this->sendOrderConfirmationEmail($data['customer_email'], $orderId);

        $this->session->clearCart();

        $this->jsonResponse([
            'success' => true,
            'order_id' => $orderId
        ]);
    }

    public function webhook(): void
    {
        $data = $this->getJsonData();
        
        if (empty($data['order_id']) || empty($data['status'])) {
            $this->jsonResponse(['error' => 'Invalid webhook data'], 400);
            return;
        }

        $order = $this->ordersModel->getOrderById($data['order_id']);
        if (!$order) {
            $this->jsonResponse(['error' => 'Order not found'], 404);
            return;
        }

        $this->ordersModel->updateStatus($data['order_id'], $data['status']);
        
        $this->jsonResponse(['success' => true]);
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

    private function sendOrderConfirmationEmail(string $email, int $orderId): void
    {
        // TODO: Implement email sending logic
        // This should be implemented using a proper email service
    }

    private function getJsonData(): array
    {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }
} 