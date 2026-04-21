# Edu Forum Task 3: Count Cards

This guide explains how to complete this Edu Forum code-check task step by step:

> Add summary cards at the top of the All Questions page to display total questions, total answers, and unanswered questions.

This is a likely code-check task because it is:

- visible in the UI
- backend-driven
- not too difficult
- similar to the count-card style task mentioned by your senior

This task usually does not require a DB schema change because the needed data already exists in the current tables.

---

## Files You Need To Change

- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

---

## Step 1: Add A Stats Method In The Model

### File to update

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

### What to add

Add this new method inside the `ForumModel` class, for example after `getTotalQuestionsCount()`:

```php
public function getForumStats() {
    $sql = "SELECT
            (SELECT COUNT(*)
             FROM forum_questions
             WHERE moderation_status = 'active') AS total_questions,

            (SELECT COUNT(*)
             FROM forum_answers
             WHERE moderation_status = 'active') AS total_answers,

            (SELECT COUNT(*)
             FROM forum_questions q
             WHERE q.moderation_status = 'active'
             AND (
                 SELECT COUNT(*)
                 FROM forum_answers a
                 WHERE a.question_id = q.id
                 AND a.moderation_status = 'active'
             ) = 0
            ) AS unanswered_questions";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

### Why this step matters

This method calculates all three values needed for the cards:

- total active questions
- total active answers
- total unanswered questions

---

## Step 2: Call The Stats Method In The Controller

### File to update

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

### Method to update

`showAllQuestions()`

### Add this line

After:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
```

add:

```php
$forumStats = $forumModel->getForumStats();
```

### Pass it to the view

Inside the `$data` array add:

```php
'forum_stats' => $forumStats,
```

### Example updated `$data` array

```php
$data = [
    'questions'      => $questions,
    'current_page'   => $page,
    'total_pages'    => $totalPages,
    'current_filter' => $filter,
    'current_search' => $search,
    'current_tag'    => $tag,
    'forum_stats'    => $forumStats
];
```

### Why this step matters

The controller gets the stats from the model and passes them to the view.

---

## Step 3: Add The Cards To The View

### File to update

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

### Where to insert it

Add the card section below the filter tabs and above the questions list.

That means insert it after:

```php
</nav>
```

### Code to insert

```php
<section class="forum-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">
            <?= (int)($forum_stats['total_questions'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Total Questions</div>
    </div>

    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">
            <?= (int)($forum_stats['total_answers'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Total Answers</div>
    </div>

    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:var(--text-color);">
            <?= (int)($forum_stats['unanswered_questions'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Unanswered Questions</div>
    </div>
</section>
```

### Why this step matters

This displays the stats as visible count cards on the page.

---

## Step 4: Test The Task

### Open the forum page

```text
/dashboard/forum/all
```

### Verify the cards appear

You should see:

- Total Questions
- Total Answers
- Unanswered Questions

### Verify the numbers using SQL

#### Total questions

```sql
SELECT COUNT(*) AS total_questions
FROM forum_questions
WHERE moderation_status = 'active';
```

#### Total answers

```sql
SELECT COUNT(*) AS total_answers
FROM forum_answers
WHERE moderation_status = 'active';
```

#### Unanswered questions

```sql
SELECT COUNT(*) AS unanswered_questions
FROM forum_questions q
WHERE q.moderation_status = 'active'
AND (
    SELECT COUNT(*)
    FROM forum_answers a
    WHERE a.question_id = q.id
    AND a.moderation_status = 'active'
) = 0;
```

### Why this test matters

It confirms that the frontend cards match the real database values.

---

## Common Mistakes

### 1. Forgetting to pass stats to the view

Then the page may show undefined variable errors for `$forum_stats`.

### 2. Writing the SQL query incorrectly

Make sure each count is inside the main `SELECT`.

### 3. Forgetting fallback values in the view

Use:

```php
<?= (int)($forum_stats['total_questions'] ?? 0) ?>
```

to avoid errors.

### 4. Placing the cards inside the question loop

The card section must be outside the `foreach ($questions as $q)` loop.

---

## What To Say In The Code Check

You can explain it like this:

> I added a new stats method in the model to calculate total questions, total answers, and unanswered questions. Then I called that method in the controller and passed the result to the view. Finally, I added summary cards at the top of the All Questions page to display those counts.

---
#
#
#

#
#
#
#
#
#
#
#

## Special Mention: Count By Category/Solved vs unsolved

This is another very likely code-check task that follows the same pattern as count cards, but instead of three fixed totals, you show one card per category.

### Possible code-check question

> Add summary cards to show how many questions belong to each category, such as Programming, Web Development, Mobile, Database, Algorithms, and Other.

### Files to change

- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

This task usually does not need a DB change because `category` already exists in `forum_questions`.

---

### Step 1: Add A Category Count Method In The Model

File:

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Add this method:

```php
public function getQuestionCountByCategory() {
    $sql = "SELECT category, COUNT(*) AS total
            FROM forum_questions
            WHERE moderation_status = 'active'
            GROUP BY category
            ORDER BY total DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

Why:

This groups active questions by category and returns the count for each one.

---

### Step 2: Call The Method In The Controller

File:

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

Inside `showAllQuestions()`, after:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
```

add:

```php
$categoryCounts = $forumModel->getQuestionCountByCategory();
```

Then pass it to the `$data` array:

```php
'category_counts' => $categoryCounts,
```

Example:

```php
$data = [
    'questions'       => $questions,
    'current_page'    => $page,
    'total_pages'     => $totalPages,
    'current_filter'  => $filter,
    'current_search'  => $search,
    'current_tag'     => $tag,
    'category_counts' => $categoryCounts
];
```

Why:

The controller must fetch the category counts and send them to the view.

---

### Step 3: Add Category Cards In The View

File:

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Add this section below the tabs and above the question list, after:

```php
</nav>
```

Insert:

```php
<section class="category-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <?php if (!empty($category_counts)): ?>
        <?php foreach ($category_counts as $cat): ?>
            <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
                <div style="font-size:1.5rem; font-weight:700; color:var(--text-color);">
                    <?= (int)$cat['total'] ?>
                </div>
                <div style="color:var(--text-muted); font-size:0.95rem;">
                    <?= htmlspecialchars(ucwords(str_replace('-', ' ', $cat['category']))) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
```

Why:

This creates one card per category and formats values like `web-development` as `Web Development`.

---

### Step 4: Test It

Open:

```text
/dashboard/forum/all
```

Then verify the values with:

```sql
SELECT category, COUNT(*) AS total
FROM forum_questions
WHERE moderation_status = 'active'
GROUP BY category
ORDER BY total DESC;
```

The UI cards should match the database result.

---

### Common Mistakes

1. Forgetting `GROUP BY category`
2. Forgetting to pass `category_counts` to the view
3. Putting the cards inside the question loop
4. Not formatting hyphenated category names for display

---

### What To Say In The Code Check

You can explain it like this:

> I added a new method in the model to count active questions by category using `GROUP BY category`. Then I called that method in the controller and passed the result to the view. In the All Questions page, I rendered a summary card for each category showing the total number of questions.

---
#
#
#
#
#
#
#


## Special Mention: Solved Vs Unsolved

This is another likely code-check task that follows the same count-card pattern, but instead of grouping by category, it counts questions based on whether they have an accepted answer.

### Possible code-check question

> Add summary cards to show solved questions and unsolved questions.

A question is:

- solved if it has an accepted active answer
- unsolved if it has no accepted active answer

### Files to change

- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

This task does not require a DB change because accepted-answer support already exists in `forum_answers.is_accepted`.

---

### Step 1: Add A Solved/Unsolved Stats Method In The Model

File:

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Add this method:

```php
public function getSolvedVsUnsolvedStats() {
    $sql = "SELECT
            (SELECT COUNT(*)
             FROM forum_questions q
             WHERE q.moderation_status = 'active'
             AND (
                 SELECT COUNT(*)
                 FROM forum_answers a
                 WHERE a.question_id = q.id
                 AND a.is_accepted = 1
                 AND a.moderation_status = 'active'
             ) > 0
            ) AS solved_questions,

            (SELECT COUNT(*)
             FROM forum_questions q
             WHERE q.moderation_status = 'active'
             AND (
                 SELECT COUNT(*)
                 FROM forum_answers a
                 WHERE a.question_id = q.id
                 AND a.is_accepted = 1
                 AND a.moderation_status = 'active'
             ) = 0
            ) AS unsolved_questions";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

Why:

This counts questions that have at least one accepted active answer and questions that have none.

---

### Step 2: Call The Method In The Controller

File:

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

Inside `showAllQuestions()`, after:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
```

add:

```php
$solvedStats = $forumModel->getSolvedVsUnsolvedStats();
```

Then pass it to the `$data` array:

```php
'solved_stats' => $solvedStats,
```

Example:

```php
$data = [
    'questions'      => $questions,
    'current_page'   => $page,
    'total_pages'    => $totalPages,
    'current_filter' => $filter,
    'current_search' => $search,
    'current_tag'    => $tag,
    'solved_stats'   => $solvedStats
];
```

Why:

The controller fetches the solved/unsolved counts and gives them to the view.

---

### Step 3: Add The Cards In The View

File:

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Add this section below the tabs and above the question list, after:

```php
</nav>
```

Insert:

```php
<section class="solved-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:#166534;">
            <?= (int)($solved_stats['solved_questions'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Solved Questions</div>
    </div>

    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:#b45309;">
            <?= (int)($solved_stats['unsolved_questions'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Unsolved Questions</div>
    </div>
</section>
```

Why:

This displays the solved and unsolved values as clear summary cards.

---

### Step 4: Test It

Open:

```text
/dashboard/forum/all
```

Then verify the values using:

```sql
SELECT COUNT(*) AS solved_questions
FROM forum_questions q
WHERE q.moderation_status = 'active'
AND (
    SELECT COUNT(*)
    FROM forum_answers a
    WHERE a.question_id = q.id
    AND a.is_accepted = 1
    AND a.moderation_status = 'active'
) > 0;
```

```sql
SELECT COUNT(*) AS unsolved_questions
FROM forum_questions q
WHERE q.moderation_status = 'active'
AND (
    SELECT COUNT(*)
    FROM forum_answers a
    WHERE a.question_id = q.id
    AND a.is_accepted = 1
    AND a.moderation_status = 'active'
) = 0;
```

The UI values should match the database values.

---

### Common Mistakes

1. Counting all answers instead of accepted answers
2. Forgetting `a.is_accepted = 1`
3. Forgetting `a.moderation_status = 'active'`
4. Forgetting to pass `solved_stats` to the view
5. Putting the cards inside the question loop

---

### What To Say In The Code Check

You can explain it like this:

> I added a new method in the model to count solved and unsolved questions based on whether a question has an accepted active answer. Then I called that method in the controller and passed the result to the view. Finally, I displayed two summary cards on the All Questions page for solved and unsolved questions.

---
#
#
#
#
#
#

## Special Mention: Urgent Count

This is another likely code-check task that follows the same count-card pattern, but it focuses only on questions marked as urgent.

### Possible code-check question

> Add a summary card to show the total number of urgent questions.

A question is urgent when:

```text
is_urgent = 1
```

### Files to change

- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Important:

This task assumes the `is_urgent` field already exists in `forum_questions`.  
If it does not exist yet, you must add it first.

---

### Step 1: Make Sure The Column Exists

File:

[sql/edu-hub/Questions-Table.sql](C:/Users/User/reidhub/sql/edu-hub/Questions-Table.sql)

If `is_urgent` does not exist, add:

```sql
is_urgent TINYINT(1) NOT NULL DEFAULT 0,
```

Then run:

```sql
ALTER TABLE forum_questions
ADD COLUMN is_urgent TINYINT(1) NOT NULL DEFAULT 0;
```

Why:

Without the field, there is nothing to count.

---

### Step 2: Add An Urgent Count Method In The Model

File:

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Add this method:

```php
public function getUrgentQuestionCount() {
    $sql = "SELECT COUNT(*) AS urgent_questions
            FROM forum_questions
            WHERE moderation_status = 'active'
            AND is_urgent = 1";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

Why:

This counts all active urgent questions.

---

### Step 3: Call The Method In The Controller

File:

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

Inside `showAllQuestions()`, after:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
```

add:

```php
$urgentStats = $forumModel->getUrgentQuestionCount();
```

Then pass it to the `$data` array:

```php
'urgent_stats' => $urgentStats,
```

Example:

```php
$data = [
    'questions'      => $questions,
    'current_page'   => $page,
    'total_pages'    => $totalPages,
    'current_filter' => $filter,
    'current_search' => $search,
    'current_tag'    => $tag,
    'urgent_stats'   => $urgentStats
];
```

Why:

The controller gets the count from the model and gives it to the view.

---

### Step 4: Add The Urgent Card In The View

File:

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Add this section below the tabs and above the question list, after:

```php
</nav>
```

Insert:

```php
<section class="urgent-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:#b91c1c;">
            <?= (int)($urgent_stats['urgent_questions'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Urgent Questions</div>
    </div>
</section>
```

Why:

This displays the urgent count as a visible card.

---

### Step 5: Test It

Open:

```text
/dashboard/forum/all
```

Then verify the value using:

```sql
SELECT COUNT(*) AS urgent_questions
FROM forum_questions
WHERE moderation_status = 'active'
AND is_urgent = 1;
```

The UI value should match the database count.

---

### Common Mistakes

1. `is_urgent` column does not exist
2. Forgetting `moderation_status = 'active'`
3. Forgetting to pass `urgent_stats` to the view
4. Putting the card inside the question loop

---

### What To Say In The Code Check

You can explain it like this:

> I added a new method in the model to count active urgent questions using the `is_urgent` field. Then I called that method in the controller and passed the result to the view. Finally, I displayed a summary card on the All Questions page to show the total number of urgent questions.

---
#
#
#
#
#
#

## Special Mention: Total Comments

This is another likely code-check task that follows the same count-card pattern, but it uses the `forum_comments` table.

### Possible code-check question

> Add a summary card to show the total number of comments in the forum.

### Files to change

- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

No DB change is needed because the comments table already exists.

---

### Step 1: Add A Comment Count Method In The Model

File:

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Add this method:

```php
public function getTotalCommentCount() {
    $sql = "SELECT COUNT(*) AS total_comments
            FROM forum_comments
            WHERE moderation_status = 'active'";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

Why:

This counts all active comments in the forum.

---

### Step 2: Call The Method In The Controller

File:

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

Inside `showAllQuestions()`, after:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
```

add:

```php
$commentStats = $forumModel->getTotalCommentCount();
```

Then pass it to the `$data` array:

```php
'comment_stats' => $commentStats,
```

Example:

```php
$data = [
    'questions'      => $questions,
    'current_page'   => $page,
    'total_pages'    => $totalPages,
    'current_filter' => $filter,
    'current_search' => $search,
    'current_tag'    => $tag,
    'comment_stats'  => $commentStats
];
```

Why:

The controller gets the count from the model and passes it to the view.

---

### Step 3: Add The Total Comments Card In The View

File:

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Add this section below the tabs and above the question list, after:

```php
</nav>
```

Insert:

```php
<section class="comment-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
        <div style="font-size:1.8rem; font-weight:700; color:#1d4ed8;">
            <?= (int)($comment_stats['total_comments'] ?? 0) ?>
        </div>
        <div style="color:var(--text-muted); font-size:0.95rem;">Total Comments</div>
    </div>
</section>
```

Why:

This displays the total comment count as a summary card.

---

### Step 4: Test It

Open:

```text
/dashboard/forum/all
```

Then verify the value using:

```sql
SELECT COUNT(*) AS total_comments
FROM forum_comments
WHERE moderation_status = 'active';
```

The UI value should match the database count.

---

### Common Mistakes

1. Counting all comments without checking `moderation_status = 'active'`
2. Forgetting to pass `comment_stats` to the view
3. Putting the card inside the question loop

---

### What To Say In The Code Check

You can explain it like this:

> I added a new method in the model to count active comments from the `forum_comments` table. Then I called that method in the controller and passed the result to the view. Finally, I added a summary card on the All Questions page to display the total number of comments.

---
#
#
#
#
#
#

## Special Mention: Difficulty Count

This is another likely code-check task that follows the same count-card pattern, but it groups questions by difficulty level.

### Possible code-check question

> Add summary cards to show the number of questions by difficulty level: Beginner, Intermediate, and Advanced.

### Files to change

- [app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)
- [app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)
- [app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

This task assumes the `difficulty` field already exists in `forum_questions`.

---

### Step 1: Add A Difficulty Count Method In The Model

File:

[app/models/ForumModel.php](C:/Users/User/reidhub/app/models/ForumModel.php)

Add this method:

```php
public function getQuestionCountByDifficulty() {
    $sql = "SELECT difficulty, COUNT(*) AS total
            FROM forum_questions
            WHERE moderation_status = 'active'
            GROUP BY difficulty
            ORDER BY total DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
```

Why:

This groups active questions by difficulty and returns the count for each level.

---

### Step 2: Call The Method In The Controller

File:

[app/controllers/Forum/ForumUserController.php](C:/Users/User/reidhub/app/controllers/Forum/ForumUserController.php)

Inside `showAllQuestions()`, after:

```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
```

add:

```php
$difficultyCounts = $forumModel->getQuestionCountByDifficulty();
```

Then pass it to the `$data` array:

```php
'difficulty_counts' => $difficultyCounts,
```

Example:

```php
$data = [
    'questions'          => $questions,
    'current_page'       => $page,
    'total_pages'        => $totalPages,
    'current_filter'     => $filter,
    'current_search'     => $search,
    'current_tag'        => $tag,
    'difficulty_counts'  => $difficultyCounts
];
```

Why:

The controller gets the grouped counts from the model and passes them to the view.

---

### Step 3: Add The Difficulty Cards In The View

File:

[app/views/User/edu-forum/all-questions-view.php](C:/Users/User/reidhub/app/views/User/edu-forum/all-questions-view.php)

Add this section below the tabs and above the question list, after:

```php
</nav>
```

Insert:

```php
<section class="difficulty-stats" style="display:flex; gap:16px; flex-wrap:wrap; margin:24px 0;">
    <?php if (!empty($difficulty_counts)): ?>
        <?php foreach ($difficulty_counts as $diff): ?>
            <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
                <div style="font-size:1.5rem; font-weight:700; color:var(--text-color);">
                    <?= (int)$diff['total'] ?>
                </div>
                <div style="color:var(--text-muted); font-size:0.95rem;">
                    <?= htmlspecialchars(ucfirst($diff['difficulty'])) ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
```

Why:

This creates one card per difficulty level.

---

### Step 4: Test It

Open:

```text
/dashboard/forum/all
```

Then verify the values using:

```sql
SELECT difficulty, COUNT(*) AS total
FROM forum_questions
WHERE moderation_status = 'active'
GROUP BY difficulty
ORDER BY total DESC;
```

The UI values should match the database values.

---

### Common Mistakes

1. `difficulty` field does not exist
2. Forgetting `GROUP BY difficulty`
3. Forgetting to pass `difficulty_counts` to the view
4. Putting the cards inside the question loop

---

### What To Say In The Code Check

You can explain it like this:

> I added a new method in the model to count active questions by difficulty using `GROUP BY difficulty`. Then I called that method in the controller and passed the result to the view. Finally, I displayed a summary card for each difficulty level on the All Questions page.

---

## Quick Checklist

- [ ] Add `getForumStats()` in `ForumModel.php`
- [ ] Call it in `showAllQuestions()`
- [ ] Pass stats to the view using `forum_stats`
- [ ] Add 3 cards in `all-questions-view.php`
- [ ] Test the card values against the database

---
#
#
#

# Edu Forum Task 2: Count Cards -> Filter Logic

This guide explains how to complete **Task 2** step by step, using the count cards you created:

> Extend the Count Cards task by making the cards clickable filters. When a user clicks a specific category card (e.g., "Web Development"), filter the **All Questions** page to show only questions from that category.

This Task 2 assumes **Task 3 (Count Cards)** is already completed.
That means the category cards are already rendering on the page.

---

## What Task 2 Means

In Task 3, you display aggregate totals.
In Task 2, you use those values in **business logic** to filter the main feed.

For this example (Category):
- Task 3: Display a card for each category with its count.
- Task 2: Make the card a clickable link (`?category=web-development`) and filter the SQL query.

---

## Files You Need To Change

- `app/controllers/Forum/ForumUserController.php`
- `app/models/ForumModel.php`
- `app/views/User/edu-forum/all-questions-view.php`

---

## Full Flow

1. User clicks a count card in the view
2. Controller reads the filter from `$_GET`
3. Controller passes it to the model
4. Model adds a SQL `WHERE` condition
5. Filtered questions are returned
6. The selected filter is preserved in tabs and pagination

---

## Step 1: Read Category In The Controller

### File
`app/controllers/Forum/ForumUserController.php`

### Method
`showAllQuestions()`

### Find this code
```php
$filter = $_GET['filter'] ?? 'newest';
$tag    = $_GET['tag'] ?? null;
$search = $_GET['search'] ?? null;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
```

### Insert this line
Add this line **after** `$search` and **before** `$page`:
```php
$category = trim($_GET['category'] ?? '');
```

### Explanation
- `$_GET['category']` reads the selected category when the user clicks a card.
- Example URL: `/dashboard/forum/all?category=web-development`

---

## Step 2: Pass Category To The Model

### File
`app/controllers/Forum/ForumUserController.php`

### Find this code
```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search);
```

### Replace it with
```php
$questions = $forumModel->getAllQuestions($filter, $tag, $search, $limit, $offset, $category ?: null);
$totalQuestions = $forumModel->getTotalQuestionsCount($filter, $tag, $search, $category ?: null);
```

### Explanation
- The controller must send the selected category to the model.
- `$category ?: null` means: if it has a value, send it; if empty, send `null`.

---

## Step 3: Send Category To The View

### File
`app/controllers/Forum/ForumUserController.php`

### Find the `$data` array
```php
$data = [
    'questions'       => $questions,
    'current_page'    => $page,
    'total_pages'     => $totalPages,
    'current_filter'  => $filter,
    'current_search'  => $search,
    'current_tag'     => $tag,
    'category_counts' => $categoryCounts
];
```

### Insert this line inside the array
```php
    'current_category' => $category,
```

### Explanation
- The view needs to know which category is currently selected to highlight the active card.

---

## Step 4: Update The Model Method Signatures

### File
`app/models/ForumModel.php`

### Find these methods
```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0)
// ...
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null)
```

### Replace them with
```php
public function getAllQuestions($filter = 'newest', $tag = null, $search = null, $limit = 10, $offset = 0, $category = null)
// ...
public function getTotalQuestionsCount($filter = 'newest', $tag = null, $search = null, $category = null)
```

---

## Step 5: Add SQL Condition In BOTH Model Methods

### File
`app/models/ForumModel.php`

### Find this area (Inside BOTH `getAllQuestions` and `getTotalQuestionsCount`)
```php
if ($tag) {
    $whereClauses[] = "q.tags LIKE :tag";
    $params[':tag'] = "%$tag%";
}
```

### Insert this block
Add it **after** the tag block:
```php
if ($category) {
    $whereClauses[] = "q.category = :category";
    $params[':category'] = $category;
}
```

### Explanation
- This is the actual business logic. It ensures both the fetched list AND the pagination counts are accurately filtered.

---

## Step 6: Make The Count Cards Clickable In The View

### File
`app/views/User/edu-forum/all-questions-view.php`

### Find the Category Cards HTML (From Task 3)
```php
        <?php foreach ($category_counts as $cat): ?>
            <div class="stat-card" style="background:var(--surface); border:1px solid var(--border-color); border-radius:8px; padding:20px; min-width:180px;">
                <div style="font-size:1.5rem; font-weight:700; color:var(--text-color);">
                    <?= (int)$cat['total'] ?>
                </div>
                <div style="color:var(--text-muted); font-size:0.95rem;">
                    <?= htmlspecialchars(ucwords(str_replace('-', ' ', $cat['category']))) ?>
                </div>
            </div>
        <?php endforeach; ?>
```

### Replace the `<div>` with an `<a>` tag
```php
        <?php foreach ($category_counts as $cat): ?>
            <?php 
                // Check if this card is the currently selected filter
                $isActive = ($current_category === $cat['category']); 
            ?>
            <a href="?category=<?= urlencode($cat['category']) ?>" class="stat-card" style="text-decoration:none; background:var(--surface); border:1px solid <?= $isActive ? '#0466C8' : 'var(--border-color)' ?>; border-radius:8px; padding:20px; min-width:180px; display:block; box-shadow: <?= $isActive ? '0 0 0 2px rgba(4,102,200,0.2)' : 'none' ?>;">
                <div style="font-size:1.5rem; font-weight:700; color: <?= $isActive ? '#0466C8' : 'var(--text-color)' ?>;">
                    <?= (int)$cat['total'] ?>
                </div>
                <div style="color: <?= $isActive ? '#0466C8' : 'var(--text-muted)' ?>; font-size:0.95rem; font-weight: <?= $isActive ? 'bold' : 'normal' ?>;">
                    <?= htmlspecialchars(ucwords(str_replace('-', ' ', $cat['category']))) ?>
                </div>
            </a>
        <?php endforeach; ?>
```

### Add a "Clear Filter" link (Optional but recommended)
Right above the cards loop, add:
```php
    <?php if (!empty($current_category)): ?>
        <div style="width: 100%; margin-bottom: 10px;">
            <a href="/dashboard/forum/all" style="color: #ef4444; font-size: 0.9rem; text-decoration: none; font-weight: bold;">✖ Clear Category Filter</a>
        </div>
    <?php endif; ?>
```

### Explanation
- Changing the `div` to an `a` tag makes the whole card clickable.
- `href="?category=..."` sends the filter to the URL.
- The `$isActive` checks allow us to add a blue border and text color to visually highlight which card is currently active!

---

## Step 7: Preserve Category In Tabs

### File
`app/views/User/edu-forum/all-questions-view.php`

### Find the tab links
```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?>" 
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>
```

### Add this part to each tab
```php
<?= $current_category ? '&category='.urlencode($current_category) : '' ?>
```

### Updated code
```php
<a href="?filter=newest<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_category ? '&category='.urlencode($current_category) : '' ?>" 
   class="tab-link <?= $current_filter === 'newest' ? 'is-active' : '' ?>">Newest</a>

<a href="?filter=trending<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_category ? '&category='.urlencode($current_category) : '' ?>" 
   class="tab-link <?= $current_filter === 'trending' ? 'is-active' : '' ?>">Trending</a>

<a href="?filter=unanswered<?= $current_search ? '&search='.urlencode($current_search) : '' ?><?= $current_tag ? '&tag='.urlencode($current_tag) : '' ?><?= $current_category ? '&category='.urlencode($current_category) : '' ?>" 
   class="tab-link <?= $current_filter === 'unanswered' ? 'is-active' : '' ?>">Unanswered</a>
```

### Explanation
- Without this, the category filter is lost when the user clicks between `Newest` and `Trending`.

---

## Step 8: Preserve Category In Pagination

### File
`app/views/User/edu-forum/all-questions-view.php`

### Find this code at the bottom
```php
$base_link = "?filter=" . urlencode($current_filter);
if ($current_search) $base_link .= "&search=" . urlencode($current_search);
if ($current_tag) $base_link .= "&tag=" . urlencode($current_tag);
```

### Add this line
```php
if ($current_category) $base_link .= "&category=" . urlencode($current_category);
```

### Explanation
- This keeps the filter active when the user clicks "Next Page".

---

## How To Explain This In The Exam

You can say:

> "In Task 2, I extended the Count Cards from Task 3 to act as business logic filters. In the view, I converted the card `div`s into `a` tags passing a `category` URL parameter. In the controller, I read that parameter from `$_GET` and passed it to the model. In the model, I added a conditional SQL `WHERE` clause to filter the questions and update the pagination counts. Finally, I ensured the filter state is preserved across the tabs and pagination links."

---

## Testing Checklist

- [ ] Click the "Web Development" count card.
- [ ] Ensure the URL changes to `?category=web-development`.
- [ ] Ensure the feed filters to only show those questions.
- [ ] Verify the clicked card gets a blue border (active state).
- [ ] Click `Trending` and verify the category filter remains.
- [ ] Click next page and confirm the category filter remains.
- [ ] Click "Clear Category Filter" to reset the view.
