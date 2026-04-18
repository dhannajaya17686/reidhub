# Community Feed System Setup

## Overview
I've created a complete Instagram-like community feed system with role-based access for community admins.

## What's Been Added

### 1. Database Tables
Location: `sql/community/create-posts-table.sql`

Run this SQL to create the required tables:
```sql
- community_posts (main posts table)
- post_likes (for post likes)
- post_comments (for comments)
- community_admins (tracks who can create/edit posts)
```

### 2. Files Created

**Models:**
- `app/models/CommunityPost.php` - Handles all database operations for posts

**Views:**
- `app/views/User/community/community-feed.php` - Main Instagram-like feed
- `app/views/User/community/create-post.php` - Post creation form (admin only)

**CSS:**
- `public/css/app/user/community/community-feed.css` - Styling for the feed

**Controllers:**
- Updated `CommunityUserController.php` with feed logic and admin checking
- Fixed `CommunityAdminController.php` class name issue

**Routes:**
- `/dashboard/community` - Main feed (for all users)
- `/dashboard/community/create-post` - Create post (admin only)
- `/dashboard/community/my-posts` - View your posts (admin only)
- `/dashboard/community/delete-post` - Delete post API (admin only)

## Setup Instructions

### Step 1: Create Database Tables
```bash
# Connect to your MySQL database
mysql -u your_username -p reidhub < sql/community/create-posts-table.sql
```

### Step 2: Make a User a Community Admin
```sql
-- Replace 1 with your actual user ID
INSERT INTO community_admins (user_id, role_type, created_at) 
VALUES (1, 'club_admin', NOW());
```

To find your user ID after logging in:
```sql
SELECT id, email, first_name, last_name FROM users WHERE email = 'your@email.com';
```

### Step 3: (Optional) Add Sample Posts
```bash
mysql -u your_username -p reidhub < sql/community/setup-community-admin.sql
```

## Features

### For All Users:
✅ View community feed (Instagram-like)
✅ See posts from community admins
✅ Like posts (functionality placeholder)
✅ Comment on posts (functionality placeholder)
✅ Share posts

### For Community Admins:
✅ Create new posts
✅ Add titles and descriptions
✅ Upload images (multi-image support)
✅ Edit their own posts
✅ Delete their own posts
✅ View "My Posts" section
✅ Special admin badge displayed

## How It Works

1. **Login Flow:**
   - User logs in → redirected to `/dashboard/user`
   - User clicks "Community" in sidebar → goes to `/dashboard/community`

2. **Community Feed:**
   - Shows all published posts from database
   - Posts are ordered by creation date (newest first)
   - Each post shows author info, content, images, likes, and comments

3. **Role Checking:**
   - System checks if user is in `community_admins` table
   - If yes: Shows "Create Post" button and edit/delete options
   - If no: Only shows the feed

4. **Admin Features:**
   - Community admins see a purple gradient badge
   - Can create posts with title, content, and images
   - Can edit/delete only their own posts
   - Has "My Posts" section to manage content

## Database Schema

```
community_posts:
- id (primary key)
- author_id (foreign key to users)
- title (optional)
- content (required)
- post_type (general, club, event, announcement)
- images (JSON array)
- status (draft, published, archived)
- created_at, updated_at

community_admins:
- id (primary key)
- user_id (foreign key to users)
- role_type (club_admin, event_coordinator, moderator)
- created_at
```

## Next Steps to Fully Implement

1. **Image Upload Handling:** Currently accepts file input but needs backend processing
2. **Like/Comment Functionality:** Database tables exist, need API endpoints
3. **Post Editing:** Create edit page and update endpoint
4. **My Posts View:** Create the view template
5. **Pagination:** Add pagination for large feeds
6. **Search/Filter:** Add ability to filter by post type

## Testing

1. Run the SQL scripts to create tables
2. Make yourself a community admin
3. Login as that user
4. Go to Community - you should see the feed
5. As an admin, you'll see "Create Post" button
6. Create a test post
7. It should appear in the feed

## Troubleshooting

**"No posts yet" message:**
- Check if tables were created: `SHOW TABLES LIKE 'community_%';`
- Check if posts exist: `SELECT * FROM community_posts;`

**"Create Post" button not showing:**
- Verify you're in community_admins table
- Check user_id matches your logged-in user

**Page not loading:**
- Check PHP error logs
- Verify Model class is being autoloaded
- Check Database connection is working
