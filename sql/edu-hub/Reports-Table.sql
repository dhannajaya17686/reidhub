-- 4. Reports Table (For Crowd-Moderation)
CREATE TABLE IF NOT EXISTS forum_reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    target_type ENUM('question', 'answer') NOT NULL,
    target_id BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(255) NOT NULL,
    status ENUM('pending', 'resolved', 'dismissed') DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);