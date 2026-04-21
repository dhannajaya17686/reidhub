# Edu Archive Task 7: Date Range Filter

This guide explains how to complete a common code-check task step by step:

> Add a "Date Uploaded" filter to the Edu Archive page. Allow users to filter resources to show only those uploaded **Today**, **This Week**, or **This Month**.

This task focuses on:
1. Reading a `date_range` parameter from the URL (`$_GET`).
2. Passing the parameter to the Model's filtering method.
3. Using MySQL Date functions (`CURDATE()`, `DATE_SUB()`) safely inside the query.
4. Updating the View UI to display the new dropdown and preserve its state.

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

### 1. Read the date_range filter
Find where the other filters are being read from `$_GET` (around line 85) and add the new parameter:

```php
$search = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$dateRange = $_GET['date_range'] ?? 'all'; // <-- ADD THIS
```

### 2. Pass it to the model
Update the calls to `getAllResourcesCount` and `getAllResources` to include the new `$dateRange` variable:

```php
$totalResources = $model->getAllResourcesCount($type, $subject, $year, $search, $tag, $dateRange);
// ...
$resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset, $dateRange);
```
*(Note: If you have already completed previous sorting/pagination tasks, just add `$dateRange` to the end of your argument lists).*

### 3. Pass it to the view
Add it to the `$filters` array so the view remembers the user's selection:

```php
'filters' => [
    'type' => $type,
    'subject' => $subject,
    'year' => $year,
    'search' => $search,
    'tag' => $tag,
    'date_range' => $dateRange // <-- ADD THIS
],
```

---

## Step 2: Apply The Filter In The Model

### File to update
`app/models/EduResourceModel.php`

### 1. Update the method signatures
Find `getAllResources` and `getAllResourcesCount` and add `$dateRange = 'all'` to their arguments.

Then, pass it into `appendPublicResourceFilters` inside both of those methods:

```php
$this->appendPublicResourceFilters($sql, $params, $type, $subject, $year, $search, $tag, $dateRange);
```

### 2. Update `appendPublicResourceFilters`
Update the signature of the helper method itself:

```php
private function appendPublicResourceFilters(&$sql, &$params, $type, $subject, $year, $search, $tag, $dateRange = 'all') {
```

Then, inside that method, add the MySQL date logic at the bottom:

```php
// Filter by upload date using MySQL date functions
if ($dateRange === 'today') {
    $sql .= " AND r.created_at >= CURDATE()";
} elseif ($dateRange === 'this_week') {
    $sql .= " AND r.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($dateRange === 'this_month') {
    $sql .= " AND r.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}
```

---

## Step 3: Add The Dropdown To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### 1. Add the hidden input to the Search form
Find the `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">` and add a hidden input so searching doesn't clear the date filter:

```php
<input type="hidden" name="date_range" value="<?= htmlspecialchars($filters['date_range'] ?? 'all') ?>">
```

### 2. Add the dropdown to the Filter Panel
Find the `<form method="GET" ... class="archive-filter-form">` and add the new filter group inside it (e.g., next to Content Type):

```php
<div class="archive-filter-group">
    <label for="archive-date">Date Uploaded</label>
    <div class="archive-select-wrap">
        <select id="archive-date" name="date_range" class="archive-select" onchange="this.form.submit()">
            <option value="all" <?= ($filters['date_range'] ?? 'all') == 'all' ? 'selected' : '' ?>>All Time</option>
            <option value="today" <?= ($filters['date_range'] ?? '') == 'today' ? 'selected' : '' ?>>Today</option>
            <option value="this_week" <?= ($filters['date_range'] ?? '') == 'this_week' ? 'selected' : '' ?>>This Week</option>
            <option value="this_month" <?= ($filters['date_range'] ?? '') == 'this_month' ? 'selected' : '' ?>>This Month</option>
        </select>
    </div>
</div>
```

### 3. Update the pagination URL builder
Find the `$buildArchivePageUrl` closure at the top of the file (around line 50) and add `date_range` so clicking "Next Page" remembers the filter:

```php
$query = [
    'type' => $filters['type'] ?? 'all',
    'subject' => $filters['subject'] ?? '',
    'year' => $filters['year'] ?? '',
    'q' => $filters['search'] ?? '',
    'tag' => $filters['tag'] ?? '',
    'date_range' => $filters['date_range'] ?? 'all' // <-- ADD THIS
];
```

---

## Step 4: Test The Task

1.  Navigate to `/dashboard/edu-archive`.
2.  Select "Today" from the Date Uploaded dropdown. The page should reload and only show items uploaded in the last 24 hours.
3.  Select "This Month" to see recent items.
4.  Click on Page 2 (if available) and make sure the `&date_range=this_month` parameter stays in the URL.
