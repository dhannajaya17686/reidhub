# Edu Forum Task: Answer Attachment URL

This guide explains how to complete this Edu Forum code-check task step by step:

> Add an `attachment_url` input field to the "Post Answer" form at the bottom of the question details page. Save it in the `forum_answers` table. Display it as a clickable "📎 View Attachment" link next to the answer's timestamp if the user provided a link.

This tests your ability to handle CRUD operations on the **Answer** entity rather than the Question entity, specifically dealing with URL inputs.

---

## Step 1: Add The Column To The Database

Run this SQL command in your database to add the new column to the answers table:

```sql
ALTER TABLE forum_answers
ADD COLUMN attachment_url VARCHAR(255) NULL;
```

---

## Step 2: Add The Input To The Answer Form

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Scroll to the bottom of the file where the "Post Answer" form is located (`<section class="answer-input-section">`). Find the `<form action="/dashboard/forum/answer/create" ...>`. 

**Code to insert (right above the textarea):**
```html
<div style="margin-bottom: 15px; width: 100%;">
    <label for="attachment-url" style="display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;">Attachment URL (Optional):</label>
    <input type="url" name="attachment_url" id="attachment-url" placeholder="https://..." style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.9rem;">
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
$questionId    = $_POST['question_id'] ?? null;
$content       = trim($_POST['content'] ?? '');
$attachmentUrl = trim($_POST['attachment_url'] ?? ''); // <-- ADD THIS
```

**Update the model call:**
Find:
```php
$saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content);
```
Change to:
```php
$saved = $forumModel->addAnswer($_SESSION['user_id'], $questionId, $content, $attachmentUrl);
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `addAnswer()` method. 

**Update the method signature, SQL query, and execute array:**
```php
public function addAnswer($userId, $questionId, $content, $attachmentUrl = null) { // <-- Add parameter
    $sql = "INSERT INTO forum_answers (user_id, question_id, content, attachment_url) 
            VALUES (:uid, :qid, :content, :attachment_url)"; // <-- Update query
    
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        ':uid'            => $userId,
        ':qid'            => $questionId,
        ':content'        => $content,
        ':attachment_url' => $attachmentUrl // <-- Bind value
    ]);
}
```

---

## Step 5: Display It On The Answer Card

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the `foreach ($answers as $answer):` loop. Inside the `<div class="answer-header">`, find the `<div class="answer-timestamp">` and insert the link right below it.

```php
<?php if (!empty($answer['attachment_url'])): ?>
    <div style="margin-top: 5px;">
        <a href="<?= htmlspecialchars($answer['attachment_url']) ?>" target="_blank" style="font-size: 0.8rem; color: #0284c7; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; background: #f0f9ff; padding: 2px 8px; border-radius: 12px;">
            📎 View Attachment
        </a>
    </div>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to a question detail page.
2. Scroll to the "Post Answer" form.
3. Paste a link into the new "Attachment URL" field and type your answer.
4. Click "Post Answer".
5. Verify the "📎 View Attachment" button appears in your answer header and opens the link!
