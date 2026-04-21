# Edu Forum Admin Task 3: User Suspension Stat Cards

This guide explains how to complete a Stats/Counts (Task 3) code-check specifically for the Edu Forum Admin side:

> **The Task:** Add a set of summary cards to the top of the "User Discipline" section to display the **Total Active Suspensions**, **Permanent Suspensions**, and **Temporary Suspensions** currently active in the database.

This tests your ability to write an aggregate SQL query with conditional counts (`SUM(CASE WHEN...)`), pass the data through the controller, and render it in the view.

---

## Files You Need To Change

- `app/models/ForumAdminModel.php`
- `app/controllers/Forum/ForumAdminController.php`
- `app/views/Admin/edu-forum/manage-forum-view.php`

*(Note: No database schema changes are needed for Task 3!)*

---

## Step 1: Add A Stats Method In The Model

**File to update:** `app/models/ForumAdminModel.php`

Find the `getReportStats()` method near the top of the file. Right below it, add a new method for Suspension Stats:

**Code to insert:**
```php
public function getSuspensionStats(): array
{
    // Count all currently active suspensions and categorize them by type.
    $sql = "SELECT
                COUNT(*) AS total_active,
                SUM(CASE WHEN is_permanent = 1 THEN 1 ELSE 0 END) AS permanent_count,
                SUM(CASE WHEN is_permanent = 0 THEN 1 ELSE 0 END) AS temporary_count
            FROM forum_user_suspensions
            WHERE is_active = 1 
              AND (is_permanent = 1 OR ends_at IS NULL OR ends_at > NOW())";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Provide zero defaults if the table has no active suspensions
    return $stats ?: [
        'total_active'    => 0,
        'permanent_count' => 0,
        'temporary_count' => 0
    ];
}
```

---

## Step 2: Call The Method In The Controller

**File to update:** `app/controllers/Forum/ForumAdminController.php`

Find the `showForumAdminDashboard()` method. 

Scroll down to where the `$this->viewApp(...)` array is being built. You will see `'active_suspensions'` and `'recent_admin_messages'` near the bottom of the array.

**Add the new suspension stats to the array:**
```php
'reports' => $forumAdminModel->getPendingReports(),
'suspension_stats' => $forumAdminModel->getSuspensionStats(), // <-- ADD THIS LINE
'active_suspensions' => $forumAdminModel->getActiveSuspensions(),
```

---

## Step 3: Add The Cards To The View

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

1. At the very top of the file, where the PHP variables are extracted, add the new variable so it doesn't throw a warning if it's missing (around line 52):
```php
$reports = $reports ?? [];
$suspensionStats = $suspension_stats ?? []; // <-- ADD THIS
$activeSuspensions = $active_suspensions ?? [];
```

2. Scroll down to the User Discipline section (`<div class="table-controls" id="discipline-section"...>`).

Right beneath the `<p class="section-heading-subtitle">...<p>` and *above* the `<div class="discipline-grid">`, insert the new stat cards:

**Code to insert:**
```php
<div class="page-stats page-stats--sub" style="margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-number"><?= (int)($suspensionStats['total_active'] ?? 0) ?></div>
        <div class="stat-label">Total Active Suspensions</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: #dc2626;"><?= (int)($suspensionStats['permanent_count'] ?? 0) ?></div>
        <div class="stat-label">Permanent</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: #d97706;"><?= (int)($suspensionStats['temporary_count'] ?? 0) ?></div>
        <div class="stat-label">Temporary</div>
    </div>
</div>
```

---

## Step 4: Test The Task

1. Log in as an Admin and navigate to `/dashboard/forum/admin`.
2. Scroll down to the **User Discipline** tab.
3. You should now see three stat cards displaying the exact counts of active, permanent, and temporary suspensions!
4. *(Optional)* Apply a new temporary suspension to a dummy user using the form, and verify the "Temporary" and "Total" counts increment by 1 when the page reloads.
5. *(Optional)* Click "Lift Suspension" on an active record and verify the counts decrease.
