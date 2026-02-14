# Blog Image Upload Troubleshooting Guide

## Quick Diagnostic Steps

### 1. **Run the Diagnostic Tool**
   - Access: `http://localhost:8080/blog-image-diagnostic.php`
   - This will check:
     - Storage directory exists and is writable
     - Files in the storage directory
     - Database connection and table structure
     - Blog count and image paths stored in DB
     - Allow you to test uploads

### 2. **Check Browser Console**
   - Open Developer Tools (F12)
   - Go to **Console** tab
   - You should see logs like:
     ```
     Blog "My Blog Title" - Image path from API: /storage/blogs/blog_1770562217_abc123.jpg
     Full image URL: http://localhost:8080/storage/blogs/blog_1770562217_abc123.jpg
     ```
   - If images fail to load, you'll see:
     ```
     Image failed to load: /storage/blogs/filename.jpg
     ```

### 3. **Check PHP Error Logs**
   - Look in `/storage/logs/` for any error logs
   - Look specifically for patterns like:
     ```
     === Image Upload Debug ===
     File name: ...
     File type: ...
     ✓ File uploaded successfully
     Storage path: /storage/blogs/blog_...
     ```

## Common Issues & Solutions

### **Images Not Showing After Upload**

#### Issue: Images show as placeholder (gray box with "No Image")
**Possible Causes:**
1. Image wasn't actually uploaded
2. Image path not saved in database
3. File permissions issue

**Solution:**
```
1. Open Developer Tools (F12)
2. Go to Network tab
3. Upload an image
4. Look for the image request in the Network tab
5. Check if it returns 200 (success) or 404 (not found)
6. If 404, the file wasn't uploaded
```

#### Issue: Upload says "success" but image doesn't appear
**Solution:**
```
1. Go to /blog-image-diagnostic.php
2. Check "Image Paths in Database" section
3. Look for your blog - does it have an image_path?
4. If image_path is NULL or empty, check PHP logs for upload errors
5. If image_path exists, check if file is in /public/storage/blogs/
```

### **Upload Form Submits but Image Not Saved**

**Check These Steps:**
1. **Form Submission:**
   - Verify `enctype="multipart/form-data"` is in form tag
   - Check that file input name is `blog_image`

2. **PHP Logs:**
   - The enhanced logging will show:
     ```
     === Image Upload Debug ===
     File name: myimage.jpg
     File type: image/jpeg
     File size: 2048576 bytes
     DOCUMENT_ROOT: /var/www/html/public
     Upload directory: /var/www/html/public/storage/blogs/
     Generated filename: blog_1770562217_abc123xyz.jpg
     ✓ File uploaded successfully
     Storage path: /storage/blogs/blog_1770562217_abc123xyz.jpg
     ```

3. **If Upload Fails:**
   - Messages like `❌ Invalid file type` - ensure it's JPG or PNG
   - Messages like `❌ File too large` - max 5MB
   - Messages like `❌ Failed to create directory` - permission issue

### **Images Fail to Load from Browser**

**Steps to Debug:**
```
1. Open Developer Tools (F12)
2. Go to Network tab
3. Look for image requests (should be /storage/blogs/filename.jpg)
4. Check the status code:
   - 200 = File exists and is served correctly
   - 404 = File not found
   - 403 = Permission denied
5. Click on the request to see headers
```

**If Getting 404 Errors:**
- Check if file actually exists in `/public/storage/blogs/`
- Ensure dirname is spellled correctly in database
- Path should start with `/storage/blogs/` (with leading slash)

**If Getting 403 Errors:**
- Run in Docker: `chmod 755 /var/www/html/public/storage/blogs/`
- On Windows with Docker: usually not necessary, but if needed, restart containers

### **Database Path Issues**

**To Check What's Stored:**
```sql
-- View all blog image paths
SELECT id, title, image_path, created_at FROM blogs ORDER BY created_at DESC;

-- Count blogs with/without images
SELECT 
  COUNT(*) as total,
  SUM(CASE WHEN image_path IS NULL THEN 1 ELSE 0 END) as no_image,
  SUM(CASE WHEN image_path IS NOT NULL THEN 1 ELSE 0 END) as has_image
FROM blogs;
```

## Expected Behavior

### **Successful Upload Flow:**
```
1. User fills form with title, description, category
2. User selects optional image file (JPG/PNG, max 5MB)
3. User clicks submit
4. Browser sends POST to /dashboard/community/blogs/create
5. PHP logs:
   ✓ Form validation passes
   ✓ Image uploaded to /var/www/html/public/storage/blogs/
   ✓ Path stored in database: /storage/blogs/blog_...
   ✓ Blog record created
6. User redirected to /dashboard/community/blogs
7. Blog card displays with image or gray placeholder
8. Browser console shows image path in logs
9. Network tab shows image request returns 200
```

### **Image Display:**
- If image uploaded: Real image loads
- If no image uploaded: SVG placeholder shows (gray box with "No Image")
- If image file deleted later: onerror handler shows placeholder

## Testing Upload

### **Manual Upload Test:**

Via form:
```
1. Go to /dashboard/community/blogs/create
2. Fill in all fields
3. Click "Choose Image" and select a JPG or PNG
4. Click "Create Blog"
5. Check browser console for image path log
6. Check if image loads on blog grid
7. Check /blog-image-diagnostic.php for confirmation
```

Via diagnostic tool:
```
1. Go to /blog-image-diagnostic.php
2. Scroll to "Test Upload" section
3. Click "Choose File" and select an image
4. Click "Test Upload"
5. You should see preview of uploaded image
6. Note the file path
7. Verify it's in "Files in Storage Directory" section
```

## Docker Considerations

The docker-compose.yaml mounts the entire project as a volume:
```yaml
volumes:
  - .:/var/www/html
```

This means:
- Files uploaded in container → automatically saved on host (Windows disk)
- Changes on host → immediately reflected in container
- `/public/storage/blogs/` inside container = same as `c:\Users\amapi\reidhub\public\storage\blogs\` on Windows

## File Structure Reminder

```
public/
  storage/
    blogs/              ← Uploaded images go here
      blog_1770562217_abc123.jpg
      blog_1770562217_xyz789.png
  blog-image-diagnostic.php  ← Diagnostic tool
  index.php
  
storage/               ← Different folder (not used for blogs)
  clubs/
  logs/
  
app/
  controllers/Community/CommunityUserController.php  ← Upload logic
  models/Blog.php      ← Database queries
```

## PHP Settings Check

Default settings in Docker image usually allow:
- `post_max_size`: 8MB (fine for 5MB images)
- `upload_max_filesize`: 2MB (⚠️ May need increase for large images)
- `memory_limit`: 128MB

If needed, these can be adjusted in php.ini or via Docker environment.

## Summary

If images aren't showing:
1. **First:** Run `/blog-image-diagnostic.php` to check configuration
2. **Second:** Upload a test image and check the logs
3. **Third:** Open browser console (F12) to see image path logs
4. **Fourth:** Check Network tab to see if image file loads (200 vs 404)
5. **Fifth:** Check `/public/storage/blogs/` folder directly

Most issues are either:
- **File permissions** (rare in Docker)
- **File wasn't actually uploaded** (check error_log output)
- **Wrong path stored in database** (verify with diagnostic tool)
- **Image file got deleted** (placeholder should show instead)
