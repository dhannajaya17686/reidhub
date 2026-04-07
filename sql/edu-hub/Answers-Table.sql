-- 2. Forum Answers Table
CREATE TABLE IF NOT EXISTS forum_answers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    question_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    is_accepted TINYINT(1) NOT NULL DEFAULT 0,
    moderation_status ENUM('active', 'hidden', 'deleted') NOT NULL DEFAULT 'active',
    moderation_note VARCHAR(255) NULL,
    moderated_by_admin_id BIGINT UNSIGNED NULL,
    moderated_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES forum_questions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_forum_answers_question (question_id, moderation_status, created_at),
    INDEX idx_forum_answers_accepted (question_id, is_accepted)
);
