# Edu Archive Task: Official Resource Flag

This guide explains how to complete another standard "Task 1" code-check step by step.

> **Objective:** Add an "Is this official university material?" checkbox to the Upload form. Capture this boolean data (1 or 0) in the backend, save it to the database, and display an "Official" badge on the resource cards in the public archive.

This task requires:
1. A database schema change (`TINYINT` for boolean).
2. A frontend form change (using `type="checkbox"`).
3. A backend controller change (handling unchecked states).
4. A model/query change.
5. A frontend display change (conditional rendering).

---

## Files You Need To Change

- Your SQL Database client (e.g., phpMyAdmin)
- `app/views/User/edu-archive/upload-view.php`
- `app/controllers/EduArchive/EduController.php`
- `app/models/EduResourceModel.php`
- `app/views/User/edu-archive/archive-view.php`

---

## Step 1: Add The Database Column

### What to run in your database
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `is_official` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN is_official TINYINT(1) NOT NULL DEFAULT 0;
```

### Why this step matters
MySQL uses `TINYINT(1)` to represent booleans (`1` for true, `0` for false). Setting a default of `0` ensures old resources don't break.

---

## Step 2: Add The Input To The Upload Form

### File to update
`app/views/User/edu-archive/upload-view.php`

### Where to insert it
Find the section of the form containing the `subject` or `tags` inputs. Add the new checkbox inside a `submission-row`.

### Code to insert
```php
<div class="submission-row">
  <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
    <input 
        id="upload-official" 
        type="checkbox" 
        name="is_official" 
        value="1"
        style="width: 18px; height: 18px;"
    >
    <label class="submission-label" for="upload-official" style="margin-bottom: 0;">
        This is official university material (e.g., past paper, lecture slide)
    </label>
  </div>
</div>
```

---

## Step 3: Read The Field In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`handleUpload()`

### 1. Capture the data
Find the `$data` collection array (around line 111) and add the new field to it. 
**Important:** Unchecked checkboxes do not send anything in `$_POST`. You must use `isset()` or `!empty()` to determine if it was checked.

```php
$data = [
    'user_id' => $_SESSION['user_id'],
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'subject' => $_POST['subject'],
    'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level' => $_POST['year_level'],
    'type' => $_POST['type'],
    'is_official' => isset($_POST['is_official']) ? 1 : 0 // <-- ADD THIS
];
```

---

## Step 4: Save The Field In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`createResource($data)`

### 1. Update the SQL query
Add the `is_official` column and its placeholder `:official` to the `INSERT INTO` query:

```php
$sql = "INSERT INTO edu_resources (user_id, title, description, subject, tags, type, year_level, is_official, video_link, file_path, status)
        VALUES (:uid, :title, :desc, :subject, :tags, :type, :year, :official, :link, :file, 'pending')";
```

### 2. Bind the new parameter
Map the new placeholder in the `$stmt->execute([...])` array:

```php
return $stmt->execute([
    ':uid' => $data['user_id'],
    ':title' => $data['title'],
    ':desc' => $data['description'],
    ':subject' => $data['subject'],
    ':tags' => $data['tags'],
    ':type' => $data['type'],
    ':year' => $data['year_level'],
    ':official' => $data['is_official'], // <-- ADD THIS
    ':link' => $data['video_link'] ?? null,
    ':file' => $data['file_path'] ?? null
]);
```

---

## Step 5: Display The Field On The Archive Page

### File to update
`app/views/User/edu-archive/archive-view.php`

### Where to insert it
Find the `<p class="archive-card-meta">` element inside the `$videosByYear` and `$notesByYear` loops (remember to update both the Video and Note card templates).

### Update it to
```php
<p class="archive-card-meta">
    <?= htmlspecialchars($res['subject']) ?> - <?= htmlspecialchars($yearLabel) ?>
    <br>
    
    <?php if (!empty($res['is_official'])): ?>
    <span style="display:inline-flex; align-items:center; gap: 4px; margin-top: 5px; font-size: 0.75rem; color: #047857; background: #d1fae5; padding: 2px 8px; border-radius: 12px; font-weight: 600;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
            <polyline points="9 12 11 14 15 10"></polyline>
        </svg>
        Official Material
    </span>
    <?php endif; ?>
</p>
```

### Why this works automatically
Because the model uses `SELECT r.*` inside the `getAllResources()` method, the newly added `is_official` database column is automatically fetched. We wrap the badge in an `if (!empty(...))` check so it only appears for resources that have the flag set to `1`.
