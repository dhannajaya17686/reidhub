-- 1. Create Comments Table (Nested discussions)
CREATE TABLE IF NOT EXISTS forum_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    parent_type ENUM('question', 'answer') NOT NULL,
    parent_id BIGINT UNSIGNED NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 2. Update Votes Table to support Downvotes (Optional - run only if you need downvotes)
-- First, empty the table to avoid conflicts during conversion
TRUNCATE TABLE forum_votes; 
ALTER TABLE forum_votes ADD COLUMN vote_type ENUM('up', 'down') NOT NULL DEFAULT 'up';
-- You will also need to update the UNIQUE constraint to include vote_type if a user can upvote AND downvote (rare), 
-- OR keep it as is if a user can only have ONE state (Up or Down). Keeping it as is is better.