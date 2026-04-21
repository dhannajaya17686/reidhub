# Edu Forum Admin Task 1: Message Priority Level

This guide explains how to complete a Simple CRUD (Task 1) code-check specifically for the Edu Forum Admin side:

> **The Task:** When an admin sends a direct message or warning to a user from the Forum Admin Dashboard, allow them to set a "Priority Level" (Normal or High). Save this priority level to the database when the message is submitted, and display it in the Recent Admin Messages history.

This tests your ability to add a new field to an admin moderation form, pass it through the backend, and execute a database `INSERT`.

---

## Files You Need To Change

- Database (MySQL)
- `app/views/Admin/edu-forum/manage-forum-view.php`
- `app/controllers/Forum/ForumAdminController.php`
- `app/models/ForumAdminModel.php`

---

## Step 1: Add The Column To The Database

Run this SQL command in your database to add the new column to the admin messages table.

```sql
ALTER TABLE forum_admin_messages
ADD COLUMN priority_level VARCHAR(20) NOT NULL DEFAULT 'Normal';
```

---

## Step 2: Add The Input To The Admin Dashboard Form

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

Find the "Send Warning/Message" form (search for `data-message-form`). Inside the `<div class="discipline-form-grid">`, add the new Priority Level dropdown right next to the "Type" dropdown.

**Code to insert:**
```html
<div class="discipline-field">
    <label class="filter-label" for="discipline-message-priority">Priority Level</label>
    <select id="discipline-message-priority" class="filter-select discipline-input" name="priority_level">
        <option value="Normal">Normal</option>
        <option value="High">High (Urgent)</option>
    </select>
</div>
```

---

## Step 3: Read The Field In The Controller

**File to update:** `app/controllers/Forum/ForumAdminController.php`

Find the `sendUserMessage()` method. 

**1. Capture the new input from `$_POST`:**
```php
$userId = (int)($_POST['user_id'] ?? 0);
$messageType = trim($_POST['message_type'] ?? 'warning');
$priorityLevel = trim($_POST['priority_level'] ?? 'Normal'); // <-- ADD THIS
$subject = trim($_POST['subject'] ?? '');
$body = trim($_POST['body'] ?? '');
```

**2. Update the Model method call to pass the new variable:**
Find:
```php
$ok = $forumAdminModel->sendAdminMessage((int)$admin['id'], $userId, $messageType, $subject, $body);
```
Change to:
```php
$ok = $forumAdminModel->sendAdminMessage((int)$admin['id'], $userId, $messageType, $priorityLevel, $subject, $body);
```

---

## Step 4: Save The Field In The Model

**File to update:** `app/models/ForumAdminModel.php`

Find the `sendAdminMessage()` method.

**1. Update the method signature to accept the new parameter:**
```php
public function sendAdminMessage(int $adminId, int $userId, string $type, string $priorityLevel, string $subject, string $body): bool
```

**2. Update the SQL query:**
```php
$sql = "INSERT INTO forum_admin_messages (user_id, admin_id, message_type, priority_level, subject, body)
        VALUES (:user_id, :admin_id, :message_type, :priority_level, :subject, :body)";
```

**3. Update the execute array:**
```php
return $stmt->execute([
    ':user_id'        => $userId,
    ':admin_id'       => $adminId,
    ':message_type'   => $type,
    ':priority_level' => $priorityLevel, // <-- ADD THIS
    ':subject'        => $subject,
    ':body'           => $body
]);
```

---

## Step 5: Display It On The Recent Messages History

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

Scroll down to the "Recent Admin Messages" section (`<h3 class="text-primary">Recent Admin Messages</h3>`).
Find the `foreach ($recentAdminMessages as $m):` loop.

We need to fetch this column, so first, ensure it's in the `getRecentAdminMessages()` query in your **ForumAdminModel**:
```php
// Ensure m.priority_level is selected in getRecentAdminMessages() inside ForumAdminModel.php
$sql = "SELECT m.id, m.user_id, m.message_type, m.priority_level, m.subject, m.body, m.created_at,
               u.first_name, u.last_name ...";
```

Then, in the View, find the `div` containing the subject line: `<div class="question-meta-line"><span class="question-meta-label">Subject:</span>...</div>` and add the priority badge right below it:

```php
<div class="question-meta-line">
    <span class="question-meta-label">Priority:</span>
    <?php if (($m['priority_level'] ?? 'Normal') === 'High'): ?>
        <span style="color: #dc2626; font-weight: bold;">🚨 High</span>
    <?php else: ?>
        <span style="color: #64748b;">Normal</span>
    <?php endif; ?>
</div>
```

---

## Step 6: Test The Task
1. Log in as an Admin and navigate to `/dashboard/forum/admin`.
2. Scroll down to the **User Discipline** section.
3. Under "Send Warning/Message", enter a valid User ID, select "High (Urgent)" from your new Priority Level dropdown, and fill out the subject and body.
4. Click "Send Message".
5. Look at the "Recent Admin Messages" panel next to the form and verify your newly sent message displays the "🚨 High" priority badge!
