# Edu Archive Admin Task 1: Resource Quality Dropdown

This guide explains how to complete another standard "Task 1" code-check for the Admin panel.

> **Objective:** Add a "Resource Quality" dropdown to the Admin Moderation form. Admins can categorize the quality of the uploaded material as **Standard**, **High Quality**, or **Editor's Choice**. Save this to the database, and display a colored badge in the admin dashboard resource list.

This task requires:
1. A database schema change (`VARCHAR` with a default value).
2. A frontend form change (Admin View - adding a `<select>` dropdown).
3. A backend controller change (`EduAdminController` - reading the post value).
4. A model/query change (`EduResourceModel` - updating the `UPDATE` query).
5. A frontend display change (Admin View - conditional rendering based on the string).

---

## Files You Need To Change

- Your SQL Database client (e.g., phpMyAdmin)
- `app/views/Admin/edu-archive/manage-archive-view.php`
- `app/controllers/EduArchive/EduAdminController.php`
- `app/models/EduResourceModel.php`

---

## Step 1: Add The Database Column

### What to run in your database
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `resource_quality` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN resource_quality VARCHAR(50) NOT NULL DEFAULT 'Standard';
```

### Why this step matters
This creates the column to store the string value from the dropdown. Providing a default of `'Standard'` ensures older resources don't break or have empty values.

---

## Step 2: Add The Dropdown To The Moderation Form

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the HTML form where admins edit a resource's metadata (inside the `foreach ($resources as $resource):` loop). Specifically, locate the `.resource-grid` that contains the Subject and Year dropdowns.

### Code to insert
Add the new Quality dropdown inside the grid or right below it. For example, add another `resource-field`:

```html
<div class="resource-field">
    <label>Resource Quality</label>
    <select name="resource_quality" required>
        <option value="Standard" <?= ($resource['resource_quality'] ?? '') === 'Standard' ? 'selected' : '' ?>>Standard</option>
        <option value="High Quality" <?= ($resource['resource_quality'] ?? '') === 'High Quality' ? 'selected' : '' ?>>High Quality</option>
        <option value="Editor's Choice" <?= ($resource['resource_quality'] ?? '') === "Editor's Choice" ? 'selected' : '' ?>>Editor's Choice 🏆</option>
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
Find the `$data` collection array inside the `validateMetadataInput` helper method and add the new field to it.

```php
private function validateMetadataInput($post) {
    $allowedSubjects = ['CS', 'IS', 'SE'];
    $allowedYears = ['1', '2', '3', '4', '5'];
    $allowedQualities = ['Standard', 'High Quality', "Editor's Choice"]; // <-- ADD WHITELIST

    $data = [
        'title' => trim($post['title'] ?? ''),
        'description' => trim($post['description'] ?? ''),
        'subject' => trim($post['subject'] ?? ''),
        'tags' => $this->normalizeTags($post['tags'] ?? ''),
        'year_level' => trim((string)($post['year_level'] ?? '')),
        'resource_quality' => trim($post['resource_quality'] ?? 'Standard') // <-- ADD THIS
    ];
    
    // Validate the new input
    if (!in_array($data['resource_quality'], $allowedQualities, true)) {
        return [null, 'invalid_quality_input'];
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
Add `resource_quality = :quality` to the `UPDATE` query's `SET` clause:

```php
$sql = "UPDATE edu_resources
        SET title = :title,
            description = :description,
            subject = :subject,
            tags = :tags,
            year_level = :year_level,
            resource_quality = :quality -- <-- ADD THIS
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
    ':quality' => $data['resource_quality'], // <-- ADD THIS
    ':id' => $id
]);
```

---

## Step 5: Display The Badge On The Admin Dashboard

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the `resource-meta` div inside the `resource-card-head` (around line 160).

### Update it to show the badge conditionally
Add an `if` block to display a specific badge depending on the string value saved in the database:

```php
<div class="resource-meta">
    <span>#<?= (int)$resource['id'] ?></span>
    <span class="pill status-<?= htmlspecialchars($status) ?>"><?= htmlspecialchars($statusLabels[$status] ?? ucfirst($status)) ?></span>
    
    <?php if (($resource['resource_quality'] ?? '') === 'High Quality'): ?>
        <span class="pill" style="background-color: #dbeafe; color: #0284c7; border: 1px solid #bae6fd;">High Quality</span>
    <?php elseif (($resource['resource_quality'] ?? '') === "Editor's Choice"): ?>
        <span class="pill" style="background-color: #fce7f3; color: #7e22ce; border: 1px solid #e9d5ff;">🏆 Editor's Choice</span>
    <?php endif; ?>
    
    <?php if ($status === 'approved' && $isHidden): ?><span class="pill warn">Hidden</span><?php endif; ?>
    <?php if ($hasRemovalRequest): ?><span class="pill removal">Removal requested</span><?php endif; ?>
</div>
```
*(Note: We don't display a badge for 'Standard' to keep the UI clean, only highlighting the elevated qualities).*
