# Edu Forum Task: "Expected Outcome" Text Area

This guide explains how to complete this Edu Forum code-check task step by step:

> Add an `expected_outcome` text area to the "Ask Question" form right below the description. Save it to the database. On the Question Details page (`one-question-view.php`), if this field is not empty, display it in a distinct box labeled "What I expected to happen:".

This task tests your ability to handle multi-line text input (`<textarea>`) and display it properly on a detailed view page.

---

## Step 1: Add The Column To The Database

**File to update:** `sql/edu-hub/Questions-Table.sql`

Run this SQL command in your database to add the new text column:

```sql
ALTER TABLE forum_questions
ADD COLUMN expected_outcome TEXT NULL;
```

---

## Step 2: Add The Input To The Ask Question Form

**File to update:** `app/views/User/edu-forum/add-question-view.php`

Find the Ask Question form. Scroll to the **Question Description** section (`<div class="form-group"> ... <label ...>Question Description</label>`). Insert the new text area immediately after that `form-group` closes.

```html
<div class="form-group">
  <label for="expected-outcome" class="form-label">Expected Outcome (Optional)</label>
  <p class="form-description">
    What did you expect to happen before you encountered the issue?
  </p>

  <textarea 
    id="expected-outcome" 
    name="expected_outcome" 
    class="form-textarea" 
    rows="4" 
    placeholder="I expected the function to return..."
    style="width: 100%; border: 1px solid #dbe3ef; border-radius: 10px; padding: 10px; background: #f8fafc; font-family: inherit; font-size: 0.95rem; resize: vertical;"
  ></textarea>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createQuestion()` method. 

**Update the `$data` array to include the new field:**
```php
$data = [
    'title'            => trim($_POST['title']),
    'category'         => trim($_POST['category']),
    'expected_outcome' => trim($_POST['expected_outcome'] ?? ''), // <-- ADD THIS
    'content'          => trim($_POST['description']),
    'tags'             => trim($_POST['tags'] ?? '')
];
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `createQuestion($userId, $data)` method.

**1. Update the SQL query to include the new column:**
```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, expected_outcome, tags) 
        VALUES (:uid, :title, :content, :cat, :expected_outcome, :tags)";
```

**2. Update the execute array to bind the value:**
```php
return $stmt->execute([
    ':uid'              => $userId,
    ':title'            => $data['title'],
    ':content'          => $data['content'],
    ':cat'              => $data['category'],
    ':expected_outcome' => $data['expected_outcome'], // <-- ADD THIS
    ':tags'             => $tags
]);
```

---

## Step 5: Display The Field On The Question Details Page

**File to update:** `app/views/User/edu-forum/one-question-view.php`

Find the section where the main question content is displayed:
`<div id="question-content-markdown" data-question-markdown><?= htmlspecialchars($question['content']) ?></div>`

Insert the expected outcome block right beneath it (before the `question-tags` section). Use `nl2br()` so that line breaks in the text area are preserved as `<br>` tags in HTML.

```php
<?php if (!empty($question['expected_outcome'])): ?>
    <div style="margin-top: 25px; padding: 15px; background: #f0fdf4; border-left: 4px solid #16a34a; border-radius: 6px;">
        <h4 style="margin: 0 0 8px 0; color: #166534; font-size: 1rem; font-weight: bold;">
            ✅ What I expected to happen:
        </h4>
        <div style="color: #15803d; font-size: 0.95rem; line-height: 1.5;">
            <?= nl2br(htmlspecialchars($question['expected_outcome'])) ?>
        </div>
    </div>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to `/dashboard/forum/add`.
2. Fill out a normal question, but also type a few lines into the new "Expected Outcome" text area.
3. Submit the question.
4. Click on your newly created question to view its details.
5. Verify the green "✅ What I expected to happen:" box appears beneath the main text!
6. View another question where you *didn't* fill out this field to make sure the box stays hidden.
