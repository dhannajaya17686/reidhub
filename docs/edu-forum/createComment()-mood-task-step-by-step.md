# Edu Forum Tasks: Comment Enhancements

This document covers two potential code-check tasks for the Comment system.

---

## Task 1: Comment Type (Standard CRUD, No JS)

> Add a `comment_type` dropdown to the comment form (e.g., General, Clarification, Correction). Save it to the database and display it as a small badge on the published comment.

This tests your ability to do a basic PHP/HTML CRUD operation on the **Comment** entity without needing any JavaScript.

### Step 1: Add The Column To The Database

Run this SQL command in your database:

```sql
ALTER TABLE forum_comments
ADD COLUMN comment_type VARCHAR(50) NOT NULL DEFAULT 'General';
```

### Step 2: Add The Input To The Comment Form

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the comment form inside the `<div class="comment-compose-box">`. 

**Code to insert (inside the form, right above the textarea):**
```html
<div class="form-group" style="margin-bottom: 10px;">
    <label for="comment-type" style="font-size: 0.85rem; color: var(--text-secondary);">Comment Type:</label>
    <select name="comment_type" id="comment-type" style="padding: 4px; border-radius: 4px; border: 1px solid var(--border-color); font-size: 0.85rem;">
        <option value="General">General</option>
        <option value="Clarification">Clarification</option>
        <option value="Correction">Correction</option>
    </select>
</div>
```

### Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createComment()` method.

**Add the new field:**
```php
$parentType  = $_POST['parent_type']; 
$parentId    = $_POST['parent_id'];
$content     = trim($_POST['content']);
$questionId  = $_POST['redirect_id']; 
$commentType = $_POST['comment_type'] ?? 'General'; // <-- ADD THIS
```

**Update the model call:**
```php
$forumModel->addComment($_SESSION['user_id'], $parentType, $parentId, $content, $commentType);
```

### Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `addComment()` method. Update the parameters, SQL, and execute array:

```php
public function addComment($userId, $parentType, $parentId, $content, $commentType = 'General') {
    $sql = "INSERT INTO forum_comments (user_id, parent_type, parent_id, content, comment_type) 
            VALUES (:uid, :ptype, :pid, :content, :ctype)";
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':uid'     => $userId, 
        ':ptype'   => $parentType, 
        ':pid'     => $parentId, 
        ':content' => $content,
        ':ctype'   => $commentType
    ]);
}
```

### Step 5: Display It On The Comment

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the `$question_comments` loop. Locate the author name span: `<span class="author-name">...</span>`.

**Code to insert (right after the author name):**
```php
<?php if (!empty($comment['comment_type']) && $comment['comment_type'] !== 'General'): ?>
    <span style="font-size: 0.7rem; background: #e0f2fe; color: #0284c7; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">
        <?= htmlspecialchars($comment['comment_type']) ?>
    </span>
<?php endif; ?>
```

---

## Task 2: Comment Mood / Emoji (With JS)

> Add a mood/emoji selector to the comment form. When a user clicks an emoji, use JavaScript to update a hidden input before submitting the comment. Display the chosen emoji next to the author's name on the published comment.

This tests your ability to link standard HTML form submissions with lightweight JavaScript DOM manipulation.

---

## Files You Need To Change

- sql/edu-hub/Questions-Table.sql (or directly in MySQL)
- app/views/User/edu-forum/one-question-view.php
- app/controllers/Forum/ForumUserController.php
- app/models/ForumModel.php

---

## Step 1: Add The Column To The Database

Run this SQL command in your database to add the mood column to the comments table:

```sql
ALTER TABLE forum_comments
ADD COLUMN mood VARCHAR(20) NULL;
```

---

## Step 2: Add The Hidden Input and Emoji Buttons

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the comment form inside the `<div class="comment-compose-box">`. 
There are *two* comment forms (one for the question, one in the answer loop), but for the code check, modifying the main Question comment form is usually sufficient. Let's update the Question comment form.

**Code to insert (inside the form, right above the textarea):**
```html
<div class="mood-selector" style="margin-bottom: 8px;">
    <span style="font-size: 0.8rem; color: var(--text-muted); margin-right: 5px;">Mood:</span>
    <!-- Hidden input that actually gets submitted -->
    <input type="hidden" name="mood" class="comment-mood-input" value="">
    
    <!-- Buttons to change the hidden input -->
    <button type="button" style="border: 1px solid #ccc; background: #fff; border-radius: 4px; padding: 2px 5px; cursor: pointer;" onclick="this.parentElement.querySelector('.comment-mood-input').value='👍'; this.style.borderColor='#0466C8';">👍</button>
    <button type="button" style="border: 1px solid #ccc; background: #fff; border-radius: 4px; padding: 2px 5px; cursor: pointer;" onclick="this.parentElement.querySelector('.comment-mood-input').value='❓'; this.style.borderColor='#0466C8';">❓</button>
    <button type="button" style="border: 1px solid #ccc; background: #fff; border-radius: 4px; padding: 2px 5px; cursor: pointer;" onclick="this.parentElement.querySelector('.comment-mood-input').value='💡'; this.style.borderColor='#0466C8';">💡</button>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createComment()` method.

**Current code:**
```php
$parentType = $_POST['parent_type']; 
$parentId   = $_POST['parent_id'];
$content    = trim($_POST['content']);
$questionId = $_POST['redirect_id']; 
```

**Add the new field:**
```php
$parentType = $_POST['parent_type']; 
$parentId   = $_POST['parent_id'];
$content    = trim($_POST['content']);
$questionId = $_POST['redirect_id']; 
$mood       = $_POST['mood'] ?? null; // <-- ADD THIS
```

**Update the model call:**
Find:
```php
$forumModel->addComment($_SESSION['user_id'], $parentType, $parentId, $content);
```
Change to:
```php
$forumModel->addComment($_SESSION['user_id'], $parentType, $parentId, $content, $mood);
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `addComment()` method. Update the parameters, SQL, and execute array:

```php
public function addComment($userId, $parentType, $parentId, $content, $mood = null) {
    $sql = "INSERT INTO forum_comments (user_id, parent_type, parent_id, content, mood) 
            VALUES (:uid, :ptype, :pid, :content, :mood)";
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':uid'     => $userId, 
        ':ptype'   => $parentType, 
        ':pid'     => $parentId, 
        ':content' => $content,
        ':mood'    => $mood
    ]);
}
```

---

## Step 5: Display It On The Comment

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the `$question_comments` loop. Locate the author name span: `<span class="author-name">...</span>`.

**Code to insert (right after the author name):**
```php
<?php if (!empty($comment['mood'])): ?>
    <span style="margin-left: 5px; font-size: 0.9rem;" title="Mood">
        <?= htmlspecialchars($comment['mood']) ?>
    </span>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to a question detail page.
2. Click the 👍 button in the comment compose box (the border will turn blue).
3. Type your comment and submit.
4. Verify the 👍 emoji appears next to your name in the comment list!
