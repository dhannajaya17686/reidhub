# Edu Archive Task: Search Duration (Separate Function Method)

This guide explains how to handle the requirement if the examiner says:
> **Task:** "Allow users to search resources by exact estimated minutes. **You must create a separate new function for this.**"

Instead of modifying the main search bar and existing methods, we will create a dedicated input field, a dedicated Controller check, and a brand new Model method.

---

## Step 1: Create the New Function in the Model

**File to update:** `app/models/EduResourceModel.php`

Scroll down to the **PUBLIC RESOURCE QUERIES** section (around line 140, near `getAllResources`).
Add this brand new method. It does one specific job: finds approved resources that match an exact minute count.

```php
    // --- NEW METHOD: Search resources by exact estimated time ---
    public function getResourcesByEstimatedTime($minutes) {
        $sql = "SELECT r.*, u.first_name, u.last_name
                FROM edu_resources r
                JOIN users u ON r.user_id = u.id
                WHERE r.status = 'approved'
                AND r.estimated_minutes = :minutes
                ORDER BY r.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        // Bind the minutes safely as an integer
        $stmt->bindValue(':minutes', (int)$minutes, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
```

---

## Step 2: Call the New Function in the Controller

**File to update:** `app/controllers/EduArchive/EduController.php`

Find the `index()` method. Look for where `$resources = $model->getAllResources(...)` is called (around line 96). 

We will add an `if` statement. If the user used our new "duration search", we call our new method. Otherwise, we load the normal resources.

**Replace the resource fetching section with this:**

```php
        // CHECK: Did the user search by specific duration?
        if (isset($_GET['duration_search']) && is_numeric($_GET['duration_search'])) {
            
            // Call our brand new separate function!
            $resources = $model->getResourcesByEstimatedTime((int)$_GET['duration_search']);
            
            // Override pagination stats since we are using a specific custom query
            $totalResources = count($resources);
            $totalPages = 1; 
            
        } else {
            // Normal loading behavior
            $totalResources = $model->getAllResourcesCount($type, $subject, $year, $search, $tag);
            $totalPages = max(1, (int)ceil($totalResources / $perPage));
            $page = min($page, $totalPages);
            $offset = ($page - 1) * $perPage;
            
            $resources = $model->getAllResources($type, $subject, $year, $search, $tag, $perPage, $offset);
        }
```

---

## Step 3: Add the Dedicated Input to the View

**File to update:** `app/views/User/edu-archive/archive-view.php`

Find the search bar form `<form method="GET" action="/dashboard/edu-archive" class="archive-search-form">`. 
Right next to it (or below it), create a completely separate small form dedicated to this feature:

```html
<!-- Separate Form for Exact Time Search -->
<form method="GET" action="/dashboard/edu-archive" style="display: flex; gap: 8px; margin-top: 10px;">
    <input type="number" name="duration_search" placeholder="Search exactly by minutes (e.g., 15)" 
           style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; flex: 1;">
    <button type="submit" style="padding: 10px 16px; background: #0f172a; color: white; border-radius: 8px; border: none;">
        Search Time
    </button>
</form>
```

---

### Why do it this way?
If the examiner specifically asks for a "separate new function", they are testing if you know how to write raw SQL `SELECT` queries with `JOIN`s, prepare the PDO statement, bind values, and fetch the results array without relying on the pre-built, complex filter queries already in your model!
