# Edu Forum Task: Answer Confidence Level

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a `confidence_level` dropdown to the Post Answer form. Save it and display it on the answer card.

This tests your ability to handle CRUD operations on the **Answer** entity rather than the Question entity.

---

## Files You Need To Change

- sql/edu-hub/Questions-Table.sql (or directly in MySQL)
- app/views/User/edu-forum/one-question-view.php
- app/controllers/Forum/ForumUserController.php
- app/models/ForumModel.php

---

## Step 1: Add The Column To The Database

Run this SQL command in your database to add the column to the answers table:

```sql
ALTER TABLE forum_answers
ADD COLUMN confidence_level ENUM('high', 'medium', 'low') NOT NULL DEFAULT 'medium';
```

---

## Step 2: Add The Input To The Answer Form

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Scroll to the bottom where `<section class="answer-input-section">` is located. Find the `<form action="/dashboard/forum/answer/create">`.

**Code to insert (above the textarea):**
```html
<div style="margin-bottom: 10px;">
    <label for="confidence-level" style="font-size: 0.85rem; color: var(--text-secondary);">Confidence Level:</label>
    <select name="confidence_level" id="confidence-level" style="padding: 5px; border-radius: 4px; border: 1px solid var(--border-color);">
        <option value="high">High (I am sure)</option>
        <option value="medium" selected>Medium (Pretty sure)</option>
        <option value="low">Low (Just a guess)</option>
    </select>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createAnswer()` method. 

**Current code:**
```php
$questionId = $_POST['question_id'] ?? null;
$content    = trim($_POST['content'] ?? '');
```

**Add the new field:**
```php
$questionId      = $_POST['question_id'] ?? null;
$content         = trim($_POST['content'] ?? '');
$confidenceLevel = $_POST['confidence_level'] ?? 'medium'; // <-- ADD THIS
```

**Update the model call:**
Find:
```php
$saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content);
```
Change to:
```php
$saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content, $confidenceLevel);
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `addAnswer()` method. Update the parameters, SQL, and execute array:

```php
public function addAnswer($userId, $questionId, $content, $confidenceLevel = 'medium') {
    $sql = "INSERT INTO forum_answers (user_id, question_id, content, confidence_level) 
            VALUES (:uid, :qid, :content, :confidence)";
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':uid'        => $userId,
        ':qid'        => $questionId,
        ':content'    => $content,
        ':confidence' => $confidenceLevel
    ]);
}
```

---

## Step 5: Display It On The Answer Card

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the `foreach ($answers as $answer):` loop. Inside the `<div class="answer-author-name">`, add the badge:

```php
<?php if (!empty($answer['confidence_level'])): ?>
    <span style="font-size: 0.7rem; background: #e2e8f0; padding: 2px 6px; border-radius: 4px; margin-left: 8px;">
        <?= ucfirst($answer['confidence_level']) ?> Confidence
    </span>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to a question detail page.
2. Select "High (I am sure)" from your new dropdown and submit an answer.
3. Verify the answer appears with the "High Confidence" badge next to your name.
