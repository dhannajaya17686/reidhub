# Edu Forum Admin Task 1: Add Private Admin Notes to User Suspensions

This guide explains how to complete a full-stack CRUD feature specifically for the Edu Forum Admin side:

> **The Task:** Allow administrators to attach private, internal memos to user suspensions. These notes are stored in the database for other moderators to reference but are never exposed to the suspended user.

This tests your ability to flow data completely from the Database schema up through the Model, Controller, and into the View.

---

## Files You Need To Change

- `sql/edu-hub/forum-admin-moderation.sql` (and your local MySQL Database)
- `app/views/Admin/edu-forum/manage-forum-view.php`
- `app/controllers/Forum/ForumAdminController.php`
- `app/models/ForumAdminModel.php`

---

## Step 1: Update the Database Schema

**File to update:** `sql/edu-hub/forum-admin-moderation.sql`

First, add the new column to your SQL script so your schema documentation is up-to-date. Find the `CREATE TABLE IF NOT EXISTS forum_user_suspensions` block and add `private_note`:

```sql
    -- reason explains why the user was suspended.
    reason VARCHAR(255) NOT NULL,
    -- private_note is an internal memo for other admins (not visible to users).
    private_note TEXT NULL,
```

*(Important: You must also run this command directly in your local MySQL client/phpMyAdmin to apply the change to your running database:)*
```sql
ALTER TABLE forum_user_suspensions ADD COLUMN private_note TEXT NULL AFTER reason;
```

---

## Step 2: Add the Field & Display to the View

**File to update:** `app/views/Admin/edu-forum/manage-forum-view.php`

**1. The Form:**
Scroll down to the "Suspend User" form (around line 733). Right beneath the `reason` input group, add the new textarea for the private note:

```php
<div class="discipline-field discipline-field--full">
    <label class="filter-label" for="discipline-suspend-private-note">Private Note (Optional)</label>
    <textarea id="discipline-suspend-private-note" class="table-search-input discipline-input discipline-textarea" name="private_note" rows="2" placeholder="Internal memo for other admins. The user will not see this."></textarea>
</div>
```

**2. The Display:**
Scroll down to the "Active Suspensions" history panel (around line 805). Inside the `foreach ($activeSuspensions as $s):` loop, just below where the "Ends" date is displayed, add the logic to display the note:

```php
<?php if (!empty($s['private_note'])): ?>
    <div class="question-meta-line" style="background: #f8fafc; padding: 8px; border-radius: 4px; margin-top: 8px;">
        <span class="question-meta-label" style="color: #0466C8;">Private Note:</span>
        <span><?= nl2br(htmlspecialchars($s['private_note'])) ?></span>
    </div>
<?php endif; ?>
```

---

## Step 3: Update the Controller to Capture the Note

**File to update:** `app/controllers/Forum/ForumAdminController.php`

Find the `suspendUser()` method (around line 230). Extract your new `private_note` from the `$_POST` array and pass it to the model.

**1. Extract the variable:**
```php
$userId = (int)($_POST['user_id'] ?? 0);
$reason = trim($_POST['reason'] ?? '');
$privateNote = trim($_POST['private_note'] ?? ''); // <-- ADD THIS LINE
```

**2. Pass it to the model:**
Scroll down to where `$forumAdminModel->createUserSuspension(...)` is called and add the new `$privateNote` parameter:
```php
$ok = $forumAdminModel->createUserSuspension((int)$admin['id'], $userId, $reason, $privateNote, $endsAt, $isPermanent);
```

---

## Step 4: Update the Model to Save to the Database

**File to update:** `app/models/ForumAdminModel.php`

Find the `createUserSuspension()` method (around line 385). 

**1. Update the signature:**
```php
public function createUserSuspension(int $adminId, int $userId, string $reason, string $privateNote, ?string $endsAt, bool $isPermanent): bool
```

**2. Update the SQL INSERT query:**
```php
$sql = "INSERT INTO forum_user_suspensions (user_id, admin_id, reason, private_note, ends_at, is_permanent, is_active)
        VALUES (:user_id, :admin_id, :reason, :private_note, :ends_at, :is_permanent, 1)";
```

**3. Update the execute array to bind the value:**
```php
return $stmt->execute([
    ':user_id' => $userId,
    ':admin_id' => $adminId,
    ':reason' => $reason,
    ':private_note' => $privateNote ?: null, // <-- ADD THIS LINE
    ':ends_at' => $isPermanent ? null : $endsAt,
    ':is_permanent' => $isPermanent ? 1 : 0
]);
```
