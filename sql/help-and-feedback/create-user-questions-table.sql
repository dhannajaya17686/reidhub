-- Create user_questions table for Help & Feedback system
CREATE TABLE IF NOT EXISTS user_questions (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    category ENUM('academic_issues', 'extracurricular_issues', 'sports_issues', 'infrastructure_issues', 'other_issues', 'feedbacks') NOT NULL DEFAULT 'academic_issues',
    subject VARCHAR(255) NOT NULL,
    message LONGTEXT NOT NULL,
    status ENUM('open', 'pending', 'resolved', 'closed') NOT NULL DEFAULT 'open',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_user_questions_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_category (category),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
