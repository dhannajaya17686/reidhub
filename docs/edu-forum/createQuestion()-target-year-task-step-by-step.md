# Edu Forum Task: Target Year Level

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a `target_year` dropdown to the Ask Question form so users can specify if their question is meant for 1st Year, 2nd Year, 3rd Year, or 4th Year students. Save it and display it as a badge on the question card.

This is a standard PHP/HTML CRUD task. No JavaScript is required.

---

## Step 1: Add The Column To The Database

**File to update:** `sql/edu-hub/Questions-Table.sql`

Run this SQL command in your database to add the column:

```sql
ALTER TABLE forum_questions
ADD COLUMN target_year VARCHAR(20) NULL;
```

---

## Step 2: Add The Input To The Ask Question Form

**File to update:** `app/views/User/edu-forum/add-question-view.php`

Find the Ask Question form. Insert this after the **Category** section and before the **Question Description**.

```html
<div class="form-group">
  <label for="question-target-year" class="form-label">Target Year</label>
  <p class="form-description">
    Which academic year is this question most relevant to?
  </p>

  <select name="target_year" id="question-target-year" class="form-input">
    <option value="">Any Year</option>
    <option value="1st Year">1st Year</option>
    <option value="2nd Year">2nd Year</option>
    <option value="3rd Year">3rd Year</option>
    <option value="4th Year">4th Year</option>
  </select>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createQuestion()` method. 

**Update the `$data` array to include the new field:**
```php
$data = [
    'title'       => trim($_POST['title']),
    'category'    => trim($_POST['category']),
    'target_year' => trim($_POST['target_year'] ?? ''), // <-- ADD THIS
    'content'     => trim($_POST['description']),
    'tags'        => trim($_POST['tags'] ?? '')
];
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `createQuestion($userId, $data)` method.

**1. Update the SQL query:**
```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, target_year, tags) 
        VALUES (:uid, :title, :content, :cat, :target_year, :tags)";
```

**2. Update the execute array:**
```php
return $stmt->execute([
    ':uid'         => $userId,
    ':title'       => $data['title'],
    ':content'     => $data['content'],
    ':cat'         => $data['category'],
    ':target_year' => $data['target_year'], // <-- ADD THIS
    ':tags'        => $tags
]);
```

---

## Step 5: Display The Field On The All Questions Page

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the `foreach ($questions as $q):` loop. Inside the `<div class="question-tags">` section, insert this right before the existing tags loop:

```php
<?php if (!empty($q['target_year'])): ?>
    <span class="question-tag" style="background-color: #e0e7ff; color: #1d4ed8; border-color: #bfdbfe;">
        🎓 <?= htmlspecialchars($q['target_year']) ?>
    </span>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to `/dashboard/forum/add`.
2. Select "2nd Year" from your new dropdown.
3. Submit the question.
4. Verify the "🎓 2nd Year" badge appears on the feed!
