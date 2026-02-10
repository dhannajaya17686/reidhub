-- OTP (One-Time Password) Verification Table
-- Stores temporary OTP codes for email verification during signup process
CREATE TABLE IF NOT EXISTS otps (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Unique identifier for OTP record',
    email VARCHAR(255) NOT NULL COMMENT 'Email address requesting OTP',
    otp_code VARCHAR(6) NOT NULL COMMENT '6-digit OTP code',
    attempt_count INT DEFAULT 0 COMMENT 'Number of failed verification attempts',
    is_verified BOOLEAN DEFAULT FALSE COMMENT 'Whether OTP has been successfully verified',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'When OTP was generated',
    expires_at DATETIME NOT NULL COMMENT 'When OTP expires (typically 10 minutes from creation)',
    verified_at DATETIME NULL COMMENT 'When OTP was successfully verified',
    
    -- Indices for faster lookups
    INDEX idx_email_expires (email, expires_at),
    INDEX idx_code_email (otp_code, email),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Stores OTP codes for email verification during user signup';

-- Index to clean up expired OTPs efficiently
CREATE INDEX idx_expires_at ON otps(expires_at);
