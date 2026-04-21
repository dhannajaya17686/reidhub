# Edu Archive Admin Task 1: Featured Resource Checkbox

This guide explains how to complete another standard "Task 1" code-check for the Admin panel.

> **Objective:** Add a "Mark as Featured" checkbox to the Admin Moderation form. Admins can check this box to highlight exceptional study materials. Save this boolean data (1 or 0) to the database, and display a "⭐ Featured" badge on the resource in the admin dashboard list.

This task requires:
1. A database schema change (`TINYINT` for boolean).
2. A frontend form change (Admin View - adding a checkbox).
3. A backend controller change (`EduAdminController` - handling unchecked states).
4. A model/query change (`EduResourceModel`).
5. A frontend display change (Admin View - conditional rendering).

---

## Files You Need To Change

- Your SQL Database client (e.g., phpMyAdmin)
- `app/views/Admin/edu-archive/manage-archive-view.php`
- `app/controllers/EduArchive/EduAdminController.php`
- `app/models/EduResourceModel.php`

---

## Step 1: Add The Database Column

### What to run in your database
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `is_featured` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN is_featured TINYINT(1) NOT NULL DEFAULT 0;
```

### Why this step matters
MySQL uses `TINYINT(1)` to represent booleans (`1` for true/featured, `0` for false/standard). The default of `0` ensures existing resources aren't accidentally featured.

---

## Step 2: Add The Checkbox To The Moderation Form

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the HTML form where admins edit a resource's metadata (inside the `foreach ($resources as $resource):` loop). 

### Code to insert
Add this checkbox group, for example, right above the `resource-action-zone` (the row of save/approve buttons):

```html
<div class="resource-field resource-field-full" style="display: flex; align-items: center; gap: 8px; margin-bottom: 15px;">
    <input 
        type="checkbox" 
        name="is_featured" 
        id="featured-<?= (int)$resource['id'] ?>" 
        value="1" 
        style="width: 18px; height: 18px; cursor: pointer;"
        <?= !empty($resource['is_featured']) ? 'checked' : '' ?>
    >
    <label for="featured-<?= (int)$resource['id'] ?>" style="margin: 0; font-weight: bold; cursor: pointer; color: #d97706;">
        ⭐ Mark as Featured Resource
    </label>
</div>
```
*(Note: The `<?= !empty(...) ? 'checked' : '' ?>` logic ensures the checkbox correctly shows what is currently saved in the database).*

---

## Step 3: Read The Field In The Admin Controller

### File to update
`app/controllers/EduArchive/EduAdminController.php`

### Method to update
`validateMetadataInput($post)`

### 1. Capture the data
Find the `$data` collection array inside the `validateMetadataInput` helper method and add the new field to it.
**Important:** Unchecked checkboxes do not send anything in `$_POST`. You must use `isset()` or `!empty()` to determine if it was checked.

```php
private function validateMetadataInput($post) {
    $allowedSubjects = ['CS', 'IS', 'SE'];
    $allowedYears = ['1', '2', '3', '4', '5'];

    $data = [
        'title' => trim($post['title'] ?? ''),
        'description' => trim($post['description'] ?? ''),
        'subject' => trim($post['subject'] ?? ''),
        'tags' => $this->normalizeTags($post['tags'] ?? ''),
        'year_level' => trim((string)($post['year_level'] ?? '')),
        'is_featured' => isset($post['is_featured']) ? 1 : 0 // <-- ADD THIS
    ];
    
    // ... existing validation ...
```

---

## Step 4: Save The Field In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`updateResourceMetadataByAdmin($id, $data)`

### 1. Update the SQL query
Add `is_featured = :featured` to the `UPDATE` query's `SET` clause:

```php
$sql = "UPDATE edu_resources
        SET title = :title,
            description = :description,
            subject = :subject,
            tags = :tags,
            year_level = :year_level,
            is_featured = :featured -- <-- ADD THIS
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
    ':featured' => $data['is_featured'], // <-- ADD THIS
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
Add a small conditional block to display a "Featured" pill if the resource has `is_featured` set to 1:

```php
<div class="resource-meta">
    <span>#<?= (int)$resource['id'] ?></span>
    <span class="pill status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusLabels[$status] ?? ucfirst($status)) ?></span>
    
    <?php if (!empty($resource['is_featured'])): ?>
        <span class="pill" style="background-color: #fef3c7; color: #b45309; border: 1px solid #fde68a;">⭐ Featured</span>
    <?php endif; ?>
    
    <?php if ($status === 'approved' && $isHidden): ?><span class="pill warn">Hidden</span><?php endif; ?>
    <?php if ($hasRemovalRequest): ?><span class="pill removal">Removal requested</span><?php endif; ?>
</div>
```
