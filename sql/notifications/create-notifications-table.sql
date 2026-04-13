CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_id INT NULL,
    recipient_role ENUM('user', 'seller', 'buyer', 'admin') NULL,
    content TEXT NOT NULL,
    `from` ENUM('system', 'admin', 'user') NOT NULL DEFAULT 'system',
    topic VARCHAR(120) NULL,
    `timestamp` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    isRead TINYINT(1) NOT NULL DEFAULT 0,
    type ENUM('EDU', 'MAR', 'LAF', 'CAS', 'SYS', 'ADM') NOT NULL,
    INDEX idx_notifications_recipient (recipient_role, recipient_id),
    INDEX idx_notifications_topic (topic),
    INDEX idx_notifications_type (type),
    INDEX idx_notifications_read (isRead),
    INDEX idx_notifications_timestamp (`timestamp`)
);
