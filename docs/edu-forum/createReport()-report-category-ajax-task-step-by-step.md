# Edu Forum Task: Report Category (AJAX)

This guide explains how to complete this Edu Forum code-check task step by step:

> Modify the AJAX Report system. Ask the user to provide a "Report Category" (e.g., Spam, Harassment) when they click Report. Send that category in the JSON fetch payload and save it to the database.

This tests your ability to read and modify raw JSON payloads sent via JavaScript `fetch()`, which is a very common interview/code-check topic.

---

## Files You Need To Change

- sql/edu-hub/forum-admin-moderation.sql (or directly in MySQL)
- app/views/User/edu-forum/one-question-view.php (JavaScript section)
- app/controllers/Forum/ForumUserController.php
- app/models/ForumModel.php

---

## Step 1: Add The Column To The Database

Run this SQL command to alter the `forum_reports` table:

```sql
ALTER TABLE forum_reports
ADD COLUMN category VARCHAR(50) NULL DEFAULT 'Other';
```
*(Note: `forum_reports` is assumed to exist from your base SQL. If you don't have it locally yet, create it first).*

---

## Step 2: Update the JavaScript Fetch Payload

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Scroll to the bottom to find the `ReportSystem` class inside the `<script>` tag. Find the `handleReport(event)` method.

**Current Code:**
```javascript
const reasonInput = prompt(
    'Please enter the reason for this report (minimum 5 characters):\n\nExample: Spam, abusive language, wrong information'
);
if (reasonInput === null) return;
```

**Modify it to ask for a category first, and update the body payload:**
```javascript
// 1. Add a prompt for the category
const categoryInput = prompt('Enter Report Category (e.g., Spam, Harassment, Inaccurate):', 'Spam');
if (categoryInput === null) return; // User cancelled

const reasonInput = prompt(
    'Please enter the detailed reason for this report (minimum 5 characters):'
);
if (reasonInput === null) return;

const reason = reasonInput.trim();
if (reason.length < 5) {
    alert('Please provide a clearer reason (at least 5 characters).');
    return;
}
```

**Update the `fetch` body:**
Find this line inside the `try` block:
```javascript
body: JSON.stringify({ type: type, id: id, reason: reason })
```
Change to:
```javascript
body: JSON.stringify({ type: type, id: id, reason: reason, category: categoryInput })
```

---

## Step 3: Read The JSON In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `report()` method. Remember, AJAX requests use `php://input` instead of `$_POST`.

**Current code:**
```php
$input = json_decode(file_get_contents('php://input'), true);
$forumModel = new ForumModel();

if ($forumModel->createReport($_SESSION['user_id'], $input['type'], $input['id'], $input['reason'])) {
```

**Add the extraction of the category:**
```php
$input = json_decode(file_get_contents('php://input'), true);
$forumModel = new ForumModel();

$category = $input['category'] ?? 'Other'; // <-- Extract it here

// Pass the category to the model
if ($forumModel->createReport($_SESSION['user_id'], $input['type'], $input['id'], $input['reason'], $category)) {
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `createReport()` method. Update the parameters, SQL query, and execute array.

```php
public function createReport($userId, $type, $targetId, $reason, $category = 'Other') {
    $sql = "INSERT INTO forum_reports (user_id, target_type, target_id, reason, category) 
            VALUES (:uid, :type, :tid, :reason, :category)";
            
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':uid'      => $userId, 
        ':type'     => $type, 
        ':tid'      => $targetId, 
        ':reason'   => $reason,
        ':category' => $category
    ]);
}
```

---

## Step 5: Test The Task

1. Go to a question detail page.
2. Click the "Report" button on the question or an answer.
3. The browser will first prompt you for a category. Type `Harassment`.
4. Next, it will prompt for the reason. Type `Inappropriate language`.
5. Check your database `forum_reports` table. You should see a new row with both the reason and the new `Harassment` category successfully recorded!
