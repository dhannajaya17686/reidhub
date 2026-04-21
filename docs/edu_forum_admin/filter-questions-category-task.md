# Edu Forum Admin Task 2: Filter Questions by Category

This guide explains how to complete a Filtering (Task 2) code-check specifically for the Edu Forum Admin side:

> **The Task:** In the "Question Moderation" section of the Admin Dashboard, add a new dropdown filter for **Category**. When an admin selects a specific category (e.g., Programming, Database) and clicks "Filter", the table should only display questions belonging to that category.

This tests your ability to capture `$_GET` variables, pass them to a model, and dynamically update a SQL `WHERE` clause.

---

## Files You Need To Change

- `app/views/Admin/edu-forum/manage-forum-view.php`
- `app/controllers/Forum/ForumAdminController.php`
- `app/models/ForumAdminModel.php`

*(Note: No database schema changes are needed for Task 2!)*

---

## Step 1: Add The Dropdown To The View

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

Find the `<form method="GET" action="/dashboard/forum/admin" class="advanced-filters" data-filter-form>` in the `questions-section`.

Add the new Category filter right next to the existing Status filter:

**Code to insert:**
```html
<div class="filter-group">
    <label class="filter-label" for="category-filter">Category:</label>
    <select id="category-filter" class="filter-select" name="category">
        <option value="all" <?= ($filters['category'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Categories</option>
        <option value="programming" <?= ($filters['category'] ?? '') === 'programming' ? 'selected' : '' ?>>Programming</option>
        <option value="web-development" <?= ($filters['category'] ?? '') === 'web-development' ? 'selected' : '' ?>>Web Dev</option>
        <option value="database" <?= ($filters['category'] ?? '') === 'database' ? 'selected' : '' ?>>Database</option>
    </select>
</div>
```
*(Note: The PHP inside the `<option>` tags ensures the dropdown "remembers" what the admin selected after the page reloads!)*

---

## Step 2: Read The Filter In The Controller

**File to update:** `app/controllers/Forum/ForumAdminController.php`

Find the `showForumAdminDashboard()` method. 

**1. Capture the new input from `$_GET`:**
Right below `$dateTo = trim($_GET['date_to'] ?? '');`, add:
```php
$category = trim($_GET['category'] ?? 'all'); // <-- ADD THIS
```

**2. Pass the new variable into the Model method:**
Find the `$questions = $forumAdminModel->getQuestionsForModeration(...)` call and add the new parameter:
```php
$questions = $forumAdminModel->getQuestionsForModeration(
    $status,
    $search ?: null,
    $dateFrom ?: null,
    $dateTo ?: null,
    $category // <-- ADD THIS
);
```

**3. Pass the filter back to the View so the dropdown remembers the selection:**
Inside the `$this->viewApp(...)` array, find the `'filters' => [...]` array and add the category:
```php
'filters' => [
    'status' => $status,
    'search' => $search,
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
    'category' => $category // <-- ADD THIS
],
```

---

## Step 3: Update The SQL Query In The Model

**File to update:** `app/models/ForumAdminModel.php`

Find the `getQuestionsForModeration()` method.

**1. Update the method signature to accept the new parameter:**
```php
public function getQuestionsForModeration(
    string $status = 'all',
    ?string $search = null,
    ?string $dateFrom = null,
    ?string $dateTo = null,
    string $category = 'all', // <-- ADD THIS (Before $limit)
    int $limit = 25
): array {
```

**2. Add the condition to the SQL query:**
Scroll down slightly to where the `$params` array and the `if` statements are building the query. Right before the `if ($search)` block, add this new check:

```php
if ($category !== 'all') {
    $sql .= " AND q.category = :category";
    $params[':category'] = $category;
}
```

*How this works:* If the admin leaves it on "All Categories", nothing happens. If they select "programming", it dynamically appends `AND q.category = 'programming'` to the SQL query and binds the value safely!

---

## Step 4: Test The Task

1. Log in as an Admin and navigate to `/dashboard/forum/admin`.
2. In the **Question Moderation** table controls, you should see your new "Category" dropdown.
3. Select "Programming" and click "Filter".
4. Verify the URL changes to include `?category=programming`.
5. Verify the table ONLY shows questions that have "programming" as their category!
6. Ensure the dropdown still says "Programming" instead of resetting to "All Categories".
