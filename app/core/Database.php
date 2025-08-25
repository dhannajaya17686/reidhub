<?php
class Database
{
    // Holds the single instance of the Database class
    private static $instance = null;

    // Holds the PDO connection
    private $connection;

    /**
     * Private constructor to prevent direct instantiation.
     * Establishes a database connection using configuration settings.
     */
    private function __construct()
    {
        // Load database configuration
        $config = require __DIR__ . '/../config/config.php';

        try {
            // Create a new PDO connection
            $this->connection = new PDO(
                "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset=utf8",
                $config['DB_USER'],
                $config['DB_PASS']
            );

            // Set PDO error mode to exception
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Log successful database connection
            Logger::info("Database connection established successfully.");
        } catch (PDOException $e) {
            // Log database connection error
            Logger::error("Database connection failed: " . $e->getMessage());
            throw $e; // Re-throw the exception after logging
        }
    }

    /**
     * Returns the single instance of the Database class.
     * If the instance doesn't exist, it creates one.
     *
     * @return Database The singleton instance of the Database class.
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            // Log the creation of a new Database instance
            Logger::info("Creating a new Database instance.");
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Returns the PDO connection.
     *
     * @return PDO The PDO connection object.
     */
    public function getConnection()
    {
        return $this->connection;
    }
}