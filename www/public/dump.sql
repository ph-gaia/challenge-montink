CREATE DATABASE IF NOT EXISTS montink CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE montink;

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE product_variations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    price_override DECIMAL(10,2),
    stock INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    discount_type ENUM('value', 'percent') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    minimum_value DECIMAL(10,2) NOT NULL,
    valid_until DATETIME NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    shipping DECIMAL(10,2) NOT NULL,
    discount DECIMAL(10,2) DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    coupon_id INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (coupon_id) REFERENCES coupons(id)
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert sample products
INSERT INTO products (name, price, stock) VALUES
('Smartphone XYZ', 199.99, 50),
('Notebook ABC', 349.99, 30),
('Headphones Pro', 299.99, 100),
('Smart Watch', 799.99, 25),
('Wireless Earbuds', 199.99, 75),
('Tablet Ultra', 149.99, 40),
('Gaming Mouse', 159.99, 60),
('Mechanical Keyboard', 299.99, 45),
('Monitor 27"', 129.99, 20),
('External SSD 1TB', 49.99, 35);

-- Insert sample coupons
INSERT INTO coupons (code, discount_type, discount_value, minimum_value, valid_until, status) VALUES
('WELCOME10', 'percent', 10.00, 0, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 30 DAY), 'active'),
('FREESHIP', 'value', 15.00, 0, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 15 DAY), 'active'),
('SUMMER20', 'percent', 20.00, 0, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 60 DAY), 'active'),
('FLASH50', 'value', 50.00, 0, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 7 DAY), 'active'),
('WINTER15', 'percent', 15.00, 0, DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 45 DAY), 'active');
