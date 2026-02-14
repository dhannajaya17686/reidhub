-- Grant Club Admin Permission to Users
-- This should be run by system administrators to grant users permission to become club admins
-- Users with club_admin permission can create and manage clubs

-- Example: Grant club admin permission to user with ID 1
-- Replace '1' with the actual user ID you want to make a club admin

-- Step 1: Find the user ID you want to grant permission to
-- SELECT id, email, first_name, last_name, name FROM users WHERE email = 'user@example.com';

-- Step 2: Grant club admin permission
-- INSERT INTO community_admins (user_id, role_type, created_at) 
-- VALUES (1, 'club_admin', NOW());

-- Example: Grant club admin permission to multiple users at once
-- INSERT INTO community_admins (user_id, role_type, created_at) VALUES
-- (1, 'club_admin', NOW()),
-- (2, 'club_admin', NOW()),
-- (3, 'club_admin', NOW());

-- Step 3: Verify the permission was granted
-- SELECT ca.*, u.email, u.first_name, u.last_name, u.name 
-- FROM community_admins ca
-- JOIN users u ON ca.user_id = u.id
-- WHERE ca.role_type = 'club_admin';

-- To revoke club admin permission:
-- DELETE FROM community_admins WHERE user_id = ? AND role_type = 'club_admin';

-- Note: Only system administrators (those with admin_id in session) should run these queries
-- Club admins cannot grant permissions to other users - only system admins can
