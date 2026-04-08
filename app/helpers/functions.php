<?php

/**
 * Get standardized storage path for files
 * 
 * @param string $category Category of file (e.g., 'marketplace', 'clubs', 'orders')
 * @param string $filename The filename to store
 * @param string|null $subdir Optional subdirectory (e.g., seller_id for marketplace)
 * @return string The standardized storage path
 */
function getStoragePath($category, $filename, $subdir = null)
{
    $basePath = '/storage/filestore';
    $path = $basePath . '/' . $category;
    
    if ($subdir !== null) {
        $path .= '/' . $subdir;
    }
    
    $path .= '/' . $filename;
    
    return $path;
}

/**
 * Get absolute filesystem path for storage directory
 * 
 * @param string $category Category of file (e.g., 'marketplace', 'clubs', 'orders')
 * @param string|null $subdir Optional subdirectory (e.g., seller_id for marketplace)
 * @return string The absolute filesystem path
 */
function getStorageDirectory($category, $subdir = null)
{
    $basePath = $_SERVER['DOCUMENT_ROOT'] . '/../storage/filestore';
    $path = $basePath . '/' . $category;
    
    if ($subdir !== null) {
        $path .= '/' . $subdir;
    }
    
    return $path;
}

/**
 * Ensure storage directory exists
 * 
 * @param string $category Category of file
 * @param string|null $subdir Optional subdirectory
 * @param int $mode Directory permissions
 * @return bool True if directory exists or was created successfully
 */
function ensureStorageDirectory($category, $subdir = null, $mode = 0775)
{
    $dir = getStorageDirectory($category, $subdir);
    
    if (!is_dir($dir)) {
        return mkdir($dir, $mode, true);
    }
    
    return true;
}
