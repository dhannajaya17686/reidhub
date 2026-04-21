# Edu Archive Admin Task: Video Content Count

This guide explains how to complete a standard "count" or "stats" code-check for the Admin panel.

> **Objective:** Add a new summary card to the top of the Edu Archive Admin dashboard that displays the total number of **Video** resources currently in the system (regardless of their approval status).

This task focuses on:
1. Updating an existing aggregate SQL query (`SUM(CASE WHEN...)`) in the Model.
2. Updating the View to render the new count card.
*(Note: No database schema changes are required because the `type` column already exists!)*

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
Find the `getAdminCounts()` method (around line 200). You will see a `SELECT` statement with multiple `SUM(CASE WHEN...)` lines. Add a new line to count the video resources.

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
                    SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) AS video_count, -- <-- ADD THIS
                    {$hiddenCountSql} AS hidden_count,
                    {$removalCountSql} AS removal_request_count
                FROM edu_resources";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
```

### Why this step matters
Using `SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END)` allows MySQL to count the specific resource types efficiently while it is already scanning the table to count the pending and approved statuses.

---

## Step 2: Add The Card To The Admin View

### File to update
`app/views/Admin/edu-archive/manage-archive-view.php`

### Where to insert it
Find the `<section class="edu-admin-stats">` block near the top of the file. You will see several `<a class="stat-card">` elements.

### Code to insert
Add your new Video card alongside the others (for example, right after the "Total" card at the beginning).

We will make the link point to the existing `type=video` filter so clicking the card actually filters the table!

```php
        <?php
            // Build the link to filter the dashboard by video type
            $videoLink = '/dashboard/edu-archive/admin?' . http_build_query($statBaseFilters + ['status' => 'all', 'type' => 'video', 'hidden' => '']) . '#resources-panel';
        ?>
        <a class="stat-card stat-card-link <?= ($filters['type'] ?? '') === 'video' ? 'is-active' : '' ?>" href="<?= htmlspecialchars($videoLink) ?>" style="border-left: 4px solid #8b5cf6;">
            <span>Total Videos</span>
            <strong style="color: #7c3aed;"><?= (int)($counts['video_count'] ?? 0) ?></strong>
        </a>
```

---

## Step 3: Test The Task

1.  Navigate to the Edu Archive Admin Dashboard (`/dashboard/edu-archive/admin`).
2.  Look at the top summary cards; you should now see a **"Total Videos"** card.
3.  Click the card. The page should reload, the card should become highlighted (active), and the table below should filter to only show video resources!
4.  Check the database or manually count the videos in the list to ensure the number on the card is perfectly accurate.
