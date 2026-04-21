# Edu Archive Admin Task 2: Filter by Difficulty

This guide explains how to complete a Filtering (Task 2) code-check specifically for the Edu Archive Admin side:

> **The Task:** Add a new dropdown filter for **Difficulty Level** to the global search/filter form at the top of the Admin Dashboard. When an admin selects "Beginner", "Intermediate", or "Advanced", the resource table should update to only show materials matching that difficulty.

This tests your ability to add a filter to a shared form, read `$_GET` data, and pass it to a dynamic SQL query builder.

---

## Files You Need To Change

- `app/views/Admin/edu-archive/manage-archive-view.php`
- `app/controllers/EduArchive/EduAdminController.php`
- `app/models/EduResourceModel.php`

*(Note: No database schema changes are needed for Task 2 since the column was added in Task 1!)*

---

## Step 1: Add The Dropdown To The View

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the `<form method="GET" action="/dashboard/edu-archive/admin" class="filter-form" data-filter-form>` (around line 125). 

### Code to insert
Add the new Difficulty filter right next to the "Subject" or "Type" select dropdowns:

```html
<select name="difficulty_level">
    <option value="all" <?= ($filters['difficulty_level'] ?? 'all') === 'all' ? 'selected' : '' ?>>All Difficulties</option>
    <option value="Beginner" <?= ($filters['difficulty_level'] ?? '') === 'Beginner' ? 'selected' : '' ?>>Beginner</option>
    <option value="Intermediate" <?= ($filters['difficulty_level'] ?? '') === 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
    <option value="Advanced" <?= ($filters['difficulty_level'] ?? '') === 'Advanced' ? 'selected' : '' ?>>Advanced</option>
</select>
```
*(Note: The PHP inside the `<option>` tags ensures the dropdown "remembers" what the admin selected after the page reloads!)*

---

## Step 2: Read The Filter In The Controller

### File to update
`app/controllers/EduArchive/EduAdminController.php`

### Method to update
`showManageArchive()`

### 1. Capture the new input from `$_GET`
Find where the other filters are read (around line 52) and add your new parameter:

```php
$hidden = $_GET['hidden'] ?? '';
$removal = $_GET['removal'] ?? '';
$difficultyLevel = trim($_GET['difficulty_level'] ?? 'all'); // <-- ADD THIS
```

### 2. Pass the new variable into the Model methods
Find the `$model->getAdminResourcesCount(...)` and `$model->getAdminResources(...)` calls. Add `$difficultyLevel` to the end of their arguments:

```php
$totalResources = $model->getAdminResourcesCount($status, $type, $subject ?: null, $year ?: null, $search ?: null, $tag ?: null, $hidden ?: null, $removal ?: null, $difficultyLevel);
// ...
$resources = $model->getAdminResources($status, $type, $subject ?: null, $year ?: null, $search ?: null, $tag ?: null, $hidden ?: null, $perPage, $offset, $removal ?: null, $difficultyLevel);
```

### 3. Pass the filter back to the View
Inside the `$this->viewApp(...)` array, find the `'filters' => [...]` array and add the difficulty so the dropdown remembers the selection:

```php
'filters' => [
    'status' => $status,
    'type' => $type,
    // ...
    'removal' => $removal,
    'difficulty_level' => $difficultyLevel // <-- ADD THIS
],
```

---

## Step 3: Update The SQL Query In The Model

### File to update
`app/models/EduResourceModel.php`

### 1. Update the helper method signature
Find the `appendAdminResourceFilters()` method (around line 96). This is where all admin filters are processed. Add the new argument to the end:

```php
private function appendAdminResourceFilters(&$sql, &$params, $status, $type, $subject, $year, $search, $tag, $hidden, $removal = null, $difficultyLevel = 'all') {
```

### 2. Add the condition to the SQL query
Inside that same `appendAdminResourceFilters()` method, scroll to the bottom and add this logic:

```php
// Filter by difficulty level
if ($difficultyLevel && $difficultyLevel !== 'all') {
    $sql .= " AND r.difficulty_level = :difficulty";
    $params[':difficulty'] = $difficultyLevel;
}
```

### 3. Update the main method signatures
Find `getAdminResources()` and `getAdminResourcesCount()` and update their signatures so they accept the variable you passed from the controller:

```php
public function getAdminResources($status = 'all', $type = 'all', $subject = null, $year = null, $search = null, $tag = null, $hidden = null, $limit = 200, $offset = 0, $removal = null, $difficultyLevel = 'all') {
```

```php
public function getAdminResourcesCount($status = 'all', $type = 'all', $subject = null, $year = null, $search = null, $tag = null, $hidden = null, $removal = null, $difficultyLevel = 'all') {
```

And inside both of those methods, ensure you pass `$difficultyLevel` into the helper call:
```php
$this->appendAdminResourceFilters($sql, $params, $status, $type, $subject, $year, $search, $tag, $hidden, $removal, $difficultyLevel);
```

---

## Step 4: Preserve in Pagination (View)

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

Find the `$buildAdminPageUrl` closure near the top of the file (around line 43) and add the parameter so clicking "Page 2" doesn't reset the filter:

```php
'removal' => $filters['removal'] ?? '',
'difficulty_level' => $filters['difficulty_level'] ?? 'all' // <-- ADD THIS
```

---

## Step 5: Test The Task

1. Log in as an Admin and navigate to `/dashboard/edu-archive/admin`.
2. In the global filter bar, you should see your new "All Difficulties" dropdown.
3. Select "Intermediate" and click "Search".
4. Verify the URL changes to include `?difficulty_level=Intermediate` and the table ONLY shows intermediate resources!
