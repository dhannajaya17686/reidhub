-- Table for Videos and Notes
CREATE TABLE edu_resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL, -- FIXED: Changed from INT to BIGINT UNSIGNED
    title VARCHAR(255) NOT NULL,
    description TEXT,
    subject VARCHAR(50),
    tags VARCHAR(255),
    type ENUM('video', 'note') NOT NULL,
    year_level INT, -- 1, 2, 3, 4
    
    -- Content Storage
    video_link VARCHAR(255), -- For YouTube URLs
    file_path VARCHAR(255),  -- For uploaded PDFs/Docs
    
    -- Approval System
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_feedback TEXT,
    is_hidden TINYINT(1) NOT NULL DEFAULT 0,
    removal_requested TINYINT(1) NOT NULL DEFAULT 0,
    removal_reason TEXT,
    removal_requested_at TIMESTAMP NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table for Bookmarking Resources
CREATE TABLE edu_bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL, -- FIXED: Changed from INT to BIGINT UNSIGNED
    resource_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (resource_id) REFERENCES edu_resources(id) ON DELETE CASCADE
);
