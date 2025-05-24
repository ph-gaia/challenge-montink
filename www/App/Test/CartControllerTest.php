<?php

use App\Controllers\CartController;
use Core\Session\Session;
use App\Models\ProductsModel;
use App\Models\CouponsModel;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CartControllerTest extends TestCase
{
    private CartController $cartController;
    private Session|MockObject $session;
    private ProductsModel|MockObject $productsModel;
    private CouponsModel|MockObject $couponsModel;

    protected function setUp(): void
    {
        $this->session = $this->createMock(Session::class);
        $this->productsModel = $this->createMock(ProductsModel::class);
        $this->couponsModel = $this->createMock(CouponsModel::class);
        
        $this->cartController = new CartController(['id' => 1]);
    }

    public function testAddToCartWithValidProduct()
    {
        $productId = 1;
        $product = [
            'id' => $productId,
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10
        ];

        $this->productsModel->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willReturn($product);

        $this->session->expects($this->once())
            ->method('getCart')
            ->willReturn([]);

        $this->session->expects($this->once())
            ->method('set')
            ->with('cart', [
                $productId => [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'quantity' => 1
                ]
            ]);

        $this->cartController->add();
    }

    public function testAddToCartWithExistingProduct()
    {
        $productId = 1;
        $product = [
            'id' => $productId,
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10
        ];

        $existingCart = [
            $productId => [
                'id' => $productId,
                'name' => 'Test Product',
                'price' => 99.99,
                'stock' => 10,
                'quantity' => 1
            ]
        ];

        $this->productsModel->expects($this->once())
            ->method('getProductById')
            ->with($productId)
            ->willReturn($product);

        $this->session->expects($this->once())
            ->method('getCart')
            ->willReturn($existingCart);

        $this->session->expects($this->once())
            ->method('set')
            ->with('cart', [
                $productId => [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'stock' => $product['stock'],
                    'quantity' => 2
                ]
            ]);

        $this->cartController->add();
    }

    public function testUpdateCartQuantity()
    {
        $productId = 1;
        $newQuantity = 3;
        $cart = [
            $productId => [
                'id' => $productId,
                'name' => 'Test Product',
                'price' => 99.99,
                'stock' => 10,
                'quantity' => 1
            ]
        ];

        $this->session->expects($this->once())
            ->method('getCart')
            ->willReturn($cart);

        $this->session->expects($this->once())
            ->method('updateCartQuantity')
            ->with($productId, $newQuantity);

        $this->cartController->update();
    }

    public function testRemoveFromCart()
    {
        $productId = 1;

        $this->session->expects($this->once())
            ->method('removeFromCart')
            ->with($productId);

        $this->cartController->remove();
    }

    public function testApplyValidCoupon()
    {
        $code = 'WELCOME10';
        $subtotal = 200.00;
        $coupon = [
            'id' => 1,
            'code' => $code,
            'discount_type' => 'percent',
            'discount_value' => 10.00,
            'minimum_value' => 100.00,
            'valid_until' => date('Y-m-d H:i:s', strtotime('+30 days')),
            'status' => 'active'
        ];

        $cart = [
            1 => [
                'id' => 1,
                'name' => 'Test Product',
                'price' => 100.00,
                'quantity' => 2
            ]
        ];

        $this->session->expects($this->once())
            ->method('getCart')
            ->willReturn($cart);

        $this->couponsModel->expects($this->once())
            ->method('getValidCoupon')
            ->with($code, $subtotal)
            ->willReturn($coupon);

        $this->cartController->applyCoupon();
    }

    public function testApplyInvalidCoupon()
    {
        $code = 'INVALID';
        $subtotal = 200.00;
        $cart = [
            1 => [
                'id' => 1,
                'name' => 'Test Product',
                'price' => 100.00,
                'quantity' => 2
            ]
        ];

        $this->session->expects($this->once())
            ->method('getCart')
            ->willReturn($cart);

        $this->couponsModel->expects($this->once())
            ->method('getValidCoupon')
            ->with($code, $subtotal)
            ->willReturn(null);

        $this->cartController->applyCoupon();
    }

    public function testGetCartData()
    {
        $cart = [
            1 => [
                'id' => 1,
                'name' => 'Test Product',
                'price' => 100.00,
                'quantity' => 2
            ]
        ];

        $this->session->expects($this->once())
            ->method('getCart')
            ->willReturn($cart);

        $this->cartController->getCartData();
    }
}
