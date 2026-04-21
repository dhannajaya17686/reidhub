# Edu Forum Task: Question Type

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a `question_type` dropdown to the Ask Question form so users can categorize the nature of their problem (e.g., "Theory", "Coding Error"). Save it and display it as a badge on the question card.

This is a standard CRUD task that directly involves the `createQuestion()` flow.

---

## Step 1: Add The Column To The Database

**File to update:** `sql/edu-hub/Questions-Table.sql`

Run this SQL command in your database to add the column:

```sql
ALTER TABLE forum_questions
ADD COLUMN question_type VARCHAR(50) NULL DEFAULT 'General';
```

---

## Step 2: Add The Input To The Ask Question Form

**File to update:** `app/views/User/edu-forum/add-question-view.php`

Find the Ask Question form. Insert this after the **Category** section and before the **Question Description**.

```html
<div class="form-group">
  <label for="question-type" class="form-label">Question Type</label>
  <p class="form-description">
    What kind of question is this?
  </p>

  <select name="question_type" id="question-type" class="form-input">
    <option value="General">General Question</option>
    <option value="Theory">Theory / Concept</option>
    <option value="Coding Error">Coding Error / Bug</option>
    <option value="Assignment Help">Assignment Help</option>
    <option value="Career Advice">Career Advice</option>
  </select>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createQuestion()` method. 

**Update the `$data` array to include the new field:**
```php
$data = [
    'title'         => trim($_POST['title']),
    'category'      => trim($_POST['category']),
    'question_type' => trim($_POST['question_type'] ?? 'General'), // <-- ADD THIS
    'content'       => trim($_POST['description']),
    'tags'          => trim($_POST['tags'] ?? '')
];
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `createQuestion($userId, $data)` method.

**1. Update the SQL query:**
```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, question_type, tags) 
        VALUES (:uid, :title, :content, :cat, :qtype, :tags)";
```

**2. Update the execute array:**
```php
return $stmt->execute([
    ':uid'    => $userId,
    ':title'  => $data['title'],
    ':content'=> $data['content'],
    ':cat'    => $data['category'],
    ':qtype'  => $data['question_type'], // <-- ADD THIS
    ':tags'   => $tags
]);
```

---

## Step 5: Display The Field On The All Questions Page

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the `foreach ($questions as $q):` loop. Inside the `<div class="question-tags">` section, insert this right before the existing tags loop:

```php
<?php if (!empty($q['question_type'])): ?>
    <span class="question-tag" style="background-color: #f0f9ff; color: #0369a1; border-color: #e0f2fe;">
        <?php
            // Add an emoji for visual flair based on the type
            $emoji = '❓';
            if ($q['question_type'] === 'Coding Error') $emoji = '🐛';
            if ($q['question_type'] === 'Theory') $emoji = '💡';
        ?>
        <?= $emoji ?> <?= htmlspecialchars($q['question_type']) ?>
    </span>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to `/dashboard/forum/add`.
2. Select "Coding Error" from your new dropdown.
3. Submit the question.
4. Verify the "🐛 Coding Error" badge appears on the feed!
5. Check the `forum_questions` table in your database to confirm the value was saved.

```

<!--
[PROMPT_SUGGESTION]Can you show me how to make a filter based on this new `question_type` field?[/PROMPT_SUGGESTION]
[PROMPT_SUGGESTION]What's the difference between `isset()` and `!empty()` in PHP?[/PROMPT_SUGGESTION]
-->
