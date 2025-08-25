<?php
class Router
{
    private $routes = [];

    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function dispatch($uri, $method)
    {
        // Match the route
        $route = $this->routes[$uri][$method] ?? null;

        if ($route) {
            // Extract the controller and action
            [$controllerName, $actionName] = explode('@', $route);

            // Handle subdirectories (e.g., Auth folder)
            $controllerPath = __DIR__ . '/../controllers/' . str_replace('_', '/', $controllerName) . '.php';

            // Check if the controller file exists
            if (file_exists($controllerPath)) {
                require_once $controllerPath;

                // Check if the controller class exists
                if (class_exists($controllerName)) {
                    Logger::info("Instantiating controller: $controllerName");

                    $controller = new $controllerName();

                    // Check if the action method exists
                    if (method_exists($controller, $actionName)) {
                        Logger::info("Calling action: $actionName on controller: $controllerName");
                        $controller->$actionName();
                        return;
                    } else {
                        Logger::error("Action '$actionName' not found in controller '$controllerName'.");
                    }
                } else {
                    Logger::error("Controller class '$controllerName' not found.");
                }
            } else {
                Logger::error("Controller file '$controllerPath' not found.");
            }
        } else {
            Logger::error("Route not found: URI = $uri, Method = $method");
        }

        // Default 404 response
        http_response_code(404);
        echo "404 Not Found";
    }
}