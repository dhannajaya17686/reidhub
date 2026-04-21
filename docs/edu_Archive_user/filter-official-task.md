# Edu Archive Task: "Official Materials Only" Filter

This guide explains how to complete a standard filtering code-check step by step.

> **Objective:** Build on the "Official Resource Flag" task by adding a "Show Official Only" checkbox to the public archive's filter panel. When checked, the page should only display resources that have `is_official = 1`.

This task focuses on:
1. Reading a checkbox state from the URL query string (`$_GET`).
2. Passing that boolean state into the Model.
3. Dynamically adding an `AND is_official = 1` clause to the SQL query.
4. Preserving the checkbox state in the UI across page loads and pagination.

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

### 1. Read the official_only filter
Find where the other filters are being read from `$_GET` (around line 85) and add the new parameter. Remember that unchecked checkboxes don't send data, so we check if it is set to '1'.

```php
$search = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$officialOnly = isset($_GET['official_only']) && $_GET['official_only'] === '1'; // <-- ADD THIS
```

### 2. Pass it to the model
Update the calls to `getAllResourcesCount` and `getAllResources` to include the new `$officialOnly` variable at the end of the arguments:

```php
$totalResources = $model->getAllResourcesCount($type, $subject, $year, $search, $tag, $officialOnly);
// ...
$resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset, $officialOnly);
```
*(Note: If you have already added other filters like $sort or $dateRange, just add $officialOnly to the very end).*

### 3. Pass it to the view
Add it to the `$filters` array so the view remembers if the user checked the box:

```php
'filters' => [
    'type' => $type,
    'subject' => $subject,
    'year' => $year,
    'search' => $search,
    'tag' => $tag,
    'official_only' => $officialOnly // <-- ADD THIS
],
```

---

## Step 2: Apply The Filter In The Model

### File to update
`app/models/EduResourceModel.php`

### 1. Update the method signatures
Find `getAllResources` and `getAllResourcesCount` and add `$officialOnly = false` to their arguments.

Then, pass it into `appendPublicResourceFilters` inside both of those methods:

```php
$this->appendPublicResourceFilters($sql, $params, $type, $subject, $year, $search, $tag, $officialOnly);
```

### 2. Update `appendPublicResourceFilters`
Update the signature of the helper method itself:

```php
private function appendPublicResourceFilters(&$sql, &$params, $type, $subject, $year, $search, $tag, $officialOnly = false) {
```

Then, inside that method, add the SQL logic at the bottom:

```php
// Filter to only show official university materials
if ($officialOnly) {
    $sql .= " AND r.is_official = 1";
}
```

---

## Step 3: Add The Checkbox To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### 1. Add the hidden input to the Search form
Find the `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">` and add a hidden input so searching the search bar doesn't clear the official filter:

```php
<?php if (!empty($filters['official_only'])): ?>
    <input type="hidden" name="official_only" value="1">
<?php endif; ?>
```

### 2. Add the checkbox to the Filter Panel
Find the `<form method="GET" ... class="archive-filter-form">` and add this checkbox grouping at the bottom of the filters, just before the "Clear" button:

```php
<div class="archive-filter-group" style="display: flex; align-items: center; gap: 8px; margin-top: 10px;">
    <input type="checkbox" id="archive-official" name="official_only" value="1" onchange="this.form.submit()" <?= !empty($filters['official_only']) ? 'checked' : '' ?>>
    <label for="archive-official" style="margin: 0; cursor: pointer; font-weight: 500; color: #047857;">Show Official Material Only</label>
</div>
```

### 3. Update the pagination URL builder
Find the `$buildArchivePageUrl` closure at the top of the file (around line 50) and add `official_only` so clicking "Next Page" remembers the filter:

```php
$query = [
    // ... existing lines ...
    'tag' => $filters['tag'] ?? '',
];
if (!empty($filters['official_only'])) {
    $query['official_only'] = '1';
}
```
