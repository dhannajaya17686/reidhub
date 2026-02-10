<?php

/**
 * RateLimiter - Prevents abuse of OTP and other time-sensitive operations
 * Uses file-based storage for rate limiting across requests
 */
class RateLimiter
{
    private $storePath;
    private $prefix;

    public function __construct(string $prefix = 'rate_limit')
    {
        $this->prefix = $prefix;
        $this->storePath = __DIR__ . '/../../storage/rate_limit';
        
        // Ensure directory exists
        if (!is_dir($this->storePath)) {
            @mkdir($this->storePath, 0755, true);
        }
    }

    /**
     * Check if an action is rate-limited
     *
     * @param string $identifier Unique identifier (email, IP, user_id, etc.)
     * @param string $action Action name (e.g., 'otp_send', 'otp_verify')
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $windowSeconds Time window in seconds
     * @return bool True if rate-limited, false if allowed
     */
    public function isRateLimited(
        string $identifier,
        string $action,
        int $maxAttempts = 5,
        int $windowSeconds = 300
    ): bool {
        $key = $this->getKey($identifier, $action);
        $file = $this->getFilePath($key);

        // Get current attempts
        $attempts = $this->getAttempts($file);

        // Clean old attempts outside window
        $now = time();
        $attempts = array_filter($attempts, fn($time) => ($now - $time) < $windowSeconds);

        if (count($attempts) >= $maxAttempts) {
            return true; // Rate-limited
        }

        // Record this attempt
        $attempts[] = $now;
        $this->saveAttempts($file, $attempts);

        return false;
    }

    /**
     * Get remaining attempts
     *
     * @param string $identifier Unique identifier
     * @param string $action Action name
     * @param int $maxAttempts Maximum attempts allowed
     * @param int $windowSeconds Time window in seconds
     * @return int Number of remaining attempts
     */
    public function getRemainingAttempts(
        string $identifier,
        string $action,
        int $maxAttempts = 5,
        int $windowSeconds = 300
    ): int {
        $key = $this->getKey($identifier, $action);
        $file = $this->getFilePath($key);

        $attempts = $this->getAttempts($file);
        $now = time();
        $attempts = array_filter($attempts, fn($time) => ($now - $time) < $windowSeconds);

        return max(0, $maxAttempts - count($attempts));
    }

    /**
     * Get time until rate limit resets
     *
     * @param string $identifier Unique identifier
     * @param string $action Action name
     * @param int $windowSeconds Time window in seconds
     * @return int Seconds until reset (0 if not limited)
     */
    public function getResetTime(
        string $identifier,
        string $action,
        int $windowSeconds = 300
    ): int {
        $key = $this->getKey($identifier, $action);
        $file = $this->getFilePath($key);

        $attempts = $this->getAttempts($file);
        if (empty($attempts)) {
            return 0;
        }

        $oldestAttempt = min($attempts);
        $now = time();
        $resetTime = $oldestAttempt + $windowSeconds - $now;

        return max(0, $resetTime);
    }

    /**
     * Reset rate limit for an identifier/action
     *
     * @param string $identifier Unique identifier
     * @param string $action Action name
     * @return void
     */
    public function reset(string $identifier, string $action): void
    {
        $key = $this->getKey($identifier, $action);
        $file = $this->getFilePath($key);

        if (file_exists($file)) {
            @unlink($file);
        }
    }

    /**
     * Get rate limit key
     *
     * @param string $identifier Unique identifier
     * @param string $action Action name
     * @return string Rate limit key
     */
    private function getKey(string $identifier, string $action): string
    {
        return hash('sha256', "{$this->prefix}:{$identifier}:{$action}");
    }

    /**
     * Get file path for storing attempts
     *
     * @param string $key Rate limit key
     * @return string File path
     */
    private function getFilePath(string $key): string
    {
        return $this->storePath . '/' . substr($key, 0, 2) . '/' . substr($key, 2) . '.json';
    }

    /**
     * Get attempts from file
     *
     * @param string $file File path
     * @return array Array of timestamps
     */
    private function getAttempts(string $file): array
    {
        if (!file_exists($file)) {
            return [];
        }

        try {
            $data = json_decode(file_get_contents($file), true);
            return is_array($data) ? $data : [];
        } catch (Exception $e) {
            Logger::error("Error reading rate limit file: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Save attempts to file
     *
     * @param string $file File path
     * @param array $attempts Array of timestamps
     * @return void
     */
    private function saveAttempts(string $file, array $attempts): void
    {
        try {
            $dir = dirname($file);
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }

            file_put_contents($file, json_encode($attempts));
        } catch (Exception $e) {
            Logger::error("Error writing rate limit file: " . $e->getMessage());
        }
    }

    /**
     * Clean up old rate limit files (run periodically via cron)
     *
     * @param int $maxAge Maximum age of files in seconds (default: 24 hours)
     * @return int Number of deleted files
     */
    public function cleanup(int $maxAge = 86400): int
    {
        $deleted = 0;
        $now = time();

        try {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($this->storePath),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($files as $file) {
                if ($file->isFile() && ($now - $file->getMTime()) > $maxAge) {
                    if (@unlink($file->getRealPath())) {
                        $deleted++;
                    }
                }
            }
        } catch (Exception $e) {
            Logger::error("Error cleaning up rate limit files: " . $e->getMessage());
        }

        return $deleted;
    }
}
