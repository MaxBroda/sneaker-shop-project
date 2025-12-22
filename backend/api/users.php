<?php
/**
 * Users API Endpoint
 * Handles user data retrieval
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../models/User.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$userModel = new User($pdo);

try {
    // Get specific user by ID (requires authentication)
    if (isset($_GET['user_id'])) {
        $user = Auth::require();
        $requestedUserId = intval($_GET['user_id']);

        // Users can only access their own data
        if ($user['id'] !== $requestedUserId) {
            ApiResponse::forbidden('Keine Berechtigung');
        }

        $userData = $userModel->getByIdWithAddresses($requestedUserId);

        if (!$userData) {
            ApiResponse::notFound('Benutzer nicht gefunden');
        }

        ApiResponse::success($userData);
    }

    // Get all users (public endpoint for admin/debug - consider removing in production)
    $users = $userModel->getAll();

    ApiResponse::success([
        'count' => count($users),
        'users' => $users
    ]);

} catch (Exception $e) {
    ApiResponse::serverError('Ein Fehler ist aufgetreten');
}
