<?php
/**
 * Standardized API Response Helpers
 * Ensures consistent response format across all endpoints
 */

class ApiResponse
{
    /**
     * Send a success response
     *
     * @param mixed $data Optional data to include
     * @param string|null $message Optional success message
     * @param int $statusCode HTTP status code (default 200)
     */
    public static function success($data = null, ?string $message = null, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = ['success' => true];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($message !== null) {
            $response['message'] = $message;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Send an error response
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code
     * @param array|null $errors Optional array of specific errors
     */
    public static function error(string $message, int $statusCode = 400, ?array $errors = null): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');

        $response = [
            'success' => false,
            'message' => $message
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        echo json_encode($response);
        exit;
    }

    /**
     * Send a validation error response
     *
     * @param array $errors Validation errors
     * @param string $message General error message
     */
    public static function validationError(array $errors, string $message = 'Validierungsfehler'): void
    {
        self::error($message, 422, $errors);
    }

    /**
     * Send an unauthorized response
     *
     * @param string $message Error message
     */
    public static function unauthorized(string $message = 'Nicht autorisiert'): void
    {
        self::error($message, 401);
    }

    /**
     * Send a forbidden response
     *
     * @param string $message Error message
     */
    public static function forbidden(string $message = 'Keine Berechtigung'): void
    {
        self::error($message, 403);
    }

    /**
     * Send a not found response
     *
     * @param string $message Error message
     */
    public static function notFound(string $message = 'Nicht gefunden'): void
    {
        self::error($message, 404);
    }

    /**
     * Send a server error response
     *
     * @param string $message Error message
     */
    public static function serverError(string $message = 'Serverfehler'): void
    {
        self::error($message, 500);
    }

    /**
     * Send a method not allowed response
     */
    public static function methodNotAllowed(): void
    {
        self::error('Methode nicht erlaubt', 405);
    }
}
