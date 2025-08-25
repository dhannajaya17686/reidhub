<?php
// Enable error reporting based on the environment
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autoloader for core, controllers, models, etc.
spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $paths = [
        __DIR__ . '/../app/core/' . $classPath . '.php',
        __DIR__ . '/../app/controllers/' . $classPath . '.php',
        __DIR__ . '/../app/models/' . $classPath . '.php'
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }

    // Log or throw an error if the class file is not found
    Logger::error("Class '$class' not found in the specified paths.");
    throw new Exception("Class '$class' not found.");
});

// Load routes
$routes = require __DIR__ . '/../app/routes/web.php';
if (!is_array($routes)) {
    Logger::error("Routes file did not return a valid array.");
    throw new Exception("Routes file did not return a valid array.");
}

// Initialize the Router
$router = new Router($routes);

// Parse the URI and HTTP method
$uri = filter_var(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), FILTER_SANITIZE_URL);
$method = $_SERVER['REQUEST_METHOD'];

// Log the incoming request
Logger::info("Incoming request: URI = $uri, Method = $method");

// Dispatch the request using the Router
$router->dispatch($uri, $method);