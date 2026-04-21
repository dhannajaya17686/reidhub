# Edu Forum Task: "Needs Code Review" Checkbox

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a `needs_code_review` checkbox to the "Ask Question" form. Save it as a boolean (`TINYINT(1)`) in the `forum_questions` table. If a user checks this box when asking a question, display a purple "👀 Needs Code Review" badge on the question card in the All Questions feed.

This task tests your ability to handle HTML checkboxes properly in PHP, as unchecked checkboxes do not send any data in the `$_POST` array.

---

## Step 1: Add The Column To The Database

**File to update:** `sql/edu-hub/Questions-Table.sql`

Run this SQL command in your database to add the boolean column:

```sql
ALTER TABLE forum_questions
ADD COLUMN needs_code_review TINYINT(1) NOT NULL DEFAULT 0;
```

---

## Step 2: Add The Input To The Ask Question Form

**File to update:** `app/views/User/edu-forum/add-question-view.php`

Find the Ask Question form. Insert this checkbox group below the **Question Description** section or the **Category** section.

```html
<div class="form-group" style="display: flex; align-items: center; gap: 10px; background: #f3e8ff; padding: 15px; border-radius: 8px; border: 1px solid #d8b4fe;">
  <input type="checkbox" id="question-code-review" name="needs_code_review" value="1" style="width: 18px; height: 18px; cursor: pointer;">
  <label for="question-code-review" style="color: #6b21a8; font-weight: bold; cursor: pointer;">
    👀 Request a Code Review for this question
  </label>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createQuestion()` method. 

**Update the `$data` array. Use `isset()` to safely check if the box was ticked:**
```php
$data = [
    'title'             => trim($_POST['title']),
    'category'          => trim($_POST['category']),
    'needs_code_review' => isset($_POST['needs_code_review']) ? 1 : 0, // <-- ADD THIS
    'content'           => trim($_POST['description']),
    'tags'              => trim($_POST['tags'] ?? '')
];
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumModel.php`

Find the `createQuestion($userId, $data)` method.

**1. Update the SQL query to include the new column:**
```php
$sql = "INSERT INTO forum_questions (user_id, title, content, category, needs_code_review, tags) 
        VALUES (:uid, :title, :content, :cat, :needs_code_review, :tags)";
```

**2. Update the execute array to bind the value:**
```php
return $stmt->execute([
    ':uid'               => $userId,
    ':title'             => $data['title'],
    ':content'           => $data['content'],
    ':cat'               => $data['category'],
    ':needs_code_review' => $data['needs_code_review'], // <-- ADD THIS
    ':tags'              => $tags
]);
```

---

## Step 5: Display The Badge On The All Questions Page

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the `foreach ($questions as $q):` loop. Inside the `<div class="question-tags">` section, insert this right before the existing tags loop:

```php
<?php if (isset($q['needs_code_review']) && $q['needs_code_review'] == 1): ?>
    <span class="question-tag" style="background-color: #f3e8ff; color: #7e22ce; border-color: #d8b4fe;">
        👀 Needs Code Review
    </span>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to `/dashboard/forum/add`.
2. Check the "Request a Code Review" box.
3. Submit the question.
4. Verify the purple 👀 badge appears on the feed!
5. Submit another question *without* checking the box to ensure it defaults to 0 and no badge is shown.

---

## Task 2: Adding Business Logic (Extension)

**The Task:** Extend Task 1 by adding a business rule: A user can ONLY request a code review if the selected category is `programming` or `web-development`. If they select `other` or `database` and check the box, reject the submission and show an error.

### Step 1: Add the Logic in the Controller
**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `createQuestion()` method. Right after you build the `$data` array, insert this validation check:

```php
            // TASK 2 BUSINESS LOGIC: Validate Category for Code Reviews
            if ($data['needs_code_review'] === 1) {
                if (!in_array($data['category'], ['programming', 'web-development'])) {
                    // Reject the submission and redirect back with an error
                    header("Location: /dashboard/forum/add?error=invalid_review_category");
                    exit;
                }
            }
```

**Why this fulfills Task 2:** It prevents invalid data from entering the database based on a conditional business rule before the `ForumModel` is ever called.
