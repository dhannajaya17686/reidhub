# Edu Forum Task 2: Difficulty -> Filter Logic

This guide explains how to complete **Task 2** step by step:

> Extend Task 1 by using the saved `difficulty` value to filter questions on the **All Questions** page.

This Task 2 assumes **Task 1 is already completed**.
That means the `difficulty` field already exists in the `forum_questions` table and is already being saved when a user creates a question.

---

## What Task 2 Means

In Task 1, you add and save a new field.

In Task 2, you use that field in **business logic**.

For this example:

- Task 1: save `difficulty` when creating a question
- Task 2: filter the question list using that saved `difficulty`

So the examiner may ask something like:

> "Now implement the business logic so users can filter questions by difficulty."

---

## Files You Need To Change

- app/controllers/Forum/ForumUserController.php
- app/models/ForumModel.php
- app/views/User/edu-forum/all-questions-view.php

---

## Full Flow

The flow for this task is:

1. user selects difficulty in the view
2. controller reads it from `$_GET`
3. controller passes it to the model
4. model adds a SQL `WHERE` condition
5. filtered questions are returned
6. the selected filter is preserved in tabs and pagination

---

## Step 1: Read Difficulty In The Controller

### File

app/controllers/Forum/ForumUserController.php

### Method

`showAllQuestions()`

### Find this code

```php
$filter = $_GET['filter'] ?? 'newest';
$tag    = $_GET['tag'] ?? null;
$search = $_GET['search'] ?? null;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
```

### Insert this line

Add this line **after** `$search` and **before** `$page`:

```php
$difficulty = trim($_GET['difficulty'] ?? '');
```

### Updated code

```php
$filter = $_GET['filter'] ?? 'newest';
$tag    = $_GET['tag'] ?? null;
$search = $_GET['search'] ?? null;
$difficulty = trim($_GET['difficulty'] ?? '');
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
```

### Explanation

- `$_GET['difficulty']` reads the selected difficulty from the URL.
- Example URL:

```text
/dashboard/forum/all?difficulty=beginner
```

- `trim(...)` removes unwanted spaces.
- If the user does not choose anything, the value becomes an empty string.

---

## Step 2: Pass Difficulty To The Model

### File

app/controllers/Forum/ForumUserController.php

### Find this code

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search);
```

### Replace it with

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $difficulty ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $difficulty ?: null);
```

### Explanation

- The controller must send the selected difficulty to the model.
- `$difficulty ?: null` means:
  - if difficulty has a value, send it
  - if difficulty is empty, send `null`
- This helps the model decide whether it should add the SQL filter or not.

---

## Step 3: Send Difficulty To The View

### File

app/controllers/Forum/ForumUserController.php

### Find the `$data` array

```php
$data = [
    'questions'      => $questions,
    'current_page'   => $page,
    'total_pages'    => $totalPages,
    'current_filter' => $filter,
    'current_search' => $search,
    'current_tag'    => $tag
];
```

### Insert this line inside the array

```php
'current_difficulty' => $difficulty,
```

### Updated code

```php
$data = [
    'questions'           => $questions,
    'current_page'        => $page,
    'total_pages'         => $totalPages,
    'current_filter'      => $filter,
    'current_search'      => $search,
    'current_tag'         => $tag,
    'current_difficulty'  => $difficulty
];
```

### Explanation

- The view needs to know which difficulty is currently selected.
- Without this, the dropdown cannot stay selected after page reload.

---

## Step 4: Update The Model Method Signature For Question List

### File

app/models/ForumModel.php

### Find this method

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0)
```

### Replace it with

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $difficulty = null)
```

### Explanation

- The controller is now sending one more argument.
- So the method must be updated to receive that new difficulty value.

---

## Step 5: Add Difficulty SQL Condition In `getAllQuestions()`

### File

app/models/ForumModel.php

### Find this area

```php
if ($tag) {
    $whereClauses[] = "q.tags LIKE :tag";
    $params[':tag'] = "%$tag%";
}

if ($filter === 'unanswered') {
    $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id AND a.moderation_status = 'active') = 0";
}
```

### Insert this block

Add it **after** the tag block and **before** the unanswered block:

```php
if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}
```

### Updated code

```php
if ($search) {
    $whereClauses[] = "(q.title LIKE :search OR q.content LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($tag) {
    $whereClauses[] = "q.tags LIKE :tag";
    $params[':tag'] = "%$tag%";
}

if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}

if ($filter === 'unanswered') {
    $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id AND a.moderation_status = 'active') = 0";
}
```

### Explanation

- This is the actual business logic.
- If the user chooses `beginner`, the SQL becomes:

```sql
... AND q.difficulty = :difficulty
```

- Then `:difficulty` is safely bound to `beginner`.
- If no difficulty is selected, this block does not run.

---

## Step 6: Update The Model Method Signature For Pagination Count

### File

app/models/ForumModel.php

### Find this method

```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null)
```

### Replace it with

```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $difficulty = null)
```

### Explanation

- This method calculates the total number of rows for pagination.
- It also needs the difficulty value so the total page count matches the filtered list.

---

## Step 7: Add Difficulty SQL Condition In `getTotalQuestionsCount()`

### File

app/models/ForumModel.php

### Find this area

```php
if ($search) {
     $whereClauses[] = "(title LIKE :search OR content LIKE :search)";
     $params[':search'] = "%$search%";
}
if ($tag) {
     $whereClauses[] = "tags LIKE :tag";
     $params[':tag'] = "%$tag%";
}
```

### Insert this block

Add it **after** the tag block:

```php
if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}
```

### Updated code

```php
if ($search) {
     $whereClauses[] = "(title LIKE :search OR content LIKE :search)";
     $params[':search'] = "%$search%";
}
if ($tag) {
     $whereClauses[] = "tags LIKE :tag";
     $params[':tag'] = "%$tag%";
}
if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}

if ($filter === 'unanswered') {
    $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id AND a.moderation_status = 'active') = 0";
}
```

### Explanation

- This keeps pagination correct.
- If you update only `getAllQuestions()` and forget this count query, page numbers will be wrong.

---

## Step 8: Add The Difficulty Dropdown In The View

### File

app/views/User/edu-forum/all-questions-view.php

### Find this form

```php
<form action="/dashboard/forum/all" method="GET" class="search-form" style="display:flex; gap:10px;">
    <input type="text" name="search" placeholder="Search topics..." 
           value="<?= htmlspecialchars($current_search ?? '') ?>" 
           style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; width: 250px; font-size: 0.9rem;">
    
    <input type="hidden" name="filter" value="<?= htmlspecialchars($current_filter) ?>">
    
    <button type="submit" class="btn btn--secondary" style="padding: 8px 16px;">Search</button>
</form>
```

### Insert this code

Add this **after** the search input and **before** the hidden `filter` input:

```php
<select name="difficulty" style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
    <option value="">All Difficulties</option>
    <option value="beginner" <?= ($current_difficulty ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
    <option value="intermediate" <?= ($current_difficulty ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
    <option value="advanced" <?= ($current_difficulty ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
</select>
```

### Updated form

```php
<form action="/dashboard/forum/all" method="GET" class="search-form" style="display:flex; gap:10px;">
    <input type="text" name="search" placeholder="Search topics..." 
           value="<?= htmlspecialchars($current_search ?? '') ?>" 
           style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; width: 250px; font-size: 0.9rem;">

    <select name="difficulty" style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
        <option value="">All Difficulties</option>
        <option value="beginner" <?= ($current_difficulty ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
        <option value="intermediate" <?= ($current_difficulty ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
        <option value="advanced" <?= ($current_difficulty ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
    </select>
    
    <input type="hidden" name="filter" value="<?= htmlspecialchars($current_filter) ?>">
    
    <button type="submit" class="btn btn--secondary" style="padding: 8px 16px;">Search</button>
</form>
```

### Explanation

- This creates the dropdown the user will use.
- `name="difficulty"` sends the selected value to the controller through the URL.
- `selected` keeps the chosen option visible after reload.

---

## Step 9: Preserve Difficulty In Tabs

### File

app/views/User/edu-forum/all-questions-view.php

### Find the tab links

```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>

<a href="?filter=trending<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
   class="tab-link <?= $current_filter === 'trending' ? 'is-active' : '' ?>">Trending</a>

<a href="?filter=unanswered<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
   class="tab-link <?= $current_filter === 'unanswered' ? 'is-active' : '' ?>">Unanswered</a>
```

### Add this part to each tab

```php
<?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>
```

### Updated code

```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>" 
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>

<a href="?filter=trending<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>" 
   class="tab-link <?= $current_filter === 'trending' ? 'is-active' : '' ?>">Trending</a>

<a href="?filter=unanswered<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>" 
   class="tab-link <?= $current_filter === 'unanswered' ? 'is-active' : '' ?>">Unanswered</a>
```

### Explanation

- When the user clicks `Newest`, `Trending`, or `Unanswered`, the current difficulty should stay active.
- Without this, the difficulty filter is lost when switching tabs.

---

## Step 10: Preserve Difficulty In Pagination

### File

app/views/User/edu-forum/all-questions-view.php

### Find this code

```php
$base_link = "?filter=" . urlencode($current_filter);
if ($current_search) $base_link .= "&search=" . urlencode($current_search);
if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
```

### Add this line

```php
if ($current_difficulty) $base_link .= "&difficulty=" . urlencode($current_difficulty);
```

### Updated code

```php
$base_link = "?filter=" . urlencode($current_filter);
if ($current_search) $base_link .= "&search=" . urlencode($current_search);
if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
if ($current_difficulty) $base_link .= "&difficulty=" . urlencode($current_difficulty);
```

### Explanation

- This keeps the difficulty filter when the user clicks next page or previous page.
- Without this, page 2 will forget the selected difficulty.

---

## Final Controller Code Summary

### In `showAllQuestions()`

```php
$filter = $_GET['filter'] ?? 'newest';
$tag    = $_GET['tag'] ?? null;
$search = $_GET['search'] ?? null;
$difficulty = trim($_GET['difficulty'] ?? '');
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$limit = 10;
$offset = ($page - 1) * $limit;

$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $difficulty ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $difficulty ?: null);
$totalPages = ceil($totalQuestions / $limit);

$data = [
    'questions'          => $questions,
    'current_page'       => $page,
    'total_pages'        => $totalPages,
    'current_filter'     => $filter,
    'current_search'     => $search,
    'current_tag'        => $tag,
    'current_difficulty' => $difficulty
];
```

---

## Final Model Code Summary

### Method signatures

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $difficulty = null)
```

```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $difficulty = null)
```

### Difficulty condition

```php
if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}
```

---

## Final View Code Summary

### Add to search form

```php
<select name="difficulty">
    <option value="">All Difficulties</option>
    <option value="beginner" <?= ($current_difficulty ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
    <option value="intermediate" <?= ($current_difficulty ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
    <option value="advanced" <?= ($current_difficulty ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
</select>
```

### Add to tabs

```php
<?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>
```

### Add to pagination

```php
if ($current_difficulty) $base_link .= "&difficulty=" . urlencode($current_difficulty);
```

---

## How To Explain This In The Exam

You can say:

> "In Task 2, I extended the difficulty field from Task 1 and used it in business logic. In the controller, I read the selected difficulty from `$_GET` and passed it to the model. In the model, I added a conditional SQL `WHERE` clause to filter matching questions and also updated the total count query for correct pagination. In the view, I added a difficulty dropdown and preserved the selected filter in tabs and pagination."

---

## Testing Checklist

- [ ] Open `/dashboard/forum/all?difficulty=beginner`
- [ ] Check whether only beginner questions are shown
- [ ] Try `intermediate`
- [ ] Try `advanced`
- [ ] Use the dropdown and click Search
- [ ] Click `Newest`
- [ ] Click `Trending`
- [ ] Click `Unanswered`
- [ ] Check whether the selected difficulty remains
- [ ] Go to next page and previous page
- [ ] Confirm the difficulty filter remains there too

---

## Common Mistakes

### 1. Forgetting to update the model method parameters

Then PHP will throw an argument mismatch problem.

### 2. Updating only `getAllQuestions()` and not `getTotalQuestionsCount()`

Then pagination becomes incorrect.

### 3. Forgetting to pass `current_difficulty` to the view

Then the dropdown will not stay selected.

### 4. Forgetting to preserve the filter in tabs

Then the filter resets when switching between `Newest`, `Trending`, and `Unanswered`.

### 5. Forgetting to preserve the filter in pagination

Then the filter resets when moving to another page.



 Allow students uploading a resource (video or note) to specify roughly how many minutes it takes to read or watch it. This helps other students plan their study sessions.


  generate Task 1: Simple CRUD operation without any business logic. (For example,
data from the front end can be inserted into the DB.)
3.4.1.1. Task 1 is based on Frontend, Backend and DB changes.

generate Task 2: Implement business logic. (For example, data from the front end
can be inserted into the DB based on a specific business logic.)
3.4.2.1. Task 2 should be an extension of Task 1.
