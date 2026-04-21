# Edu Archive Task 8: "My Bookmarks" Filter Toggle

This guide explains how to complete a common database-join code-check task step by step:

> Add a "Show Bookmarked Only" checkbox to the Edu Archive filter panel. When checked, the archive should only display resources that the logged-in user has bookmarked.

This task focuses on:
1. Reading a checkbox parameter from the URL (`$_GET`).
2. Passing the toggle state and the current `user_id` to the Model.
3. Dynamically adding an SQL `JOIN` clause to filter results based on a related table (`edu_bookmarks`).
4. Updating the View UI to display the checkbox and maintain its checked state.

---

## Files You Need To Change

- `app/controllers/EduArchive/EduController.php`
- `app/models/EduResourceModel.php`
- `app/views/User/edu-archive/archive-view.php`

---

## Step 1: Read The Parameter In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`index()`

### 1. Read the bookmarked_only filter
Find where the other filters are being read from `$_GET` (around line 85) and add the new parameter. Also, grab the current user's ID.

```php
$search = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$bookmarkedOnly = isset($_GET['bookmarked_only']) && $_GET['bookmarked_only'] === '1'; // <-- ADD THIS
$userId = $_SESSION['user_id'] ?? null; // <-- ADD THIS
```

### 2. Pass it to the model
Update the calls to `getAllResourcesCount` and `getAllResources` to include the new variables. 
*(Note: Add these to the end of the argument lists).*

```php
$totalResources = $model->getAllResourcesCount($type, $subject, $year, $search, $tag, $bookmarkedOnly, $userId);
// ...
$resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset, $bookmarkedOnly, $userId);
```

### 3. Pass it to the view
Add it to the `$filters` array so the view remembers if the checkbox was checked:

```php
'filters' => [
    'type' => $type,
    'subject' => $subject,
    'year' => $year,
    'search' => $search,
    'tag' => $tag,
    'bookmarked_only' => $bookmarkedOnly // <-- ADD THIS
],
```

---

## Step 2: Apply The Filter Using An SQL JOIN

### File to update
`app/models/EduResourceModel.php`

### 1. Update `getAllResourcesCount`
Find `getAllResourcesCount` and add `$bookmarkedOnly = false, $userId = null` to the arguments:

```php
public function getAllResourcesCount($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $bookmarkedOnly = false, $userId = null) {
```

Inside the method, modify the `FROM` clause to add an `INNER JOIN` if the filter is active:

```php
$sql = "SELECT COUNT(*)
        FROM edu_resources r";

// Dynamically join the bookmarks table to filter only saved items.
if ($bookmarkedOnly && $userId) {
    $sql .= " INNER JOIN edu_bookmarks b ON r.id = b.resource_id AND b.user_id = :b_uid";
}

$sql .= " WHERE r.status = 'approved'";

// ... slightly further down, bind the new parameter:
if ($bookmarkedOnly && $userId) {
    $params[':b_uid'] = $userId;
}
```

### 2. Update `getAllResources`
Do the exact same thing for `getAllResources`. Update the arguments:

```php
public function getAllResources($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $limit = 100, $offset = 0, $bookmarkedOnly = false, $userId = null) {
```

Modify the `FROM` clause:

```php
$sql = "SELECT r.*, u.first_name, u.last_name
        FROM edu_resources r
        JOIN users u ON r.user_id = u.id";

if ($bookmarkedOnly && $userId) {
    $sql .= " INNER JOIN edu_bookmarks b ON r.id = b.resource_id AND b.user_id = :b_uid";
}

$sql .= " WHERE r.status = 'approved'";

// ... and bind the parameter before executing:
if ($bookmarkedOnly && $userId) {
    $params[':b_uid'] = $userId;
}
```

---

## Step 3: Add The Checkbox To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### 1. Add the hidden input to the Search form
Find the `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">` and add a hidden input so searching doesn't clear the bookmark filter:

```php
<?php if (!empty($filters['bookmarked_only'])): ?>
    <input type="hidden" name="bookmarked_only" value="1">
<?php endif; ?>
```

### 2. Add the checkbox to the Filter Panel
Find the `<form method="GET" ... class="archive-filter-form">` and add this checkbox grouping at the end of the filters, just before the "Clear" button:

```php
<?php if (isset($_SESSION['user_id'])): ?>
<div class="archive-filter-group" style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
    <input type="checkbox" id="archive-bookmarked" name="bookmarked_only" value="1" onchange="this.form.submit()" <?= !empty($filters['bookmarked_only']) ? 'checked' : '' ?>>
    <label for="archive-bookmarked" style="margin: 0; cursor: pointer; font-weight: 500;">Show My Bookmarks Only</label>
</div>
<?php endif; ?>
```

---

## Step 4: Test The Task

1.  Navigate to `/dashboard/edu-archive`.
2.  Ensure you have bookmarked at least 1 or 2 resources.
3.  Check the "Show My Bookmarks Only" box. The page should reload and **only** display the resources you bookmarked.
4.  Uncheck the box. The page should reload and show all resources again.
5.  Try combining the checkbox with a "Subject" or "Year" filter to ensure they stack correctly.
