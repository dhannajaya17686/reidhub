# Edu Forum Task 4: Filter by Target Year

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a dropdown filter on the All Questions page to filter questions by `target_year` (e.g., 1st Year, 2nd Year, 3rd Year). When a user selects a year and clicks Search, the feed should only show questions matching that year. Ensure the selected filter remains active if the user clicks the "Trending" or "Unanswered" tabs, or goes to page 2.

This task assumes the `target_year` field already exists in the `forum_questions` table.

---

## Files You Need To Change

- `app/controllers/Forum/ForumUserController.php`
- `app/models/ForumModel.php`
- `app/views/User/edu-forum/all-questions-view.php`

---

## Step 1: Read The Filter In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `showAllQuestions()` method.

**1. Read the new filter from `$_GET`:**
Right below `$search = $_GET['search'] ?? null;`, add:
```php
$targetYear = trim($_GET['target_year'] ?? '');
```

**2. Update the model calls:**
Find the `getAllQuestions` and `getTotalQuestionsCount` calls and add `$targetYear` as a new parameter:
```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $targetYear ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $targetYear ?: null);
```

**3. Pass it to the view:**
Inside the `$data` array, add the new variable so the view can access it:
```php
$data = [
    'questions'           => $questions,
    'current_page'        => $page,
    'total_pages'         => $totalPages,
    'current_filter'      => $filter,
    'current_search'      => $search,
    'current_tag'         => $tag,
    'current_target_year' => $targetYear // <-- ADD THIS
];
```

---

## Step 2: Update The Model Method Signatures

**File to update:** `app/models/ForumModel.php`

**1. Update `getAllQuestions()`:**
Find the method definition and add the new parameter at the end:
```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $targetYear = null) {
```

**2. Update `getTotalQuestionsCount()`:**
Find the method definition and add the new parameter at the end:
```php
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $targetYear = null) {
```

---

## Step 3: Add The Filter Condition In The SQL Queries

**File to update:** `app/models/ForumModel.php`

Inside **BOTH** `getAllQuestions()` and `getTotalQuestionsCount()`, find the block where `$whereClauses` are being built (right after the `$tag` check).

Add this block:
```php
if ($targetYear) {
    $whereClauses[] = "q.target_year = :target_year";
    $params[':target_year'] = $targetYear;
}
```

*Note: If you don't add this to `getTotalQuestionsCount()`, the pagination numbers will be wrong!*

---

## Step 4: Add The Dropdown Input In The View

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the search form (`<form action="/dashboard/forum/all" method="GET" class="search-form"...>`). 

Insert this `<select>` dropdown exactly before the hidden `filter` input:

```html
<select name="target_year" style="padding: 10px 16px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 0.9rem;">
    <option value="">All Years</option>
    <option value="1st Year" <?= ($current_target_year ?? '') === '1st Year' ? 'selected' : '' ?>>1st Year</option>
    <option value="2nd Year" <?= ($current_target_year ?? '') === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
    <option value="3rd Year" <?= ($current_target_year ?? '') === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
    <option value="4th Year" <?= ($current_target_year ?? '') === '4th Year' ? 'selected' : '' ?>>4th Year</option>
</select>
```

---

## Step 5: Preserve The Filter In Tabs

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

If a user filters by "2nd Year" and then clicks the "Trending" tab, the year filter should not reset. Find the `<nav class="content-tabs">` section.

Update all three tab links to include the `target_year` if it exists:

```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_target_year ? '&target_year='.urlencode($current_target_year) : '' ?>" 
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>
   
<a href="?filter=trending<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_target_year ? '&target_year='.urlencode($current_target_year) : '' ?>" 
   class="tab-link <?= $current_filter === 'trending' ? 'is-active' : '' ?>">Trending</a>
   
<a href="?filter=unanswered<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_target_year ? '&target_year='.urlencode($current_target_year) : '' ?>" 
   class="tab-link <?= $current_filter === 'unanswered' ? 'is-active' : '' ?>">Unanswered</a>
```

---

## Step 6: Preserve The Filter In Pagination

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

If a user filters by "1st Year" and clicks "Next Page", the filter should apply to page 2 as well. Find the pagination helper variables at the bottom of the file:

```php
<?php 
    // Helper to build pagination links keeping current filters
    $base_link = "?filter=" . urlencode($current_filter);
    if ($current_search) $base_link .= "&search=" . urlencode($current_search);
    if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
?>
```

Add one more line to append the `target_year`:
```php
    if ($current_target_year) $base_link .= "&target_year=" . urlencode($current_target_year);
```

---

## Step 7: Test The Task

1. Go to `/dashboard/forum/all`.
2. Use the new dropdown to select "2nd Year" and click Search.
3. Ensure only 2nd Year questions appear.
4. Click the "Trending" tab and ensure the dropdown still says "2nd Year" and the URL contains `&target_year=2nd+Year`.
5. Click "Next" on the pagination and verify the filter remains intact.
