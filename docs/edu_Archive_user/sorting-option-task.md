# Edu Archive Task 5: Sorting Option

This guide explains how to complete a common code-check task step by step:

> Add a "Sort By" dropdown to the Edu Archive page. Allow users to sort resources by **Newest First**, **Oldest First**, and **Title (A-Z)**.

This task focuses on:
1. Reading a `sort` parameter from the URL (`$_GET`).
2. Passing the sort option to the Model.
3. Dynamically and safely changing the `ORDER BY` clause in the SQL query.
4. Updating the View to include the sort dropdown and preserve the selection.

---

## Files You Need To Change

- `app/controllers/EduArchive/EduController.php`
- `app/models/EduResourceModel.php`
- `app/views/User/edu-archive/archive-view.php`

---

## Step 1: Read The Sort Parameter In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`index()`

### 1. Read the sort filter
Find where the other filters are being read from `$_GET` (around line 85) and add the sort parameter:

```php
$search = $_GET['q'] ?? null;
$tag = $_GET['tag'] ?? null;
$sort = $_GET['sort'] ?? 'newest'; // <-- ADD THIS, default to 'newest'
```

### 2. Pass it to the model
Update the call to `getAllResources` to include the new `$sort` variable. The count method does not need it.

```php
$resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset, $sort);
```

### 3. Pass it to the view
Add it to the `$filters` array so the view can remember the user's selection:

```php
'filters' => [
    'type' => $type,
    'subject' => $subject,
    'year' => $year,
    'search' => $search,
    'tag' => $tag,
    'sort' => $sort // <-- ADD THIS
],
```

---

## Step 2: Apply The Sorting In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`getAllResources()`

### 1. Update the method signature
Find `getAllResources` and add `$sort = 'newest'` to its arguments:

```php
public function getAllResources($type = 'all', $subject = null, $year = null, $search = null, $tag = null, $limit = 100, $offset = 0, $sort = 'newest') {
```

### 2. Dynamically build the ORDER BY clause
Find the `ORDER BY` line at the end of the SQL query and replace it with this logic:

```php
// Whitelist allowed sort options for security.
$sortOptions = [
    'newest' => 'r.created_at DESC',
    'oldest' => 'r.created_at ASC',
    'title' => 'r.title ASC'
];
$orderBy = $sortOptions[$sort] ?? $sortOptions['newest'];

// Sort the results and apply the pagination limits.
$sql .= " ORDER BY {$orderBy} LIMIT :limit OFFSET :offset";
```

This replaces the old hardcoded `ORDER BY r.created_at DESC`.

---

## Step 3: Add The Dropdown To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### 1. Add the hidden input to the Search form
Find the `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">` and add a hidden input for `sort`:

```php
<input type="hidden" name="sort" value="<?= htmlspecialchars($filters['sort'] ?? 'newest') ?>">
```

### 2. Add the dropdown to the Filter Panel
Find the `<form method="GET" ... class="archive-filter-form">` and add the new sort dropdown, for example, at the end before the "Clear" button:

```php
<div class="archive-filter-group">
    <label for="archive-sort">Sort By</label>
    <div class="archive-select-wrap">
        <select id="archive-sort" name="sort" class="archive-select" onchange="this.form.submit()">
            <option value="newest" <?= ($filters['sort'] ?? 'newest') == 'newest' ? 'selected' : '' ?>>Newest First</option>
            <option value="oldest" <?= ($filters['sort'] ?? '') == 'oldest' ? 'selected' : '' ?>>Oldest First</option>
            <option value="title" <?= ($filters['sort'] ?? '') == 'title' ? 'selected' : '' ?>>Title (A-Z)</option>
        </select>
    </div>
</div>
```

### 3. Update the pagination URL builder
Find the `$buildArchivePageUrl` closure at the top of the file (around line 50) and add `sort` so clicking "Next Page" remembers the sort order:

```php
$query = [
    'type' => $filters['type'] ?? 'all',
    'subject' => $filters['subject'] ?? '',
    'year' => $filters['year'] ?? '',
    'q' => $filters['search'] ?? '',
    'tag' => $filters['tag'] ?? '',
    'sort' => $filters['sort'] ?? 'newest' // <-- ADD THIS
];
```

---

## Step 4: Test The Task

1.  Navigate to `/dashboard/edu-archive`.
2.  Use the new "Sort By" dropdown and select "Oldest First". The resources should reorder.
3.  Select "Title (A-Z)". The resources should reorder alphabetically.
4.  Apply another filter (e.g., "Subject: Computer Science") and confirm the sorting is still applied.
5.  If you have multiple pages, click "Next" and ensure the sort order is maintained on the second page.
