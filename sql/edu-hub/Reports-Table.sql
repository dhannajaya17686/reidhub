-- 4. Reports Table (For Crowd-Moderation)
CREATE TABLE IF NOT EXISTS forum_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    target_type ENUM('question', 'answer', 'comment') NOT NULL,
    target_id BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    reviewed_by_admin_id BIGINT UNSIGNED NULL,
    review_message VARCHAR(255) NULL,
    reviewed_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_forum_reports_status (status, created_at),
    INDEX idx_forum_reports_target (target_type, target_id)
);
