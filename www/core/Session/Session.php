<?php

namespace Core\Session;

class Session
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session value
     * 
     * @param string $key The session key
     * @param mixed $value The value to store
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value
     * 
     * @param string $key The session key
     * @param mixed $default Default value if key doesn't exist
     * @return mixed The session value or default
     */
    public function get(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a session key exists
     * 
     * @param string $key The session key
     * @return bool True if key exists
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a session value
     * 
     * @param string $key The session key
     * @return void
     */
    public function remove(string $key): void
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Clear all session data
     * 
     * @return void
     */
    public function clear(): void
    {
        session_destroy();
    }

    /**
     * Get cart data
     * 
     * @return array Cart data
     */
    public function getCart(): array
    {
        return $this->get('cart', []);
    }

    /**
     * Add item to cart
     * 
     * @param int $productId Product ID
     * @param array $productData Product data
     * @return void
     */
    public function addToCart(int $productId, array $productData): void
    {
        $cart = $this->getCart();
        $cart[$productId] = $productData;
        $this->set('cart', $cart);
    }

    /**
     * Update cart item quantity
     * 
     * @param int $productId Product ID
     * @param int $quantity New quantity
     * @return void
     */
    public function updateCartQuantity(int $productId, int $quantity): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            $this->set('cart', $cart);
        }
    }

    /**
     * Remove item from cart
     * 
     * @param int $productId Product ID
     * @return void
     */
    public function removeFromCart(int $productId): void
    {
        $cart = $this->getCart();
        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            $this->set('cart', $cart);
        }
    }

    /**
     * Clear cart
     * 
     * @return void
     */
    public function clearCart(): void
    {
        $this->remove('cart');
    }
} 