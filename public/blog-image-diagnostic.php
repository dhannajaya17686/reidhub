<?php
/**
 * Blog Image Upload Diagnostic Tool
 * Access at: http://localhost:8080/blog-image-diagnostic.php
 * This script helps debug image upload and retrieval issues
 */

// Check if storage directory exists and is writable
$storageDir = __DIR__ . '/storage/blogs';
$storageURL = '/storage/blogs';

echo "<h1>Blog Image Upload Diagnostic</h1>";
echo "<hr>";

// 1. Check if storage directory exists
echo "<h2>1. Storage Directory Check</h2>";
if (is_dir($storageDir)) {
    echo "<p style='color: green;'>✓ Directory exists: $storageDir</p>";
} else {
    echo "<p style='color: red;'>✗ Directory NOT found: $storageDir</p>";
    echo "<p>Attempting to create directory...</p>";
    if (mkdir($storageDir, 0777, true)) {
        echo "<p style='color: green;'>✓ Directory created successfully</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create directory</p>";
    }
}

// 2. Check if directory is writable
echo "<h2>2. Directory Permissions Check</h2>";
if (is_writable($storageDir)) {
    echo "<p style='color: green;'>✓ Directory is writable</p>";
} else {
    echo "<p style='color: red;'>✗ Directory is NOT writable (chmod 777 may be needed)</p>";
}

// 3. List files in storage directory
echo "<h2>3. Files in Storage Directory</h2>";
if (is_dir($storageDir)) {
    $files = array_diff(scandir($storageDir), ['.', '..']);
    if (count($files) > 0) {
        echo "<p>Found " . count($files) . " file(s):</p>";
        echo "<ul>";
        foreach ($files as $file) {
            $filePath = $storageDir . '/' . $file;
            $fileSize = filesize($filePath);
            $fileURL = $storageURL . '/' . $file;
            $fileSizeMB = number_format($fileSize / 1024 / 1024, 2);
            
            echo "<li>";
            echo "<strong>$file</strong> ($fileSizeMB MB)<br>";
            echo "Path: <code>$fileURL</code><br>";
            echo "Full: <code>$filePath</code><br>";
            
            // Try to display the image
            echo "Preview: <img src='$fileURL' alt='$file' style='max-width: 200px; border: 1px solid #ccc; margin: 10px 0;' onerror=\"this.style.color='red'; this.innerHTML='Image failed to load'\" /><br>";
            echo "</li><br>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>No files found in storage directory (images may not have been uploaded yet)</p>";
    }
} else {
    echo "<p style='color: red;'>Cannot list directory - directory does not exist</p>";
}

// 4. Check database connection
echo "<h2>4. Database Check</h2>";
try {
    // Try to include the database config
    $dbConfig = include(__DIR__ . '/../app/config/config.php');
    if (is_array($dbConfig) && isset($dbConfig['db'])) {
        echo "<p style='color: green;'>✓ Database configuration found</p>";
        
        // Try to connect
        try {
            $db = new PDO(
                "mysql:host=" . $dbConfig['db']['host'] . ";dbname=" . $dbConfig['db']['database'],
                $dbConfig['db']['username'],
                $dbConfig['db']['password']
            );
            echo "<p style='color: green;'>✓ Database connection successful</p>";
            
            // Check if blogs table exists
            $result = $db->query("SHOW TABLES LIKE 'blogs'");
            if ($result->rowCount() > 0) {
                echo "<p style='color: green;'>✓ Blogs table exists</p>";
                
                // Get table structure
                $columns = $db->query("DESCRIBE blogs")->fetchAll(PDO::FETCH_ASSOC);
                echo "<h3>Table Structure:</h3>";
                echo "<table border='1' cellpadding='5'>";
                echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
                foreach ($columns as $col) {
                    echo "<tr>";
                    echo "<td>{$col['Field']}</td>";
                    echo "<td>{$col['Type']}</td>";
                    echo "<td>{$col['Null']}</td>";
                    echo "<td>{$col['Key']}</td>";
                    echo "<td>{$col['Default']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
                
                // Count blogs and show image_path values
                $blogCount = $db->query("SELECT COUNT(*) as count FROM blogs")->fetch(PDO::FETCH_ASSOC)['count'];
                echo "<h3>Blog Count: $blogCount</h3>";
                
                if ($blogCount > 0) {
                    echo "<h3>Image Paths in Database:</h3>";
                    $blogs = $db->query("SELECT id, title, image_path, created_at FROM blogs ORDER BY created_at DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
                    echo "<table border='1' cellpadding='5'>";
                    echo "<tr><th>ID</th><th>Title</th><th>Image Path</th><th>Created</th><th>Status</th></tr>";
                    foreach ($blogs as $blog) {
                        $haspath = $blog['image_path'] ? '✓' : '(none)';
                        $statusColor = $blog['image_path'] ? 'green' : 'orange';
                        echo "<tr>";
                        echo "<td>{$blog['id']}</td>";
                        echo "<td>" . substr($blog['title'], 0, 30) . "</td>";
                        echo "<td><code>{$blog['image_path']}</code></td>";
                        echo "<td>{$blog['created_at']}</td>";
                        echo "<td style='color: $statusColor; font-weight: bold;'>$haspath</td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
            } else {
                echo "<p style='color: red;'>✗ Blogs table does NOT exist</p>";
                echo "<p>Please run: <code>CREATE TABLE blogs ...</code> from create-blogs-table.sql</p>";
            }
        } catch (PDOException $e) {
            echo "<p style='color: red;'>✗ Database connection failed:</p>";
            echo "<p><code>" . htmlspecialchars($e->getMessage()) . "</code></p>";
        }
    } else {
        echo "<p style='color: red;'>Database configuration not found</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Error loading diagnostic: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 5. Test file permissions
echo "<h2>5. Test Upload</h2>";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    echo "<h3>Upload Test Result:</h3>";
    $file = $_FILES['test_image'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'test_' . time() . '.' . $extension;
        $uploadPath = $storageDir . '/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
            $testURL = $storageURL . '/' . $filename;
            echo "<p style='color: green;'>✓ Test image uploaded successfully!</p>";
            echo "<p>File: <code>$testURL</code></p>";
            echo "<p>Preview:<br><img src='$testURL' style='max-width: 300px; border: 1px solid #ccc;' /></p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to move uploaded file</p>";
        }
    } else {
        $errors = [
            UPLOAD_ERR_INI_SIZE => 'File too large (php.ini limit)',
            UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
            UPLOAD_ERR_PARTIAL => 'File upload incomplete',
            UPLOAD_ERR_NO_FILE => 'No file selected',
            UPLOAD_ERR_NO_TMP_DIR => 'No temp directory',
            UPLOAD_ERR_CANT_WRITE => 'Cannot write to disk',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by extension'
        ];
        $errorMsg = $errors[$file['error']] ?? 'Unknown error';
        echo "<p style='color: red;'>✗ Upload failed: $errorMsg</p>";
    }
} else {
    echo "<form method='POST' enctype='multipart/form-data'>";
    echo "<input type='file' name='test_image' accept='image/*' required>";
    echo "<button type='submit'>Test Upload</button>";
    echo "</form>";
}

?>
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
code {
    background: #f0f0f0;
    padding: 2px 6px;
    border-radius: 3px;
}
form {
    background: white;
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
}
input, button {
    padding: 8px 12px;
    margin: 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
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
