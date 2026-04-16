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
        // Try exact match first
        $route = $this->routes[$uri][$method] ?? null;

        // If no exact match, try pattern matching for dynamic routes
        if (!$route) {
            $route = $this->matchDynamicRoute($uri, $method);
        }

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

    /**
     * Match dynamic routes with parameters like /orders/{id}/chat
     */
    private function matchDynamicRoute($uri, $method)
    {
        foreach ($this->routes as $routePattern => $methods) {
            // Check if this route pattern has the requested method
            if (!isset($methods[$method])) {
                continue;
            }

            // Convert route pattern to regex (e.g., /orders/{id}/chat -> /orders/(\d+)/chat)
            $regex = $this->patternToRegex($routePattern);
            
            if (preg_match($regex, $uri, $matches)) {
                // Extract parameters and store in GET or POST
                $this->extractRouteParameters($routePattern, $uri);
                return $methods[$method];
            }
        }

        return null;
    }

    /**
     * Convert route pattern to regex pattern
     * E.g., /orders/{id}/chat -> /^\/orders\/([^\/]+)\/chat$/
     */
    private function patternToRegex($pattern)
    {
        // Escape special regex characters except for the parameter placeholders
        $regex = preg_quote($pattern, '/');
        
        // Replace escaped braces with capture group
        // preg_quote converts { to \{ and } to \}
        $regex = str_replace('\\{id\\}', '([0-9]+)', $regex);
        
        return '/^' . $regex . '$/';
    }

    /**
     * Extract dynamic parameters from URI and store in $_GET
     */
    private function extractRouteParameters($pattern, $uri)
    {
        // Get parameter names from pattern
        preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $pattern, $paramNames);
        $names = $paramNames[1] ?? [];

        if (empty($names)) {
            return;
        }

        // Get values from URI
        $regex = $this->patternToRegex($pattern);
        if (preg_match($regex, $uri, $matches)) {
            // Skip the first match (full string)
            array_shift($matches);
            
            // Assign values to $_GET
            foreach ($names as $index => $name) {
                if (isset($matches[$index])) {
                    $_GET[$name] = $matches[$index];
                }
            }
        }
    }
}