<?php
require_once __DIR__ . '/db_connection.php';

function authenticate()
{
    global $pdo;

    $authHeader = null;

    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        }
    }

    if (!$authHeader && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    }

    if (!$authHeader && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
        } elseif (isset($headers['authorization'])) {
            $authHeader = $headers['authorization'];
        }
    }

    if (!$authHeader) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Fehlender Token']);
        exit;
    }

    $token = trim(str_replace('Bearer ', '', $authHeader));
    $stmt = $pdo->prepare("
        SELECT users.id, users.email, users.first_name, users.last_name, users.role
        FROM users
        JOIN user_tokens ON users.id = user_tokens.user_id
        WHERE user_tokens.token = ?
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Ungültiger Token']);
        exit;
    }

    return $user;
}

function getUserFromToken($token, $pdo) {
    try {
        error_log("[AUTH] getUserFromToken called with token: " . substr($token, 0, 20) . "...");
        
        $stmt = $pdo->prepare("
            SELECT users.id, users.email, users.first_name, users.last_name, users.role
            FROM users
            JOIN user_tokens ON users.id = user_tokens.user_id
            WHERE user_tokens.token = ?
        ");
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            error_log("[AUTH] User found: " . $user['email']);
        } else {
            error_log("[AUTH] No user found for token");
            
            $checkStmt = $pdo->prepare("SELECT COUNT(*) as count FROM user_tokens WHERE token = ?");
            $checkStmt->execute([$token]);
            $tokenExists = $checkStmt->fetch(PDO::FETCH_ASSOC);
            error_log("[AUTH] Token exists in user_tokens: " . ($tokenExists['count'] > 0 ? 'yes' : 'no'));
        }
        
        return $user;
    } catch (Exception $e) {
        error_log("[AUTH] Exception in getUserFromToken: " . $e->getMessage());
        return null;
    }
}
