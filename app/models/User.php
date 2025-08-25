<?php
class User extends Model
{
    // The name of the database table associated with this model
    protected $table = 'users';

    /**
     * Finds a user by their email address.
     *
     * @param string $email The email address to search for.
     * @return array|false The user record as an associative array, or false if not found.
     */
    public function findByEmail($email)
    {
        // Log the email being searched
        Logger::info("Searching for user with email: $email");

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Log the result of the search
        if ($result) {
            Logger::info("User found with email: $email");
        } else {
            Logger::warning("No user found with email: $email");
        }

        return $result;
    }

    /**
     * Creates a new user record in the database.
     *
     * @param array $data An associative array containing 'email' and 'password'.
     * @return bool True on success, false on failure.
     */
    public function create($data)
    {
        // Log the creation attempt
        Logger::info("Attempting to create a new user with email: " . $data['email']);

        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $success = $stmt->execute([$data['email'], $hashed]);

        // Log the result of the creation
        if ($success) {
            Logger::info("User successfully created with email: " . $data['email']);
        } else {
            Logger::error("Failed to create user with email: " . $data['email']);
        }

        return $success;
    }

    /**
     * Verifies a user's email and password.
     *
     * @param string $email The user's email address.
     * @param string $password The user's plain-text password.
     * @return bool True if the credentials are valid, false otherwise.
     */
    public function verify($email, $password)
    {
        // Log the verification attempt
        Logger::info("Attempting to verify user with email: $email");

        $user = $this->findByEmail($email);

        if ($user) {
            // Verify the password
            $isValid = password_verify($password, $user['password']);

            // Log the result of the verification
            if ($isValid) {
                Logger::info("Password verification successful for email: $email");
            } else {
                Logger::warning("Password verification failed for email: $email");
            }

            return $isValid;
        } else {
            // Log if the user was not found
            Logger::warning("Verification failed: No user found with email: $email");
            return false;
        }
    }
}