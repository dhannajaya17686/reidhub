# Edu Forum Task: "Target Audience" Field

This guide explains how to complete this Edu Forum code-check task step by step:

> **Objective:** Add a "Target Audience" dropdown to the "Ask a Question" form. Capture this data when the user submits the form, save it to the database, and display it on the individual question details page.

This is a classic standard CRUD-style task covering the database, the frontend form, the controller, the model, and the frontend view.

---

## Files You Need To Change

- `sql/edu-hub/Questions-Table.sql` (or run directly in your DB client)
- `app/views/User/edu-forum/add-question-view.php`
- `app/controllers/Forum/ForumUserController.php`
- `app/models/ForumModel.php`
- `app/views/User/edu-forum/one-question-view.php`

---

## Step 1: Add The Column To The Database

### What to run in your database
Open your database management tool (e.g., phpMyAdmin) and run the following SQL command to add the `target_audience` column to your `forum_questions` table:

```sql
ALTER TABLE forum_questions
ADD COLUMN target_audience VARCHAR(50) DEFAULT 'General';
```

### Why this step matters
This creates the field in the database so MySQL can actually store the input. If you skip this, the PHP `INSERT` query will fail.

---

## Step 2: Add The Input To The Ask Question Form

### File to update
`app/views/User/edu-forum/add-question-view.php`

### Where to insert it
Find the form where users enter their question details. Insert this after the **Category** section and before the **Question Description** section.

### Code to insert
```html
<div class="form-group">
  <label for="question-target-audience" class="form-label">Target Audience</label>
  <p class="form-description">
    Who is the primary audience for this question?
  </p>

  <select name="target_audience" id="question-target-audience" class="form-input">
    <option value="General">General</option>
    <option value="Undergraduates">Undergraduates</option>
    <option value="Postgraduates">Postgraduates</option>
  </select>
</div>
```

---

## Step 3: Read The Field In The Controller

### File to update
`app/controllers/Forum/ForumUserController.php`

### Method to update
`createQuestion()`

### Update the `$data` array
Find the `$data` array where the form data is collected from `$_POST`. Make sure the `target_audience` field is captured:

```php
$data = [
    'title'           => trim($_POST['title']),
    'category'        => trim($_POST['category']),
    'target_audience' => trim($_POST['target_audience'] ?? 'General'), // <-- ADD THIS
    'content'         => trim($_POST['description']),
    'tags'            => trim($_POST['tags'] ?? '')
];
```

---

## Step 4: Save The Field In The Model

### File to update
`app/models/ForumModel.php`

### Method to update
`createQuestion($userId, $data)`

### 1. Update the SQL query
Add `target_audience` to both the column list and the `VALUES` placeholders:

```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, target_audience, tags) 
        VALUES (:uid, :title, :content, :cat, :target_audience, :tags)";
```

### 2. Update the execute array
Bind the new placeholder to the data coming from the controller:

```php
return $stmt->execute([
    ':uid'             => $userId,
    ':title'           => $data['title'],
    ':content'         => $data['content'],
    ':cat'             => $data['category'],
    ':target_audience' => $data['target_audience'], // <-- ADD THIS
    ':tags'            => $tags
]);
```

---

## Step 5: Display The Field On The Question Details Page

### File to update
`app/views/User/edu-forum/one-question-view.php`

### Where to insert it
Find the `<div class="question-tags">` section (around line 265). Insert this right inside that container, before the existing tags loop:

```php
<?php if (!empty($question['target_audience']) && $question['target_audience'] !== 'General'): ?>
    <span class="question-tag" style="background-color: #fdf4ff; color: #9333ea; border-color: #f3e8ff;">
        🎯 <?= htmlspecialchars($question['target_audience']) ?>
    </span>
<?php endif; ?>
```

### Why this works
Because the model uses `SELECT q.*` in the `getQuestionById` method, the new `target_audience` column is automatically pulled from the database and is available inside the `$question` array. We wrap it in a condition so it only displays if it's not the default "General" audience.

---

# Edu Forum Task 2: Filter by Target Audience

This guide explains how to complete the follow-up Filtering (Task 2) code-check:

> **The Task:** Add a dropdown filter on the All Questions page to filter questions by `target_audience`. When a user selects an audience and clicks Search, the feed should only show questions matching that audience. Ensure the selected filter remains active in the tabs and pagination.

This tests your ability to read `$_GET` data, pass it to the model, and dynamically update a SQL `WHERE` clause.

---

## Files You Need To Change

- `app/controllers/Forum/ForumUserController.php`
- `app/models/ForumModel.php`
- `app/views/User/edu-forum/all-questions-view.php`

---

## Step 1: Read The Filter In The Controller

### File to update
`app/controllers/Forum/ForumUserController.php`

### Method to update
`showAllQuestions()`

### 1. Read the new filter from `$_GET`:
Right below `$search = $_GET['search'] ?? null;`, add:
```php
$targetAudience = trim($_GET['target_audience'] ?? '');
```

### 2. Update the model calls:
Find the `getAllQuestions` and `getTotalQuestionsCount` calls and add `$targetAudience` as a new parameter:
```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $targetAudience ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $targetAudience ?: null);
```

### 3. Pass it to the view:
Inside the `$data` array, add the new variable so the view can access it:
```php
$data = [
    'questions'               => $questions,
    'current_page'            => $page,
    'total_pages'             => $totalPages,
    'current_filter'          => $filter,
    'current_search'          => $search,
    'current_tag'             => $tag,
    'current_target_audience' => $targetAudience // <-- ADD THIS
];
```

---

## Step 2: Update The Model Method Signatures

### File to update
`app/models/ForumModel.php`

### 1. Update `getAllQuestions()`:
Find the method definition and add the new parameter at the end:
```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $targetAudience = null) {
```

### 2. Update `getTotalQuestionsCount()`:
Find the method definition and add the new parameter at the end:
```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $targetAudience = null) {
```

---

## Step 3: Add The Filter Condition In The SQL Queries

### File to update
`app/models/ForumModel.php`

Inside **BOTH** `getAllQuestions()` and `getTotalQuestionsCount()`, find the block where `$whereClauses` are being built (right after the `$tag` check).

Add this block:
```php
if ($targetAudience) {
    $whereClauses[] = "q.target_audience = :target_audience";
    $params[':target_audience'] = $targetAudience;
}
```

*Note: If you don't add this to `getTotalQuestionsCount()`, the pagination numbers will be wrong!*

---

## Step 4: Add The Dropdown Input In The View

### File to update
`app/views/User/edu-forum/all-questions-view.php`

Find the search form (`<form action="/dashboard/forum/all" method="GET" class="search-form"...>`). 

Insert this `<select>` dropdown exactly before the hidden `filter` input:

```html
<select name="target_audience" style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
    <option value="">All Audiences</option>
    <option value="General" <?= ($current_target_audience ?? '') === 'General' ? 'selected' : '' ?>>General</option>
    <option value="Undergraduates" <?= ($current_target_audience ?? '') === 'Undergraduates' ? 'selected' : '' ?>>Undergraduates</option>
    <option value="Postgraduates" <?= ($current_target_audience ?? '') === 'Postgraduates' ? 'selected' : '' ?>>Postgraduates</option>
</select>
```

---

## Step 5: Preserve The Filter In Tabs

### File to update
`app/views/User/edu-forum/all-questions-view.php`

If a user filters by "Undergraduates" and then clicks the "Trending" tab, the filter should not reset. Find the `<nav class="content-tabs">` section.

Update all three tab links to include the `target_audience` if it exists:

```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_target_audience ? '&target_audience='.urlencode($current_target_audience) : '' ?>" 
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>
   
<a href="?filter=trending<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_target_audience ? '&target_audience='.urlencode($current_target_audience) : '' ?>" 
   class="tab-link <?= $current_filter === 'trending' ? 'is-active' : '' ?>">Trending</a>
   
<a href="?filter=unanswered<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_target_audience ? '&target_audience='.urlencode($current_target_audience) : '' ?>" 
   class="tab-link <?= $current_filter === 'unanswered' ? 'is-active' : '' ?>">Unanswered</a>
```

---

## Step 6: Preserve The Filter In Pagination

### File to update
`app/views/User/edu-forum/all-questions-view.php`

If a user filters by "Postgraduates" and clicks "Next Page", the filter should apply to page 2 as well. Find the pagination helper variables at the bottom of the file:

```php
<?php 
    // Helper to build pagination links keeping current filters
    $base_link = "?filter=" . urlencode($current_filter);
    if ($current_search) $base_link .= "&search=" . urlencode($current_search);
    if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
?>
```

Add one more line to append the `target_audience`:
```php
    if ($current_target_audience) $base_link .= "&target_audience=" . urlencode($current_target_audience);
```

---

## Step 7: Test The Task

1. Go to `/dashboard/forum/all`.
2. Use the new dropdown to select "Undergraduates" and click Search.
3. Ensure only undergraduate questions appear.
4. Click the "Trending" tab and ensure the dropdown still says "Undergraduates" and the URL contains `&target_audience=Undergraduates`.
5. Click "Next" on the pagination and verify the filter remains intact.

---

# Edu Forum Task 3: Target Audience Count Cards

This guide explains how to complete the final Summary (Task 3) code-check for this field:

> **The Task:** Add summary cards to the top of the All Questions page showing the total number of active questions for each Target Audience (e.g., General, Undergraduates, Postgraduates).

This task relies purely on PHP and SQL (`GROUP BY`), completely avoiding any JavaScript.

---

## Files You Need To Change

- `app/models/ForumModel.php`
- `app/controllers/Forum/ForumUserController.php`
- `app/views/User/edu-forum/all-questions-view.php`

---

## Step 1: Add The Count Method In The Model

### File to update
`app/models/ForumModel.php`

Add this new method (for example, right after `getTotalQuestionsCount`):

```php
public function getQuestionCountByAudience() {
    $sql = "SELECT target_audience, COUNT(*) AS total
            FROM forum_questions
            WHERE moderation_status = 'active'
            GROUP BY target_audience
            ORDER BY total DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

---

## Step 2: Call The Method In The Controller

### File to update
`app/controllers/Forum/ForumUserController.php`

### Method to update
`showAllQuestions()`

### 1. Fetch the counts:
Right after fetching `$questions` and `$totalQuestions`, add:
```php
$audienceCounts = $forumModel->getQuestionCountByAudience();
```

### 2. Pass it to the view:
Add it into the `$data` array:
```php
$data = [
    'questions'               => $questions,
    'current_page'            => $page,
    'total_pages'             => $totalPages,
    'current_filter'          => $filter,
    'current_search'          => $search,
    'current_tag'             => $tag,
    'current_target_audience' => $targetAudience,
    'audience_counts'         => $audienceCounts // <-- ADD THIS
];
```

---

## Step 3: Display The Cards In The View

### File to update
`app/views/User/edu-forum/all-questions-view.php`

Find the section right below the tabs (`</nav>`) and above the `<section class="questions-section">`. Insert this PHP loop to generate the cards dynamically:

```php
<section class="audience-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <?php if (!empty($audience_counts)): ?>
        <?php foreach ($audience_counts as $aud): ?>
            <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
                <div style="font-size:1.5rem; font-weight:700; color:var(--text-color);">
                    <?= (int)$aud['total'] ?>
                </div>
                <div style="color:var(--text-muted); font-size:0.95rem;">
                    <?= htmlspecialchars($aud['target_audience'] ?: 'General') ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
```

---

## Step 4: Test The Task

1. Go to `/dashboard/forum/all`.
2. Look right below the Newest/Trending tabs.
3. You should see stat cards grouping all active questions by their Target Audience (e.g., showing how many are for Undergraduates vs Postgraduates).
