-- Create Blog Views Tracking Table
-- Tracks unique views per user to prevent duplicate counting
CREATE TABLE IF NOT EXISTS blog_views (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    blog_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_blog_user_view (blog_id, user_id),
    FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_blog_id (blog_id),
    INDEX idx_user_id (user_id),
    INDEX idx_viewed_at (viewed_at)
);
