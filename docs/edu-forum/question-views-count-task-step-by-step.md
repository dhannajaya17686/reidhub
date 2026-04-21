# Edu Forum Task 1: Question Views Counter

This guide explains how to complete a standard "Task 1" counting code-check step by step.

> **Objective:** Track and display how many times a question has been viewed. When a user opens a question's details page, increment its view count in the database by 1. Display this total view count on the question details page.

*Note: The `views` column (`INT DEFAULT 0`) already exists in the standard `forum_questions` table schema for this project, so no database alteration is needed!*

This task focuses on:
1. Writing an `UPDATE` SQL query to increment an integer column.
2. Triggering the model method in the Controller when a page is loaded.
3. Displaying the integer on the frontend View.

---

## Files You Need To Change

- `app/models/ForumModel.php`
- `app/controllers/Forum/ForumUserController.php`
- `app/views/User/edu-forum/one-question-view.php`

---

## Step 1: Add The Increment Method To The Model

### File to update
`app/models/ForumModel.php`

### What to add
Add this new method inside the `ForumModel` class (for example, right below the `getQuestionById` method):

```php
    // --- NEW: Increment the view count for a specific question ---
    public function incrementQuestionViews($id) {
        // The "views = views + 1" syntax safely increments the integer directly in MySQL
        $sql = "UPDATE forum_questions SET views = views + 1 WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
```

### Why this step matters
Instead of fetching the current views, adding 1 in PHP, and saving it back (which can cause race conditions if two people click at the exact same millisecond), `views = views + 1` delegates the math to the database, ensuring perfect accuracy.

---

## Step 2: Trigger The Count In The Controller

### File to update
`app/controllers/Forum/ForumUserController.php`

### Method to update
`showQuestion()`

### 1. Call the increment method
Find the `showQuestion()` method. We need to increment the views *before* we fetch the question details, so the user sees the newly updated number.

Update the top of the method to look like this:

```php
    public function showQuestion()
    {
        $id = isset($_GET['id']) ? $_GET['id'] : 0;
       
        $forumModel = new ForumModel();
        
        // <-- ADD THIS: Increment the view count every time the page loads
        if ($id > 0) {
            $forumModel->incrementQuestionViews($id);
        }

        // Fetch the specific question details
        $question = $forumModel->getQuestionById($id);
```

---

## Step 3: Display The Count On The View

### File to update
`app/views/User/edu-forum/one-question-view.php`

### Where to insert it
Find the `author-details` div near the top of the file (around line 192), which currently displays the author's name and the date badge.

### Code to insert
Add a new span right next to the date badge to show the views:

```php
            <div class="author-details">
                <h2 class="author-name"><?= htmlspecialchars($question['first_name'] . ' ' . $question['last_name']) ?></h2>
                <span class="author-badge"><?= date('M j, Y', strtotime($question['created_at'])) ?></span>
                <!-- ADD THIS: View Count Badge -->
                <span class="author-badge" style="margin-left: 10px; background: #f1f5f9; color: #475569;">👁️ <?= (int)($question['views'] ?? 0) ?> Views</span>
            </div>
```

---

## Step 4: Test The Task

1. Navigate to `/dashboard/forum/all`.
2. Click on any question to view its details.
3. Look next to the author's name at the top. It should say "👁️ 1 Views" (or higher).
4. Refresh the page. The count should immediately jump to "2 Views".
