USE reidhub;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name  VARCHAR(100) NOT NULL,
  email      VARCHAR(255) NOT NULL,
  reg_no     VARCHAR(32)  NOT NULL,
  password   VARCHAR(255) NOT NULL, -- bcrypt hash
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT users_email_unique UNIQUE (email),
  CONSTRAINT users_reg_no_unique UNIQUE (reg_no),
  CONSTRAINT users_regno_chk CHECK (reg_no REGEXP '^[0-9]{4}(is|cs)[0-9]{3}$')
)