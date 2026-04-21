# Edu Forum Task: Estimated Time Field

This guide explains how to complete this Edu Forum code-check task step by step:

> Add an `estimated_time` field to questions so users can mention how long they have been stuck or how urgent the issue is. Save it and display it in the question card.

This is a standard CRUD-style task because it includes:

1. frontend change
2. controller/backend change
3. model/query change
4. database schema change
5. UI display change

---

## Files You Need To Change

- sql/edu-hub/Questions-Table.sql
- app/views/User/edu-forum/add-question-view.php
- app/controllers/Forum/ForumUserController.php
- app/models/ForumModel.php
- app/views/User/edu-forum/all-questions-view.php

---

## Step 1: Add The Column To The Database

### File to update

sql/edu-hub/Questions-Table.sql

### What to insert

Add this line inside the `forum_questions` table, after `category`:

```sql
-- estimated_time stores how long the user has been stuck on the issue.
estimated_time VARCHAR(50) NULL,
```

### Run the actual database update

If your database is already created, run this in phpMyAdmin or your SQL client:

```sql
ALTER TABLE forum_questions
ADD COLUMN estimated_time VARCHAR(50) NULL;
```

### Why this step matters

This creates the field in the database so MySQL can actually store the input. If you skip this, the PHP insert query will fail and crash.

---

## Step 2: Add The Input To The Ask Question Form

### File to update

app/views/User/edu-forum/add-question-view.php

### Where to insert it

Find the form where users enter their question details. Insert this after the **Category** section and before the **Question Description** section.

### Code to insert

```html
<div class="form-group">
  <label for="question-estimated-time" class="form-label">Estimated Time Stuck</label>
  <p class="form-description">
    How long have you been stuck on this issue? (e.g., "2 hours", "3 days")
  </p>

  <input
    type="text"
    id="question-estimated-time"
    name="estimated_time"
    class="form-input"
    placeholder="e.g., 2 hours"
    maxlength="50"
  >
</div>
```

### Why this step matters

The `name="estimated_time"` attribute is exactly what PHP will look for in the `$_POST` array when the user clicks submit.

---

## Step 3: Read The Field In The Controller

### File to update

app/controllers/Forum/ForumUserController.php

### Method to update

`createQuestion()`

### Current code pattern

The `$data` array currently looks like this:

```php
$data = [
    'title'    => trim($_POST['title']),
    'category' => trim($_POST['category']),
    'content'  => trim($_POST['description']),
    'tags'     => trim($_POST['tags'] ?? '')
];
```

### Update it to include the new field

```php
$data = [
    'title'          => trim($_POST['title']),
    'category'       => trim($_POST['category']),
    'estimated_time' => trim($_POST['estimated_time'] ?? ''),
    'content'        => trim($_POST['description']),
    'tags'           => trim($_POST['tags'] ?? '')
];
```

### Why this step matters

The controller receives the submitted form value via `$_POST` and packages it into the `$data` array to pass to the model.

---

## Step 4: Save The Field In The Model

### File to update

app/models/ForumModel.php

### Method to update

`createQuestion($userId, $data)`

### 1. Update the SQL query

Add `estimated_time` to both the column list and the `VALUES` placeholders:

```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, estimated_time, tags) 
        VALUES (:uid, :title, :content, :cat, :estimated_time, :tags)";
```

### 2. Update the execute array

Bind the new placeholder to the data coming from the controller:

```php
return $stmt->execute([
    ':uid'            => $userId,
    ':title'          => $data['title'],
    ':content'        => $data['content'],
    ':cat'            => $data['category'],
    ':estimated_time' => $data['estimated_time'],
    ':tags'           => $tags
]);
```

---

## Step 5: Display The Field On The All Questions Page

### File to update

app/views/User/edu-forum/all-questions-view.php

### Where to insert it

Find the `foreach ($questions as $q):` loop. Inside the question card, locate the `<div class="question-tags">` section. Insert this right before the existing tags loop:

```php
<?php if (!empty($q['estimated_time'])): ?>
    <span class="question-tag" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5;">
        ⏳ Stuck: <?= htmlspecialchars($q['estimated_time']) ?>
    </span>
<?php endif; ?>
```

### Why this works

Because the model does a `SELECT q.*` in the `getAllQuestions` method, the new `estimated_time` column is automatically pulled from the database and is available inside the `$q` array.
