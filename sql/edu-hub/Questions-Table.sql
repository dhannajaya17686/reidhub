-- 1. Forum Questions Table
CREATE TABLE IF NOT EXISTS forum_questions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'General',
    tags TEXT NULL,
    views INT DEFAULT 0,
    moderation_status ENUM('active', 'hidden', 'deleted') NOT NULL DEFAULT 'active',
    moderation_note VARCHAR(255) NULL,
    moderated_by_admin_id BIGINT UNSIGNED NULL,
    moderated_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FULLTEXT(title, content),
    INDEX idx_forum_questions_moderation_status (moderation_status, created_at),
    INDEX idx_forum_questions_user (user_id, created_at)
);
