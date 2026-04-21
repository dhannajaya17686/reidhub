# Edu Archive Task: Add "Estimated Time" to Resource Uploads

This guide explains how to complete a custom two-part task for the Edu Archive:

> **Task 1 (Simple CRUD):** Allow students uploading a resource to specify roughly how many minutes it takes to read or watch it.
> **Task 2 (Business Logic / Validation):** Add strict numeric validation to ensure users don't submit impossible timeframes (e.g., 0, negative, or > 300 minutes).

---

## Task 1: Simple CRUD Operation

### Step 1: Add The Column To The Database
Run this SQL command in your MySQL database to add the new column to the resources table:
```sql
ALTER TABLE edu_resources
ADD COLUMN estimated_minutes INT NULL;
```

### Step 2: Add The Input To The Upload Form
**File:** `app/views/User/edu-archive/upload-view.php`
Find the form and add a numeric input field for `estimated_minutes` (e.g., inside `.submission-row` next to the Year dropdown):
```html
<div>
  <label class="submission-label" for="upload-estimated-minutes">Estimated Minutes</label>
  <input id="upload-estimated-minutes" type="number" name="estimated_minutes" class="submission-input" placeholder="Enter estimated duration">
</div>
```

### Step 3: Capture The Data In The Controller
**File:** `app/controllers/EduArchive/EduController.php`
Find the `handleUpload()` method. Update the `$data` array to capture the new input. Use `isset()` and `(int)` to safely cast the value:
```php
$data = [
    'user_id'           => $_SESSION['user_id'],
    'title'             => trim($_POST['title']),
    'description'       => trim($_POST['description']),
    'subject'           => $_POST['subject'],
    'estimated_minutes' => isset($_POST['estimated_minutes']) ? (int)$_POST['estimated_minutes'] : null, // <-- ADD THIS
    'tags'              => $this->normalizeTags($_POST['tags'] ?? ''),
    'year_level'        => $_POST['year_level'],
    'type'              => $_POST['type']
];
```

### Step 4: Save The Data In The Model
**File:** `app/models/EduResourceModel.php`
Find the `createResource()` method. 

**1. Update the SQL INSERT query:**
```php
$sql = "INSERT INTO edu_resources (user_id, title, description, subject, estimated_minutes, tags, type, year_level, video_link, file_path, status)
        VALUES (:uid, :title, :desc, :subject, :estimated_minutes, :tags, :type, :year, :link, :file, 'pending')";
```

**2. Update the execute array to bind the value:**
```php
return $stmt->execute([
    ':uid'               => $data['user_id'],
    ':title'             => $data['title'],
    ':desc'              => $data['description'],
    ':subject'           => $data['subject'],
    ':estimated_minutes' => $data['estimated_minutes'], // <-- ADD THIS
    ':tags'              => $data['tags'],
    ':type'              => $data['type'],
    ':year'              => $data['year_level'],
    ':link'              => $data['video_link'] ?? null,
    ':file'              => $data['file_path'] ?? null
]);
```

---

## Task 2: Implement Business Logic (Numeric Bounds Validation)

Now we add strict validation to ensure users don't submit invalid times (like negative minutes or unrealistic study durations).

### Step 1: Add Numeric Validation in the Controller
**File:** `app/controllers/EduArchive/EduController.php`
Inside `handleUpload()`, right below where you define the `$data` array, insert this validation logic:

```php
// TASK 2: Validate Estimated Minutes if the user provided one.
// Rule: Must be between 1 and 300 minutes (5 hours).
if ($data['estimated_minutes'] !== null) {
    if ($data['estimated_minutes'] < 1 || $data['estimated_minutes'] > 300) {
        // Reject the submission and redirect back to the form with a specific error
        header("Location: /dashboard/edu-archive/upload?error=invalid_duration");
        exit;
    }
}
```

### Step 2: Test The Business Logic
1. Go to the "Upload Resource" page in your browser.
2. Fill out the required fields. Type `-5` or `1000` into the Estimated Minutes field and submit. It should **fail**, refuse to save to the database, and redirect you with `?error=invalid_duration` in the URL.
3. Type a valid duration like `45` and submit. It should **pass** and save to the database successfully!
