USE reidhub;

-- Add profile_picture column to users table if it doesn't exist
ALTER TABLE users 
ADD COLUMN profile_picture VARCHAR(255) NULL DEFAULT NULL 
AFTER email;
