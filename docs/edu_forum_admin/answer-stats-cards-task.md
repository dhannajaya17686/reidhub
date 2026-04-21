# Edu Forum Admin Task 3: Answer Moderation Stat Cards

This guide explains how to complete a Stats/Counts (Task 3) code-check specifically for the Edu Forum Admin side:

> **The Task:** Add a set of summary cards right above the "Answer Moderation" table to display the **Total Answers**, **Active Answers**, and **Hidden Answers** currently in the database.

This tests your ability to write an aggregate SQL query, pass the data through the controller, and render it securely in the view.

---

## Files You Need To Change

- `app/models/ForumAdminModel.php`
- `app/controllers/Forum/ForumAdminController.php`
- `app/views/Admin/edu-forum/manage-forum-view.php`

*(Note: No database schema changes are needed for Task 3!)*

---

## Step 1: Add A Stats Method In The Model

**File to update:** `app/models/ForumAdminModel.php`

Find the `getQuestionStats()` method near the top of the file. Right below it, add a new method for Answers:

**Code to insert:**
```php
public function getAnswerStats(): array
{
    // Count all answers and group them by moderation status for dashboard cards.
    $sql = "SELECT
                COUNT(*) AS total_answers,
                SUM(CASE WHEN moderation_status = 'active' THEN 1 ELSE 0 END) AS active_answers,
                SUM(CASE WHEN moderation_status = 'hidden' THEN 1 ELSE 0 END) AS hidden_answers,
                SUM(CASE WHEN moderation_status = 'deleted' THEN 1 ELSE 0 END) AS deleted_answers
            FROM forum_answers";
            
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);

    // Provide zero defaults if the table is empty
    return $stats ?: [
        'total_answers'   => 0,
        'active_answers'  => 0,
        'hidden_answers'  => 0,
        'deleted_answers' => 0
    ];
}
```

---

## Step 2: Call The Method In The Controller

**File to update:** `app/controllers/Forum/ForumAdminController.php`

Find the `showForumAdminDashboard()` method. 

Scroll down to where the `$this->viewApp(...)` array is being built. You will see `'question_stats'` and `'report_stats'` already there.

**Add the new answer stats to the array:**
```php
'question_stats' => $forumAdminModel->getQuestionStats(),
'answer_stats'   => $forumAdminModel->getAnswerStats(), // <-- ADD THIS LINE
'report_stats'   => $forumAdminModel->getReportStats(),
```

---

## Step 3: Add The Cards To The View

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

1. At the very top of the file, where the PHP variables are extracted, add the new variable so it doesn't throw a warning if it's missing:
```php
$questionStats = $question_stats ?? [];
$answerStats = $answer_stats ?? []; // <-- ADD THIS
$reportStats = $report_stats ?? [];
```

2. Scroll down to the Answer Moderation section (`<div class="table-controls" id="answers-section"...>`).

Right beneath the `<p class="section-heading-subtitle">...<p>` and *above* the `<div class="data-table-container">`, insert the new stat cards:

**Code to insert:**
```php
<div class="page-stats page-stats--sub" style="margin-bottom: 20px;">
    <div class="stat-card">
        <div class="stat-number"><?= (int)($answerStats['total_answers'] ?? 0) ?></div>
        <div class="stat-label">Total Answers</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: #059669;"><?= (int)($answerStats['active_answers'] ?? 0) ?></div>
        <div class="stat-label">Active</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: #d97706;"><?= (int)($answerStats['hidden_answers'] ?? 0) ?></div>
        <div class="stat-label">Hidden</div>
    </div>
    <div class="stat-card">
        <div class="stat-number" style="color: #dc2626;"><?= (int)($answerStats['deleted_answers'] ?? 0) ?></div>
        <div class="stat-label">Deleted</div>
    </div>
</div>
```

---

## Step 4: Test The Task

1. Log in as an Admin and navigate to `/dashboard/forum/admin`.
2. Scroll down to the **Answer Moderation** tab.
3. You should now see four stat cards displaying the exact counts of total, active, hidden, and deleted answers!
4. *(Optional)* Hide or delete an answer using the table buttons, and verify the counts update automatically when the page reloads.
