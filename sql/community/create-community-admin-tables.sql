-- Community Admin Roles Table (for club admins, etc.)
CREATE TABLE IF NOT EXISTS community_admins (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  role_type ENUM('club_admin', 'event_coordinator', 'community_admin', 'moderator') DEFAULT 'club_admin',
  club_id BIGINT UNSIGNED NULL,
  approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  request_id BIGINT UNSIGNED,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_user (user_id),
  INDEX idx_role (role_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
