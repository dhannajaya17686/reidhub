# Edu Forum Task: Urgent Checkbox

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a "Mark as Urgent" checkbox to the Ask Question form. Save it as a boolean/tinyint in the database and display a red URGENT badge on the question card if checked.

This tests your ability to handle HTML checkboxes properly in PHP.

---

## Step 1: Add The Column To The Database

**File to update:** `sql/edu-hub/Questions-Table.sql`

Run this SQL command in your database to add the boolean column:

```sql
ALTER TABLE forum_questions
ADD COLUMN is_urgent TINYINT(1) NOT NULL DEFAULT 0;
```

---

## Step 2: Add The Input To The Ask Question Form

**File to update:** `app/views/User/edu-forum/add-question-view.php`

Find the Ask Question form. Insert this below the **Category** section.

```html
<div class="form-group" style="display: flex; align-items: center; gap: 10px; background: #fee2e2; padding: 15px; border-radius: 8px;">
  <input type="checkbox" id="question-urgent" name="is_urgent" value="1" style="width: 18px; height: 18px;">
  <label for="question-urgent" style="color: #991b1b; font-weight: bold; cursor: pointer;">
    Mark this question as URGENT
  </label>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createQuestion()` method. 

**Update the `$data` array. Use `isset()` because unchecked checkboxes do not send anything in `$_POST`:**
```php
$data = [
    'title'     => trim($_POST['title']),
    'category'  => trim($_POST['category']),
    'is_urgent' => isset($_POST['is_urgent']) ? 1 : 0, // <-- ADD THIS
    'content'   => trim($_POST['description']),
    'tags'      => trim($_POST['tags'] ?? '')
];
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `createQuestion($userId, $data)` method.

**1. Update the SQL query:**
```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, is_urgent, tags) 
        VALUES (:uid, :title, :content, :cat, :is_urgent, :tags)";
```

**2. Update the execute array:**
```php
return $stmt->execute([
    ':uid'       => $userId,
    ':title'     => $data['title'],
    ':content'   => $data['content'],
    ':cat'       => $data['category'],
    ':is_urgent' => $data['is_urgent'], // <-- ADD THIS
    ':tags'      => $tags
]);
```

---

## Step 5: Display The Badge On The All Questions Page

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the `foreach ($questions as $q):` loop. Inside the `<div class="question-tags">` section, insert this right before the existing tags loop:

```php
<?php if ($q['is_urgent'] == 1): ?>
    <span class="question-tag" style="background-color: #ef4444; color: white; border: none; font-weight: bold;">
        🚨 URGENT
    </span>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to `/dashboard/forum/add`.
2. Check the "Mark this question as URGENT" box.
3. Submit the question.
4. Verify the red 🚨 URGENT badge appears on the feed!
