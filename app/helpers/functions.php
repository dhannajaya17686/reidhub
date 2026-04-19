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

/**
 * Get time ago string (e.g., "5 minutes ago")
 * 
 * @param string $datetime DateTime string
 * @return string Human readable time difference
 */
function getTimeAgo($datetime)
{
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = round($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        $weeks = round($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    }
}

/**
 * Get user initials from first and last name
 * 
 * @param string $firstName First name
 * @param string $lastName Last name
 * @return string User initials (e.g., "JS" for John Smith)
 */
function getUserInitials($firstName = '', $lastName = '')
{
    $first = strtoupper(substr($firstName, 0, 1));
    $last = strtoupper(substr($lastName, 0, 1));
    return ($first ?: 'U') . ($last ?: '');
}

