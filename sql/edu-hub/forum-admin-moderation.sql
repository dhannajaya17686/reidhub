-- Forum admin moderation extensions

-- Core moderation columns now live in the base forum table definitions.
-- This file keeps the forum-admin-specific tables required by the admin module.

-- 1) User moderation actions: suspension and warnings/messages
CREATE TABLE IF NOT EXISTS forum_user_suspensions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    admin_id BIGINT UNSIGNED NOT NULL,
    reason VARCHAR(255) NOT NULL,
    starts_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    ends_at DATETIME NULL,
    is_permanent TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_forum_user_suspensions_active ON forum_user_suspensions (user_id, is_active, ends_at);

CREATE TABLE IF NOT EXISTS forum_admin_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    admin_id BIGINT UNSIGNED NOT NULL,
    message_type ENUM('warning', 'message') NOT NULL DEFAULT 'warning',
    subject VARCHAR(120) NOT NULL,
    body TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE INDEX idx_forum_admin_messages_user ON forum_admin_messages (user_id, created_at);
