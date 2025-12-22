<?php
/**
 * Login API Endpoint
 * Authenticates users and returns a session token
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../utils/validation.php';

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true) ?? [];

    // Validate input
    $validator = Validator::make($data)
        ->required('email', 'E-Mail ist erforderlich')
        ->required('password', 'Passwort ist erforderlich')
        ->email('email');

    if ($validator->fails()) {
        ApiResponse::validationError($validator->getErrors());
    }

    $email = trim($data['email']);
    $password = $data['password'];

    // Find user by email
    $user = Auth::getUserByEmail($email);

    if (!$user || !Auth::verifyPassword($user, $password)) {
        ApiResponse::unauthorized('Ungültige E-Mail oder Passwort');
    }

    // Get user's address
    $addrStmt = $pdo->prepare("
        SELECT street, house_number, city, postal_code, country 
        FROM addresses 
        WHERE user_id = ? AND is_default = 1
    ");
    $addrStmt->execute([$user['id']]);
    $address = $addrStmt->fetch(PDO::FETCH_ASSOC);

    // Create new token
    $token = Auth::createToken($user['id']);

    ApiResponse::success([
        'user' => [
            'id' => $user['id'],
            'email' => $user['email'],
            'firstName' => $user['first_name'],
            'lastName' => $user['last_name'],
            'role' => $user['role'],
            'address' => $address ?: null
        ],
        'token' => $token
    ], 'Login erfolgreich');

} catch (Exception $e) {
    // Log the actual error for debugging
    error_log('Login error: ' . $e->getMessage());
    error_log('Stack trace: ' . $e->getTraceAsString());
    ApiResponse::serverError('Ein Fehler ist aufgetreten: ' . $e->getMessage());
}
