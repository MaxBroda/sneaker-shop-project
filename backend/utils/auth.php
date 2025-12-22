<?php
/**
 * Authentication Utilities
 * Centralized authentication handling for all API endpoints
 */

require_once __DIR__ . '/db_connection.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/response.php';

class Auth
{
    private static ?PDO $pdo = null;

    /**
     * Initialize PDO connection
     */
    private static function getPdo(): PDO
    {
        if (self::$pdo === null) {
            global $pdo;
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    /**
     * Extract Bearer token from Authorization header
     * Handles various server configurations
     */
    public static function extractToken(): ?string
    {
        $authHeader = null;

        // Try multiple methods to get Authorization header
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
        }

        if (!$authHeader && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $authHeader = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (!$authHeader && function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        }

        if (!$authHeader) {
            return null;
        }

        // Extract Bearer token
        if (preg_match('/Bearer\s+(.+)$/i', $authHeader, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    /**
     * Hash a token for secure storage
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Generate a new authentication token
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Create and store a new token for a user
     * Returns the plain token (store the hash)
     */
    public static function createToken(int $userId): string
    {
        $pdo = self::getPdo();
        $token = self::generateToken();
        $hashedToken = self::hashToken($token);
        $expiresAt = date('Y-m-d H:i:s', time() + Config::getTokenExpiry());

        $stmt = $pdo->prepare("
            INSERT INTO user_tokens (user_id, token, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $hashedToken, $expiresAt]);

        return $token;
    }

    /**
     * Validate a token and get the associated user
     * Returns user array or null if invalid/expired
     */
    public static function validateToken(string $token): ?array
    {
        $pdo = self::getPdo();
        $hashedToken = self::hashToken($token);

        $stmt = $pdo->prepare("
            SELECT 
                u.id, 
                u.email, 
                u.first_name, 
                u.last_name, 
                u.role,
                ut.expires_at
            FROM users u
            JOIN user_tokens ut ON u.id = ut.user_id
            WHERE ut.token = ?
        ");
        $stmt->execute([$hashedToken]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        // Check expiration
        if ($result['expires_at'] && strtotime($result['expires_at']) < time()) {
            // Token expired, clean it up
            self::revokeToken($token);
            return null;
        }

        // Remove expires_at from user data
        unset($result['expires_at']);
        return $result;
    }

    /**
     * Revoke a specific token
     */
    public static function revokeToken(string $token): bool
    {
        $pdo = self::getPdo();
        $hashedToken = self::hashToken($token);

        $stmt = $pdo->prepare("DELETE FROM user_tokens WHERE token = ?");
        $stmt->execute([$hashedToken]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Revoke all tokens for a user
     */
    public static function revokeAllTokens(int $userId): void
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM user_tokens WHERE user_id = ?");
        $stmt->execute([$userId]);
    }

    /**
     * Clean up expired tokens (run periodically)
     */
    public static function cleanupExpiredTokens(): int
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("DELETE FROM user_tokens WHERE expires_at < ?");
        $stmt->execute([date('Y-m-d H:i:s')]);
        return $stmt->rowCount();
    }

    /**
     * Get the currently authenticated user
     * Returns null if not authenticated
     */
    public static function user(): ?array
    {
        $token = self::extractToken();
        if (!$token) {
            return null;
        }
        return self::validateToken($token);
    }

    /**
     * Check if request is authenticated
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Require authentication - sends 401 and exits if not authenticated
     */
    public static function require(): array
    {
        $user = self::user();
        if (!$user) {
            ApiResponse::unauthorized('Fehlender oder ungültiger Token');
        }
        return $user;
    }

    /**
     * Require a specific role - sends 403 and exits if wrong role
     */
    public static function requireRole(string $role): array
    {
        $user = self::require();
        if ($user['role'] !== $role) {
            ApiResponse::forbidden("Diese Aktion ist nur für {$role} erlaubt");
        }
        return $user;
    }

    /**
     * Require seller role
     */
    public static function requireSeller(): array
    {
        return self::requireRole('seller');
    }

    /**
     * Require customer role
     */
    public static function requireCustomer(): array
    {
        return self::requireRole('customer');
    }

    /**
     * Get user by ID
     */
    public static function getUserById(int $userId): ?array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT id, email, first_name, last_name, role, created_at 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Get user by email
     */
    public static function getUserByEmail(string $email): ?array
    {
        $pdo = self::getPdo();
        $stmt = $pdo->prepare("
            SELECT id, email, first_name, last_name, password_hash, role, created_at 
            FROM users 
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Verify password for a user
     */
    public static function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, $user['password_hash']);
    }
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use Auth::validateToken() instead
 */
function getUserFromToken(string $token, PDO $pdo): ?array
{
    return Auth::validateToken($token);
}

/**
 * Legacy function for backward compatibility
 * @deprecated Use Auth::require() instead
 */
function authenticate(): array
{
    return Auth::require();
}
