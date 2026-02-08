-- Clubs & Societies Tables
CREATE TABLE IF NOT EXISTS clubs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT,
  category ENUM('academic', 'cultural', 'sports', 'technology', 'arts', 'social', 'other') DEFAULT 'other',
  creator_id BIGINT UNSIGNED NOT NULL,
  image_url VARCHAR(500),
  member_count INT UNSIGNED DEFAULT 0,
  status ENUM('active', 'inactive', 'archived') DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_creator (creator_id),
  INDEX idx_category (category),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Club Memberships
CREATE TABLE IF NOT EXISTS club_memberships (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  club_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  role ENUM('member', 'admin', 'owner') DEFAULT 'member',
  joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_membership (club_id, user_id),
  INDEX idx_club (club_id),
  INDEX idx_user (user_id),
  INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Events Table
CREATE TABLE IF NOT EXISTS events (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  creator_id BIGINT UNSIGNED NOT NULL,
  club_id BIGINT UNSIGNED NULL,
  event_date DATETIME NOT NULL,
  location VARCHAR(255),
  category VARCHAR(100),
  max_attendees INT UNSIGNED,
  image_url VARCHAR(500),
  google_form_url VARCHAR(500),
  status ENUM('upcoming', 'ongoing', 'completed', 'cancelled') DEFAULT 'upcoming',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (club_id) REFERENCES clubs(id) ON DELETE SET NULL,
  INDEX idx_creator (creator_id),
  INDEX idx_club (club_id),
  INDEX idx_event_date (event_date),
  INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event Attendees
CREATE TABLE IF NOT EXISTS event_attendees (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  event_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  registered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY unique_attendee (event_id, user_id),
  INDEX idx_event (event_id),
  INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample Data
INSERT INTO clubs (name, description, category, creator_id, image_url, member_count, status) VALUES
('Technology & Innovation Club', 'Join us to explore cutting-edge technologies, participate in hackathons, and build innovative projects together.', 'technology', 1, 'https://via.placeholder.com/800x400/4A90E2/ffffff?text=Tech+Club', 50, 'active'),
('Campus Sports Club', 'Stay active and healthy! Join our sports club for regular matches, training sessions, and inter-campus tournaments.', 'sports', 1, 'https://via.placeholder.com/800x400/2ECC71/ffffff?text=Sports+Club', 75, 'active'),
('Debate Society', 'Sharpen your critical thinking and public speaking skills. Weekly debates on current affairs and philosophical topics.', 'academic', 1, 'https://via.placeholder.com/800x400/9B59B6/ffffff?text=Debate+Society', 30, 'active'),
('Cultural Exchange Club', 'Celebrate diversity! Learn about different cultures, languages, and traditions through events and workshops.', 'cultural', 1, 'https://via.placeholder.com/800x400/E74C3C/ffffff?text=Cultural+Club', 45, 'active'),
('Arts & Creativity Club', 'Express yourself through art, music, drama, and creative writing. Showcase your talents at our monthly exhibitions.', 'arts', 1, 'https://via.placeholder.com/800x400/F39C12/ffffff?text=Arts+Club', 38, 'active');
