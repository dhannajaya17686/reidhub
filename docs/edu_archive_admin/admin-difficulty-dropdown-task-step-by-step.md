# Edu Archive Admin Task 1: Resource Difficulty Dropdown

This guide explains how to complete another standard "Task 1" code-check for the Admin panel.

> **Objective:** Add a "Difficulty Level" dropdown to the Admin Moderation form. Admins can classify the material as **Beginner**, **Intermediate**, or **Advanced**. Save this to the database, and display a small difficulty badge in the admin dashboard resource list.

This task requires:
1. A database schema change (`VARCHAR` with a default value).
2. A frontend form change (Admin View - adding a `<select>` dropdown).
3. A backend controller change (`EduAdminController` - reading and validating the post value).
4. A model/query change (`EduResourceModel` - updating the `UPDATE` query).
5. A frontend display change (Admin View - rendering the data).

---

## Files You Need To Change

- Your SQL Database client (e.g., phpMyAdmin)
- `app/views/Admin/edu-archive/manage-archive-view.php`
- `app/controllers/EduArchive/EduAdminController.php`
- `app/models/EduResourceModel.php`

---

## Step 1: Add The Database Column

### What to run in your database
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `difficulty_level` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN difficulty_level VARCHAR(50) NOT NULL DEFAULT 'Beginner';
```

### Why this step matters
This creates the column to store the string value from the dropdown. Providing a default of `'Beginner'` ensures older resources don't break or have completely empty values.

---

## Step 2: Add The Dropdown To The Moderation Form

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the HTML form where admins edit a resource's metadata (inside the `foreach ($resources as $resource):` loop). Specifically, locate the `.resource-grid` that contains the Subject and Year dropdowns.

### Code to insert
Add the new Difficulty dropdown inside that grid layout:

```html
<div class="resource-field">
    <label>Difficulty Level</label>
    <select name="difficulty_level" required>
        <option value="Beginner" <?= ($resource['difficulty_level'] ?? '') === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
        <option value="Intermediate" <?= ($resource['difficulty_level'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
        <option value="Advanced" <?= ($resource['difficulty_level'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
    </select>
</div>
```
*(Note: The `<?= ... ? 'selected' : '' ?>` logic ensures the dropdown correctly shows what is currently saved in the database).*

---

## Step 3: Read The Field In The Admin Controller

### File to update
`app/controllers/EduArchive/EduAdminController.php`

### Method to update
`validateMetadataInput($post)`

### 1. Capture the data
Find the `$data` collection array inside the `validateMetadataInput` helper method and add the new field to it. Also, set up a whitelist array to validate the input securely.

```php
private function validateMetadataInput($post) {
    $allowedSubjects = ['CS', 'IS', 'SE'];
    $allowedYears = ['1', '2', '3', '4', '5'];
    $allowedDifficulties = ['Beginner', 'Intermediate', 'Advanced']; // <-- ADD WHITELIST

    $data = [
        'title' => trim($post['title'] ?? ''),
        'description' => trim($post['description'] ?? ''),
        'subject' => trim($post['subject'] ?? ''),
        'tags' => $this->normalizeTags($post['tags'] ?? ''),
        'year_level' => trim((string)($post['year_level'] ?? '')),
        'difficulty_level' => trim($post['difficulty_level'] ?? 'Beginner') // <-- ADD THIS
    ];
    
    if (!in_array($data['difficulty_level'], $allowedDifficulties, true)) {
        return [null, 'invalid_difficulty_input']; // <-- VALIDATE IT
    }
    
    // ... existing validation ...
```

---

## Step 4: Save The Field In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`updateResourceMetadataByAdmin($id, $data)`

### 1. Update the SQL query
Add `difficulty_level = :difficulty` to the `UPDATE` query's `SET` clause:

```php
$sql = "UPDATE edu_resources
        SET title = :title,
            description = :description,
            subject = :subject,
            tags = :tags,
            year_level = :year_level,
            difficulty_level = :difficulty -- <-- ADD THIS
        WHERE id = :id";
```

### 2. Bind the new parameter
Map the new placeholder in the `$stmt->execute([...])` array:

```php
return $stmt->execute([
    ':title' => $data['title'],
    ':description' => $data['description'],
    ':subject' => $data['subject'],
    ':tags' => $data['tags'],
    ':year_level' => $data['year_level'],
    ':difficulty' => $data['difficulty_level'], // <-- ADD THIS
    ':id' => $id
]);
```

---

## Step 5: Display The Badge On The Admin Dashboard

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the `resource-meta` div inside the `resource-card-head` (around line 160).

### Update it to show the badge
Add a small badge to display the currently saved difficulty:

```php
<div class="resource-meta">
    <span>#<?= (int)$resource['id'] ?></span>
    <span class="pill status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusLabels[$status] ?? ucfirst($status)) ?></span>
    
    <?php if (!empty($resource['difficulty_level'])): ?>
        <span class="pill" style="background-color: #f3f4f6; color: #4b5563; border: 1px solid #d1d5db;">
            🎓 <?= htmlspecialchars($resource['difficulty_level']) ?>
        </span>
    <?php endif; ?>
    
    <?php if ($status === 'approved' && $isHidden): ?><span class="pill warn">Hidden</span><?php endif; ?>
    <?php if ($hasRemovalRequest): ?><span class="pill removal">Removal requested</span><?php endif; ?>
</div>
```
