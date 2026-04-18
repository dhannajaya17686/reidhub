# Club Admin Permission System

## Overview
The club admin permission system separates club management from general community features. Only users explicitly granted "club admin" permission by system administrators can create and manage clubs.

## Permission Levels

### 1. Regular Users
- Browse all clubs
- Join clubs
- View club details
- See their joined clubs

### 2. Club Admins (Granted by System Admin)
- All regular user permissions
- Create new clubs
- Edit clubs they own
- Delete clubs they own
- Access club admin portal
- Manage club members
- View "My Clubs" section

### 3. System Administrators
- Grant/revoke club admin permissions
- Access all admin features
- Manage all community content

## Granting Club Admin Permission

### Step 1: Find User ID
```sql
SELECT id, email, first_name, last_name, name 
FROM users 
WHERE email = 'user@example.com';
```

### Step 2: Grant Permission
```sql
INSERT INTO community_admins (user_id, role_type, created_at) 
VALUES (USER_ID, 'club_admin', NOW());
```

### Step 3: Verify
```sql
SELECT ca.*, u.email, u.name 
FROM community_admins ca
JOIN users u ON ca.user_id = u.id
WHERE ca.role_type = 'club_admin';
```

## Database Structure

### community_admins Table
```sql
CREATE TABLE community_admins (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  role_type ENUM('club_admin', 'event_coordinator', 'moderator'),
  club_id BIGINT UNSIGNED NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

### Key Points
- `role_type = 'club_admin'` grants club management permissions
- `club_id` is NULL for general club admins
- Multiple role_types can exist for different permissions

## User Interface Changes

### For Non-Club-Admins
- No "Switch to Club Admin" button visible
- No "My Clubs" tab in clubs page
- No "Create New Club" button
- Can only browse and join clubs

### For Club Admins
- "Switch to Club Admin" button appears in Community sidebar submenu
- "My Clubs" tab shows clubs they own
- "Create New Club" button appears
- Access to Club Admin Portal at `/dashboard/club-admin/dashboard`

## Club Admin Portal Features

### Dashboard (`/dashboard/club-admin/dashboard`)
- View all clubs you own
- Quick actions: View Club, Manage Members, Edit Club

### Members Management (`/dashboard/club-admin/members?club_id=X`)
- View all club members
- See member roles (Owner, Admin, Member)
- Remove members (except owners)

### Future Features
- Events management
- Announcements
- Membership applications

## Security Checks

### Controller Level
All club admin routes check permission:
```php
if (!$this->checkIfClubAdmin($user['id'])) {
    header('Location: /dashboard/community/clubs');
    exit;
}
```

### View Level
UI elements conditionally render:
```php
<?php if ($data['isClubAdmin']): ?>
  <!-- Club admin only content -->
<?php endif; ?>
```

### Sidebar Level
Permission check before showing switch:
```php
<?php if ($isCommunitySection && $userIsClubAdmin): ?>
  <!-- Switch to Club Admin button -->
<?php endif; ?>
```

## Revoking Permission

### Remove Club Admin Access
```sql
DELETE FROM community_admins 
WHERE user_id = USER_ID AND role_type = 'club_admin';
```

**Note**: Revoking permission does NOT delete clubs the user created. Their clubs remain active but they lose the ability to edit/manage them through the admin portal.

## Files Modified

### Controllers
- `app/controllers/Community/CommunityUserController.php`
  - Added `checkIfClubAdmin()` method
  - Updated club CRUD methods to check permission
  - Added club admin portal methods

### Views
- `app/views/components/sidebar.php` - Permission-based navigation
- `app/views/User/community/clubs/all-clubs.php` - Conditional tabs
- `app/views/User/community/club-admin/` - Admin portal views

### SQL Scripts
- `sql/community/grant-club-admin.sql` - Permission granting guide
- `sql/community/create-posts-table.sql` - Table definitions

## Best Practices

1. **Only grant club admin to trusted users** - They can create public-facing content
2. **Regular audits** - Periodically review who has club admin access
3. **Clear communication** - Let users know how to request club admin access
4. **Documentation** - Keep track of why each user was granted permission

## Troubleshooting

### "Switch to Club Admin" button not appearing
- Check if user has club_admin role in community_admins table
- Verify session is active and user_id is set
- Check database connection

### Permission denied when accessing club admin portal
- Verify user has 'club_admin' role_type (not just any role)
- Check that community_admins table exists
- Ensure user_id matches correctly

### Can't create clubs even with permission
- Verify permission with: `SELECT * FROM community_admins WHERE user_id = ? AND role_type = 'club_admin'`
- Check file upload permissions on `/public/storage/clubs/` directory
- Review error logs in `/storage/logs/`
