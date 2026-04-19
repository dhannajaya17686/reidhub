-- 3. Votes Table (Handles Upvotes for BOTH Questions and Answers)
CREATE TABLE IF NOT EXISTS forum_votes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    target_type ENUM('question', 'answer') NOT NULL, 
    vote_type ENUM('up', 'down') NOT NULL DEFAULT 'up'
    target_id BIGINT UNSIGNED NOT NULL, 
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_vote (user_id, target_type, target_id), -- Prevents double voting
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);