# Edu Archive Task 1: Difficulty Level Field

This guide explains how to complete this Edu Archive code-check task step by step:

> Add a `difficulty_level` dropdown to the Upload Resource form. Save it in the `edu_resources` table and display it as a badge on the resource cards in the public archive.

This is a standard CRUD-style task because it includes:

1. frontend change
2. controller/backend change
3. model/query change
4. database schema change
5. UI display change

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

Open your database tool and run the following SQL command to add the column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN difficulty_level VARCHAR(50) NOT NULL DEFAULT 'Beginner';
```

### Why this step matters

This creates the field in the database. If you skip this, the PHP insert query will crash because MySQL won't know where to save the difficulty data.

---

## Step 2: Add The Input To The Upload Form

### File to update

`app/views/User/edu-archive/upload-view.php`

### Where to insert it

Find the `<div class="submission-row">` that contains the **Year** and **Tags** inputs. Add the new dropdown right above or below them. For example, replace the entire Year/Tags row with a 3-column layout, or just add it in a new row:

### Code to insert

```php
<div class="submission-row">
  <div>
    <label class="submission-label" for="upload-difficulty">Difficulty Level</label>
    <select id="upload-difficulty" name="difficulty_level" class="submission-select" required>
      <option value="Beginner">Beginner</option>
      <option value="Intermediate">Intermediate</option>
      <option value="Advanced">Advanced</option>
    </select>
  </div>
  
  <!-- Keep the existing Tags input next to it -->
  <div>
    <label class="submission-label" for="upload-tags">Tags</label>
    <input id="upload-tags" type="text" name="tags" class="submission-input" placeholder="database, os, algorithms">
  </div>
</div>
```

### Why this step matters

The `name="difficulty_level"` attribute is what PHP will read from the `$_POST` array when the user clicks submit.

---

## Step 3: Read The Field In The Controller

### File to update

`app/controllers/EduArchive/EduController.php`

### Method to update

`handleUpload()`

### Current code pattern

You will see the `$allowedSubjects` and `$allowedYears` arrays, followed by the `$data` collection array.

### Update it to include Difficulty

**1. Add the allowed values:**
```php
$allowedDifficulties = ['Beginner', 'Intermediate', 'Advanced'];
```

**2. Add it to the `$data` array:**
```php
$data = [
    'user_id' => $_SESSION['user_id'],
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'subject' => $_POST['subject'],
    'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level' => $_POST['year_level'],
    'type' => $_POST['type'],
    'difficulty_level' => $_POST['difficulty_level'] // <-- ADD THIS
];
```

**3. Add it to the validation check:**
```php
if (!in_array($data['difficulty_level'], $allowedDifficulties, true)) {
    header("Location: /dashboard/edu-archive/upload?error=invalid_input");
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

Add `difficulty_level` to the `INSERT INTO` columns and the `VALUES` placeholders:

```php
$sql = "INSERT INTO edu_resources (user_id, title, description, subject, tags, type, year_level, difficulty_level, video_link, file_path, status)
        VALUES (:uid, :title, :desc, :subject, :tags, :type, :year, :difficulty, :link, :file, 'pending')";
```

### 2. Update the execute array

Bind the new placeholder:

```php
return $stmt->execute([
    ':uid' => $data['user_id'],
    ':title' => $data['title'],
    ':desc' => $data['description'],
    ':subject' => $data['subject'],
    ':tags' => $data['tags'],
    ':type' => $data['type'],
    ':year' => $data['year_level'],
    ':difficulty' => $data['difficulty_level'], // <-- ADD THIS
    ':link' => $data['video_link'] ?? null,
    ':file' => $data['file_path'] ?? null
]);
```

---

## Step 5: Display Difficulty On The Archive Page

### File to update

`app/views/User/edu-archive/archive-view.php`

### Where to insert it

Find the `<p class="archive-card-meta">` element inside the `$videosByYear` and `$notesByYear` loops.

### Update it to

```php
<p class="archive-card-meta">
    <?= htmlspecialchars($res['subject']) ?> - <?= htmlspecialchars($yearLabel) ?>
    <br>
    <span style="display:inline-block; padding: 2px 8px; margin-top: 6px; background: #e0f2fe; color: #0284c7; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
        <?= htmlspecialchars($res['difficulty_level'] ?? 'Beginner') ?>
    </span>
</p>
```

### Why this works

Because the model uses `SELECT r.*` in `getAllResources()`, the new column is automatically available inside the `$res` array when iterating through the resources!
