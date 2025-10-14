<?php
class Model
{
    // Holds the database connection
    protected $db;

    /**
     * Constructor for the Model class.
     * Initializes the database connection by retrieving it from the Database singleton.
     */
    public function __construct()
    {
        // Log the initialization of the Model
        Logger::info("Initializing the Model and establishing a database connection.");
        // Get the database connection from the Database singleton
        $this->db = Database::getInstance()->getConnection();
        // Log successful database connection assignment
        Logger::info("Database connection successfully assigned to the Model.");
    }
}