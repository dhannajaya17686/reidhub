# Edu Forum Admin Task 2: Filter Answers by Accepted Status

This guide explains how to complete another Filtering (Task 2) code-check specifically for the Edu Forum Admin side:

> **The Task:** Add a new dropdown filter called **"Accepted Solution"** to the global filters form at the top of the Admin Dashboard. When an admin selects "Yes" or "No", the **Answer Moderation** table should update to only show answers that match that status.

This tests your ability to add a filter to a shared form, read `$_GET` data, and modify an SQL query for a specific dataset (`forum_answers`).

---

## Files You Need To Change

- `app/views/Admin/edu-forum/manage-forum-view.php`
- `app/controllers/Forum/ForumAdminController.php`
- `app/models/ForumAdminModel.php`

*(Note: No database schema changes are needed for Task 2!)*

---

## Step 1: Add The Dropdown To The View

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

Find the global `<form method="GET" action="/dashboard/forum/admin" class="advanced-filters" data-filter-form>` located in the `table-controls` of the questions section.

Add the new "Accepted Solution" filter right next to the "Status" filter:

**Code to insert:**
```html
<div class="filter-group">
    <label class="filter-label" for="accepted-filter">Accepted Solution:</label>
    <select id="accepted-filter" class="filter-select" name="is_accepted">
        <option value="all" <?= ($filters['is_accepted'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Answers</option>
        <option value="yes" <?= ($filters['is_accepted'] ?? '') === 'yes' ? 'selected' : '' ?>>Yes</option>
        <option value="no" <?= ($filters['is_accepted'] ?? '') === 'no' ? 'selected' : '' ?>>No</option>
    </select>
</div>
```

---

## Step 2: Read The Filter In The Controller

**File to update:** `app/controllers/Forum/ForumAdminController.php`

Find the `showForumAdminDashboard()` method. 

**1. Capture the new input from `$_GET`:**
Right below `$dateTo = trim($_GET['date_to'] ?? '');`, add:
```php
$isAccepted = trim($_GET['is_accepted'] ?? 'all'); // <-- ADD THIS
```

**2. Pass the new variable into the Answer Model method:**
Find the `$answers = $forumAdminModel->getAnswersForModeration(...)` call (inside the `$this->viewApp(...)` array) and add the new parameter at the end:
```php
'answers' => $forumAdminModel->getAnswersForModeration(
    $status,
    $search ?: null,
    $dateFrom ?: null,
    $dateTo ?: null,
    $isAccepted // <-- ADD THIS
),
```

**3. Pass the filter back to the View so the dropdown remembers the selection:**
Inside the `'filters' => [...]` array, add the new field so the UI doesn't reset when the page reloads:
```php
'filters' => [
    'status' => $status,
    'search' => $search,
    'date_from' => $dateFrom,
    'date_to' => $dateTo,
    'is_accepted' => $isAccepted // <-- ADD THIS
],
```

---

## Step 3: Update The SQL Query In The Model

**File to update:** `app/models/ForumAdminModel.php`

Find the `getAnswersForModeration()` method.

**1. Update the method signature to accept the new parameter:**
```php
public function getAnswersForModeration(
    string $status = 'all',
    ?string $search = null,
    ?string $dateFrom = null,
    ?string $dateTo = null,
    string $isAccepted = 'all', // <-- ADD THIS (Before $limit)
    int $limit = 25
): array {
```

**2. Add the condition to the SQL query:**
Scroll down slightly to where the `$params` array and the `if` statements are building the query. Right before the `ORDER BY` clause is added to the `$sql` string, insert this new check:

```php
if ($isAccepted === 'yes') {
    $sql .= " AND a.is_accepted = 1";
} elseif ($isAccepted === 'no') {
    $sql .= " AND a.is_accepted = 0";
}
```

*How this works:* If the admin leaves it on "All Answers", the query doesn't change. If they select "Yes", it appends `AND a.is_accepted = 1` to strictly return only accepted solutions! (Since these are integers, we don't even need PDO placeholders, we can safely hardcode `1` and `0` into the SQL string).

---

## Step 4: Test The Task

1. Log in as an Admin and navigate to `/dashboard/forum/admin`.
2. In the global filter bar at the top of the page, you should see your new "Accepted Solution" dropdown.
3. Select "Yes" and click "Filter".
4. Scroll down to the **Answer Moderation** table. Verify that *every* answer displayed in the table currently has the "✓ Solved" badge (or says "Accepted: Yes" in the chip).
5. Verify the URL changes to include `?is_accepted=yes`.
