-- Products table for marketplace items (corrected with proper condition values)
CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category ENUM('merchandise', 'second-hand') NOT NULL,
    product_type ENUM('apparel', 'accessories', 'stationery', 'electronics', 'books', 'other') NOT NULL,
    condition_type ENUM('brand_new', 'used') NOT NULL,
    stock_quantity INT NOT NULL DEFAULT 1,
    status ENUM('active', 'archived') NOT NULL DEFAULT 'active',
    payment_methods JSON, -- Store accepted payment methods: ['cash_on_delivery', 'preorder']
    images JSON, -- Store image URLs as JSON array
    bank_name VARCHAR(100),
    bank_branch VARCHAR(100),
    account_name VARCHAR(255),
    account_number VARCHAR(50),
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_category (category),
    INDEX idx_product_type (product_type),
    INDEX idx_condition (condition_type),
    INDEX idx_status (status),
    INDEX idx_seller (seller_id)
);