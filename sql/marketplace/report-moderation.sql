-- Marketplace report + seller moderation tables
-- Run this after core marketplace tables (products, orders, users, admins).

CREATE TABLE IF NOT EXISTS marketplace_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    reporter_id BIGINT UNSIGNED NOT NULL,
    seller_id BIGINT UNSIGNED NOT NULL,
    category ENUM('inappropriate', 'spam', 'fraud', 'copyright', 'other') NOT NULL DEFAULT 'other',
    reason TEXT NOT NULL,
    status ENUM('pending', 'under-review', 'resolved', 'archived') NOT NULL DEFAULT 'pending',
    admin_notes TEXT NULL,
    reviewed_by_admin_id BIGINT UNSIGNED NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    KEY idx_marketplace_reports_order_id (order_id),
    KEY idx_marketplace_reports_seller_id (seller_id),
    KEY idx_marketplace_reports_reporter_id (reporter_id),
    KEY idx_marketplace_reports_status (status),
    KEY idx_marketplace_reports_created_at (created_at),

    CONSTRAINT fk_marketplace_reports_order_id
        FOREIGN KEY (order_id) REFERENCES orders(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_marketplace_reports_product_id
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_marketplace_reports_reporter_id
        FOREIGN KEY (reporter_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_marketplace_reports_seller_id
        FOREIGN KEY (seller_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_marketplace_reports_reviewed_by_admin_id
        FOREIGN KEY (reviewed_by_admin_id) REFERENCES admins(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS marketplace_seller_actions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seller_id BIGINT UNSIGNED NOT NULL,
    report_id BIGINT UNSIGNED NULL,
    admin_id BIGINT UNSIGNED NOT NULL,
    action_type ENUM('warning', 'ban', 'unban') NOT NULL,
    reason TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    KEY idx_marketplace_seller_actions_seller_id (seller_id),
    KEY idx_marketplace_seller_actions_report_id (report_id),
    KEY idx_marketplace_seller_actions_admin_id (admin_id),
    KEY idx_marketplace_seller_actions_action_type (action_type),
    KEY idx_marketplace_seller_actions_created_at (created_at),

    CONSTRAINT fk_marketplace_seller_actions_seller_id
        FOREIGN KEY (seller_id) REFERENCES users(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_marketplace_seller_actions_report_id
        FOREIGN KEY (report_id) REFERENCES marketplace_reports(id)
        ON DELETE SET NULL,
    CONSTRAINT fk_marketplace_seller_actions_admin_id
        FOREIGN KEY (admin_id) REFERENCES admins(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
