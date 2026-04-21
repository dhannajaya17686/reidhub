# Edu Archive Task 2: Filter by Difficulty Level

This guide explains how to complete the second Edu Archive code-check task step by step:

> Build on Task 1 by adding a "Difficulty" dropdown to the filter panel on the public archive page. Update the controller and model to filter the database results when a user selects a difficulty level.

This task focuses on:
1. Reading `$_GET` parameters
2. Passing parameters through the Controller to the Model
3. Dynamically building SQL `WHERE` clauses safely
4. Updating the View UI to retain the selected filter state across pagination

---

## Files You Need To Change

- `app/controllers/EduArchive/EduController.php`
- `app/models/EduResourceModel.php`
- `app/views/User/edu-archive/archive-view.php`

---

## Step 1: Read The URL Parameter In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`index()`

### 1. Read the difficulty filter
Find where the filters are being read from `$_GET` (around line 84) and add the difficulty parameter:

```php
$search = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$difficulty = $_GET['difficulty'] ?? null; // <-- ADD THIS
```

### 2. Pass it to the model methods
Update the calls to `getAllResourcesCount` and `getAllResources` to include the new `$difficulty` variable:

```php
$totalResources = $model->getAllResourcesCount($type, $subject, $year, $search, $tag, $difficulty);
// ...
$resources = $model->getAllResources($type, $subject, $year, $search, $tag, $difficulty, $perPage, $offset);
```

### 3. Pass it to the view
Add it to the `$filters` array that gets passed to `viewApp()` so the view can remember what the user selected:

```php
'filters' => [
    'type' => $type,
    'subject' => $subject,
    'year' => $year,
    'search' => $search,
    'tag' => $tag,
    'difficulty' => $difficulty // <-- ADD THIS
],
```

---

## Step 2: Apply The Filter In The Model

### File to update
`app/models/EduResourceModel.php`

### 1. Update the method signatures
Find `getAllResources` and `getAllResourcesCount` and add `$difficulty = null` to their arguments:

```php
public function getAllResources($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $difficulty = null, $limit = 100, $offset = 0)
// ...
public function getAllResourcesCount($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $difficulty = null)
```

Pass it into `appendPublicResourceFilters` inside both of those methods:

```php
$this->appendPublicResourceFilters($sql, $params, $type, $subject, $year, $search, $tag, $difficulty);
```

### 2. Update `appendPublicResourceFilters`
Update the signature of the helper method itself:

```php
private function appendPublicResourceFilters(&$sql, &$params, $type, $subject, $year, $search, $tag, $difficulty = null) {
```

Then, inside that method, add the new SQL condition at the bottom:

```php
// Filter by difficulty level
if ($difficulty) {
    $sql .= " AND r.difficulty_level = :difficulty";
    $params[':difficulty'] = $difficulty;
}
```

---

## Step 3: Add The Dropdown To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### 1. Add the hidden input to the Search form
Find the `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">` and add the hidden input so searching doesn't clear the difficulty:

```php
<input type="hidden" name="difficulty" value="<?= htmlspecialchars($filters['difficulty'] ?? '') ?>">
```

### 2. Add the dropdown to the Filter Panel
Find the `<form method="GET" ... class="archive-filter-form">` and add the new filter group inside it (e.g., under the Year dropdown):

```php
<div class="archive-filter-group">
    <label for="archive-difficulty">Difficulty</label>
    <div class="archive-select-wrap">
        <select id="archive-difficulty" name="difficulty" class="archive-select" onchange="this.form.submit()">
            <option value="">All Difficulties</option>
            <option value="Beginner" <?= ($filters['difficulty'] ?? '') == 'Beginner' ? 'selected' : '' ?>>Beginner</option>
            <option value="Intermediate" <?= ($filters['difficulty'] ?? '') == 'Intermediate' ? 'selected' : '' ?>>Intermediate</option>
            <option value="Advanced" <?= ($filters['difficulty'] ?? '') == 'Advanced' ? 'selected' : '' ?>>Advanced</option>
        </select>
    </div>
</div>
```

### 3. Update the pagination URL builder
Find the `$buildArchivePageUrl` closure at the top of the file (around line 50) and add difficulty so clicking "Next Page" remembers the filter:

```php
$query = [
    'type' => $filters['type'] ?? 'all',
    'subject' => $filters['subject'] ?? '',
    'year' => $filters['year'] ?? '',
    'q' => $filters['search'] ?? '',
    'tag' => $filters['tag'] ?? '',
    'difficulty' => $filters['difficulty'] ?? '' // <-- ADD THIS
];
```

### 4. (Optional) Add to Active Filters display
Find the `$activeFilters` array builder at the top (around line 72) and add:

```php
if (!empty($filters['difficulty'])) {
    $activeFilters[] = 'Difficulty: ' . $filters['difficulty'];
}
```
