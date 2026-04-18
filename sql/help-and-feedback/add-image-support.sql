-- Add image_path column to user_questions table
ALTER TABLE user_questions ADD COLUMN image_path VARCHAR(500) NULL AFTER message;
