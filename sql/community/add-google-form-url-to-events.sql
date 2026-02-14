-- Add google_form_url column to events table
ALTER TABLE events 
ADD COLUMN google_form_url VARCHAR(500) AFTER image_url;
