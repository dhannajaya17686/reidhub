# Edu Forum Task: Edit Reason (JS Modal)

This guide explains how to complete this Edu Forum code-check task step by step:

> Add an `edit_reason` field to the Question Edit Modal. Save it to the database when a user updates their question, and clear the input via JavaScript when the modal is closed.

This tests your ability to hook HTML forms into existing JavaScript functions.

---

## Files You Need To Change

- sql/edu-hub/Questions-Table.sql
- app/views/User/edu-forum/one-question-view.php
- app/controllers/Forum/ForumUserController.php
- app/models/ForumModel.php

---

## Step 1: Add The Column To The Database

Run this SQL command:

```sql
ALTER TABLE forum_questions
ADD COLUMN edit_reason VARCHAR(255) NULL;
```

---

## Step 2: Add The Input To The Edit Modal

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Scroll down to `<div id="editModal" class="modal-overlay">`. Inside the form, below the Content textarea, add:

```html
<div class="form-group" id="editReasonGroup">
    <label for="editReason" class="form-label">Reason for Edit (Optional)</label>
    <input type="text" name="edit_reason" id="editReason" class="form-input" placeholder="e.g., Fixed typos">
</div>
```

---

## Step 3: Update The JavaScript

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Scroll to the bottom of the file to find `function initializeEditModal()`. Update it so the edit reason clears out when a new edit starts.

**Inside `window.openEditModal = function(...)` add:**
```javascript
document.getElementById('editReason').value = ''; // Clear old reason

if (type === 'question') {
    document.getElementById('editReasonGroup').style.display = 'block';
} else {
    document.getElementById('editReasonGroup').style.display = 'none'; // Hide for answers
}
```

---

## Step 4: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `updateContent()` method.

**Current code:**
```php
$type = $_POST['type'];
$id = $_POST['id'];
$content = trim($_POST['content']);
$title = isset($_POST['title']) ? trim($_POST['title']) : null;
```

**Add the new field:**
```php
$editReason = trim($_POST['edit_reason'] ?? '');
```

**Update the model call:**
Find:
```php
$success = $forumModel->updateQuestion($id, $_SESSION['user_id'], $title, $content);
```
Change to:
```php
$success = $forumModel->updateQuestion($id, $_SESSION['user_id'], $title, $content, $editReason);
```

---

## Step 5: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `updateQuestion()` method. 

**Update SQL and Execute:**
```php
public function updateQuestion($id, $userId, $title, $content, $editReason = null) {
    if (!$this->checkOwnership('question', $id, $userId)) return false;

    $sql = "UPDATE forum_questions 
            SET title = :title, 
                content = :content, 
                edit_reason = :reason 
            WHERE id = :id";
            
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':title'   => $title, 
        ':content' => $content, 
        ':reason'  => $editReason,
        ':id'      => $id
    ]);
}
```

---

## Step 6: Display It On The Question Page

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find `<h1 class="question-title-main">`. Right below it, add:

```php
<?php if (!empty($question['edit_reason'])): ?>
    <p style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">
        Edited: <?= htmlspecialchars($question['edit_reason']) ?>
    </p>
<?php endif; ?>
```

Test it by editing a question you own!
