# Edu Forum Admin - Practice Tasks Master List

This folder contains step-by-step guides for completing code-check tasks related to the **Edu Forum Admin** dashboard. 

Below is the complete list of practice tasks and their descriptions, categorized by the exam rubric.

---

## 📝 Task 1: Simple CRUD Operations
*These tasks test your ability to add a new database column, capture it from a form (`$_POST`), and save it via the controller and model.*

### 1. Message Priority Level
**Description:** When an admin sends a direct message or warning to a user from the Forum Admin Dashboard, allow them to set a "Priority Level" (Normal or High). Save this priority level to the database when the message is submitted, and display it with a badge in the Recent Admin Messages history.
**Focus:** `<select>` dropdowns, `INSERT` queries.

### 2. Suspension Internal Note
**Description:** When an admin suspends a user, allow them to add an optional "Internal Note" that is only meant for other admins to see (e.g., "User was warned 3 times previously"). Save this note to the database and display it in the "Active Suspensions" history cards.
**Focus:** `<input type="text">`, `INSERT` queries.

---

## 🔍 Task 2: Filtering & Sorting
*These tasks test your ability to capture URL parameters (`$_GET`), pass them through the controller, and dynamically modify a SQL `WHERE` clause.*

### 1. Filter Questions by Category
**Description:** In the "Question Moderation" section, add a new dropdown filter for **Category**. When an admin selects a specific category (e.g., Programming, Database) and clicks "Filter", the table should dynamically update to only display questions belonging to that category.
**Focus:** `$_GET` handling, dynamic `WHERE` queries with PDO placeholders.

### 2. Filter Answers by Accepted Status
**Description:** Add a new dropdown filter called **"Accepted Solution"** to the global filters form. When an admin selects "Yes" or "No", the **Answer Moderation** table should update to strictly show answers that match that status (is_accepted = 1 or 0).
**Focus:** Form preservation, applying filters to specific tables in a joined query.

---

## 📊 Task 3: Summary Cards & Counts
*These tasks test your ability to write aggregate SQL queries (`COUNT`, `SUM(CASE WHEN...)`) and display the results without needing any database schema changes.*

### 1. Answer Moderation Stat Cards
**Description:** Add a set of summary cards right above the "Answer Moderation" table to display the **Total Answers**, **Active Answers**, **Hidden Answers**, and **Deleted Answers** currently in the database.
**Focus:** `SUM(CASE WHEN...)`, Controller data passing, basic HTML/CSS rendering.

### 2. User Suspension Stat Cards
**Description:** Add a set of summary cards to the top of the "User Discipline" section to display the **Total Active Suspensions**, **Permanent Suspensions**, and **Temporary Suspensions** currently active.
**Focus:** SQL date comparisons (`ends_at > NOW()`), `SUM` aggregation.

---

*Tip: Refer to the individual markdown files in the parent directory for the exact step-by-step code solutions to these tasks!*
