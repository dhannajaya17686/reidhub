-- 1. Create Comments Table (Nested discussions)
CREATE TABLE IF NOT EXISTS forum_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_type ENUM('question', 'answer') NOT NULL,
    parent_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    moderation_status ENUM('active', 'hidden', 'deleted') NOT NULL DEFAULT 'active',
    moderation_note VARCHAR(255) NULL,
    moderated_by_admin_id BIGINT UNSIGNED NULL,
    moderated_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_forum_comments_parent (parent_type, parent_id, moderation_status, created_at)
);

-- 2. Update Votes Table to support Downvotes (Optional - run only if you need downvotes)
-- First, empty the table to avoid conflicts during conversion
TRUNCATE TABLE forum_votes; 
ALTER TABLE forum_votes ADD COLUMN vote_type ENUM('up', 'down') NOT NULL DEFAULT 'up';
-- You will also need to update the UNIQUE constraint to include vote_type if a user can upvote AND downvote (rare), 
-- OR keep it as is if a user can only have ONE state (Up or Down). Keeping it as is is better.
