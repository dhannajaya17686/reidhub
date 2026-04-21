# Edu Archive Task: Estimated Duration Field

This guide explains how to complete another standard "Task 1" code-check step by step.

> **Objective:** Add an "Estimated Duration (mins)" number field to the Upload Resource form. Capture this integer in the backend, save it to the database, and display it with a clock icon on the resource cards in the public archive.

This is a classic CRUD task that requires:
1. A database schema change
2. A frontend form change (using `type="number"`)
3. A backend controller change (casting to integer)
4. A model/query change
5. A frontend display change

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
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `estimated_duration` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN estimated_duration INT NULL DEFAULT 0;
```

### Why this step matters
This prepares the database to receive the new numerical data. The `INT` type ensures only whole numbers are stored.

---

## Step 2: Add The Input To The Upload Form

### File to update
`app/views/User/edu-archive/upload-view.php`

### Where to insert it
Find the section of the form containing the `subject` or `tags` inputs. Add the new Duration number input inside a `submission-row`.

### Code to insert
```php
<div class="submission-row">
  <div>
    <label class="submission-label" for="upload-duration">Estimated Duration (mins)</label>
    <input 
        id="upload-duration" 
        type="number" 
        name="estimated_duration" 
        class="submission-input" 
        placeholder="e.g., 15" 
        min="1"
        max="600"
        required
    >
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
Find the `$data` collection array (around line 111) and add the new field to it. Since it's a number, cast it using `(int)` to ensure safety:

```php
$data = [
    'user_id' => $_SESSION['user_id'],
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'subject' => $_POST['subject'],
    'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level' => $_POST['year_level'],
    'type' => $_POST['type'],
    'estimated_duration' => (int)($_POST['estimated_duration'] ?? 0) // <-- ADD THIS
];
```

### 2. Validate the input (Optional but recommended)
Add a quick check to make sure the user didn't enter a negative number or zero:

```php
if ($data['estimated_duration'] <= 0) { 
    header("Location: /dashboard/edu-archive/upload?error=invalid_duration");
    exit;
}
```
*(Place this near your other `!in_array` validation checks).*

---

## Step 4: Save The Field In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`createResource($data)`

### 1. Update the SQL query
Add the `estimated_duration` column and its placeholder `:duration` to the `INSERT INTO` query:

```php
$sql = "INSERT INTO edu_resources (user_id, title, description, subject, tags, type, year_level, estimated_duration, video_link, file_path, status)
        VALUES (:uid, :title, :desc, :subject, :tags, :type, :year, :duration, :link, :file, 'pending')";
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
    ':duration' => $data['estimated_duration'], // <-- ADD THIS
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
    <?php if (!empty($res['estimated_duration'])): ?>
    <span style="display:inline-flex; align-items:center; gap: 4px; margin-top: 5px; font-size: 0.75rem; color: #b45309; background: #fef3c7; padding: 2px 8px; border-radius: 12px; font-weight: 600;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <polyline points="12 6 12 12 16 14"></polyline>
        </svg>
        <?= (int)$res['estimated_duration'] ?> mins
    </span>
    <?php endif; ?>
</p>
```

### Why this works automatically
Because the model uses `SELECT r.*` inside the `getAllResources()` method, the newly added `estimated_duration` database column is automatically fetched and available in the `$res` array.
