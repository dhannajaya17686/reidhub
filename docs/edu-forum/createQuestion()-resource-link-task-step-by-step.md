# Edu Forum Task: Resource Link

This guide explains how to complete this Edu Forum code-check task step by step:

> Add a `resource_link` field to the Ask Question form where users can paste a URL (like a documentation link or GitHub repo). Save it and display it as a clickable link on the question card.

This is a standard CRUD task focusing on URL data.

---

## Step 1: Add The Column To The Database

**File to update:** `sql/edu-hub/Questions-Table.sql`

Run this SQL command in your database:

```sql
ALTER TABLE forum_questions
ADD COLUMN resource_link VARCHAR(255) NULL;
```

---

## Step 2: Add The Input To The Ask Question Form

**File to update:** `app/views/User/edu-forum/add-question-view.php`

Find the Ask Question form. Insert this after the **Category** section.

```html
<div class="form-group">
  <label for="question-resource-link" class="form-label">Reference Link (Optional)</label>
  <p class="form-description">
    Attach a helpful link (e.g., documentation, tutorial, or GitHub).
  </p>

  <input
    type="url"
    id="question-resource-link"
    name="resource_link"
    class="form-input"
    placeholder="https://..."
  >
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
    'resource_link' => trim($_POST['resource_link'] ?? ''), // <-- ADD THIS
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
$sql = "INSERT INTO forum_questions (user_id, title, content, category, resource_link, tags) 
        VALUES (:uid, :title, :content, :cat, :resource_link, :tags)";
```

**2. Update the execute array:**
```php
return $stmt->execute([
    ':uid'           => $userId,
    ':title'         => $data['title'],
    ':content'       => $data['content'],
    ':cat'           => $data['category'],
    ':resource_link' => $data['resource_link'], // <-- ADD THIS
    ':tags'          => $tags
]);
```

---

## Step 5: Display The Link On The All Questions Page

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the `foreach ($questions as $q):` loop. Inside the `<div class="question-tags">` section, insert this right before the existing tags loop:

```php
<?php if (!empty($q['resource_link'])): ?>
    <a href="<?= htmlspecialchars($q['resource_link']) ?>" target="_blank" class="question-tag" style="background-color: #f1f5f9; color: #475569; border: 1px dashed #cbd5e1;">
        🔗 View Reference
    </a>
<?php endif; ?>
```

---

## Step 6: Test The Task
1. Go to `/dashboard/forum/add`.
2. Type a URL like `https://php.net` into the Reference Link field.
3. Submit the question.
4. Verify the "🔗 View Reference" link appears on the feed and opens the URL when clicked!
