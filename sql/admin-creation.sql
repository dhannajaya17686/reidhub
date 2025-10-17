USE reidhub;

-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email      VARCHAR(255) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL, -- bcrypt hash
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin (email: admin@reidhub.com, password: admin123)
INSERT INTO admins (email, password) VALUES (
  'admin@reidhub.com',
  '$2y$10$wH6QwQwQwQwQwQwQwQwQOeQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQwQw' -- bcrypt for 'admin123'
);

