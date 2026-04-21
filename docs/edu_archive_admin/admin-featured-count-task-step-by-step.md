# Edu Archive Admin Task: Featured Resource Count

This guide explains how to complete a standard "count" or "stats" code-check for the Admin panel.

> **Objective:** Add a new summary card to the top of the Edu Archive Admin dashboard that displays the total number of **Featured Resources**.

This task assumes you have already added the `is_featured` column to the `edu_resources` table (from a previous task).

This task focuses on:
1. Updating an existing aggregate SQL query (`SUM(CASE WHEN...)`) in the Model.
2. Updating the View to render the new count card.
*(Note: The controller usually doesn't need to change for this, as it already passes the entire `$counts` array to the view).*

---

## Files You Need To Change

- `app/models/EduResourceModel.php`
- `app/views/Admin/edu-archive/manage-archive-view.php`

---

## Step 1: Update The Count Query In The Model

### File to update
`app/models/EduResourceModel.php`

### Method to update
`getAdminCounts()`

### 1. Update the SQL statement
Find the `getAdminCounts()` method (around line 200). You will see a `SELECT` statement with multiple `SUM(CASE WHEN...)` lines. Add a new line to count the featured resources.

```php
    public function getAdminCounts() {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        $hiddenCountSql = $moderationColumnsReady ? "SUM(CASE WHEN status = 'approved' AND is_hidden = 1 THEN 1 ELSE 0 END)" : "0";
        $removalCountSql = $moderationColumnsReady ? "SUM(CASE WHEN removal_requested = 1 THEN 1 ELSE 0 END)" : "0";

        $sql = "SELECT
                    COUNT(*) AS total_count,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) AS pending_count,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) AS approved_count,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) AS rejected_count,
                    SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) AS featured_count, -- <-- ADD THIS
                    {$hiddenCountSql} AS hidden_count,
                    {$removalCountSql} AS removal_request_count
                FROM edu_resources";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
```

### Why this step matters
Adding this line to the existing query is highly efficient because it calculates the new total at the exact same time it calculates all the other dashboard totals, hitting the database only once.

---

## Step 2: Add The Card To The Admin View

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the `<section class="edu-admin-stats">` block near the top of the file. You will see several `<a class="stat-card">` elements.

### Code to insert
Add your new Featured card alongside the others (for example, right after the "Approved" card).

```php
        <a class="stat-card stat-card-link" href="/dashboard/edu-archive/admin?featured_only=1#resources-panel" style="border-left: 4px solid #d97706;">
            <span>Featured</span>
            <strong style="color: #b45309;"><?= (int)($counts['featured_count'] ?? 0) ?></strong>
        </a>
```
*(Note: We added a subtle orange border and text color to visually distinguish it as the "Featured" stat).*

---

## Step 3: Test The Task

1.  Navigate to the Edu Archive Admin Dashboard (`/dashboard/edu-archive/admin`).
2.  Look at the top summary cards; you should now see a **"Featured"** card.
3.  Find a resource in the list below, check the "Mark as Featured Resource" box, and click **Save Metadata**.
4.  The page will reload, and the Featured count card at the top should increase by 1!
