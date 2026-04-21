# Edu Archive Task: Resource Language Field

This guide explains how to complete a standard "Task 1" code-check step by step.

> **Objective:** Add a "Language" dropdown (e.g., English, Sinhala, Tamil) to the Upload Resource form. Capture this data in the backend, save it to the database, and display it on the resource cards in the public archive.

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
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `language` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN language VARCHAR(50) NOT NULL DEFAULT 'English';
```

### Why this step matters
This prepares the database to receive the new data. If you skip this, the PHP `INSERT` query will throw a fatal SQL error when it tries to save the language field.

---

## Step 2: Add The Input To The Upload Form

### File to update
`app/views/User/edu-archive/upload-view.php`

### Where to insert it
Find the section of the form containing the `year_level` and `type` dropdowns. Add the new Language dropdown right next to them or in a new `submission-row`.

### Code to insert
```php
<div class="submission-row">
  <div>
    <label class="submission-label" for="upload-language">Resource Language</label>
    <select id="upload-language" name="language" class="submission-select" required>
      <option value="English">English</option>
      <option value="Sinhala">Sinhala</option>
      <option value="Tamil">Tamil</option>
    </select>
  </div>
</div>
```

---

## Step 3: Read The Field In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`handleUpload()`

### 1. Define allowed values
Find where `$allowedSubjects` is defined (around line 105), and add an array for allowed languages to prevent malicious inputs:

```php
$allowedLanguages = ['English', 'Sinhala', 'Tamil'];
```

### 2. Capture the data
Add the new field to the `$data` collection array:

```php
$data = [
    'user_id' => $_SESSION['user_id'],
    'title' => trim($_POST['title']),
    'description' => trim($_POST['description']),
    'subject' => $_POST['subject'],
    'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level' => $_POST['year_level'],
    'type' => $_POST['type'],
    'language' => $_POST['language'] // <-- ADD THIS
];
```

### 3. Validate the input
Add the language check to the main `!in_array` validation statement:

```php
if (!in_array($data['subject'], $allowedSubjects, true) ||
    !in_array((string)$data['year_level'], $allowedYears, true) ||
    !in_array($data['type'], $allowedTypes, true) ||
    !in_array($data['language'], $allowedLanguages, true)) { // <-- ADD THIS
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
Add the `language` column and its placeholder `:lang` to the `INSERT INTO` query:

```php
$sql = "INSERT INTO edu_resources (user_id, title, description, subject, tags, type, year_level, language, video_link, file_path, status)
        VALUES (:uid, :title, :desc, :subject, :tags, :type, :year, :lang, :link, :file, 'pending')";
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
    ':lang' => $data['language'], // <-- ADD THIS
    ':link' => $data['video_link'] ?? null,
    ':file' => $data['file_path'] ?? null
]);
```

---

## Step 5: Display The Field On The Archive Page

### File to update
`app/views/User/edu-archive/archive-view.php`

### Where to insert it
Find the `<p class="archive-card-meta">` element inside the `$videosByYear` and `$notesByYear` loops (there are two separate places to update in this file).

### Update it to
```php
<p class="archive-card-meta">
    <?= htmlspecialchars($res['subject']) ?> - <?= htmlspecialchars($yearLabel) ?>
    <br>
    <span style="display:inline-block; margin-top: 5px; font-size: 0.75rem; color: #475569; border: 1px solid #cbd5e1; padding: 2px 6px; border-radius: 4px;">
        🌐 <?= htmlspecialchars($res['language'] ?? 'English') ?>
    </span>
</p>
```

### Why this works automatically
Because the model uses `SELECT r.*` inside the `getAllResources()` method, the newly added database column is automatically fetched and passed into the `$res` array inside the View!
