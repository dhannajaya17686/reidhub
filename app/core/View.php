<?php
class View
{
    /**
     * Renders a view file and passes data to it.
     *
     * @param string $view The name of the view file to render (without the .php extension).
     * @param array $data An associative array of data to be extracted and passed to the view.
     */
    public static function render($view, $data = [])
    {
        // Log the view being rendered and the data passed
        Logger::info("Rendering view: $view with data: " . json_encode($data));

        // Extract the data array into variables for use in the view
        extract($data);

        // Include the specified view file
        require_once __DIR__ . '/../views/' . $view . '.php';

        // Log successful rendering of the view
        Logger::info("Successfully rendered view: $view");
    }
}