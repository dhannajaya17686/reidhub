# Edu Forum Task 1: Difficulty Field

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a `difficulty` field to the Ask Question form. Save it in the `forum_questions` table and display it on the all questions page.

This is a standard CRUD-style task because it includes:

1. frontend change
2. controller/backend change
3. model/query change
4. database schema change
5. UI display change

---

## Files You Need To Change

- [sql/edu-hub/Questions-Table.sql](C:/Users/User/reidhub/sql/edu-hub/Questions-Table.sql)
- [app/views/User/edu-forum/add-question-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/add-question-view.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

---

## Step 1: Add The Database Column

### File to update

[sql/edu-hub/Questions-Table.sql](C:/Users/User/reidhub/sql/edu-hub/Questions-Table.sql)

### What to insert

Add this line after `category`:

```sql
-- difficulty stores the selected question difficulty level.
difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
```

### Example placement

```sql
category VARCHAR(50) DEFAULT 'General',
difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
tags TEXT NULL,
```

### Run the actual database update

If your database is already created, run:

```sql
ALTER TABLE forum_questions
ADD COLUMN difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner';
```

### Why this step matters

This creates the field in the database so MySQL can store the selected difficulty.

---

## Step 2: Add The Difficulty Input To The Ask Question Form

### File to update

[app/views/User/edu-forum/add-question-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/add-question-view.php)

### Where to insert it

Insert this after the **Category** section and before the **Question Description** section.

### Code to insert

```php
<div class="form-group">
  <label for="question-difficulty" class="form-label form-label--required">Difficulty Level</label>
  <p class="form-description">
    Select the difficulty level of your question.
  </p>

  <select
    id="question-difficulty"
    name="difficulty"
    class="form-input"
    required
  >
    <option value="beginner">Beginner</option>
    <option value="intermediate">Intermediate</option>
    <option value="advanced">Advanced</option>
  </select>
</div>
```

### Why this step matters

The user needs a frontend input to choose the difficulty value.

---

## Step 3: Read The Field In The Controller

### File to update

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

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

### Update it to

```php
$data = [
    'title'      => trim($_POST['title']),
    'category'   => trim($_POST['category']),
    'difficulty' => trim($_POST['difficulty'] ?? 'beginner'),
    'content'    => trim($_POST['description']),
    'tags'       => trim($_POST['tags'] ?? '')
];
```

### Why this step matters

The controller receives the submitted value and passes it to the model.

---

## Step 4: Save The Field In The Model

### File to update

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

### Method to update

`createQuestion($userId, $data)`

### Update the SQL query

Find:

```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, tags)
        VALUES (:uid, :title, :content, :cat, :tags)";
```

Replace it with:

```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, difficulty, tags)
        VALUES (:uid, :title, :content, :cat, :difficulty, :tags)";
```

### Update the execute array

Find:

```php
return $stmt->execute([
    ':uid' => $userId,
    ':title' => $data['title'],
    ':content' => $data['content'],
    ':cat' => $data['category'],
    ':tags' => $tags
]);
```

Replace it with:

```php
return $stmt->execute([
    ':uid' => $userId,
    ':title' => $data['title'],
    ':content' => $data['content'],
    ':cat' => $data['category'],
    ':difficulty' => $data['difficulty'],
    ':tags' => $tags
]);
```

### Why this step matters

This is the part that actually inserts the selected difficulty into the database.

---

## Step 5: Display Difficulty On The All Questions Page

### File to update

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

### Where to insert it

Inside the `.question-tags` section in the question card.

### Current code pattern

```php
<div class="question-tags">
    <?php if (!empty($q['tags'])): ?>
        <?php foreach (explode(',', $q['tags']) as $tag): ?>
            <?php $tag = trim($tag); ?>
            <a href="/dashboard/forum/all?tag=<?= urlencode($tag) ?>" class="question-tag">
                <?= htmlspecialchars($tag) ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
```

### Update it to

```php
<div class="question-tags">
    <?php if (!empty($q['difficulty'])): ?>
        <span class="question-tag">
            <?= htmlspecialchars(ucfirst($q['difficulty'])) ?>
        </span>
    <?php endif; ?>

    <?php if (!empty($q['tags'])): ?>
        <?php foreach (explode(',', $q['tags']) as $tag): ?>
            <?php $tag = trim($tag); ?>
            <a href="/dashboard/forum/all?tag=<?= urlencode($tag) ?>" class="question-tag">
                <?= htmlspecialchars($tag) ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
```

### Why this works

`getAllQuestions()` already uses:

```sql
SELECT q.*, ...
```

So once the `difficulty` column exists in the `forum_questions` table, it becomes available as `$q['difficulty']`.

---

## Step 6: Test The Task

### 1. Open the form

```text
/dashboard/forum/add
```

### 2. Fill the fields

- title
- category
- difficulty
- description
- tags

### 3. Submit the form

It should go to:

```text
/dashboard/forum/create
```

and then redirect to:

```text
/dashboard/forum/all?success=created
```

### 4. Verify the result

Check that the new question shows the difficulty badge on the all questions page.

### 5. Verify in the database

Run:

```sql
SELECT id, title, difficulty
FROM forum_questions
ORDER BY id DESC;
```

---

## Common Mistakes

### 1. Forgetting the DB column

Then MySQL will complain that `difficulty` does not exist.

### 2. Forgetting `$_POST['difficulty']`

Then the value never reaches the model.

### 3. Forgetting `:difficulty` in the SQL query

Then the insert is incomplete.

### 4. Forgetting the execute parameter

You must include:

```php
':difficulty' => $data['difficulty']
```

---

## What To Say In The Code Check

You can explain it like this:

> I added a new `difficulty` column to the `forum_questions` table. Then I added a difficulty dropdown to the Ask Question form. In the controller, I read the submitted difficulty value from `$_POST` and passed it to the model. In the model, I updated the insert query to save the difficulty. Finally, I displayed the saved difficulty on the all questions page.

---

## Quick Checklist

- [ ] Add `difficulty` column to the database
- [ ] Add difficulty input to `add-question-view.php`
- [ ] Read `difficulty` in `ForumUserController::createQuestion()`
- [ ] Save `difficulty` in `ForumModel::createQuestion()`
- [ ] Display `difficulty` in `all-questions-view.php`
- [ ] Test by creating a new question
