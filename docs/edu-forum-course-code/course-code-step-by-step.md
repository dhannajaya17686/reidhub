# Edu Forum Course Code Task Guide

This guide explains how to implement this Edu Forum code-check task step by step:

> Add a new `course_code` field to the Ask Question form. Save the value in the `forum_questions` table and display it on the all questions page.

This is a standard CRUD-style task because it includes:

1. frontend change
2. controller/backend change
3. model/query change
4. database schema change
5. UI display change

---

## Files You Need To Change

### 1. Database table file

[sql/edu-hub/Questions-Table.sql](C:/Users/User/reidhub/sql/edu-hub/Questions-Table.sql)

### 2. Ask Question form

[app/views/User/edu-forum/add-question-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/add-question-view.php)

### 3. User controller

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

### 4. Forum model

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

### 5. All Questions page

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

---

## Step 1: Add The Column To The Database

### File to update

[sql/edu-hub/Questions-Table.sql](C:/Users/User/reidhub/sql/edu-hub/Questions-Table.sql)

### What to insert

Add this line inside the `forum_questions` table, after `category`:

```sql
-- course_code stores the related course or module code for the question.
course_code VARCHAR(20) NULL,
```

### Example placement

```sql
category VARCHAR(50) DEFAULT 'General',
course_code VARCHAR(20) NULL,
tags TEXT NULL,
```

### Update the actual database

If the project database is already created, also run:

```sql
ALTER TABLE forum_questions
ADD COLUMN course_code VARCHAR(20) NULL;
```

### Why this step matters

Without the new column, MySQL cannot store the submitted course code.

---

## Step 2: Add The Input To The Ask Question Form

### File to update

[app/views/User/edu-forum/add-question-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/add-question-view.php)

### Where to insert it

Add this section after the **Category** section and before the **Question Description** section.

### Code to insert

```php
<div class="form-group">
  <label for="question-course-code" class="form-label">Course Code</label>
  <p class="form-description">
    Add the related course or module code for this question.
  </p>

  <input
    type="text"
    id="question-course-code"
    name="course_code"
    class="form-input"
    placeholder="e.g., SE1010"
    maxlength="20"
  >
</div>
```

### Important detail

The key part is:

```html
name="course_code"
```

That is what PHP will receive through:

```php
$_POST['course_code']
```

---

## Step 3: Read The Field In The Controller

### File to update

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

### Method to update

`createQuestion()`

### Current code pattern

Inside `createQuestion()`, there is a `$data` array like this:

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
    'title'       => trim($_POST['title']),
    'category'    => trim($_POST['category']),
    'course_code' => trim($_POST['course_code'] ?? ''),
    'content'     => trim($_POST['description']),
    'tags'        => trim($_POST['tags'] ?? '')
];
```

### Why this step matters

The controller collects the submitted form data and passes it to the model.  
If you do not add `course_code` here, the model will never receive it.

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
$sql = "INSERT INTO forum_questions (user_id, title, content, category, course_code, tags)
        VALUES (:uid, :title, :content, :cat, :course_code, :tags)";
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
    ':course_code' => $data['course_code'],
    ':tags' => $tags
]);
```

### Why this step matters

This is the actual insert into the database.  
Even if the form and controller are correct, the value will not be stored unless the insert query includes `course_code`.

---

## Step 5: Display Course Code On The All Questions Page

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
    <?php if (!empty($q['course_code'])): ?>
        <span class="question-tag">
            <?= htmlspecialchars(strtoupper($q['course_code'])) ?>
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

So once `course_code` exists in `forum_questions`, it automatically becomes available as `$q['course_code']`.

---

## Step 6: Test The Feature

### same like for add 'semester'

### 1. Open the add question page

```text
/dashboard/forum/add
```

### 2. Fill the form

- title
- category
- course code
- description
- tags

### 3. Submit the form

It should post to:

```text
/dashboard/forum/create
```

and then redirect back to:

```text
/dashboard/forum/all?success=created
```

### 4. Verify the question card

Check whether the course code appears in the question card.

### 5. Verify the database

Run:

```sql
SELECT id, title, course_code
FROM forum_questions
ORDER BY id DESC;
```

You should see the newly saved `course_code`.

---

## Common Errors And Fixes

### Error 1: Unknown column `course_code`

Reason:

The real database was not updated yet.

Fix:

```sql
ALTER TABLE forum_questions
ADD COLUMN course_code VARCHAR(20) NULL;
```

### Error 2: SQL syntax error in `INSERT`

Reason:

The insert query is missing commas or placeholders are in the wrong order.

Correct query:

```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, course_code, tags)
        VALUES (:uid, :title, :content, :cat, :course_code, :tags)";
```

### Error 3: Value not saving

Reason:

One of these is missing:

- `name="course_code"` in the form
- `'course_code' => trim($_POST['course_code'] ?? '')` in the controller
- `:course_code` in the model query
- `':course_code' => $data['course_code']` in the execute array

---

## Short Explanation For The Code Check

You can explain your work like this:

> I added a new `course_code` column to the `forum_questions` table. Then I added a course code input to the Ask Question form. In the controller, I read the submitted course code from `$_POST` and passed it into the model. In the model, I updated the insert query to save the course code to the database. Finally, I displayed the saved course code on the all questions page.

---

































#
#
#
#
#

## Filter Tasks Guide

If they ask you to **filter questions** using `course_code`, `semester`, `department`, `urgent`, or a similar field, the pattern is different from insert-only tasks.

For filter tasks, you usually need to update:

1. controller
2. model
3. view
4. pagination links
5. tab links

If the field already exists in the database, you do not need a new DB column.  
If the field does not exist yet, first add and save the field, then implement the filter.

---

### Example: Filter By `course_code`

#### 1. Read the filter in the controller

File:

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

Inside `showAllQuestions()` add:

```php
$courseCode = trim($_GET['course_code'] ?? '');
```

Update the model call:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $courseCode);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $courseCode);
```

Pass it to the view:

```php
'current_course_code' => $courseCode,
```

#### 2. Update the model method

File:

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Change:

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0)
```

to:

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $courseCode = null)
```

Add this filter logic:

```php
if ($courseCode) {
    $whereClauses[] = "q.course_code = :course_code";
    $params[':course_code'] = $courseCode;
}
```

Do the same in `getTotalQuestionsCount()`:

```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $courseCode = null)
```

and:

```php
if ($courseCode) {
    $whereClauses[] = "q.course_code = :course_code";
    $params[':course_code'] = $courseCode;
}
```

#### 3. Add the filter input in the view

File:

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Inside the search form add:

```php
<input
    type="text"
    name="course_code"
    placeholder="Course code..."
    value="<?= htmlspecialchars($current_course_code ?? '') ?>"
    style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; width: 180px; font-size: 0.9rem;"
>
```

#### 4. Preserve it in tab links

Add this to the tab URLs:

```php
<?= $current_course_code ? '&course_code=' . urlencode($current_course_code) : '' ?>
```

Example:

```php
<a href="?filter=newest<?= $current_search ? '&search=' . urlencode($current_search) : '' ?><?= $current_tag ? '&tag=' . urlencode($current_tag) : '' ?><?= $current_course_code ? '&course_code=' . urlencode($current_course_code) : '' ?>">
```

#### 5. Preserve it in pagination

In the pagination base link, add:

```php
if ($current_course_code) $base_link .= "&course_code=" . urlencode($current_course_code);
```

---

### If They Ask For Another Filter

The same pattern applies. Only the field name changes.

#### Filter by `semester`

Controller:

```php
$semester = trim($_GET['semester'] ?? '');
```

Model:

```php
if ($semester) {
    $whereClauses[] = "q.semester = :semester";
    $params[':semester'] = $semester;
}
```

View example:

```php
<select name="semester">
    <option value="">All Semesters</option>
    <option value="semester-1">Semester 1</option>
    <option value="semester-2">Semester 2</option>
</select>
```

#### Filter by `department`

Controller:

```php
$department = trim($_GET['department'] ?? '');
```

Model:

```php
if ($department) {
    $whereClauses[] = "q.department = :department";
    $params[':department'] = $department;
}
```

#### Filter by `is_urgent`

Controller:

```php
$urgent = isset($_GET['urgent']) && $_GET['urgent'] === '1' ? 1 : null;
```

Model:

```php
if ($urgent !== null) {
    $whereClauses[] = "q.is_urgent = :urgent";
    $params[':urgent'] = $urgent;
}
```

View example:

```php
<select name="urgent">
    <option value="">All</option>
    <option value="1">Urgent Only</option>
</select>
```

---

### Quick Rule To Remember For Filter Questions

If they ask you to add a filter, think in this order:

1. read from `$_GET`
2. pass to the model
3. add a `WHERE` condition
4. add the input in the view
5. keep the filter in links and pagination

---

### Short Explanation For A Filter Code Check

You can say:

> I added a new GET filter in the controller, passed it to the model, updated the SQL query with a conditional WHERE clause, added the filter input in the view, and preserved the selected filter in tabs and pagination.

---

## Quick Checklist

- [ ] Add `course_code` column to the database
- [ ] Add the course code input to `add-question-view.php`
- [ ] Read `course_code` in `ForumUserController::createQuestion()`
- [ ] Save `course_code` in `ForumModel::createQuestion()`
- [ ] Display `course_code` in `all-questions-view.php`
- [ ] Test by creating a new question








same like for add 'semester'
