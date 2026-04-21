# Edu Archive Task: Resource Institution Field

This guide explains how to complete another standard "Task 1" code-check step by step.

> **Objective:** Add an "Institution" text field (e.g., UCSC, UoM, SLIIT) to the Upload Resource form. Capture this data in the backend, save it to the database, and display it on the resource cards in the public archive.

This is a classic CRUD task that requires:
1. A database schema change
2. A frontend form change
3. A backend controller change
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
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `institution` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN institution VARCHAR(100) NULL DEFAULT 'UCSC';
```

### Why this step matters
This prepares the database to receive the new data. If you skip this, the PHP `INSERT` query will throw a fatal SQL error when it tries to save the institution field.

---

## Step 2: Add The Input To The Upload Form

### File to update
`app/views/User/edu-archive/upload-view.php`

### Where to insert it
Find the section of the form containing the `subject` or `tags` inputs. Add the new Institution text input inside a `submission-row`.

### Code to insert
```php
<div class="submission-row">
  <div>
    <label class="submission-label" for="upload-institution">Institution / University</label>
    <input 
        id="upload-institution" 
        type="text" 
        name="institution" 
        class="submission-input" 
        placeholder="e.g., UCSC, UoM, SLIIT" 
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
Find the `$data` collection array (around line 111) and add the new field to it. Since it's a text input, we use `trim()` and `htmlspecialchars()` (or just strip tags) to clean it:

```php
$data = [
    'user_id' => $_SESSION['user_id'],
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'subject' => $_POST['subject'],
    'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level' => $_POST['year_level'],
    'type' => $_POST['type'],
    'institution' => trim(strip_tags($_POST['institution'] ?? '')) // <-- ADD THIS
];
```

### 2. Validate the input (Optional but recommended)
Add a quick check to make sure the user didn't leave it completely blank after trimming:

```php
if (empty($data['title']) || empty($data['subject']) || empty($data['year_level']) || empty($data['type']) || empty($data['institution'])) { // <-- Added institution here
    header("Location: /dashboard/edu-archive/upload?error=missing_fields");
    exit;
}
```

---

## Step 4: Save The Field In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`createResource($data)`

### 1. Update the SQL query
Add the `institution` column and its placeholder `:inst` to the `INSERT INTO` query:

```php
$sql = "INSERT INTO edu_resources (user_id, title, description, subject, tags, type, year_level, institution, video_link, file_path, status)
        VALUES (:uid, :title, :desc, :subject, :tags, :type, :year, :inst, :link, :file, 'pending')";
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
    ':inst' => $data['institution'], // <-- ADD THIS
    ':link' => $data['video_link'] ?? null,
    ':file' => $data['file_path'] ?? null
]);
```

---

## Step 5: Display The Field On The Archive Page

### File to update
`app/views/User/edu-archive/archive-view.php`

### Where to insert it
Find the `<p class="archive-card-meta">` element inside the `$videosByYear` and `$notesByYear` loops (there are two separate places to update in this file, just like the Language task).

### Update it to
```php
<p class="archive-card-meta">
    <?= htmlspecialchars($res['subject']) ?> - <?= htmlspecialchars($yearLabel) ?>
    <br>
    <span style="display:inline-flex; align-items:center; gap: 4px; margin-top: 5px; font-size: 0.75rem; color: #0f172a; background: #f1f5f9; padding: 2px 8px; border-radius: 12px; font-weight: 500;">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <?= htmlspecialchars($res['institution'] ?? 'UCSC') ?>
    </span>
</p>
```

### Why this works automatically
Because the model uses `SELECT r.*` inside the `getAllResources()` method, the newly added `institution` database column is automatically fetched and passed into the `$res` array inside the View!
