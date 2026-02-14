-- Quick Setup Script for Community Feature
-- Run this after logging in as a user

-- 1. Make a user a community admin (replace USER_ID with your actual user ID)
-- You can find your user ID by checking the session after login or querying the users table

-- Example: Make user with ID 1 a community admin
INSERT INTO community_admins (user_id, role_type, created_at) 
VALUES (1, 'club_admin', NOW());

-- To find your user ID, you can run:
-- SELECT id, email, first_name, last_name FROM users;

-- 2. Insert some sample posts (optional for testing)
INSERT INTO community_posts (author_id, title, content, post_type, status, created_at) VALUES
(1, 'Welcome to ReidHub Community!', 'We''re excited to launch our new community feed where you can share updates, events, and connect with fellow students. Stay tuned for more features!', 'announcement', 'published', NOW()),
(1, 'Tech Club Meeting This Friday', 'Join us this Friday at 3 PM in Room 204 for our weekly tech club meeting. We''ll be discussing the upcoming hackathon!', 'club', 'published', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, NULL, 'Just finished my final project! Feeling accomplished 🎉', 'general', 'published', DATE_SUB(NOW(), INTERVAL 5 HOUR));

-- Check community admins
SELECT ca.*, u.email, u.first_name, u.last_name 
FROM community_admins ca
JOIN users u ON ca.user_id = u.id;

-- View all posts
SELECT p.*, u.first_name, u.last_name 
FROM community_posts p
JOIN users u ON p.author_id = u.id
ORDER BY p.created_at DESC;
