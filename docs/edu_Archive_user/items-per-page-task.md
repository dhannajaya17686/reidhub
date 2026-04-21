# Edu Archive Task 6: Items Per Page Dropdown

This guide explains how to complete a common code-check task step by step:

> Add an "Items Per Page" dropdown to the Edu Archive page. Allow users to choose whether to display **12**, **24**, or **48** resources per page.

This task focuses on:
1. Reading a `per_page` parameter from the URL (`$_GET`).
2. Overriding the hardcoded `$perPage` variable in the Controller safely.
3. Updating the View to include the new dropdown and preserve the selection.
4. Ensuring pagination links carry over the selected `per_page` value.

---

## Files You Need To Change

- `app/controllers/EduArchive/EduController.php`
- `app/views/User/edu-archive/archive-view.php`

---

## Step 1: Read The Parameter In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`index()`

### 1. Read the per_page filter
Find where the other filters are being read from `$_GET` (around line 85). Right below it, replace the hardcoded `$perPage = 12;` line with this logic to safely read the user's choice:

```php
// Read the requested per_page amount, default to 12, restrict to allowed values for safety.
$requestedPerPage = (int)($_GET['per_page'] ?? 12);
$perPage = in_array($requestedPerPage, [12, 24, 48]) ? $requestedPerPage : 12;
```

### 2. Pass it to the view
Add it to the `$filters` array when calling `$this->viewApp(...)` so the view can remember the user's selection:

```php
'filters' => [
    'type' => $type,
    'subject' => $subject,
    'year' => $year,
    'search' => $search,
    'tag' => $tag,
    'per_page' => $perPage // <-- ADD THIS
],
```

---

## Step 2: Add The Dropdown To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### 1. Add the hidden input to the Search form
Find the `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">` and add a hidden input for `per_page`:

```php
<input type="hidden" name="per_page" value="<?= htmlspecialchars($filters['per_page'] ?? 12) ?>">
```

### 2. Add the dropdown to the Filter Panel
Find the `<form method="GET" ... class="archive-filter-form">` and add the new "Per Page" dropdown, for example, right next to the Content Type dropdown:

```php
<div class="archive-filter-group">
    <label for="archive-per-page">Per Page</label>
    <div class="archive-select-wrap">
        <select id="archive-per-page" name="per_page" class="archive-select" onchange="this.form.submit()">
            <option value="12" <?= ($filters['per_page'] ?? 12) == 12 ? 'selected' : '' ?>>12 items</option>
            <option value="24" <?= ($filters['per_page'] ?? 12) == 24 ? 'selected' : '' ?>>24 items</option>
            <option value="48" <?= ($filters['per_page'] ?? 12) == 48 ? 'selected' : '' ?>>48 items</option>
        </select>
    </div>
</div>
```

### 3. Update the pagination URL builder
Find the `$buildArchivePageUrl` closure at the top of the file (around line 50) and add `per_page` so clicking "Next Page" remembers how many items to show:

```php
$query = [
    'type' => $filters['type'] ?? 'all',
    'subject' => $filters['subject'] ?? '',
    'year' => $filters['year'] ?? '',
    'q' => $filters['search'] ?? '',
    'tag' => $filters['tag'] ?? '',
    'per_page' => $filters['per_page'] ?? 12 // <-- ADD THIS
];
```

---

## Step 3: Test The Task

1.  Navigate to `/dashboard/edu-archive`.
2.  By default, you should see up to 12 items.
3.  Change the "Per Page" dropdown to 24. The page should reload and display up to 24 items.
4.  Click on page 2 (if you have enough resources). Ensure the `&per_page=24` parameter stays in the URL and the page still shows 24 items.
5.  Perform a search and verify the `per_page` limit is respected in the search results.
