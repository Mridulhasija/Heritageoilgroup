<?php

/**
 * IP-based Rate Limiter for Login Attempts (SQL-based)
 * 
 * Prevents brute force attacks by limiting login attempts per IP address
 * Uses database for better scalability and performance
 */
class RateLimiter
{
    private $conn;
    private $tableName;
    private $maxAttempts;
    private $timeWindow; // in seconds
    private $blockDuration; // in seconds

    /**
     * @param mysqli $conn Database connection
     * @param string $tableName Table name (default: 'tbl_rate_limit')
     * @param int $maxAttempts Maximum attempts allowed (default: 5)
     * @param int $timeWindow Time window in seconds (default: 900 = 15 minutes)
     * @param int $blockDuration Block duration in seconds (default: 3600 = 1 hour)
     */
    public function __construct($conn, $tableName = 'tbl_rate_limit', $maxAttempts = 5, $timeWindow = 900, $blockDuration = 3600)
    {
        $this->conn = $conn;
        $this->tableName = $tableName;
        $this->maxAttempts = $maxAttempts;
        $this->timeWindow = $timeWindow;
        $this->blockDuration = $blockDuration;

        // Create table if it doesn't exist
        $this->createTable();
    }

    /**
     * Create rate limit table if it doesn't exist
     */
    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->tableName}` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `ip_address` VARCHAR(45) NOT NULL,
            `attempts` INT(11) NOT NULL DEFAULT 0,
            `first_attempt` INT(11) NOT NULL,
            `last_attempt` INT(11) NOT NULL,
            `blocked_until` INT(11) NOT NULL DEFAULT 0,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `ip_address` (`ip_address`),
            KEY `blocked_until` (`blocked_until`),
            KEY `last_attempt` (`last_attempt`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->conn->query($sql);
    }

    /**
     * Get client IP address (handles proxies and load balancers)
     * @return string IP address
     */
    private function getClientIP()
    {
        $ipKeys = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    // Validate IP
                    if (filter_var(
                        $ip,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                    ) !== false) {
                        return $ip;
                    }
                }
            }
        }

        return isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    /**
     * Clean up old records (attempts outside time window)
     * @param string $ip IP address
     */
    private function cleanOldAttempts($ip)
    {
        $currentTime = time();
        $cutoffTime = $currentTime - $this->timeWindow;

        $stmt = $this->conn->prepare("UPDATE `{$this->tableName}` 
                                      SET `attempts` = 0, `first_attempt` = 0 
                                      WHERE `ip_address` = ? AND `last_attempt` < ? AND `blocked_until` = 0");
        $stmt->bind_param("si", $ip, $cutoffTime);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Check if IP is currently blocked
     * @param string $ip IP address
     * @return bool True if blocked
     */
    public function isBlocked($ip = null)
    {
        if ($ip === null) {
            $ip = $this->getClientIP();
        }

        $currentTime = time();

        $stmt = $this->conn->prepare("SELECT `blocked_until` FROM `{$this->tableName}` 
                                      WHERE `ip_address` = ? AND `blocked_until` > ?");
        $stmt->bind_param("si", $ip, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();
        $isBlocked = $result->num_rows > 0;
        $stmt->close();

        // Clean up expired blocks
        if (!$isBlocked) {
            $this->cleanExpiredBlocks($ip);
        }

        return $isBlocked;
    }

    /**
     * Clean up expired blocks
     * @param string $ip IP address
     */
    private function cleanExpiredBlocks($ip)
    {
        $currentTime = time();
        $stmt = $this->conn->prepare("UPDATE `{$this->tableName}` 
                                      SET `blocked_until` = 0, `attempts` = 0, `first_attempt` = 0 
                                      WHERE `ip_address` = ? AND `blocked_until` > 0 AND `blocked_until` <= ?");
        $stmt->bind_param("si", $ip, $currentTime);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Record a failed login attempt
     * @param string $ip IP address (optional)
     * @return array ['allowed' => bool, 'remaining' => int, 'blocked_until' => int, 'attempts' => int]
     */
    public function recordAttempt($ip = null)
    {
        if ($ip === null) {
            $ip = $this->getClientIP();
        }

        $currentTime = time();
        $cutoffTime = $currentTime - $this->timeWindow;

        // Clean old attempts first
        $this->cleanOldAttempts($ip);

        // Get or create record
        $stmt = $this->conn->prepare("SELECT `attempts`, `first_attempt`, `blocked_until` 
                                      FROM `{$this->tableName}` 
                                      WHERE `ip_address` = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            // Update existing record
            $attempts = $row['attempts'];
            $firstAttempt = $row['first_attempt'];
            $blockedUntil = $row['blocked_until'];

            // Reset attempts if outside time window
            if ($firstAttempt < $cutoffTime) {
                $attempts = 0;
                $firstAttempt = $currentTime;
            }

            // Increment attempts
            $attempts++;
            $remaining = max(0, $this->maxAttempts - $attempts);

            // Block if max attempts exceeded
            if ($attempts >= $this->maxAttempts) {
                $blockedUntil = $currentTime + $this->blockDuration;
            }

            $stmt = $this->conn->prepare("UPDATE `{$this->tableName}` 
	                                          SET `attempts` = ?, `first_attempt` = ?, 
	                                              `last_attempt` = ?, `blocked_until` = ? 
	                                          WHERE `ip_address` = ?");
            $stmt->bind_param("iiiis", $attempts, $firstAttempt, $currentTime, $blockedUntil, $ip);
            $stmt->execute();
            $stmt->close();

            return [
                'allowed' => ($attempts < $this->maxAttempts),
                'remaining' => $remaining,
                'blocked_until' => $blockedUntil,
                'attempts' => $attempts
            ];
        } else {
            // Create new record
            $attempts = 1;
            $remaining = $this->maxAttempts - 1;
            $blockedUntil = ($attempts >= $this->maxAttempts) ? ($currentTime + $this->blockDuration) : 0;

            $stmt = $this->conn->prepare("INSERT INTO `{$this->tableName}` 
                                          (`ip_address`, `attempts`, `first_attempt`, `last_attempt`, `blocked_until`) 
                                          VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("siiii", $ip, $attempts, $currentTime, $currentTime, $blockedUntil);
            $stmt->execute();
            $stmt->close();

            return [
                'allowed' => true,
                'remaining' => $remaining,
                'blocked_until' => $blockedUntil,
                'attempts' => $attempts
            ];
        }
    }

    /**
     * Clear attempts for an IP (on successful login)
     * @param string $ip IP address (optional)
     */
    public function clearAttempts($ip = null)
    {
        if ($ip === null) {
            $ip = $this->getClientIP();
        }

        $stmt = $this->conn->prepare("DELETE FROM `{$this->tableName}` WHERE `ip_address` = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Get remaining attempts for an IP
     * @param string $ip IP address (optional)
     * @return int Remaining attempts
     */
    public function getRemainingAttempts($ip = null)
    {
        if ($ip === null) {
            $ip = $this->getClientIP();
        }

        $currentTime = time();
        $cutoffTime = $currentTime - $this->timeWindow;

        $stmt = $this->conn->prepare("SELECT `attempts`, `first_attempt` 
                                      FROM `{$this->tableName}` 
                                      WHERE `ip_address` = ?");
        $stmt->bind_param("s", $ip);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if (!$row) {
            return $this->maxAttempts;
        }

        // Reset if outside time window
        if ($row['first_attempt'] < $cutoffTime) {
            return $this->maxAttempts;
        }

        $attempts = $row['attempts'];
        return max(0, $this->maxAttempts - $attempts);
    }

    /**
     * Get time until block expires (in seconds)
     * @param string $ip IP address (optional)
     * @return int Seconds until unblock, 0 if not blocked
     */
    public function getBlockTimeRemaining($ip = null)
    {
        if ($ip === null) {
            $ip = $this->getClientIP();
        }

        $currentTime = time();

        $stmt = $this->conn->prepare("SELECT `blocked_until` FROM `{$this->tableName}` 
                                      WHERE `ip_address` = ? AND `blocked_until` > ?");
        $stmt->bind_param("si", $ip, $currentTime);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $stmt->close();
            return $row['blocked_until'] - $currentTime;
        }

        $stmt->close();
        return 0;
    }

    /**
     * Format time remaining in human-readable format
     * @param int $seconds Seconds remaining
     * @return string Formatted time
     */
    public function formatTimeRemaining($seconds)
    {
        if ($seconds < 60) {
            return $seconds . ' second' . ($seconds !== 1 ? 's' : '');
        } elseif ($seconds < 3600) {
            $minutes = floor($seconds / 60);
            return $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
        } else {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);
            $result = $hours . ' hour' . ($hours !== 1 ? 's' : '');
            if ($minutes > 0) {
                $result .= ' ' . $minutes . ' minute' . ($minutes !== 1 ? 's' : '');
            }
            return $result;
        }
    }

    /**
     * Clean up old records (maintenance function - can be called via cron)
     * Removes records older than time window and expired blocks
     */
    public function cleanup()
    {
        $currentTime = time();
        $cutoffTime = $currentTime - $this->timeWindow;

        // Delete old records that are not blocked
        $stmt = $this->conn->prepare("DELETE FROM `{$this->tableName}` 
                                      WHERE `last_attempt` < ? AND `blocked_until` = 0");
        $stmt->bind_param("i", $cutoffTime);
        $stmt->execute();
        $stmt->close();

        // Clear expired blocks
        $stmt = $this->conn->prepare("UPDATE `{$this->tableName}` 
                                      SET `blocked_until` = 0, `attempts` = 0, `first_attempt` = 0 
                                      WHERE `blocked_until` > 0 AND `blocked_until` <= ?");
        $stmt->bind_param("i", $currentTime);
        $stmt->execute();
        $stmt->close();
    }
}
