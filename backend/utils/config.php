<?php
/**
 * Application Configuration
 * Loads environment variables and provides centralized config access
 */

class Config
{
    private static ?array $env = null;

    /**
     * Load environment variables from .env file
     */
    public static function load(): void
    {
        if (self::$env !== null) {
            return;
        }

        self::$env = [];
        $envFile = __DIR__ . '/../.env';

        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Skip comments
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse KEY=value
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Remove quotes if present
                    if (preg_match('/^["\'](.*)["\']\s*$/', $value, $matches)) {
                        $value = $matches[1];
                    }

                    self::$env[$key] = $value;
                    putenv("$key=$value");
                }
            }
        }
    }

    /**
     * Get an environment variable with optional default
     */
    public static function get(string $key, $default = null)
    {
        self::load();

        // Check our loaded env first, then system env
        if (isset(self::$env[$key])) {
            return self::$env[$key];
        }

        $value = getenv($key);
        return $value !== false ? $value : $default;
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebug(): bool
    {
        return self::get('DEBUG_MODE', 'false') === 'true';
    }

    /**
     * Get the Mollie API key
     */
    public static function getMollieApiKey(): string
    {
        $key = self::get('MOLLIE_API_KEY');
        if (empty($key)) {
            throw new RuntimeException('MOLLIE_API_KEY is not configured');
        }
        return $key;
    }

    /**
     * Get the application URL
     */
    public static function getAppUrl(): string
    {
        return self::get('APP_URL', 'http://localhost:8080');
    }

    /**
     * Get the frontend URL
     */
    public static function getFrontendUrl(): string
    {
        return self::get('FRONTEND_URL', 'http://localhost:3000');
    }

    /**
     * Get token expiry time in seconds
     * Default: 30 days (2592000 seconds)
     */
    public static function getTokenExpiry(): int
    {
        return (int) self::get('TOKEN_EXPIRY_SECONDS');
    }
}

// Auto-load on include
Config::load();
