# Edu Archive Task: Search by Estimated Time

This guide explains how to complete a specific search logic task:

> **Task:** Allow users to use the existing search bar to find resources based on their estimated time duration. For example, typing "15" should return videos or notes that take 15 minutes.

---

## The Logic Explained

Normally, a search bar looks for **text** inside the `title`, `description`, or `tags` columns using SQL wildcards (`LIKE '%search%'`).

However, `estimated_minutes` is an **integer** (number) column. If a user types a word like "database", we shouldn't search the minutes column. But if they type a number like "15", we *should* check the minutes column.

To solve this, we use PHP's `is_numeric()` function. If the search term is a number, we dynamically add `OR r.estimated_minutes = :search_num` to the SQL query.

---

## Step 1: Update the Model

We need to update the search logic in **both** the public view and the admin view so the search bar works the same way everywhere.

**File to update:** `app/models/EduResourceModel.php`

### 1. Update `appendPublicResourceFilters()`
Find the search block inside the `appendPublicResourceFilters` method (around line 85) and replace it with this:

```php
        // Search for text inside the title, description, or tags using wildcards.
        if ($search) {
            $sql .= " AND (r.title LIKE :search OR r.description LIKE :search OR r.tags LIKE :search";
            
            // If the user typed a number in the search bar, also check the estimated_minutes column
            if (is_numeric(trim($search))) {
                $sql .= " OR r.estimated_minutes = :search_num";
                $params[':search_num'] = (int)trim($search);
            }
            $sql .= ")";
            $params[':search'] = "%$search%";
        }
```

### 2. Update `appendAdminResourceFilters()`
Find the exact same search block inside the `appendAdminResourceFilters` method (around line 118) and replace it with the same code:

```php
        // Search for keywords across title, description, and tags.
        if ($search) {
            $sql .= " AND (r.title LIKE :search OR r.description LIKE :search OR r.tags LIKE :search";
            
            // If the user typed a number in the search bar, also check the estimated_minutes column
            if (is_numeric(trim($search))) {
                $sql .= " OR r.estimated_minutes = :search_num";
                $params[':search_num'] = (int)trim($search);
            }
            $sql .= ")";
            $params[':search'] = "%$search%";
        }
```

---

## Step 2: Test the Feature
1. Go to the **Upload Resource** page and add a test video with `15` as the Estimated Minutes.
2. Go to the **Edu Archive** public feed.
3. Type `15` into the search bar and press Enter.
4. Your test video should appear in the results because the SQL query automatically detected the number and checked the `estimated_minutes` column!
