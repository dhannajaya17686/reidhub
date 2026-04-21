# Edu Archive Admin Task 1: Internal Moderation Notes

This guide explains how to complete a standard "Task 1" code-check for the Admin panel.

> **Objective:** Add an "Internal Notes" text area to the Admin Moderation form (where admins approve resources or edit metadata). Capture this note, save it to the database, and display it in the admin dashboard resource list so other admins can see it. This note should remain invisible to the student who uploaded it.

This task requires:
1. A database schema change.
2. A frontend form change (Admin View).
3. A backend controller change (`EduAdminController`).
4. A model/query change (`EduResourceModel`).
5. A frontend display change (Admin View).

---

## Files You Need To Change

- Your SQL Database client (e.g., phpMyAdmin)
- `app/views/Admin/edu-archive/manage-archive-view.php`
- `app/controllers/EduArchive/EduAdminController.php`
- `app/models/EduResourceModel.php`

---

## Step 1: Add The Database Column

### What to run in your database
Open your database management tool (like phpMyAdmin) and run the following SQL command to add the `internal_notes` column to your `edu_resources` table:

```sql
ALTER TABLE edu_resources
ADD COLUMN internal_notes TEXT NULL AFTER admin_feedback;
```

### Why this step matters
This creates a safe place in the database to store private admin comments. Using `TEXT` allows for longer notes.

---

## Step 2: Add The Input To The Moderation Form

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the HTML modal or form where admins edit a resource's metadata (usually containing inputs for `title`, `description`, `subject`, etc.). 

### Code to insert
Add this new textarea inside the form, perhaps right after the Description field:

```html
<div class="form-group" style="margin-top: 15px;">
    <label for="edit-internal-notes" style="font-weight: bold; color: #b91c1c;">
        🔒 Internal Admin Notes (Hidden from users)
    </label>
    <textarea 
        id="edit-internal-notes" 
        name="internal_notes" 
        rows="3" 
        class="form-control" 
        style="width: 100%; border: 1px solid #fca5a5; background: #fef2f2;"
        placeholder="Leave a private note for other admins..."
    ><?= htmlspecialchars($res['internal_notes'] ?? '') ?></textarea>
</div>
```
*(Note: Ensure your JS that populates the edit modal also passes the `internal_notes` value into this textarea if it uses JavaScript to open the modal).*

---

## Step 3: Read The Field In The Admin Controller

### File to update
`app/controllers/EduArchive/EduAdminController.php`

### Method to update
`validateMetadataInput($post)`

### 1. Capture the data
Find the `$data` collection array inside the `validateMetadataInput` helper method and add the new field to it:

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
        'internal_notes' => trim($post['internal_notes'] ?? '') // <-- ADD THIS
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
Add `internal_notes = :notes` to the `UPDATE` query's `SET` clause:

```php
$sql = "UPDATE edu_resources
        SET title = :title,
            description = :description,
            subject = :subject,
            tags = :tags,
            year_level = :year_level,
            internal_notes = :notes -- <-- ADD THIS
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
    ':notes' => $data['internal_notes'], // <-- ADD THIS
    ':id' => $id
]);
```

---

## Step 5: Display The Field On The Admin Dashboard

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the table or cards where the resources are listed in the admin panel.

### Update it to show the note
Add a small indicator or text block inside the row/card to display the note if it exists:

```php
<?php if (!empty($res['internal_notes'])): ?>
    <div style="margin-top: 8px; padding: 8px; background-color: #fef2f2; border-left: 3px solid #ef4444; font-size: 0.85rem; color: #991b1b;">
        <strong>Admin Note:</strong> <?= nl2br(htmlspecialchars($res['internal_notes'])) ?>
    </div>
<?php endif; ?>
```
