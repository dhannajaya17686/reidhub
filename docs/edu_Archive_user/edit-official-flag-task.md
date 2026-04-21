# Edu Archive Task: Edit Official Resource Flag

This guide explains how to complete a standard "Task 3" code-check step by step.

> **Objective:** Build on the previous "Official Resource Flag" task by allowing users to edit this flag after submission. Add the checkbox to the Edit form, pre-fill it based on the database, and update the controller/model to save the changes.

This task focuses on:
1. Pre-filling a checkbox using PHP ternary operators (`checked` attribute).
2. Handling unchecked boxes during a `POST` update request.
3. Updating an existing `UPDATE` SQL query.

---

## Files You Need To Change

- `app/views/User/edu-archive/edit-submission-view.php`
- `app/controllers/EduArchive/EduController.php`
- `app/models/EduResourceModel.php`

---

## Step 1: Add The Checkbox To The Edit View

### File to update
`app/views/User/edu-archive/edit-submission-view.php`

### What to do
Find the section of the form where the `year_level` and `tags` are displayed. Add the checkbox, making sure to use `<?= ... ?>` to output the `checked` attribute if the database says this resource is already official.

### Code to insert
```php
<div class="submission-row">
  <div style="display: flex; align-items: center; gap: 10px; margin-top: 10px;">
    <input 
        id="edit-official" 
        type="checkbox" 
        name="is_official" 
        value="1"
        style="width: 18px; height: 18px;"
        <?= !empty($resource['is_official']) ? 'checked' : '' ?>
    >
    <label class="submission-label" for="edit-official" style="margin-bottom: 0;">
        This is official university material (e.g., past paper, lecture slide)
    </label>
  </div>
</div>
```

---

## Step 2: Read The Field In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`updateSubmission()`

### Capture the data
Find the `$data` collection array (around line 340) and add the `is_official` field to it. 
Just like in the upload process, unchecked checkboxes do not send anything in `$_POST`, so we must use `isset()`.

```php
$data = [
    'title' => trim($_POST['title'] ?? ''),
    'description' => trim($_POST['description'] ?? ''),
    'subject' => trim($_POST['subject'] ?? ''),
    'tags' => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level' => trim($_POST['year_level'] ?? ''),
    'type' => $type,
    'is_official' => isset($_POST['is_official']) ? 1 : 0, // <-- ADD THIS
    'video_link' => null,
    'file_path' => null
];
```

---

## Step 3: Save The Change In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`updateResource($id, $userId, $data)`

### 1. Update the SQL query
Add `is_official = :official` to the `UPDATE` query's `SET` clause:

```php
$sql = "UPDATE edu_resources 
        SET title = :title,
            description = :desc,
            subject = :subject,
            tags = :tags,
            year_level = :year,
            is_official = :official, -- <-- ADD THIS
            type = :type,
            video_link = :link,
            file_path = :file,
            status = 'pending'
        WHERE id = :id AND user_id = :uid AND status = 'pending'";
```

### 2. Bind the new parameter
Map the new placeholder in the `$stmt->execute([...])` array:

```php
$stmt->execute([
    // ... existing parameters ...
    ':year' => $data['year_level'],
    ':official' => $data['is_official'], // <-- ADD THIS
    ':type' => $data['type'],
    // ... existing parameters ...
]);
```
