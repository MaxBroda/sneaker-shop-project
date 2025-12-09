<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/User.php';

$userModel = new User($pdo);

try {
    if (isset($_GET['user_id'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'] 
            ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] 
            ?? '';
        if (!$authHeader && function_exists('getallheaders')) {
            $headers = array_change_key_case(getallheaders(), CASE_LOWER);
            $authHeader = $headers['authorization'] ?? '';
        }

        if (!$authHeader || !preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Nicht authentifiziert']);
            exit;
        }

        $token = $matches[1];
        $user = getUserFromToken($token, $pdo);

        if (!$user) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Ungültiger Token']);
            exit;
        }

        $userId = intval($_GET['user_id']);

        if ($user['id'] != $userId) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Keine Berechtigung']);
            exit;
        }

        $userData = $userModel->getByIdWithAddresses($userId);

        if (!$userData) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Benutzer nicht gefunden']);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data' => $userData
        ]);
        exit;
    }

    $users = $userModel->getAll();

    echo json_encode([
        'success' => true,
        'count' => count($users),
        'users' => $users
    ]);
} catch (Exception $e) {
    http_response_code(500);
    error_log("Users API Error: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
