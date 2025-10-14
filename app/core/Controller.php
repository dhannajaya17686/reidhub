<?php

class Controller
{
    /**
     * Loads a view file and passes data to it.
     *
     * @param string $view The name of the view file to load (without the .php extension).
     * @param array $data An associative array of data to be extracted and passed to the view.
     */
    public function view($view, $data = [])
    {
        // Log the view being loaded along with the data being passed
        Logger::info("Loading view: $view with data: " . json_encode($data));

        // Extract the data array into variables for use in the view
        extract($data);

        // Include the specified view file from the views directory
        require_once __DIR__ . '/../views/' . $view . '.php';

        // Log that the view was successfully loaded
        Logger::info("Successfully loaded view: $view");
    }

    /**
     * Renders a view inside the main app layout with sidebar and header components.
     * Use this for pages that are part of the main application (not login/logout pages).
     *
     * @param string $view The middle content view file (without the .php extension).
     * @param array $data An associative array of data to be extracted and passed to the view.
     * @param string $title The page title (optional, defaults to 'ReidHub').
     */
    public function viewApp($view, $data = [], $title = 'ReidHub')
    {
        // Log the app view being loaded
        Logger::info("Loading app view: $view with data: " . json_encode($data) . " and title: $title");

        // Capture the middle content
        ob_start();
        extract($data);
        require_once __DIR__ . '/../views/' . $view . '.php';
        $content = ob_get_clean();

        // Pass $content, $title and $data to the layout
        extract($data);
        $data['content'] = $content;
        $data['title'] = $title;
        require_once __DIR__ . '/../views/User/layout.php';

        // Log that the app view was successfully loaded
        Logger::info("Successfully loaded app view: $view with layout and title: $title");
    }
}