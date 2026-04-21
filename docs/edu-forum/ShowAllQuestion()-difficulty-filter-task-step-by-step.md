# Edu Forum Task 2: Difficulty Filter

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a filter on the All Questions page so users can filter questions by difficulty level.

This task assumes the `difficulty` field already exists in the `forum_questions` table and is already being saved when a question is created.

This is a filter-style task because it includes:

1. controller change
2. model query change
3. view filter input
4. preserving the filter in tabs
5. preserving the filter in pagination

---

## Files You Need To Change

- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

---

## Step 1: Read The Filter In The Controller

### File to update

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

### Method to update

`showAllQuestions()`

### Current code pattern

You already have:

```php
$filter = $_GET['filter'] ?? 'newest';
$tag    = $_GET['tag'] ?? null;
$search = $_GET['search'] ?? null;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
```

### Add this

```php
$difficulty = trim($_GET['difficulty'] ?? '');
```

So it becomes:

```php
$filter = $_GET['filter'] ?? 'newest';
$tag    = $_GET['tag'] ?? null;
$search = $_GET['search'] ?? null;
$difficulty = trim($_GET['difficulty'] ?? '');
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
```

### Update the model calls

Find:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search);
```

Replace with:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $difficulty ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $difficulty ?: null);
```

### Pass it to the view

Inside the `$data` array add:

```php
'current_difficulty' => $difficulty,
```

So the data array becomes like:

```php
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

### Why this step matters

The controller must read the selected difficulty from the URL and pass it to both the model and the view.

---

## Step 2: Update The Model Method Signatures

### File to update

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

### Update `getAllQuestions()`

Find:

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0)
```

Replace with:

```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $difficulty = null)
```

### Update `getTotalQuestionsCount()`

Find:

```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null)
```

Replace with:

```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $difficulty = null)
```

### Why this step matters

The model must be able to accept the new filter value coming from the controller.

---

## Step 3: Add The Filter Condition In `getAllQuestions()`

### File to update

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Inside `getAllQuestions()`, after the tag filter block, add:

```php
if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}
```

### Example pattern

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
```

### Why this step matters

This adds a `WHERE` condition only when the user actually selects a difficulty.

---

## Step 4: Add The Filter Condition In `getTotalQuestionsCount()`

### File to update

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Inside `getTotalQuestionsCount()`, add:

```php
if ($difficulty) {
    $whereClauses[] = "q.difficulty = :difficulty";
    $params[':difficulty'] = $difficulty;
}
```

### Example pattern

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
```

### Why this step matters

The pagination total must match the filtered list.  
If this is not updated, the page count will be wrong.

---

## Step 5: Add The Filter Input In The View

### File to update

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

### Where to insert it

Inside the existing search form, before the submit button.

### Code to insert

```php
<select name="difficulty" style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
    <option value="">All Difficulties</option>
    <option value="beginner" <?= ($current_difficulty ?? '') === 'beginner' ? 'selected' : '' ?>>Beginner</option>
    <option value="intermediate" <?= ($current_difficulty ?? '') === 'intermediate' ? 'selected' : '' ?>>Intermediate</option>
    <option value="advanced" <?= ($current_difficulty ?? '') === 'advanced' ? 'selected' : '' ?>>Advanced</option>
</select>
```

### Example updated form

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

### Why this step matters

The user needs a visible filter control in the UI.

---

## Step 6: Preserve The Filter In Tabs

### File to update

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

### Current tab pattern

```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>">
```

### Add this part

```php
<?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>
```

### Example updated tab

```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_difficulty ? '&difficulty='.urlencode($current_difficulty) : '' ?>"
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>
```

Do the same for the `Trending` and `Unanswered` tabs.

### Why this step matters

Without this, the difficulty filter disappears when the user clicks a tab.

---

## Step 7: Preserve The Filter In Pagination

### File to update

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

### Current code pattern

```php
$base_link = "?filter=" . urlencode($current_filter);
if ($current_search) $base_link .= "&search=" . urlencode($current_search);
if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
```

### Update it to

```php
$base_link = "?filter=" . urlencode($current_filter);
if ($current_search) $base_link .= "&search=" . urlencode($current_search);
if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
if ($current_difficulty) $base_link .= "&difficulty=" . urlencode($current_difficulty);
```

### Why this step matters

Without this, the selected difficulty filter is lost when the user goes to the next or previous page.

---

## Step 8: Test The Task

### Test 1

Open:

```text
/dashboard/forum/all?difficulty=beginner
```

Only beginner questions should show.

### Test 2

Use the dropdown in the page and choose:

- Beginner
- Intermediate
- Advanced

Make sure the question list changes accordingly.

### Test 3

Click:

- Newest
- Trending
- Unanswered

Make sure the difficulty filter stays active.

### Test 4

If there are multiple pages, click next and previous and make sure the filter stays active there too.

---

## Common Mistakes

### 1. Forgetting to update the method parameters

Then PHP may show an argument mismatch error.

### 2. Updating `getAllQuestions()` but not `getTotalQuestionsCount()`

Then pagination numbers become wrong.

### 3. Forgetting to preserve the filter in tabs

Then the filter resets when switching tabs.

### 4. Forgetting to preserve the filter in pagination

Then the filter resets when changing pages.

---

## What To Say In The Code Check

You can explain it like this:

> I added a new difficulty filter in the All Questions page. In the controller, I read the selected difficulty from `$_GET` and passed it to the model. In the model, I updated the SQL queries with a conditional `WHERE` clause so only matching questions are fetched and counted. In the view, I added a difficulty dropdown and preserved the filter in tabs and pagination.

---

## Other Same-Pattern Filter Tasks

These are other likely code-check tasks that follow the exact same filter pattern:

1. read from `$_GET` in the controller
2. pass the value to the model
3. add a `WHERE` condition in SQL
4. add a filter input in the view
5. preserve the filter in tabs and pagination

---

### 1. Filter by `semester`

Possible code-check question:

> Add a semester filter to the All Questions page.

Step 1: read it in the controller:

```php
$semester = trim($_GET['semester'] ?? '');
```

Pass it to the model and the view:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $semester ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $semester ?: null);
'current_semester' => $semester,
```

Step 2: add the SQL condition in the model:

```php
if ($semester) {
    $whereClauses[] = "q.semester = :semester";
    $params[':semester'] = $semester;
}
```

Step 3: add the view input:

```php
<select name="semester">
    <option value="">All Semesters</option>
    <option value="semester-1">Semester 1</option>
    <option value="semester-2">Semester 2</option>
</select>
```

Step 4: preserve it in tabs and pagination:

```php
<?= $current_semester ? '&semester=' . urlencode($current_semester) : '' ?>
```

```php
if ($current_semester) $base_link .= "&semester=" . urlencode($current_semester);
```

---

### 2. Filter by `department`

Possible code-check question:

> Add a department filter so users can view questions by department.

Step 1: read it in the controller:

```php
$department = trim($_GET['department'] ?? '');
```

Pass it to the model and the view:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $department ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $department ?: null);
'current_department' => $department,
```

Step 2: add the SQL condition:

```php
if ($department) {
    $whereClauses[] = "q.department = :department";
    $params[':department'] = $department;
}
```

Step 3: add the view input:

```php
<select name="department">
    <option value="">All Departments</option>
    <option value="computing">Computing</option>
    <option value="business">Business</option>
</select>
```

Step 4: preserve it:

```php
<?= $current_department ? '&department=' . urlencode($current_department) : '' ?>
```

```php
if ($current_department) $base_link .= "&department=" . urlencode($current_department);
```

---

### 3. Filter by `question_type`

Possible code-check question:

> Add a question type filter to the forum page.

Step 1: read it in the controller:

```php
$questionType = trim($_GET['question_type'] ?? '');
```

Pass it to the model and the view:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $questionType ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $questionType ?: null);
'current_question_type' => $questionType,
```

Step 2: add the SQL condition:

```php
if ($questionType) {
    $whereClauses[] = "q.question_type = :question_type";
    $params[':question_type'] = $questionType;
}
```

Step 3: add the view input:

```php
<select name="question_type">
    <option value="">All Types</option>
    <option value="theory">Theory</option>
    <option value="coding">Coding</option>
    <option value="error">Error</option>
    <option value="assignment">Assignment</option>
</select>
```

Step 4: preserve it:

```php
<?= $current_question_type ? '&question_type=' . urlencode($current_question_type) : '' ?>
```

```php
if ($current_question_type) $base_link .= "&question_type=" . urlencode($current_question_type);
```

---

### 4. Filter by `is_urgent`

Possible code-check question:

> Add an urgent-only filter to the All Questions page.

Step 1: read it in the controller:

```php
$urgent = isset($_GET['urgent']) && $_GET['urgent'] === '1' ? 1 : null;
```

Pass it to the model and the view:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $urgent);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $urgent);
'current_urgent' => $urgent,
```

Step 2: add the SQL condition:

```php
if ($urgent !== null) {
    $whereClauses[] = "q.is_urgent = :urgent";
    $params[':urgent'] = $urgent;
}
```

Step 3: add the view input:

```php
<select name="urgent">
    <option value="">All</option>
    <option value="1">Urgent Only</option>
</select>
```

Step 4: preserve it:

```php
<?= $current_urgent !== null ? '&urgent=' . urlencode($current_urgent) : '' ?>
```

```php
if ($current_urgent !== null) $base_link .= "&urgent=" . urlencode($current_urgent);
```

---

### 5. Filter by `category`

Possible code-check question:

> Add a category filter dropdown to the All Questions page.

Step 1: read it in the controller:

```php
$category = trim($_GET['category'] ?? '');
```

Pass it to the model and the view:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $category ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $category ?: null);
'current_category' => $category,
```

Step 2: add the SQL condition:

```php
if ($category) {
    $whereClauses[] = "q.category = :category";
    $params[':category'] = $category;
}
```

Step 3: add the view input:

```php
<select name="category">
    <option value="">All Categories</option>
    <option value="programming">Programming</option>
    <option value="web-development">Web Dev</option>
    <option value="database">Database</option>
</select>
```

Step 4: preserve it:

```php
<?= $current_category ? '&category=' . urlencode($current_category) : '' ?>
```

```php
if ($current_category) $base_link .= "&category=" . urlencode($current_category);
```

This one is especially likely because `category` already exists in your current code.

---

### 6. Filter by tag

Possible code-check question:

> Add a tag filter dropdown or tag input to the All Questions page.

Step 1: your controller already reads:

```php
$tag = $_GET['tag'] ?? null;
```

Step 2: your model already supports:

```php
q.tags LIKE :tag
```

Step 3: add or improve the view input:

```php
<input type="text" name="tag" placeholder="Tag...">
```

or:

```php
<select name="tag">
    <option value="">All Tags</option>
    <option value="javascript">javascript</option>
    <option value="react">react</option>
</select>
```

Step 4: preserve it:

Your current code already preserves `tag` in tabs and pagination, so this is one of the easiest filter tasks.

---

### 7. Filter by solved / unsolved

Possible code-check question:

> Add a filter to show only solved questions.

Step 1: read it in the controller:

```php
$status = trim($_GET['status'] ?? '');
```

Pass it to the model and the view:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $status ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $status ?: null);
'current_status' => $status,
```

Step 2: add the SQL condition for solved:

```php
if ($status === 'solved') {
    $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a
                        WHERE a.question_id = q.id
                        AND a.is_accepted = 1
                        AND a.moderation_status = 'active') > 0";
}
```

Step 3: add the SQL condition for unsolved:

```php
if ($status === 'unsolved') {
    $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a
                        WHERE a.question_id = q.id
                        AND a.is_accepted = 1
                        AND a.moderation_status = 'active') = 0";
}
```

Step 4: add the view input:

```php
<select name="status">
    <option value="">All Questions</option>
    <option value="solved">Solved</option>
    <option value="unsolved">Unsolved</option>
</select>
```

Step 5: preserve it:

```php
<?= $current_status ? '&status=' . urlencode($current_status) : '' ?>
```

```php
if ($current_status) $base_link .= "&status=" . urlencode($current_status);
```

This is very possible because your forum already supports accepted answers.

---

### 8. Filter by `my questions`

Possible code-check question:

> Add a filter to show only the logged-in user’s questions.

Step 1: read it in the controller:

```php
$myQuestions = isset($_GET['mine']) && $_GET['mine'] === '1';
```

Step 2: add the SQL condition:

```php
if ($myQuestions && isset($_SESSION['user_id'])) {
    $whereClauses[] = "q.user_id = :uid";
    $params[':uid'] = (int)$_SESSION['user_id'];
}
```

Step 3: add the view input:

```php
<select name="mine">
    <option value="">All Questions</option>
    <option value="1">My Questions</option>
</select>
```

Step 4: preserve it:

```php
<?= $current_mine ? '&mine=1' : '' ?>
```

```php
if ($current_mine) $base_link .= "&mine=1";
```

This may appear either as a filter or as a separate page.

---

### 9. Filter by date range

Possible code-check question:

> Add date filters to show questions posted between two dates.

Step 1: read both values in the controller:

```php
$dateFrom = trim($_GET['date_from'] ?? '');
$dateTo = trim($_GET['date_to'] ?? '');
```

Step 2: add the SQL conditions:

```php
if ($dateFrom) {
    $whereClauses[] = "DATE(q.created_at) >= :date_from";
    $params[':date_from'] = $dateFrom;
}

if ($dateTo) {
    $whereClauses[] = "DATE(q.created_at) <= :date_to";
    $params[':date_to'] = $dateTo;
}
```

Step 3: add the view inputs:

```php
<input type="date" name="date_from">
<input type="date" name="date_to">
```

Step 4: preserve them:

```php
<?= $current_date_from ? '&date_from=' . urlencode($current_date_from) : '' ?>
<?= $current_date_to ? '&date_to=' . urlencode($current_date_to) : '' ?>
```

---

### 10. Filter unanswered through the search form

Possible code-check question:

> Add an unanswered-only filter in the search form.

Step 1: your controller already uses:

```php
$filter = $_GET['filter'] ?? 'newest';
```

Step 2: your model already handles unanswered:

```php
if ($filter === 'unanswered') {
    $whereClauses[] = "(SELECT COUNT(*) FROM forum_answers a WHERE a.question_id = q.id AND a.moderation_status = 'active') = 0";
}
```

Step 3: expose it in the search form:

```php
<select name="filter">
    <option value="newest">Newest</option>
    <option value="trending">Trending</option>
    <option value="unanswered">Unanswered</option>
</select>
```

Step 4: preserve it:

Your current pagination already keeps `filter`, so this is usually one of the easiest filter tasks.

---

### Most Likely Same-Pattern Tasks In This Codebase

If you want the best practice order, these are the strongest ones:

1. `semester`
2. `department`
3. `is_urgent`
4. `question_type`
5. `category`
6. `solved / unsolved`

---

## Quick Checklist

- [ ] Read `difficulty` from `$_GET` in `showAllQuestions()`
- [ ] Pass `difficulty` to `getAllQuestions()` and `getTotalQuestionsCount()`
- [ ] Update both model method signatures
- [ ] Add the `q.difficulty = :difficulty` condition in both model methods
- [ ] Add a difficulty dropdown in `all-questions-view.php`
- [ ] Preserve the filter in tabs
- [ ] Preserve the filter in pagination
- [ ] Test filter behavior
