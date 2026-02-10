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

    /**
     * Generate and save a new OTP for email verification
     *
     * @param string $email Email address
     * @return string|false 6-digit OTP code or false on failure
     */
    public function generateAndSaveOTP(string $email): string|false
    {
        try {
            // Load configuration
            $config = require __DIR__ . '/../config/config.php';
            $otpLength = $config['OTP_LENGTH'] ?? 6;
            $expiryMinutes = $config['OTP_EXPIRY_MINUTES'] ?? 10;

            // Generate random 6-digit OTP
            $otpCode = str_pad(random_int(0, 999999), $otpLength, '0', STR_PAD_LEFT);

            // Calculate expiry time
            $expiresAt = date('Y-m-d H:i:s', time() + ($expiryMinutes * 60));

            // Insert OTP into database
            $stmt = $this->db->prepare("
                INSERT INTO otps (email, otp_code, attempt_count, expires_at, created_at)
                VALUES (?, ?, 0, ?, NOW())
            ");

            $success = $stmt->execute([$email, $otpCode, $expiresAt]);

            if ($success) {
                Logger::info("OTP generated and saved for email: $email");
                return $otpCode;
            } else {
                Logger::error("Failed to save OTP for email: $email");
                return false;
            }
        } catch (Exception $e) {
            Logger::error("Error generating OTP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify an OTP code for an email address
     *
     * @param string $email Email address
     * @param string $otpCode OTP code to verify
     * @return bool True if valid, false otherwise
     */
    public function verifyOTPCode(string $email, string $otpCode): bool
    {
        try {
            $config = require __DIR__ . '/../config/config.php';
            $maxAttempts = $config['OTP_MAX_ATTEMPTS'] ?? 5;

            // Get the OTP record
            $stmt = $this->db->prepare("
                SELECT * FROM otps 
                WHERE email = ? 
                AND otp_code = ? 
                AND is_verified = FALSE
                AND expires_at > NOW()
                LIMIT 1
            ");
            $stmt->execute([$email, $otpCode]);
            $otp = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$otp) {
                // Increment attempt count for invalid codes
                $this->incrementOTPAttempt($email, $otpCode);
                Logger::warning("Invalid OTP attempt for email: $email");
                return false;
            }

            // Check if max attempts exceeded
            if ($otp['attempt_count'] >= $maxAttempts) {
                Logger::warning("Max OTP attempts exceeded for email: $email");
                return false;
            }

            // Mark OTP as verified
            $stmt = $this->db->prepare("
                UPDATE otps 
                SET is_verified = TRUE, verified_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$otp['id']]);

            Logger::info("OTP successfully verified for email: $email");
            return true;
        } catch (Exception $e) {
            Logger::error("Error verifying OTP: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Increment failed OTP attempt count
     *
     * @param string $email Email address
     * @param string $otpCode OTP code
     * @return void
     */
    private function incrementOTPAttempt(string $email, string $otpCode): void
    {
        try {
            $stmt = $this->db->prepare("
                UPDATE otps 
                SET attempt_count = attempt_count + 1
                WHERE email = ? AND otp_code = ? AND is_verified = FALSE
            ");
            $stmt->execute([$email, $otpCode]);
        } catch (Exception $e) {
            Logger::error("Error incrementing OTP attempt: " . $e->getMessage());
        }
    }

    /**
     * Get the latest unverified OTP for an email
     *
     * @param string $email Email address
     * @return array|null OTP record or null
     */
    public function getLatestUnverifiedOTP(string $email): ?array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM otps 
                WHERE email = ? 
                AND is_verified = FALSE
                AND expires_at > NOW()
                ORDER BY created_at DESC
                LIMIT 1
            ");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (Exception $e) {
            Logger::error("Error fetching OTP: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if email is already verified
     *
     * @param string $email Email address
     * @return bool True if a verified OTP exists for this email
     */
    public function isEmailVerified(string $email): bool
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM otps 
                WHERE email = ? AND is_verified = TRUE
                LIMIT 1
            ");
            $stmt->execute([$email]);
            $count = $stmt->fetchColumn();
            return $count > 0;
        } catch (Exception $e) {
            Logger::error("Error checking email verification: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up expired OTP records
     * Can be called periodically via cron or background job
     *
     * @return int Number of deleted records
     */
    public function cleanupExpiredOTPs(): int
    {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM otps 
                WHERE expires_at < NOW() 
                OR (is_verified = TRUE AND verified_at < DATE_SUB(NOW(), INTERVAL 1 DAY))
            ");
            $stmt->execute();
            $deletedCount = $stmt->rowCount();
            Logger::info("Cleaned up $deletedCount expired OTP records");
            return $deletedCount;
        } catch (Exception $e) {
            Logger::error("Error cleaning up OTPs: " . $e->getMessage());
            return 0;
        }
    }
}