# Edu Forum Task 6: "My Contributions" Count Cards

This guide explains how to complete this Edu Forum code-check task step by step:

> At the top of the All Questions page, if a user is logged in, display two personalized stat cards: "My Questions" (total questions authored by the logged-in user) and "My Answers" (total answers authored by the logged-in user). You will need to write a new method in `ForumModel` that accepts `$_SESSION['user_id']`.

This is a variation of the count-card task that requires you to use `$_SESSION['user_id']` and ensure you only show the UI to logged-in users.

---

## Files You Need To Change

- `app/models/ForumModel.php`
- `app/controllers/Forum/ForumUserController.php`
- `app/views/User/edu-forum/all-questions-view.php`

---

## Step 1: Add A Contributions Method In The Model

**File to update:** `app/models/ForumModel.php`

Add this new method inside the `ForumModel` class (for example, below `getTotalQuestionsCount`):

```php
public function getUserContributionsCount($userId) {
    // We use subqueries to get both counts in a single database call.
    // We also make sure to only count 'active' content (not deleted).
    $sql = "SELECT 
            (SELECT COUNT(*) FROM forum_questions 
             WHERE user_id = :uid AND moderation_status = 'active') AS total_questions,
             
            (SELECT COUNT(*) FROM forum_answers 
             WHERE user_id = :uid AND moderation_status = 'active') AS total_answers";
             
    $stmt = $this->db->prepare($sql);
    $stmt->execute([':uid' => $userId]);
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

---

## Step 2: Call The Method In The Controller

**File to update:** `app/controllers/Forum/ForumUserController.php`

Find the `showAllQuestions()` method.

**1. Fetch the stats if the user is logged in:**
Right below `$totalPages = ceil($totalQuestions / $limit);`, add this block:

```php
$userContributions = null;
if (isset($_SESSION['user_id'])) {
    $userContributions = $forumModel->getUserContributionsCount($_SESSION['user_id']);
}
```

**2. Pass it to the view:**
Inside the `$data` array, add the new variable so the view can access it:

```php
$data = [
    'questions'          => $questions,
    'current_page'       => $page,
    'total_pages'        => $totalPages,
    'current_filter'     => $filter,
    'current_search'     => $search,
    'current_tag'        => $tag,
    'user_contributions' => $userContributions // <-- ADD THIS
];
```

---

## Step 3: Add The Cards To The View

**File to update:** `app/views/User/edu-forum/all-questions-view.php`

Find the section where the tabs end (`</nav>`), which is right above `<section class="questions-section">`.

Insert this block to render the cards. Notice the `if (isset($_SESSION['user_id']))` check so guests do not see an empty card section!

```php
<!-- CODE CHECK: My Contributions Stats -->
<?php if (isset($_SESSION['user_id']) && !empty($user_contributions)): ?>
    <section class="my-contributions-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
        
        <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px; border-left: 4px solid #0ea5e9;">
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">
                <?= (int)($user_contributions['total_questions'] ?? 0) ?>
            </div>
            <div style="color:var(--text-muted); font-size:0.95rem; font-weight: 500;">
                My Questions
            </div>
        </div>

        <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px; border-left: 4px solid #10b981;">
            <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">
                <?= (int)($user_contributions['total_answers'] ?? 0) ?>
            </div>
            <div style="color:var(--text-muted); font-size:0.95rem; font-weight: 500;">
                My Answers
            </div>
        </div>
        
    </section>
<?php endif; ?>
```

---

## Step 4: Test The Task

1. Log into your application as a standard user.
2. Go to `/dashboard/forum/all`.
3. Verify the two cards ("My Questions" and "My Answers") appear at the top of the feed.
4. Check the numbers against your user's actual activity.
5. Open an Incognito window (or log out) and go to the forum feed. Verify that the cards **do not** appear for guest users.

---

## Common Mistakes

### 1. Forgetting to pass the `$userId` to the Model
If you try to read `$_SESSION['user_id']` directly inside the `ForumModel.php` file, it violates MVC architecture. Always pass it from the controller!

### 2. Forgetting the `isset($_SESSION['user_id'])` check
If you don't check for an active session before calling the model method, your app will crash with an "Undefined array key 'user_id'" error when a guest visits the page.

### 3. Missing `moderation_status = 'active'`
If you forget to include the moderation status check in your SQL query, your counts will falsely include deleted or hidden posts.

---

## What To Say In The Code Check

> "I added a method in the model to count active questions and answers for a specific user ID. In the controller, I checked if the user is logged in. If they are, I call the model method passing their session ID and send the counts to the view. Finally, I added a conditional block in the view to display the summary cards only to authenticated users."
