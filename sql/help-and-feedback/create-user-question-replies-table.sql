-- Create user_question_replies table for Help & Feedback system
CREATE TABLE IF NOT EXISTS user_question_replies (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    admin_id BIGINT UNSIGNED NOT NULL,
    reply_message LONGTEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_question_replies_question FOREIGN KEY (question_id) REFERENCES user_questions(id) ON DELETE CASCADE,
    CONSTRAINT fk_question_replies_admin FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE,
    INDEX idx_question_id (question_id),
    INDEX idx_admin_id (admin_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
