<?php
class User extends Model
{
    // The name of the database table associated with this model
    protected $table = 'users';

    /**
     * Finds a user by their email address.
     *
     * @param string $email Email address to search for.
     * @return array|null Associative user record or null if not found.
     */
    public function findByEmail(string $email): ?array
    {
        Logger::info("Searching for user with email: $email");

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

        if ($result) {
            Logger::info("User found with email: $email");
        } else {
            Logger::warning("No user found with email: $email");
        }
        return $result;
    }

    /**
     * Creates a new user record (email + password only).
     *
     * @param array{email:string,password:string} $data User data.
     * @return bool True on success, false on failure.
     */
    public function create(array $data): bool
    {
        Logger::info("Attempting to create a new user with email: " . $data['email']);

        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT);
        $success = $stmt->execute([$data['email'], $hashed]);

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
     * @param string $email Email address.
     * @param string $password Plain-text password.
     * @return bool True if credentials are valid, false otherwise.
     */
    public function verify(string $email, string $password): bool
    {
        Logger::info("Attempting to verify user with email: $email");

        $user = $this->findByEmail($email);
        if (!$user) {
            Logger::warning("Verification failed: No user found with email: $email");
            return false;
        }

        $isValid = password_verify($password, $user['password']);
        if ($isValid) {
            Logger::info("Password verification successful for email: $email");
        } else {
            Logger::warning("Password verification failed for email: $email");
        }
        return $isValid;
    }

    /**
     * Check if a user exists by email.
     *
     * @param string $email Email to check.
     * @return bool True if exists, false otherwise.
     */
    public function existsByEmail(string $email): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Check if a user exists by registration number.
     *
     * @param string $regNo Registration number to check.
     * @return bool True if exists, false otherwise.
     */
    public function existsByRegNo(string $regNo): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE reg_no = ? LIMIT 1");
        $stmt->execute([$regNo]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Create a user with profile fields.
     *
     * @param array{
     *   first_name:string,
     *   last_name:string,
     *   email:string,
     *   reg_no:string,
     *   password:string
     * } $data User profile data.
     * @return int|false New user ID on success, false on failure.
     */
    public function createWithProfile(array $data)
    {
        try {
            $sql = "INSERT INTO users (first_name, last_name, email, reg_no, password, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $this->db->prepare($sql);
            $hashed = password_hash($data['password'], PASSWORD_BCRYPT);

            $ok = $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['reg_no'],
                $hashed
            ]);
            
            if (!$ok) {
                Logger::error("Failed to create user: " . implode(' | ', $stmt->errorInfo()));
                return false;
            }else{
                Logger::info("User successfully created with email: " . $data['email']);
            }
            return (int)$this->db->lastInsertId();
        } catch (Throwable $e) {
            Logger::error("DB error creating user: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Find a user by registration number.
     * @param string $regNo
     * @return array|null
     */
    public function findByRegNo(string $regNo): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE LOWER(reg_no) = ? LIMIT 1");
        $stmt->execute([strtolower($regNo)]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Find a user by primary key ID.
     * @param int $id User ID.
     * @return array|null User row or null.
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, first_name, last_name, email, reg_no, created_at, updated_at
            FROM {$this->table}
            WHERE id = ? LIMIT 1
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}