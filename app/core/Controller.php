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
}