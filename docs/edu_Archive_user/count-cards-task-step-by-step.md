# Edu Archive Task 4: Count Cards

This guide explains how to complete a very common code-check task step by step:

> Add summary cards to the top of the Edu Archive public page displaying the **Total Resources**, **Total Videos**, and **Total Notes** available in the archive.

This task focuses on:
1. Writing an aggregate SQL query in the Model (`COUNT` and `SUM(CASE WHEN ...)`).
2. Calling the method in the Controller and passing the data to the View.
3. Displaying the data using HTML/CSS in the View.

---

## Files You Need To Change

- `app/models/EduResourceModel.php`
- `app/controllers/EduArchive/EduController.php`
- `app/views/User/edu-archive/archive-view.php`

---

## Step 1: Add A Stats Method In The Model

### File to update
`app/models/EduResourceModel.php`

### What to add
Add this new method inside the `EduResourceModel` class, for example, right below `getAllResourcesCount()`:

```php
    // Get aggregate counts for the archive summary cards.
    public function getArchiveStats() {
        $moderationColumnsReady = $this->ensureArchiveModerationColumns();
        
        $sql = "SELECT
                    COUNT(*) AS total_resources,
                    SUM(CASE WHEN type = 'video' THEN 1 ELSE 0 END) AS total_videos,
                    SUM(CASE WHEN type = 'note' THEN 1 ELSE 0 END) AS total_notes
                FROM edu_resources
                WHERE status = 'approved'";
                
        // Ensure we don't count resources hidden by the admin.
        if ($moderationColumnsReady) {
            $sql .= " AND (is_hidden = 0 OR is_hidden IS NULL)";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
```

### Why this step matters
This method safely calculates all three required values in a single, efficient database query.

---

## Step 2: Call The Stats Method In The Controller

### File to update
`app/controllers/EduArchive/EduController.php`

### Method to update
`index()`

### 1. Fetch the stats
Find the part of the method where you are fetching the resources (around line 96), and add the call to your new stats method:

```php
$resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset);
$archiveStats = $model->getArchiveStats(); // <-- ADD THIS
```

### 2. Pass it to the view
Add it to the array passed to `$this->viewApp(...)`:

```php
$this->viewApp('User/edu-archive/archive-view', [
    'resources' => $resources,
    'archive_stats' => $archiveStats, // <-- ADD THIS
    'filters' => ['type' => $type, 'subject' => $subject, 'year' => $year, 'search' => $search, 'tag' => $tag],
    // ...
], 'Edu Archive - ReidHub');
```

---

## Step 3: Add The Cards To The View

### File to update
`app/views/User/edu-archive/archive-view.php`

### Where to insert it
Find the `.archive-top` div (around line 93) which contains the page title and buttons. Insert the cards directly below it, just above the search form.

### Code to insert
```php
<!-- Summary Stats Cards -->
<section class="archive-stats" style="display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 24px;">
    
    <div class="stat-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size: 2rem; font-weight: 700; color: #0f172a; line-height: 1;">
            <?= (int)($archive_stats['total_resources'] ?? 0) ?>
        </div>
        <div style="color: #64748b; font-size: 0.9rem; margin-top: 4px; font-weight: 500;">
            Total Resources
        </div>
    </div>

    <div class="stat-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size: 2rem; font-weight: 700; color: #0284c7; line-height: 1;">
            <?= (int)($archive_stats['total_videos'] ?? 0) ?>
        </div>
        <div style="color: #64748b; font-size: 0.9rem; margin-top: 4px; font-weight: 500;">
            Educational Videos
        </div>
    </div>

    <div class="stat-card" style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; flex: 1; min-width: 150px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div style="font-size: 2rem; font-weight: 700; color: #059669; line-height: 1;">
            <?= (int)($archive_stats['total_notes'] ?? 0) ?>
        </div>
        <div style="color: #64748b; font-size: 0.9rem; margin-top: 4px; font-weight: 500;">
            Study Notes
        </div>
    </div>

</section>
```

---

## Step 4: Test The Task

### 1. Open the archive page
Navigate to `/dashboard/edu-archive`.

### 2. Verify the cards
You should see the three new cards at the top showing the counts.

### 3. Cross-check with the database
Run this SQL query in your database client and verify the numbers match the UI exactly:
```sql
SELECT type, COUNT(*) FROM edu_resources WHERE status = 'approved' GROUP BY type;
```
