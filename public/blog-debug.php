<?php
/**
 * Blog Creation Debug Tool
 * Access at: http://localhost:8080/blog-debug.php
 */

session_start();

// Get database config
$dbConfig = include(__DIR__ . '/../app/config/config.php');

echo "<h1>Blog Creation Debug</h1>";
echo "<hr>";

// 1. Check PHP logs
echo "<h2>1. Recent PHP Error Logs (Last 20 lines)</h2>";
$errorLogPath = __DIR__ . '/../storage/logs/app_log.log';
if (file_exists($errorLogPath)) {
    $lines = file($errorLogPath);
    $recentLines = array_slice($lines, -20);
    echo "<pre style='background: #f5f5f5; padding: 10px; max-height: 400px; overflow-y: auto;'>";
    foreach ($recentLines as $line) {
        // Highlight important lines
        if (strpos($line, 'Blog') !== false || strpos($line, 'Error') !== false || strpos($line, 'Exception') !== false) {
            echo "<span style='color: red; font-weight: bold;'>" . htmlspecialchars($line) . "</span>";
        } else {
            echo htmlspecialchars($line);
        }
    }
    echo "</pre>";
} else {
    echo "<p>Log file not found at: $errorLogPath</p>";
}

// 2. Check database
echo "<h2>2. Database Status</h2>";
try {
    $db = new PDO(
        "mysql:host=" . $dbConfig['db']['host'] . ";dbname=" . $dbConfig['db']['database'],
        $dbConfig['db']['username'],
        $dbConfig['db']['password']
    );
    echo "<p style='color: green;'>✓ Database connected</p>";
    
    // Check blogs table
    $result = $db->query("SHOW TABLES LIKE 'blogs'");
    if ($result->rowCount() > 0) {
        echo "<p style='color: green;'>✓ Blogs table exists</p>";
        
        // Count blogs
        $count = $db->query("SELECT COUNT(*) as count FROM blogs")->fetch(PDO::FETCH_ASSOC)['count'];
        echo "<p>Total blogs in database: <strong>$count</strong></p>";
        
        // Show last 5 blogs
        if ($count > 0) {
            echo "<h3>Last 5 Blogs Created:</h3>";
            $blogs = $db->query("SELECT id, author_id, title, created_at FROM blogs ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
            echo "<table border='1' cellpadding='10'>";
            echo "<tr><th>ID</th><th>Author ID</th><th>Title</th><th>Created</th></tr>";
            foreach ($blogs as $blog) {
                echo "<tr>";
                echo "<td>{$blog['id']}</td>";
                echo "<td>{$blog['author_id']}</td>";
                echo "<td>" . substr($blog['title'], 0, 50) . "...</td>";
                echo "<td>{$blog['created_at']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Blogs table does not exist</p>";
        echo "<p>Run the SQL file: <code>sql/community/create-blogs-table.sql</code></p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 3. Session test
echo "<h2>3. Session Test</h2>";
$_SESSION['test_message'] = 'Test session message set at ' . date('Y-m-d H:i:s');
echo "<p>✓ Session test message set</p>";
echo "<p>Session ID: " . session_id() . "</p>";
if (isset($_SESSION['test_message'])) {
    echo "<p style='color: green;'>✓ Can retrieve session message: " . htmlspecialchars($_SESSION['test_message']) . "</p>";
} else {
    echo "<p style='color: red;'>✗ Cannot retrieve session message</p>";
}

// 4. Test form submission
echo "<h2>4. Test Form Submission</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_submit'])) {
    echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px;'>";
    echo "<h3>✓ Form submission received!</h3>";
    echo "<p><strong>POST Data:</strong></p>";
    echo "<pre>";
    foreach ($_POST as $key => $value) {
        if ($key !== 'test_submit') {
            echo "$key: " . substr($value, 0, 100) . (strlen($value) > 100 ? '...' : '') . "\n";
        }
    }
    echo "</pre>";
    echo "</div>";
}

?>

<h2>5. Quick Test Form</h2>
<form method="POST">
    <input type="text" name="test_title" placeholder="Test Title" required>
    <textarea name="test_content" placeholder="Test Content" required></textarea>
    <button type="submit" name="test_submit">Test Form Submission</button>
</form>

<h2>6. Test Real Blog Creation</h2>
<p>To test actual blog creation with all the logging, do this:</p>
<ol>
    <li>Go to <code>/dashboard/community/blogs/create</code></li>
    <li>Fill in all fields</li>
    <li>Open DevTools: Press <strong>F12</strong></li>
    <li>Go to <strong>Console</strong> tab</li>
    <li>Click <strong>Submit</strong></li>
    <li>You should see blue ✓ or red ❌ messages in the console</li>
</ol>

<h2>7. Check Blog Form JavaScript</h2>
<p>The form includes console logging. After submitting the form, check your browser console (F12) for messages like:</p>
<pre style='background: #fff3e0; padding: 10px;'>
🔵 Form submission started
✓ Form validation passed
📤 Submitting form data...
</pre>

<h2>8. Check PHP Error Logs</h2>
<p>After submitting, PHP writes detailed logs with patterns like:</p>
<pre style='background: #fff3e0; padding: 10px;'>
=== Blog Creation Started ===
User ID: 1
Title: My Blog Title
✓ Form validation passes
✓ Image successfully uploaded: /storage/blogs/...
📝 Blog Model: Starting createBlog
✓✓ Blog created successfully with ID: 123
✓✓✓ Blog created successfully! ID: 123
</pre>

<h2>9. Refresh This Page To See Latest Logs</h2>
<button onclick="window.location.reload();">Refresh</button>

<style>
body {
    font-family: Arial, sans-serif;
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    background: #f5f5f5;
}
h1, h2, h3 {
    color: #333;
}
table { 
    background: white;
    border-collapse: collapse;
}
td, th {
    padding: 10px;
    text-align: left;
}
form {
    background: white;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
}
input, textarea, button {
    padding: 10px;
    margin: 5px 5px 5px 0;
    border: 1px solid #ccc;
    border-radius: 3px;
    font-size: 14px;
}
button {
    background: #007bff;
    color: white;
    cursor: pointer;
}
button:hover {
    background: #0056b3;
}
</style>
