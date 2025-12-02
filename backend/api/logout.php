<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:3000');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../utils/db_connection.php';
require_once __DIR__ . '/../utils/cors.php';
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../models/Cart.php';

session_start();

$headers = array_change_key_case(getallheaders(), CASE_LOWER);
$authHeader = $headers['authorization'] ?? '';

if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Fehlender oder ungültiger Token'
    ]);
    exit;
}

$token = substr($authHeader, 7);

try {
    $user = getUserFromToken($token, $pdo);
    $userId = $user ? $user['id'] : null;

    $stmt = $pdo->prepare("DELETE FROM user_tokens WHERE token = ?");
    $stmt->execute([$token]);

    if ($stmt->rowCount() > 0) {
        $oldSessionId = $_SESSION['cart_session_id'] ?? null;
        
        if ($oldSessionId) {
            $deleteStmt = $pdo->prepare("DELETE FROM cart_items WHERE session_id = ? AND user_id IS NULL");
            $deleteStmt->execute([$oldSessionId]);
        }
        
        session_regenerate_id(true);
        $_SESSION['cart_session_id'] = bin2hex(random_bytes(16));
        
        echo json_encode([
            'success' => true,
            'message' => 'Erfolgreich ausgeloggt'
        ]);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Ungültiger oder abgelaufener Token'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server fehler: ' . $e->getMessage()
    ]);
}
