-- Add community_admin permission option to community_admins.role_type enum
-- Keeps legacy moderator values valid for existing rows.
ALTER TABLE community_admins
MODIFY COLUMN role_type ENUM('club_admin', 'event_coordinator', 'community_admin', 'moderator')
DEFAULT 'club_admin';
